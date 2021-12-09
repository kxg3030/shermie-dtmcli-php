<?php

namespace sett\transaction\contract;

/**
 * Interface ITransCommon
 * @package sett\transaction\contract
 * tcc|xa|msg
 */
interface ITransWithSaga
{

    public function withGid(string $transGid);

    public function submit();

    public function abort();
}