<?php

namespace sett\transaction;

use Exception;
use sett\constant\DtmConstant;
use sett\transaction\contract\ITransExcludeSaga;

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
        $this->payloads[]   = $postData;
        return $this;
    }

    public function withQueryPrepare(string $queryUrl): MsgTrans {
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
            "query_prepared" => $this->queryUrl
        ]);
    }

    /**
     * @throws Exception
     */
    public function prepare(): bool {
        return $this->prepareRequest([
            "gid"            => $this->transGid,
            "trans_type"     => DtmConstant::MsgTrans,
            "steps"          => $this->transSteps,
            "payloads"       => $this->payloads,
            "query_prepared" => $this->queryUrl
        ]);
    }

    public function abort() {
        // TODO: Implement abort() method.
    }
}