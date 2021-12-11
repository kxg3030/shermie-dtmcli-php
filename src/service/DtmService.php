<?php

namespace Sett\Dtmcli\service;

use Sett\Dtmcli\constant\DtmConstant;
use Sett\Dtmcli\traits\HttpTrait;
use Sett\Dtmcli\traits\SingletonTrait;
use Sett\Dtmcli\traits\UtilsTrait;

class DtmService extends BaseService
{
    use SingletonTrait;
    use HttpTrait;
    use UtilsTrait;


    public function createNewGid() {
        $client = $this->client();
        $body   = $client->get($this->combineUrl(DtmConstant::GetNewGidPath))->getBody()->getContents();
        return json_decode($body, false);
    }

    /**
     * @param string $dtmHost
     */
    public function setDtmHost(string $dtmHost) {
        $this->dtmHost = $dtmHost;
    }
}