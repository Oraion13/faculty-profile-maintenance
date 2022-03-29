<?php

session_start();

if (!isset($_SESSION['logged_in'])) {
    header($_SERVER["SERVER_PROTOCOL"] . ' 400 ', true, 400);
    echo json_encode(
        array('message' => 'no user logged in')
    );
    die();
}

echo json_encode(
    array('message' => $_SESSION['username'] . ' logged out')
);

session_unset();
session_destroy();
