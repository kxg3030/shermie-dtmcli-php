<?php

use sett\service\DtmService;
use sett\transaction\TccTrans;

require __DIR__ . "/../vendor/autoload.php";

$baseUrl = "http://127.0.0.1:8081/api/busi_start";
// tcc
try {
    $trans   = new TccTrans("127.0.0.1:36789");
    $gid     = $trans->createNewGid();
    $success = $trans->withOperate($gid, function (TccTrans $tccTrans) use ($baseUrl) {
        $result = $tccTrans->callBranch(
            ["amount" => 30],
            "$baseUrl/TransOut",
            "$baseUrl/TransOutConfirm",
            "$baseUrl/TransOutRevert"
        );
        if (!$result) {
            die("error");
        }
        return $tccTrans->callBranch(
            ["amount" => 30],
            "$baseUrl/TransIn",
            "$baseUrl/TransInConfirm",
            "$baseUrl/TransInRevert"
        );
    });
    die("result $success");
} catch (Exception $exception) {
    die($exception->getMessage());
}