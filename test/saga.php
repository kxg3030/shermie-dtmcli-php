<?php

use Sett\Dtmcli\transaction\SagaTrans;
use Sett\Dtmcli\transaction\TccTrans;

require __DIR__ . "/../vendor/autoload.php";
$baseUrl = "http://127.0.0.1:18310";
// saga
try {
    $trans = new SagaTrans("http://127.0.0.1:36789");
    $gid   = $trans->createNewGid();
    $trans
        ->withGid($gid)
        ->withOperate("$baseUrl/api/saga-trans-out", "$baseUrl/api/saga-trans-out-revert", ["amount" => 30, "user_id" => 1])
        ->withOperate("$baseUrl/api/saga-trans-in", "$baseUrl/api/saga-trans-in-revert", ["amount" => 30, "user_id" => 2]);
    $success = $trans->submit();
    echo "transaction result {$success}";
} catch (Exception $exception) {
    var_dump($exception->getTraceAsString());
    echo "transaction with error " . $exception->getMessage();
}
