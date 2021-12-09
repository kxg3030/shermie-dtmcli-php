<?php

use sett\service\DtmService;
use sett\transaction\SagaTrans;
use sett\transaction\TccTrans;

require __DIR__ . "/../vendor/autoload.php";
$baseUrl = "http://127.0.0.1:8082/api/busi_start";
// saga
try {
    $trans = new SagaTrans("127.0.0.1:36789");
    $gid   = $trans->createNewGid();
    $trans
        ->withGid($gid)
        ->withOperate("$baseUrl/TransOut", "$baseUrl/TransOutCompensate", ["amount" => 30])
        ->withOperate("$baseUrl/TransIn", "$baseUrl/TransInCompensate", ["amount" => 30]);
    $success = $trans->submit();
    die("result $success");
} catch (Exception $exception) {
    var_dump($exception->getTraceAsString());
    die($exception->getMessage());
}
