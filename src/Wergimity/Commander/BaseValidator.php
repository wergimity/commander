<?php
namespace Wergimity\Commander;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Wergimity\Commander\Contract\CommandInterface;
use Wergimity\Commander\Contract\CommandValidatorInterface;
use Wergimity\Commander\Exception\CommandValidationException;
use Validator;

abstract class BaseValidator implements CommandValidatorInterface
{
    /** @var  CommandInterface */
    protected $command;

    /** @var  Redirector */
    protected $redirect;

    public function __construct(Redirector $redirector, CommandInterface $command)
    {
        $this->redirect = $redirector;
        $this->command = $command;
    }

    public function validate()
    {
        $data = get_object_vars($this->command);
        $validator = Validator::make($data, $this->rules());

        $method = 'moreValidation';
        if(method_exists($this, $method)) {
            $this->$method($validator);
        }

        if($validator->fails()) {
            /** @var RedirectResponse $redirect */
            $redirect = $this->failed();

            $response = $redirect
                ->withInput()
                ->withErrors($validator);

            throw new CommandValidationException($response);
        }
    }
}