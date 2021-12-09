<?php

namespace sett\transaction;

use Exception;
use sett\constant\DtmConstant;
use sett\transaction\contract\ITransExcludeSaga;

class TccTrans extends TransBase implements ITransExcludeSaga
{
    public $branchId    = "";
    public $subBranchId = 0;

    /**
     * @throws Exception
     */
    public function withOperate(string $gid, callable $callback): bool {
        $success = $this->withNewGid($gid)->prepare();
        $result  = $callback($this);
        if ($success && $result) {
            return $this->submit();
        }
        return $this->abort();
    }

    /**
     * @throws Exception
     */
    public function prepare(): bool {
        return $this->prepareRequest(["gid" => $this->transGid, "trans_type" => DtmConstant::TccTrans]);
    }


    /**
     * @throws Exception
     */
    public function submit(): bool {
        if (empty($this->transGid)) {
            throw new Exception("gid can not be empty");
        }
        return $this->submitRequest(["gid" => $this->transGid, "trans_type" => DtmConstant::TccTrans]);
    }

    /**
     * @throws Exception
     */
    public function abort(): bool {
        if (empty($this->transGid)) {
            throw new Exception("gid can not be empty");
        }
        return $this->abortRequest(["gid" => $this->transGid, "trans_type" => DtmConstant::TccTrans]);
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
    public function callBranch(array $postData, string $tryUrl, string $confirmUrl, string $cancelUrl): bool {
        $branchId = $this->subBranchId();
        $regData  = [
            "branch_id"  => $branchId,
            "cancel"     => $cancelUrl,
            "confirm"    => $confirmUrl,
            "data"       => json_encode($postData, JSON_UNESCAPED_UNICODE),
            "trans_type" => DtmConstant::TccTrans,
            "gid"        => $this->transGid,
        ];
        $this->registerBranch($regData);
        return $this->requestBranch($postData, $branchId, $tryUrl, DtmConstant::TccTrans, DtmConstant::ActionTry);
    }

}