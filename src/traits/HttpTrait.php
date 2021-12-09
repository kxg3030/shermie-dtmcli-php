<?php

namespace sett\traits;

use GuzzleHttp\Client;

trait HttpTrait
{
    protected $client;


    protected function client(array $config = []): Client {
        return new Client(array_merge([
            "verify"  => false,
            "timeout" => 60
        ], $config));
    }
}