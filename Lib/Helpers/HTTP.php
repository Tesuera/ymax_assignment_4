<?php

namespace Lib\Helpers;

class HTTP {
    static function redirect($uri) {
        header("location:$uri");
        exit();
    }
}