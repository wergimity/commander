<?php
namespace Wergimity\Commander;

class CommandTranslator
{
    /**
     * @param string $command
     *
     * @return string
     */
    public function toValidator($command)
    {
        return $this->getNeighborClass($command, 'Validator');
    }

    /**
     * @param string $command
     *
     * @return string
     */
    public function toHandler($command)
    {
        return $this->getNeighborClass($command, 'Handler');
    }

    /**
     * @param string $class
     * @param string $suffix
     *
     * @return string
     */
    protected function getNeighborClass($class, $suffix)
    {
        $reflection = new \ReflectionClass($class);
        $newClass = str_replace('Command', $suffix, $reflection->getShortName());
        $result = sprintf('%s\\%s', $reflection->getNamespaceName(), $newClass);

        return $result;
    }
} 