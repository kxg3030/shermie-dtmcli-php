<?php

namespace sett\transaction\contract;

/**
 * Interface ITransCommon
 * @package sett\transaction\contract
 * tcc|xa|msg
 */
interface ITransExcludeSaga
{

    public function withNewGid(string $transGid);

    public function prepare();

    public function submit();

    public function abort();
}