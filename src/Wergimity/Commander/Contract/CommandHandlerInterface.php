<?php
namespace Wergimity\Commander\Contract;

use Wergimity\Commander\Contract\CommandInterface;

interface CommandHandlerInterface
{
    /**
     * @param CommandInterface $command
     *
     * @return mixed
     */
    public function handle($command);
}