<?php

namespace Sett\Dtmcli\transaction;

use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Sett\Dtmcli\constant\DtmConstant;
use Sett\Dtmcli\transaction\contract\IDatabase;
use Sett\Dtmcli\transaction\contract\ITransWithPrepare;
use Sett\Dtmcli\transaction\contract\ITransWithSubmit;

class MsgTrans extends TransBase implements ITransWithPrepare, ITransWithSubmit {

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
     * @throws Exception|GuzzleException
     */
    public function submit(): bool {
        return $this->submitRequest([
            "gid"            => $this->transGid,
            "trans_type"     => DtmConstant::MsgTrans,
            "steps"          => $this->transSteps,
            "payloads"       => $this->payloads,
            "query_prepared" => $this->queryPrepare,
            "wait_result"    => $this->waitResult,
            "custom_data"    => $this->customData,
        ]);
    }

    /**
     * @throws Exception
     * @throws GuzzleException
     */
    public function prepare(): bool {
        return $this->prepareRequest();
    }


    /**
     * @param string $queryUri
     * @param IDatabase $database
     * @param \Closure $callback
     * @return bool
     * @throws GuzzleException
     * @throws Exception
     */
    public function doAndSubmit(string $queryUri, IDatabase $database, \Closure $callback): bool {
        $this->queryPrepare = $queryUri;
        $barrierFrom        = [
            "trans_type" => DtmConstant::MsgTrans,
            "gid"        => $this->transGid,
            "branch_id"  => "00",
            "op"         => DtmConstant::MsgTrans
        ];
        $preparePost        = [
            "gid"            => $this->transGid,
            "trans_type"     => DtmConstant::MsgTrans,
            "steps"          => $this->transSteps,
            "payloads"       => $this->payloads,
            "query_prepared" => $this->queryPrepare,
            "wait_result"    => $this->waitResult,
            "custom_data"    => $this->customData,
        ];
        // 发送预请求
        $success = $this->prepareRequest($preparePost);
        if (!$success) {
            throw new Exception("msg trans prepare fail");
        }
        $success = $callback($database);
        if (!$success) {
            // 查询执行结果
            $success = $this->requestBranch([], $barrierFrom["branch_id"], $this->queryPrepare, $barrierFrom["trans_type"], $barrierFrom["op"]);
            if (!$success) {
                // 修改事务状态为异常
                $this->abortRequest($preparePost);
            }
            return false;
        }
        return $this->submitRequest($preparePost);
    }
}