<?php
namespace Wergimity\Commander;

use App;
use Wergimity\Commander\Commander;

trait CommanderTrait
{
    protected function execute($command, array $input = null, array $decorators = null)
    {
        return $this->getCommander()->execute($command, $input, $decorators);
    }

    /** @return Commander */
    protected function getCommander()
    {
        return App::make(Commander::class);
    }
} 