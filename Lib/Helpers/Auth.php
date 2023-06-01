<?php

namespace Lib\Helpers;

class Auth {
    public function is_authenticated() {
        if(isset($_SESSION['auth'])) {
            return true;
        } else {
            HTTP::redirect("./login.php");
            return;
        }
    }
}