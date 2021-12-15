<?php

use Sett\Dtmcli\transaction\SagaTrans;
use Sett\Dtmcli\transaction\TccTrans;

require __DIR__ . "/../vendor/autoload.php";
$baseUrl = "http://127.0.0.1:18310";
// saga
try {
    $trans = new SagaTrans("172.19.0.89:36789");
    $gid   = $trans->createNewGid();
    $trans
        ->withGid($gid)
        ->withOperate("$baseUrl/dtm/saga/transOut", "$baseUrl/dtm/saga/transOutRevert", ["amount" => 30])
        ->withOperate("$baseUrl/dtm/saga/transIn", "$baseUrl/dtm/saga/transInRevert", ["amount" => 30]);
    $success = $trans->submit();
    echo "transaction result {$success}";
} catch (Exception $exception) {
    var_dump($exception->getTraceAsString());
    echo "transaction with error " . $exception->getMessage();
}
