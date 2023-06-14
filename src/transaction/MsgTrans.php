<?php

namespace Sett\Dtmcli\transaction;

use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Sett\Dtmcli\constant\DtmConstant;
use Sett\Dtmcli\transaction\contract\ITransExcludeSaga;

class MsgTrans extends TransBase implements ITransExcludeSaga
{

    /**
     * @param string $actionUrl
     * @param array $postData
     * @return MsgTrans
     */
    public function withOperate(string $actionUrl, array $postData = []): MsgTrans {
        $this->transSteps[] = [
            "action" => $actionUrl
        ];
        $this->payloads[]   = json_encode($postData, JSON_UNESCAPED_UNICODE);
        return $this;
    }

    public function withQueryUrl(string $queryUrl): MsgTrans {
        $this->queryUrl = $queryUrl;
        return $this;
    }

    /**
     * @throws Exception
     */
    public function submit(): bool {
        return $this->submitRequest([
            "gid"            => $this->transGid,
            "trans_type"     => DtmConstant::MsgTrans,
            "steps"          => $this->transSteps,
            "payloads"       => $this->payloads,
            "query_prepared" => $this->queryUrl,
            "wait_result"    => $this->waitResult
        ]);
    }

    /**
     * @throws Exception
     * @throws GuzzleException
     */
    public function prepare(): bool {
        return $this->prepareRequest([
            "gid"            => $this->transGid,
            "trans_type"     => DtmConstant::MsgTrans,
            "steps"          => $this->transSteps,
            "payloads"       => $this->payloads,
            "query_prepared" => $this->queryUrl,
            "wait_result"    => $this->waitResult
        ]);
    }

    public function abort() {
        // TODO: Implement abort() method.
    }
}