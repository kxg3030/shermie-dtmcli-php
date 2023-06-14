<?php

use GuzzleHttp\Exception\GuzzleException;
use Sett\Dtmcli\transaction\contract\IDatabase;
use Sett\Dtmcli\transaction\MsgTrans;

require __DIR__ . "/../vendor/autoload.php";

$baseUrl = "http://127.0.0.1:18310";

// 本地事务和submit操作同时成功，如果有一个异常，就请求queryPrepare回查，并且将主事务变为abort
// 模拟场景：本地记录写入到数据库成功，请求外部接口操作成功
// 需要在业务库里面建立一张本地事务记录表
try {
    $trans = new MsgTrans("127.0.0.1:36789");
    $gid   = $trans->createNewGid();
    $trans->withGid($gid)
        ->withOperate("http://127.0.0.1:9999/index.php", ["amount" => 3])
        ->doAndSubmit("http://127.0.0.1:9999/query.php", function () {
            // insert ignore插入数据到数据表:barrier，原因：committed
            // 本地事务处理
            return true;
        });
} catch (Exception $exception) {
    var_dump($exception->getTraceAsString());
    echo "exception with error " . $exception->getMessage();
} catch (GuzzleException $e) {
}