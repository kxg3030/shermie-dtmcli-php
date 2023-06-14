<?php

namespace Sett\Dtmcli\transaction\contract;

/**
 * Interface ITransCommon
 * @package sett\transaction\contract
 * tcc|xa|msg
 */
interface ITransWithSubmit
{
    public function submit();
}