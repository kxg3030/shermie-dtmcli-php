<?php

namespace Sett\Dtmcli\transaction;

use Exception;
use Sett\Dtmcli\constant\DtmConstant;
use Sett\Dtmcli\transaction\contract\IDatabase;

class BarrierTrans
{
    public $transType;
    public $transGid;
    public $branchId;
    public $operate;
    public $barrierId = 0;

    public function __construct(array $query) {
        $this->transType = $query["trans_type"];
        $this->transGid  = $query["gid"];
        $this->branchId  = $query["branch_id"];
        $this->operate   = $query["op"];
    }

    /**
     * @return mixed
     */
    public function getTransGid() {
        return $this->transGid;
    }

    /**
     * @param mixed $transGid
     */
    public function setTransGid($transGid) {
        $this->transGid = $transGid;
    }

    /**
     * @return mixed
     */
    public function getBranchId() {
        return $this->branchId;
    }

    /**
     * @param mixed $branchId
     */
    public function setBranchId($branchId) {
        $this->branchId = $branchId;
    }

    /**
     * @return mixed
     */
    public function getOperate() {
        return $this->operate;
    }

    /**
     * @param mixed $operate
     */
    public function setOperate($operate) {
        $this->operate = $operate;
    }

    /**
     * @return mixed
     */
    public function getTransType() {
        return $this->transType;
    }

    /**
     * @param mixed $transType
     */
    public function setTransType($transType) {
        $this->transType = $transType;
    }

    /**
     * @param IDatabase $database
     * @param callable $callback
     * @return bool
     * @throws Exception
     */
    public function call(IDatabase $database, callable $callback): bool {
        $database->beginTrans();
        try {
            $this->barrierId++;
            $barrierId    = sprintf("%02d", $this->barrierId);
            $originType   = [
                DtmConstant::ActionCancel     => DtmConstant::ActionTry,
                DtmConstant::ActionCompensate => DtmConstant::Action
            ][$this->operate];
            $first        = $this->insertBarrier($database, $originType, $barrierId);
            $second       = $this->insertBarrier($database, $this->operate, $barrierId);
            $actionIgnore = $this->operate == DtmConstant::ActionCancel || $this->operate == DtmConstant::ActionCompensate;
            if ($actionIgnore && $first && $second == false) {
                $database->commit();
                return true;
            }
            $success = $callback($database);
            if (!$success) {
                $database->rollback();
                return false;
            }
            $database->commit();
        } catch (Exception $exception) {
            $database->rollback();
            throw $exception;
        }
        return true;
    }

    private function insertBarrier(IDatabase $database, $originType, $barrierId): bool {
        $sql = "insert ignore into dtm_barrier.barrier(trans_type, gid, branch_id, op, barrier_id, reason) ";
        $sql .= "values('{$this->transType}','{$this->transGid}','{$this->branchId}','{$originType}','{$barrierId}','{$this->operate}')";
        return $database->execute($sql);
    }
}