<?php

namespace Sett\Dtmcli\transaction\contract;

interface IDatabase
{
    public function execute(string $query):bool;

    public function query(string $query): bool;

    public function rollback();

    public function commit();
}