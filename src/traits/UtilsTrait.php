<?php

namespace sett\dtmcli\traits;

trait UtilsTrait
{


    protected function combineUrl(string $path): string {
        return sprintf("%s%s", $this->dtmHost, $path);
    }
}