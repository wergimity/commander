<?php
namespace Wergimity\Commander\Contract;

use Wergimity\Commander\Contract\CommandInterface;

interface CommandDecoratorInterface
{
    /** @param CommandInterface $command */
    public function decorate($command);
} 