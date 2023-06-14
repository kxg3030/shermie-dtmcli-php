<?php

namespace Sett\Dtmcli\transaction\contract;

/**
 * Interface ITransCommon
 * @package sett\transaction\contract
 * tcc|xa|msg
 */
interface ITransWithAbort
{

    public function abort();

}