<?php

use Sett\Dtmcli\transaction\BarrierTrans;
use Sett\Dtmcli\transaction\contract\IDatabase;
use Sett\Dtmcli\transaction\SagaTrans;
use Sett\Dtmcli\transaction\TccTrans;
use Sett\Dtmcli\transaction\MsgTrans;

require __DIR__ . "/../vendor/autoload.php";

class UserDatabase implements IDatabase
{

    public function execute(string $query): bool {
        // TODO: Implement execute() method.
    }

    public function query(string $query): bool {
        // TODO: Implement query() method.
    }

    public function rollback() {
        // TODO: Implement rollback() method.
    }

    public function commit() {
        // TODO: Implement commit() method.
    }

    public function beginTrans() {
        // TODO: Implement beginTrans() method.
    }
}

$baseUrl = "http://127.0.0.1:18310";
try {
    $trans    = new BarrierTrans([
        "trans_type" => "tcc",
        "gid"        => "ac130059_4pQHea5Xtsq",
        "op"         => "prepare",
        "branch_id"  => "01"
    ]);
    $database = new UserDatabase();
    $success  = $trans->call($database, function (IDatabase $database) {
        // 使用当前数据库连接操作,保证所有操作都在一个本地事务中
        // do what you want...
        return true;
    });
    echo "transaction result {$success}";
} catch (Exception $exception) {
    var_dump($exception->getTraceAsString());
    echo "exception with error " . $exception->getMessage();
}