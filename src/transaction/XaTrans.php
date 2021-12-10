<?php

namespace sett\transaction;

use Exception;
use sett\constant\DtmConstant;
use sett\transaction\contract\ITransExcludeSaga;

class XaTrans extends TransBase implements ITransExcludeSaga
{
    public $branchId    = "";
    public $subBranchId = 0;

    /**
     * @throws Exception
     */
    public function submit(): bool {
        if (empty($this->transGid)) {
            throw new Exception("gid can not be empty");
        }
        return $this->submitRequest(["gid" => $this->transGid, "trans_type" => DtmConstant::XaTrans, "wait_result" => $this->waitResult]);
    }

    /**
     * @throws Exception
     */
    function prepare(): bool {
        if (empty($this->transGid)) {
            throw new Exception("gid can not be empty");
        }
        return $this->prepareRequest(["gid" => $this->transGid, "trans_type" => DtmConstant::XaTrans, "wait_result" => $this->waitResult]);
    }

    /**
     * @throws Exception
     */
    public function abort(): bool {
        if (empty($this->transGid)) {
            throw new Exception("gid can not be empty");
        }
        return $this->abortRequest(["gid" => $this->transGid, "trans_type" => DtmConstant::XaTrans]);
    }


    /**
     * @throws Exception
     */
    public function subBranchId(): string {
        if (strlen($this->branchId) >= 99) {
            throw new Exception("branch id is large than 99");
        }
        if ($this->subBranchId >= 20) {
            throw new Exception("branch id is large than 20");
        }
        $this->subBranchId++;
        return $this->branchId . sprintf("%02d", $this->subBranchId);
    }

    /**
     * @throws Exception
     */
    public function withOperate(string $gid, callable $callback): bool {
        $this->prepare();
        $success = $callback($this);
        if ($success) {
            return $this->submit();
        }
        return $this->abort();
    }

    /**
     * @throws Exception
     */
    public function callBranch(array $postData, string $tryUrl): bool {
        $branchId = $this->subBranchId();
        return $this->requestBranch($postData, $branchId, $tryUrl, DtmConstant::XaTrans, DtmConstant::Action);
    }
}