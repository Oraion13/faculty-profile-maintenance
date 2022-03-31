<?php

session_start();

require_once '../../utils/send.php';

if (!isset($_SESSION['logged_in'])) {
    send(400, 'no user logged in');
    die();
}

$username = $_SESSION['username'];

session_unset();
session_destroy();

send(200, $username . ' logged out');
