<?php

namespace sett\dtmcli\traits;

trait SingletonTrait
{
    private static $_instance = null;

    public static function instance() {
        self::$_instance || self::$_instance = new self();
        return self::$_instance;
    }

    private function __construct() {
    }

    private function __clone() {

    }
}