<?php

namespace App\Helpers;

class Response {
    public static function set($status = true, $msg = null, $data = null) {
        return compact('status', 'msg', 'data');
    }

    public static function handle(\Exception $e = null) {
        return self::set(false, $e->getMessage());
    }

    public static function fail() {
        return self::set(false, 'Unexpected Error 503');
    }
}

?>
