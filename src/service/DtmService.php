<?php

namespace sett\service;

use sett\constant\DtmConstant;
use sett\traits\HttpTrait;
use sett\traits\SingletonTrait;
use sett\traits\UtilsTrait;

class DtmService extends BaseService
{
    use SingletonTrait;
    use HttpTrait;
    use UtilsTrait;


    public function createNewGid() {
        $client = $this->client();
        $body   = $client->get($this->combineUrl(DtmConstant::GetNewGidPath))->getBody()->getContents();
        return json_decode($body, JSON_UNESCAPED_UNICODE);
    }

    /**
     * @param string $dtmHost
     */
    public function setDtmHost(string $dtmHost) {
        $this->dtmHost = $dtmHost;
    }
}