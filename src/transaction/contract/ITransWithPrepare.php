<?php

namespace Sett\Dtmcli\transaction\contract;

/**
 * Interface ITransCommon
 * @package sett\transaction\contract
 * tcc|xa|msg
 */
interface ITransWithPrepare
{

    public function prepare();

}