<?php

namespace sett\transaction\contract;

interface IDatabase
{
    public function execute(string $query);

    public function query(string $query): bool;

    public function rollback();

    public function commit();
}