<?php

session_start();

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');

require_once '../../../../config/DbConnection.php';
require_once '../../../../models/Type_5.php';
require_once '../../../../utils/send.php';
require_once '../../../api.php';

// TYPE 5 file
class Photo_api extends Type_5 implements api
{
    private $Photo;

    // Initialize connection with DB
    public function __construct()
    {
        // Connect with DB
        $dbconnection = new DbConnection();
        $db = $dbconnection->connect();

        // Create an object for users table to do operations
        $this->Photo = new Type_5($db);

        // Set table name
        $this->Photo->table = 'faculty_photo';

        // Set column names
        $this->Photo->id_name = 'photo_id';
        $this->Photo->text_name = 'photo_name';
        $this->Photo->from_name = 'photo_type';
        $this->Photo->to_name = 'photo';
    }

    // Get all data
    public function get()
    {
        // Get the user info from DB
        $all_data = $this->Photo->read();

        if ($all_data) {
            $data = array();
            while ($row = $all_data->fetch(PDO::FETCH_ASSOC)) {
                $photo = array(
                    "photo_id" => $row['photo_id'],
                    "user_id" => $row['user_id'],
                    "photo_name" => $row['photo_name'],
                    "photo_type" => $row['photo_type'],
                    "photo" => base64_encode($row['photo'])
                );
                array_push($data, $photo);
            }
            echo json_encode($data);
            die();
        } else {
            send(400, 'error', 'no photo found');
            die();
        }
    }

    // Get all data of a user info by ID
    public function get_by_id()
    {
        // Get the user info from DB
        $this->Photo->user_id = $_GET['ID'];
        $all_data = $this->Photo->read_row();
        $data = $all_data->fetch(PDO::FETCH_ASSOC);

        if ($data) {
            $photo = array(
                "photo_id" => $data['photo_id'],
                "user_id" => $data['user_id'],
                "photo_name" => $data['photo_name'],
                "photo_type" => $data['photo_type'],
                "photo" => base64_encode($data['photo'])
            );
            echo json_encode($photo);
            die();
        } else {
            send(400, 'error', 'no photo found');
            die();
        }
    }

    public function update_by_id($DB_data, $to_update, $update_str)
    {
        if (!$this->Photo->update_row($update_str)) {
            // If can't update_by_id the data, throw an error message
            send(400, 'error', $update_str . ' for ' . $_SESSION['username'] . ' cannot be updated');
            die();
        }
    }

    // UPDATE (PUT) a existing user's info
    public function put()
    {
        // // Authorization
        // if ($_SESSION['user_id'] != $_GET['ID']) {
        //     send(401, 'error', 'unauthorized');
        //     die();
        // }

        // Get input data as json
        // $data = json_decode(file_get_contents("php://input"));

        // Clean the data
        $this->Photo->user_id = $_SESSION['user_id'];
        $this->Photo->text_title = $_FILES['photo']['name'];
        $this->Photo->from_text = $_FILES['photo']['type'];
        $this->Photo->to_int = file_get_contents($_FILES['photo']['tmp_name']);

        // Get the user info from DB
        $all_data = $this->Photo->read_row();
        $data = $all_data->fetch(PDO::FETCH_ASSOC);

        $error = false;
        $message = '';
        // If user info already exists, update the user info that changed
        if ($data) {
            $this->Photo->id = $data['photo_id'];

            $this->update_by_id($data['photo_name'], $_FILES['photo']['name'], 'photo_name');
            $this->update_by_id($data['photo_type'], $_FILES['photo']['type'], 'photo_type');
            $this->update_by_id($data['photo'], file_get_contents($_FILES['photo']['tmp_name']), 'photo');

            // If updated successfully, get_by_id the data, else throw an error message 
            $this->get_by_id();
        } else {
            // If not user pic found, upload new one
            if ($this->Photo->post()) {
                $this->get_by_id();
            } else {
                send(400, 'error', 'photo cannot be uploaded or created');
            }
        }
    }

    // Delete a user pic
    public function delete_by_id()
    {
        $this->Photo->user_id = $_SESSION['user_id'];
        if ($this->Photo->delete_by_uid()) {
            send(200, 'message', 'image deleted successfully');
        } else {
            send(400, 'error', 'photo cannot deleted');
        }
    }
}


// GET all the user info
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $Photo_api = new Photo_api();
    if (isset($_GET['ID'])) {
        $Photo_api->get_by_id();
    } else {
        $Photo_api->get();
    }
}

// To check if an user is logged in
if (!isset($_SESSION['user_id'])) {
    send(400, 'error', 'no user logged in');
    die();
}

// If a user logged in ...

// POST a new user info
if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'PUT') {
    $Photo_api = new Photo_api();
    $Photo_api->put();
}

// // UPDATE (PUT) a existing user's info
// if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
//     $Photo_api = new Photo_api();
//     $Photo_api->put();
// }

// DELETE a user's photo
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $Photo_api = new Photo_api();
    $Photo_api->delete_by_id();
}
