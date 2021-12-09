<?php

use sett\service\DtmService;
use sett\transaction\SagaTrans;
use sett\transaction\TccTrans;

require __DIR__ . "/../vendor/autoload.php";

// tcc
try {
    $trans = new \sett\transaction\MsgTrans("127.0.0.1:36789");
    $gid   = $trans->createNewGid();
    $trans
        ->withOperate("http://127.0.0.1:8081/api/busi/TransOut", ["amount" => 30])
        ->withOperate("http://127.0.0.1:8081/api/busi/TransIn", ["amount" => 30])
        ->withQueryUrl("http://127.0.0.1:8081/api/busi/query")
        ->prepare();
    $success = $trans->submit();
    die("result $success");
} catch (Exception $exception) {
    var_dump($exception->getTraceAsString());
    die($exception->getMessage());
}