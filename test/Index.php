<?php

use sett\service\DtmService;
use sett\transaction\TccTrans;

require __DIR__ . "/vendor/autoload.php";

try {
    $trans = new TccTrans();
    $trans->setDtmHost("127.0.0.1:36789");
    $gid     = $trans->createNewGid();
    $success = $trans->withOperate($gid, function (TccTrans $tccTrans) {

    });
} catch (Exception $exception) {

}