<?php

namespace Sett\Dtmcli\transaction;

use Exception;
use GuzzleHttp\Exception\RequestException;
use Sett\Dtmcli\constant\DtmConstant;
use Sett\Dtmcli\traits\HttpTrait;
use Sett\Dtmcli\traits\UtilsTrait;

abstract class TransBase
{
    use HttpTrait;
    use UtilsTrait;

    // 事务gid
    public $transGid = "";
    // dtm服务地址
    protected $dtmHost = "";
    // 事务顺序
    public $transSteps = [];
    // 接口负载
    public $payloads = [];
    // 消息超时查询地址
    public $queryUrl = "";
    // 是否等待事务结果
    protected $waitResult = false;

    public function __construct(string $dtmHost = "") {
        $this->dtmHost = $dtmHost;
    }

    /**
     * @param bool $waitResult
     */
    public function setWaitResult(bool $waitResult) {
        $this->waitResult = $waitResult;
    }

    /**
     * @param string $dtmHost
     * @return TransBase
     */
    public function setDtmHost(string $dtmHost): TransBase {
        $this->dtmHost = $dtmHost;
        return $this;
    }


    /**
     * @throws Exception
     */
    protected function prepareRequest(array $postData = []): bool {
        if ($this->dtmHost == "") {
            throw new Exception("dtm host is empty");
        }
        $body     = $this->client()->post($this->combineUrl(DtmConstant::TransPreparePath), [
            "json" => $postData,
        ])->getBody()->getContents();
        $response = json_decode($body, false);
        if (strpos($body, DtmConstant::Failure) !== false) {
            throw new Exception("prepare request fail：" . $response->message);
        }
        return true;
    }

    /**
     * @throws Exception
     */
    protected function abortRequest(array $postData = []): bool {
        $body     = $this->client()->post($this->combineUrl(DtmConstant::TransPreparePath), [
            "json" => $postData,
        ])->getBody()->getContents();
        $response = json_decode($body, false);
        if (strpos($body, DtmConstant::Failure) !== false) {
            throw new Exception("abort request fail：" . $response->message);
        }
        return true;
    }

    /**
     * @throws Exception
     */
    protected function submitRequest(array $postData): bool {
        $body     = $this->client()->post($this->combineUrl(DtmConstant::TransSubmitPath), [
            "json" => $postData
        ])->getBody()->getContents();
        $response = json_decode($body, false);
        if (strpos($body, DtmConstant::Failure) !== false) {
            throw new Exception("submit request fail：" . $response->message);
        }
        return true;
    }

    /**
     * @throws Exception
     */
    public function createNewGid(): string {
        $body     = $this->client()->get($this->combineUrl(DtmConstant::GetNewGidPath))->getBody()->getContents();
        $response = json_decode($body, false);
        if (strpos($body, DtmConstant::Failure) !== false) {
            throw new Exception("create gid fail：" . $response->message);
        }
        return $response->gid;
    }

    /**
     * @throws Exception
     */
    public function registerBranch(array $postData): bool {
        $body     = $this->client()->post($this->combineUrl(DtmConstant::RegisterTccBranchPath), [
            "json" => $postData
        ])->getBody()->getContents();
        $response = json_decode($body, false);
        if (strpos($body, DtmConstant::Failure) !== false) {
            throw new Exception("register branch fail：" . $response->message);
        }
        return true;
    }

    /**
     * @throws Exception
     */
    public function requestBranch(array $postData, string $branchId, $tryUrl, $transType, $operate): bool {
        $queryData = [
            "dtm"        => sprintf("%s%s", $this->dtmHost, "/api/dtmsvr"),
            "gid"        => $this->transGid,
            "branch_id"  => $branchId,
            "trans_type" => $transType,
            "op"         => $operate
        ];
        $body      = $this->client()->post($tryUrl, [
            "query" => http_build_query($queryData),
            "json"  => $postData,
        ])->getBody()->getContents();
        if (strpos($body, DtmConstant::Failure) !== false) {
            throw new Exception("try branch return fail");
        }
        return true;
    }

    public function withGid(string $transGid): TransBase {
        $this->transGid = $transGid;
        return $this;
    }
}