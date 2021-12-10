<?php

namespace sett\dtmcli\transaction\contract;

/**
 * Interface ITransCommon
 * @package sett\transaction\contract
 * tcc|xa|msg
 */
interface ITransWithSaga
{
    public function submit();

    public function abort();
}