<?php
namespace Wergimity\Commander\Contract;

use Illuminate\Routing\Redirector;
use Illuminate\Validation\Validator;

interface CommandValidatorInterface
{
    /** @return array */
    public function rules();

    /** @return Redirector */
    public function failed();
}