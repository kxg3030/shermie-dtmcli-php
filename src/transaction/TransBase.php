<?php

namespace Sett\Dtmcli\transaction;

use Exception;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Sett\Dtmcli\constant\DtmConstant;
use Sett\Dtmcli\exception\FailException;
use Sett\Dtmcli\exception\OngoingException;
use Sett\Dtmcli\traits\HttpTrait;
use Sett\Dtmcli\traits\UtilsTrait;

abstract class TransBase {
    use HttpTrait;
    use UtilsTrait;

    // dtm服务地址
    public $dtmHost = "";
    // 事务gid
    public $transGid = "";
    // 事务类型
    public $transType = "";
    // 自定义数据
    public $customData = [];
    // 事务顺序
    public $transSteps = [];
    // 接口负载
    public $payloads = [];
    // 消息超时查询地址
    public $queryPrepare = "";
    // 当前操作
    public $operation = "";
    // 是否等待事务结果
    public $waitResult = false;
    // 自定义头部
    public $branchHeader = [];

    public function __construct(string $dtmHost = "") {
        $this->dtmHost = $dtmHost;
    }

    /**
     * @param string $transGid
     */
    public function setTransGid(string $transGid) {
        $this->transGid = $transGid;
    }

    /**
     * @param string $transType
     */
    public function setTransType(string $transType) {
        $this->transType = $transType;
    }

    /**
     * @param string $customData
     */
    public function setCustomData(string $customData) {
        $this->customData = $customData;
    }

    /**
     * @param array $transSteps
     */
    public function setTransSteps(array $transSteps) {
        $this->transSteps = $transSteps;
    }

    /**
     * @param array $payloads
     */
    public function setPayloads(array $payloads) {
        $this->payloads = $payloads;
    }

    /**
     * @param string $queryPrepare
     */
    public function setQueryPrepare(string $queryPrepare) {
        $this->queryPrepare = $queryPrepare;
    }

    /**
     * @param string $operation
     */
    public function setOperation(string $operation) {
        $this->operation = $operation;
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
     * @param array $branchHeader
     */
    public function setBranchHeader(array $branchHeader) {
        $this->branchHeader = $branchHeader;
    }

    /**
     * @param Response $response
     * @throws OngoingException|FailException
     */
    public function transStatus(ResponseInterface $response) {
        if ($response->getStatusCode() == 425) {
            throw new OngoingException("trans on going");
        }
        if ($response->getStatusCode() == 409) {
            throw new FailException("trans fail");
        }
    }


    /**
     * @throws Exception|GuzzleException
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
            throw new FailException("prepare request fail：" . $response->message);
        }
        return true;
    }

    /**
     * @throws Exception|GuzzleException
     */
    protected function abortRequest(array $postData = []): bool {
        $response = $this->client()->post($this->combineUrl(DtmConstant::TransAbortPath), [
            "json" => $postData,
        ]);
        $body     = $response->getBody()->getContents();
        $response = json_decode($body, false);
        if (strpos($body, DtmConstant::Failure) !== false) {
            throw new FailException("abort request fail：" . $response->message);
        }
        return true;
    }

    /**
     * @throws FailException|GuzzleException
     */
    protected function submitRequest(array $postData): bool {
        $response = $this->client()->post($this->combineUrl(DtmConstant::TransSubmitPath), [
            "json" => $postData,
        ]);
        $body     = $response->getBody()->getContents();
        $response = json_decode($body, false);
        if (strpos($body, DtmConstant::Failure) !== false) {
            throw new FailException("submit request fail：" . $response->message);
        }
        return true;
    }

    /**
     * @throws Exception|GuzzleException
     */
    public function createNewGid(): string {
        $response = $this->client()->get($this->combineUrl(DtmConstant::GetNewGidPath));
        $body     = $response->getBody()->getContents();

        if (strpos($body, DtmConstant::Failure) !== false) {
            throw new FailException("create gid fail");
        }
        $data = json_decode($body, false);
        return $data->gid;
    }

    /**
     * @throws Exception|GuzzleException
     */
    public function registerBranch(array $postData): bool {
        $response = $this->client()->post($this->combineUrl(DtmConstant::RegisterTccBranchPath), [
            "json" => $postData,
        ]);
        $body     = $response->getBody()->getContents();

        if (strpos($body, DtmConstant::Failure) !== false) {
            throw new FailException("register branch fail");
        }
        return true;
    }

    /**
     * @throws FailException|GuzzleException
     */
    public function requestBranch(array $postData, string $branchId, $tryUrl, $transType, $operate): bool {
        $queryData = [
            "dtm"            => sprintf("%s%s", $this->dtmHost, "/api/dtmsvr"),
            "gid"            => $this->transGid,
            "branch_id"      => $branchId,
            "trans_type"     => $transType,
            "op"             => $operate,
            "branch_headers" => $this->branchHeader,
        ];
        $body      = $this->client()->post($tryUrl, [
            "query" => http_build_query($queryData),
            "json"  => $postData,
        ])->getBody()->getContents();
        if (strpos($body, DtmConstant::Failure) !== false) {
            throw new FailException("try branch return fail");
        }
        return true;
    }

    public function withGid(string $transGid): TransBase {
        $this->transGid = $transGid;
        return $this;
    }
}
