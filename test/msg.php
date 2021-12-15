<?php

use Sett\Dtmcli\transaction\SagaTrans;
use Sett\Dtmcli\transaction\TccTrans;
use Sett\Dtmcli\transaction\MsgTrans;

require __DIR__ . "/../vendor/autoload.php";

$baseUrl = "http://127.0.0.1:18310";
try {
    $trans = new MsgTrans("172.19.0.89:36789");
    $gid   = $trans->createNewGid();
    $trans
        ->withOperate("$baseUrl/dtm/msg/transOut", ["amount" => 30])
        ->withOperate("$baseUrl/dtm/msg/transIn", ["amount" => 30])
        ->withQueryUrl("$baseUrl/dtm/msg/query")
        ->prepare();
    $success = $trans->submit();
    echo "transaction result {$success}";
} catch (Exception $exception) {
    var_dump($exception->getTraceAsString());
    echo "exception with error " . $exception->getMessage();
}