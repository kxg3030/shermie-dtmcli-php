<?php

use GuzzleHttp\Exception\GuzzleException;
use Sett\Dtmcli\transaction\contract\IDatabase;
use Sett\Dtmcli\transaction\MsgTrans;

require __DIR__ . "/../vendor/autoload.php";

$baseUrl = "http://127.0.0.1:18310";

class UserDatabase implements IDatabase {

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

try {
    $userDatabase = new UserDatabase();
    $trans        = new MsgTrans("172.19.0.89:36789");
    $gid          = $trans->createNewGid();
    $success      = $trans->withGid($gid)
        ->doAndSubmit("$baseUrl/dtm/msg/transOut", $userDatabase, function (IDatabase $database) {
            // 扣除用户余额
            return true;
        });
    echo "transaction result {$success}";
} catch (Exception $exception) {
    var_dump($exception->getTraceAsString());
    echo "exception with error " . $exception->getMessage();
} catch (GuzzleException $e) {
}