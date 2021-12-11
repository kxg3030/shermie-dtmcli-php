<?php

use Sett\Dtmcli\transaction\TccTrans;

require __DIR__ . "/../vendor/autoload.php";

// 业务代码域名端口
$baseUrl = "http://127.0.0.1:18310";
// tcc
try {
    $trans   = new TccTrans("127.0.0.1:36789");
    $gid     = $trans->createNewGid();
    $success = $trans->withOperate($gid, function (TccTrans $tccTrans) use ($baseUrl) {
        $result = $tccTrans->callBranch(
            ["amount" => 30],
            "$baseUrl/dtm/tcc/transOut",
            "$baseUrl/dtm/tcc/transOutConfirm",
            "$baseUrl/dtm/tcc/transOutCancel"
        );
        if (!$result) {
            echo "call branch fail\n";
            return false;
        }
        return $tccTrans->callBranch(
            ["amount" => 30],
            "$baseUrl/dtm/tcc/transIn",
            "$baseUrl/dtm/tcc/transInConfirm",
            "$baseUrl/dtm/tcc/transInCancel"
        );
    });
    echo "transaction result {$success}\n";
} catch (Exception $exception) {
    die($exception->getMessage());
}