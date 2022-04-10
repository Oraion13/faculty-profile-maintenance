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

$User_info->user_id = $_GET['ID'];
$all_data = $User_info->read();

// GET all the user info
function get_all()
{
    // Connect with DB
    $dbconnection = new DbConnection();
    $db = $dbconnection->connect();

    // Create an object for users table to do operations
    $User_info = new User_info($db);

    $User_info->user_id = $_GET['ID'];
    $all_data = $User_info->read();

    if ($all_data) {
        echo json_encode($all_data);
        die();
    } else {
        send(400, 'error', 'no user info found');
        die();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    get_all();
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
    $User_info->user_id = $_SESSION['user_id'];
    $User_info->phone = $data->phone;
    $User_info->address = $data->address;
    $User_info->position_id = $data->position_id;
    $User_info->department_id = $data->department_id;
    $User_info->position_present_where = $data->position_present_where;
    $User_info->position_present_from = $data->position_present_from;

    // If no user info exists
    if (!$all_data) {
        // If no user info exists, insert and get the data
        if ($User_info->create()) {
            get_all();
        } else {
            send(400, 'error', 'user info cannot be created');
        }
    } else {
        send(400, 'error', 'user info already exists');
    }
}

// UPDATE (PUT) a existing user's info
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    // Authorization
    if ($_SESSION['user_id'] != $_GET['ID']) {
        send(401, 'error', 'unauthorized');
        die();
    }

    // Get input data as json
    $data = json_decode(file_get_contents("php://input"));

    // Clean the data
    $User_info->user_id = $_SESSION['user_id'];
    $User_info->phone = $data->phone;
    $User_info->address = $data->address;
    $User_info->position_id = $data->position_id;
    $User_info->department_id = $data->department_id;
    $User_info->position_present_where = $data->position_present_where;
    $User_info->position_present_from = $data->position_present_from;

    $error = false;
    $message = '';

    // If user info already exists, update the user info that changed
    if ($all_data) {
        $User_info->user_info_id = $all_data['user_info_id'];
        if (strcmp($all_data['phone'], $data->phone) !== 0) {
            if (!$User_info->update('phone')) {
                $error = true;
                $message .= ',phone number,';
            }
        }
        if (strcmp($all_data['address'], $data->address) !== 0) {
            if (!$User_info->update('address')) {
                $error = true;
                $message .= ',address,';
            }
        }
        if (strcmp($all_data['position_id'], $data->position_id) !== 0) {
            if (!$User_info->update('position_id')) {
                $error = true;
                $message .= ',faculty position,';
            }
        }
        if (strcmp($all_data['department_id'], $data->department_id) !== 0) {
            if (!$User_info->update('department_id')) {
                $error = true;
                $message .= ',department,';
            }
        }
        if (strcmp($all_data['position_present_where'], $data->position_present_where) !== 0) {
            if (!$User_info->update('position_present_where')) {
                $error = true;
                $message .= ',position present where,';
            }
        }
        if (strcmp($all_data['position_present_from'], $data->position_present_from) !== 0) {
            if (!$User_info->update('position_present_from')) {
                $error = true;
                $message .= ',position present from,';
            }
        }
        if (strcmp($all_data['department_id'], $data->department_id) !== 0) {
            if (!$User_info->update('department_id')) {
                $error = true;
                $message .= ',department,';
            }
        }

        // If updated successfully, get the data, else throw an error message 
        if ($error) {
            send(400, 'error', substr($message, 1, -1) . ' cannot be updated');
        } else {
            get_all();
        }
    } else {
        send(400, 'error', 'no user info found');
    }
}
