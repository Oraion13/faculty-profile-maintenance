<?php

session_start();

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');

require_once '../../../config/DbConnection.php';
require_once '../../../models/Positions_prev.php';
require_once '../../../utils/send.php';

// Connect with DB
$dbconnection = new DbConnection();
$db = $dbconnection->connect();

// Create an object for users table to do operations
$Positions_prev = new Positions_prev($db);

// If a user logged in ...
$Positions_prev->user_id = $_GET['ID'];
$all_data = $Positions_prev->read();

// GET all the user info
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if ($all_data) {
        while ($row = $all_data->fetch(PDO::FETCH_ASSOC)) {
            echo json_encode($row);
        }
        die();
    } else {
        send(400, 'error', 'no user info about previous positions found');
        die();
    }
}

// To check if an user is already logged in
if (!isset($_SESSION['user_id']) || !isset($_GET['ID'])) {
    send(400, 'error', 'no user logged in');
    die();
}

// If a user logged in ...
// POST a new user info
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Authorization
    if ($_SESSION['user_id'] != $_GET['ID']) {
        send(401, 'error', 'unauthorized');
        die();
    }

    // Get input data as json
    $data = json_decode(file_get_contents("php://input"));

    // Clean the data
    $Positions_prev->user_id = $_SESSION['user_id'];
    $Positions_prev->position_id = $data->position_id;
    $Positions_prev->department_id = $data->department_id;
    $Positions_prev->position_present_where = $data->position_present_where;
    $Positions_prev->position_present_from = $data->position_present_from;
    $Positions_prev->position_present_to = $data->position_present_to;

    // Try to add a new previous position for user
    if ($Positions_prev->create()) {
        send(200, 'message', 'previous position added successfully');
    } else {
        send(400, 'error', 'previous position cannot be added');
    }
}
