<?php

use sett\service\DtmService;
use sett\transaction\TccTrans;

require __DIR__ . "/../vendor/autoload.php";

// tcc
try {
    $trans   = new TccTrans("127.0.0.1:36789");
    $gid     = $trans->createNewGid();
    $success = $trans->withOperate($gid, function (TccTrans $tccTrans) {
        $result = $tccTrans->callBranch(
            ["amount" => 30],
            "http://127.0.0.1:8081/api/busi/TransOut",
            "http://127.0.0.1:8081/api/busi/TransOutConfirm",
            "http://127.0.0.1:8081/api/busi/TransOutRevert"
        );
        if (!$result) {
            die("error");
        }
        return $tccTrans->callBranch(
            ["amount" => 30],
            "http://127.0.0.1:8081/api/busi/TransIn",
            "http://127.0.0.1:8081/api/busi/TransInConfirm",
            "http://127.0.0.1:8081/api/busi/TransInRevert"
        );
    });
    die("result $success");
} catch (Exception $exception) {
    die($exception->getMessage());
}