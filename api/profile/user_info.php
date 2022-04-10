<?php

session_start();

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');

require_once '../../config/DbConnection.php';
require_once '../../models/User_info.php';
require_once '../../utils/send.php';

// Connect with DB
$dbconnection = new DbConnection();
$db = $dbconnection->connect();

// Create an object for users table to do operations
$User_info = new User_info($db);

// If a user logged in ...
$User_info->user_id = $_GET['ID'];
$all_data = $User_info->read();

// GET all the user info
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if ($all_data) {
        echo json_encode($all_data);
        die();
    } else {
        send(400, 'error', 'no user info found');
        die();
    }
}

// To check if an user is already logged in
if (!isset($_SESSION['user_id']) || !isset($_GET['ID'])) {
    send(400, 'error', 'no user logged in');
    die();
}

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
    $User_info->user_id = $_GET['ID'];
    $User_info->phone = $data->phone;
    $User_info->address = $data->address;
    $User_info->present_position = $data->present_position;
    $User_info->present_position_from = $data->present_position_from;
    $User_info->position_id = $data->position_id;
    $User_info->department_id = $data->department_id;
    $User_info->prev_position = $data->prev_position;

    // If no user info exists
    if (!$all_data) {
        // If no user info exists
        if ($User_info->create()) {
            send(200, 'message', 'user info created successfully');
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
    $User_info->user_id = $_GET['ID'];
    $User_info->phone = $data->phone;
    $User_info->address = $data->address;
    $User_info->present_position = $data->present_position;
    $User_info->present_position_from = $data->present_position_from;
    $User_info->position_id = $data->position_id;
    $User_info->department_id = $data->department_id;
    $User_info->prev_position = $data->prev_position;

    $error = false;
    $message = '';
    $errormsg = '';

    // If user info already exists, update the user info that changed
    if ($all_data) {
        $User_info->user_info_id = $all_data['user_info_id'];
        if (strcmp($all_data['phone'], $data->phone) !== 0) {
            if ($User_info->update('phone')) {
                $message .= 'phone number ';
            } else {
                $error = true;
                $errormsg .= 'phone number ';
            }
        }
        if (strcmp($all_data['address'], $data->address) !== 0) {
            if ($User_info->update('address')) {
                $message .= 'address ';
            } else {
                $error = true;
                $errormsg .= 'address ';
            }
        }
        if (strcmp($all_data['present_position'], $data->present_position) !== 0) {
            if ($User_info->update('present_position')) {
                $message .= 'present position ';
            } else {
                $error = true;
                $errormsg .= 'present position ';
            }
        }
        if (strcmp($all_data['prev_position'], $data->prev_position) !== 0) {
            if ($User_info->update('prev_position')) {
                $message .= 'faculty\'s previous position ';
            } else {
                $error = true;
                $errormsg .= 'faculty\'s previous position ';
            }
        }
        if (strcmp($all_data['present_position_from'], $data->present_position_from) !== 0) {
            if ($User_info->update('present_position_from')) {
                $message .= 'present position from ';
            } else {
                $error = true;
                $errormsg .= 'present position from ';
            }
        }
        if (strcmp($all_data['position_id'], $data->position_id) !== 0) {
            if ($User_info->update('position_id')) {
                $message .= 'faculty position ';
            } else {
                $error = true;
                $errormsg .= 'faculty position ';
            }
        }
        if (strcmp($all_data['department_id'], $data->department_id) !== 0) {
            if ($User_info->update('department_id')) {
                $message .= 'department ';
            } else {
                $error = true;
                $errormsg .= 'department ';
            }
        }

        if ($error) {
            send(400, 'error', $errormsg . 'cannot be updated');
        } else {
            send(200, 'message', $message . 'updated successfully');
        }
    } else {
        send(400, 'error', 'no user info found');
    }
}
