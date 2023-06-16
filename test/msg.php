<?php

use GuzzleHttp\Exception\GuzzleException;
use Sett\Dtmcli\transaction\MsgTrans;

require __DIR__ . "/../vendor/autoload.php";

$baseUrl = "http://127.0.0.1:18310";
// 正向外部接口insert ignore插入数据到数据表:barrier，原因：committed，正向接口不允许失败，否则会一直被dtm请求
// 正向回查接口insert ignore插入数据到数据表:barrier，原因：rollback
// 正向回查接口查询gid对应事务的reason，如果是rollback，返回FAILURE，否则返回SUCCESS

try {
    $trans = new MsgTrans("127.0.0.1:36789");
    $gid   = $trans->createNewGid();
    $trans->withGid($gid)
        ->withOperate("http://127.0.0.1:9999/index.php", ["amount" => 3])
        ->doAndSubmit("http://127.0.0.1:9999/query.php", function () {
            // 本地事务处理
            return true;
        });
} catch (Exception $exception) {
    echo "exception with error " . $exception->getMessage();
} catch (GuzzleException $e) {
}