<?php

namespace @namespace\Contracts;

use Config;

class @module_base_titleContract
{
    protected function getModule() {
        return '@module';
    }

    protected function getModuleId() {
        return Config::get('@module.constants.MODULE_ID');
    }
}
