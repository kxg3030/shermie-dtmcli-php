<?php

use sett\service\DtmService;
use sett\transaction\SagaTrans;
use sett\transaction\TccTrans;

require __DIR__ . "/../vendor/autoload.php";

// tcc
try {
    $trans = new SagaTrans("127.0.0.1:36789");
    $gid   = $trans->createNewGid();
    $trans
        ->withGid($gid)
        ->withOperate("http://127.0.0.1:8081/api/busi/TransOut", "http://127.0.0.1:8081/api/busi/TransOutRevert", ["amount" => 30])
        ->withOperate("http://127.0.0.1:8081/api/busi/TransIn", "http://127.0.0.1:8081/api/busi/TransInRevert", ["amount" => 30]);
    $success = $trans->submit();
    die("result $success");
} catch (Exception $exception) {
    var_dump($exception->getTraceAsString());
    die($exception->getMessage());
}