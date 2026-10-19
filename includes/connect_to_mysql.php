<?php
define("DB_SERVER", "db");
define("DB_USER", "db");
define("DB_PASS", "db");
define("DB_NAME", "Mario_Activity");

$connection = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);

if (mysqli_connect_errno()) {
    die("Database connection failed: " .
        mysqli_connect_error() .
        "(" . mysqli_connect_errno() . ")"
    );
}          
?>