<?php
namespace Wergimity\Commander;

use Event;
use Illuminate\Foundation\Application;
use Input;
use Wergimity\Commander\BaseValidator;
use Wergimity\Commander\CommandTranslator;
use Wergimity\Commander\Contract\CommandInterface;
use Wergimity\Commander\Contract\DispachableCommandInterface;
use Str;
use Validator;

class Commander
{
    /** @var  Application */
    protected $app;

    protected $translator;

    function __construct(Application $app, CommandTranslator $translator)
    {
        $this->app = $app;
        $this->translator = $translator;
    }

    public function execute($command, array $input = null, array $decorators = null)
    {
        $commandClass = $this->resolveCommand($command);
        $validator = $this->resolveValidator($commandClass);
        $handler = $this->resolveHandler($command);
        $event = $this->isDispatchable($commandClass) ? $this->getEventName($command) : null;

        if(is_null($input)) {
            $input = Input::all();
        }

        $this->mapCommandProperties($commandClass, $input);


        if(!is_null($validator)) {
            $validator->validate();
        }

        if(!is_null($decorators)) {
            $this->decorate($commandClass, $decorators);
        }

        if($event) Event::fire($event . '.before', [$commandClass]);
        $result =  $handler->handle($commandClass);
        if($event) Event::fire($event . '.after', [$commandClass, $result]);

        return $result;
    }

    protected function isDispatchable($class)
    {
        return $class instanceof DispachableCommandInterface;
    }

    protected function getEventName($command)
    {
        $result = explode('\\', $command);
        array_pop($result);
        $result = array_map(['Str', 'snake'], $result);

        $result = implode('.', $result);

        return $result;
    }

    /**
     * @param $command
     *
     * @return BaseValidator|null
     */
    protected function resolveValidator($command)
    {
        $validator = $this->translator->toValidator(get_class($command));
        if(!class_exists($validator)) {
            return null;
        }

        /** @var BaseValidator $result */
        $result = $this->app->make($validator, ['command' => $command]);

        return $result;
    }

    protected function decorate($command, array $decorators) {
        foreach($decorators as $class) {
            if(!class_exists($class)) {
                throw new \Exception('Decorator ' . $class . ' not found');
            }

            $decorator = $this->app->make($class);
            $decorator->decorate($command);
        }
    }

    protected function resolveCommand($command)
    {
        if(!class_exists($command)) {
            throw new \Exception('Command does not exist');
        }

        $reflection = new \ReflectionClass($command);

        if(!$reflection->isInstantiable()) {
            throw new \Exception('Command is not instantiable');
        }

        $interface = CommandInterface::class;
        if(!$reflection->implementsInterface($interface))
        {
            throw new \Exception("Command must implement {$interface}");
        }

        return $this->app->make($command);
    }

    protected function mapCommandProperties(&$class, $input)
    {
        foreach($input as $key => $value) {
            if(property_exists($class, $key)) {
                $class->$key = $value;
            }
        }
    }

    protected function resolveHandler($command)
    {
        $handler = $this->translator->toHandler($command);
        if(!class_exists($handler)) {
            throw new \Exception('Handler for command not found!');
        }

        return $this->app->make($handler);
    }

}