<?php

namespace Sett\Dtmcli\transaction;

use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Sett\Dtmcli\constant\DtmConstant;
use Sett\Dtmcli\exception\FailException;
use Sett\Dtmcli\transaction\contract\ITransWithAbort;
use Sett\Dtmcli\transaction\contract\ITransWithPrepare;
use Sett\Dtmcli\transaction\contract\ITransWithSubmit;

class MsgTrans extends TransBase implements ITransWithPrepare, ITransWithSubmit, ITransWithAbort {

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
        $this->queryPrepare = $queryUrl;
        return $this;
    }


    /**
     * @throws FailException|GuzzleException
     */
    public function submit(): bool {
        return $this->submitRequest([
            "gid"            => $this->transGid,
            "trans_type"     => DtmConstant::MsgTrans,
            "steps"          => $this->transSteps,
            "payloads"       => $this->payloads,
            "query_prepared" => $this->queryPrepare,
            "wait_result"    => $this->waitResult,
            "branch_headers" => $this->branchHeader,
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
            "query_prepared" => $this->queryPrepare,
            "wait_result"    => $this->waitResult,
            "branch_headers" => $this->branchHeader,
        ]);
    }


    /**
     * @param string $queryUri
     * @param \Closure $callback
     * @throws GuzzleException|FailException
     */
    public function doAndSubmit(string $queryUri, \Closure $callback) {
        $this->queryPrepare = $queryUri;
        $barrierFrom        = [
            "trans_type" => DtmConstant::MsgTrans,
            "gid"        => $this->transGid,
            "branch_id"  => "00",
            "op"         => DtmConstant::MsgTrans
        ];
        // 发送预请求
        $this->prepare();
        try {
            $callback();
            $this->submit();
        } catch (FailException $failException) {
            $this->abort();
            throw $failException;
        } catch (\Throwable $throwable) {
            $this->queryPrepare($barrierFrom);
            throw $throwable;
        }
    }

    /**
     * @param array $barrierFrom
     * @throws GuzzleException|FailException
     */
    public function queryPrepare(array $barrierFrom) {
        try {
            $this->requestBranch([], $barrierFrom["branch_id"], $this->queryPrepare, $barrierFrom["trans_type"], $barrierFrom["op"]);
        } catch (FailException $exception) {
            $this->abort();
            throw $exception;
        }
    }

    /**
     * @throws GuzzleException
     */
    public function abort() {
        $this->abortRequest([
            'gid'            => $this->transGid,
            'trans_type'     => DtmConstant::MsgTrans,
            "branch_headers" => $this->branchHeader,
        ]);
    }
}