<?php

ob_start();

define("DB_HOST", 'localhost');
define("DB_USER", 'root');
define("DB_PASSWORD", '');
define("DB_NAME", 'loodle');

function db_connect()
{
    $conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    confirm_db_connect();
    return $conn;
}
function db_disconnect()
{
    if (isset($conn)) {
        mysqli_close($conn);
    }
}
function db_escape($conn, $string)
{
    return mysqli_real_escape_string($conn, $string);
}
function confirm_db_connect()
{
    if (mysqli_connect_errno()) {
        $msg = "Database failed to connect: ";
        $msg .= mysqli_connect_error();
        $msg .= " (" . mysqli_connect_errno() . ")";
        exit($msg);
    }
}
function confirm_query_result($result_set)
{
    if (!$result_set) {
        exit("Database query result failed.");
    }
}

$db = db_connect();