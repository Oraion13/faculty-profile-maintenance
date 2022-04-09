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

// To check if an user is already logged in
if (!isset($_SESSION['user_id'])) {
    send(400, 'error', 'no user logged in');
    die();
}

// If a user logged in ...
$User_info->user_id = $_SESSION['user_id'];
$all_data = $User_info->read();

// If method is GET, send the data
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if ($all_data) {
        echo json_encode($all_data);
    }
}

// If method is POST, store the data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get input data as json
    $data = json_decode(file_get_contents("php://input"));

    // Clean the data
    $User_info->user_id = $data->user_id;
    $User_info->phone = $data->phone;
    $User_info->address = $data->address;
    $User_info->present_position = $data->present_position;
    $User_info->present_position_from = $data->present_position_from;
    $User_info->position_id = $data->position_id;
    $User_info->department_id = $data->department_id;
    $User_info->prev_position_id = $data->prev_position_id;

    // If user info already exists
    if ($all_data) {
        $User_info->user_info_id = $all_data['user_info_id'];
        if ($data->phone && strcmp($all_data['phone'], $data->phone) !== 0) {
            if ($User_info->update('phone')) {
                send(200, 'message', 'phone number updated successfully');
            } else {
                send(400, 'error', 'phone number cannot be updated');
            }
        }
        if ($data->address && strcmp($all_data['address'], $data->address) !== 0) {
            if ($User_info->update('address')) {
                send(200, 'message', 'address updated successfully');
            } else {
                send(400, 'error', 'address cannot be updated');
            }
        }
        if ($data->present_position && strcmp($all_data['present_position'], $data->present_position) !== 0) {
            if ($User_info->update('present_position')) {
                send(200, 'message', 'present position updated successfully');
            } else {
                send(400, 'error', 'present position cannot be updated');
            }
        }
        if ($data->present_position_from && strcmp($all_data['present_position_from'], $data->present_position_from) !== 0) {
            if ($User_info->update('present_position_from')) {
                send(200, 'message', 'present position from updated successfully');
            } else {
                send(400, 'error', 'present position from cannot be updated');
            }
        }
        if ($data->position_id && strcmp($all_data['position_id'], $data->position_id) !== 0) {
            if ($User_info->update('position_id')) {
                send(200, 'message', 'faculty position updated successfully');
            } else {
                send(400, 'error', 'faculty position cannot be updated');
            }
        }
        if ($data->department_id && strcmp($all_data['department_id'], $data->department_id) !== 0) {
            if ($User_info->update('department_id')) {
                send(200, 'message', 'department updated successfully');
            } else {
                send(400, 'error', 'department cannot be updated');
            }
        }
        if ($data->prev_position_id && strcmp($all_data['prev_position_id'], $data->prev_position_id) !== 0) {
            if ($User_info->update('prev_position_id')) {
                send(200, 'message', 'faculty\'s previous position updated successfully');
            } else {
                send(400, 'error', 'faculty\'s previous position cannot be updated');
            }
        }
    } else {
        // If no user info exists
        if ($User_info->create()) {
            send(200, 'message', 'user info created successfully');
        } else {
            send(400, 'error', 'user cannot be created');
        }
    }
}
