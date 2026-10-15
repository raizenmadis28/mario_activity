<?php
define("DB_SERVER", "localhost");
define("DB_USER", "myroot");
define("DB_PASS", "@RBmadis28");
define("DB_NAME", "Mario_Activity");

$connection = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);

if (mysqli_connect_errno()) {
    die("Database connection failed: " .
        mysqli_connect_error() .
        "(" . mysqli_connect_errno() . ")"
    );
}          
?>