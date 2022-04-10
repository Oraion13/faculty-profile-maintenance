<?php

session_start();

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');

require_once '../../../config/DbConnection.php';
require_once '../../../models/User_info.php';
require_once '../../../utils/send.php';

// Connect with DB
$dbconnection = new DbConnection();
$db = $dbconnection->connect();

// Create an object for users table to do operations
$User_info = new User_info($db);

// If a user logged in ...
$User_info->user_id = $_GET['ID'];
$all_data = $User_info->read();