<?php

namespace sett\dtmcli\service;

use sett\dtmcli\constant\DtmConstant;
use sett\dtmcli\traits\HttpTrait;
use sett\dtmcli\traits\SingletonTrait;
use sett\dtmcli\traits\UtilsTrait;

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