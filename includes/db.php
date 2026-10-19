<?php
function db(): mysqli {
    static $c = null;
    if ($c === null) {
        $c = new mysqli("db", "db", "db", "Mario_Activity");
        if ($c->connect_error) die("DB connection failed: " . $c->connect_error);
        $c->set_charset("utf8mb4");
    }
    return $c;
}