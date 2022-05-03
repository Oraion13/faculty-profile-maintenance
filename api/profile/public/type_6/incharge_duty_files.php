<?php

session_start();

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');

require_once '../../../../config/DbConnection.php';
require_once '../../../../models/Type_6.php';
require_once '../../../../utils/send.php';
require_once '../../../api.php';
require_once '../../../../utils/loggedin_verified.php';

// TYPE 6 file
class Incharge_duty_files_api extends Type_6 implements api
{
    private $Incharge_duty_file;

    // Initialize connection with DB
    public function __construct()
    {
        // Connect with DB
        $dbconnection = new DbConnection();
        $db = $dbconnection->connect();

        // Create an object for users table to do operations
        $this->Incharge_duty_file = new Type_6($db);

        // Set table name
        $this->Incharge_duty_file->table = 'faculty_incharge_duty_files';

        // Set column names
        $this->Incharge_duty_file->id_name = 'incharge_duty_file_id';
        $this->Incharge_duty_file->text_name = 'incharge_duty_file_name';
        $this->Incharge_duty_file->from_name = 'incharge_duty_file_type';
        $this->Incharge_duty_file->to_name = 'incharge_duty_file';
        $this->Incharge_duty_file->text_int_name = 'incharge_duty_file_at';
    }

    // Get all data
    public function get()
    {
        // Get the user info from DB
        $all_data = $this->Incharge_duty_file->read();

        if ($all_data) {
            $data = array();
            while ($row = $all_data->fetch(PDO::FETCH_ASSOC)) {
                $row['incharge_duty_file'] = base64_encode($row['incharge_duty_file']);
                array_push($data, $row);
            }
            echo json_encode($data);
            die();
        } else {
            send(400, 'error', 'no info about incharge duties found');
            die();
        }
    }

    // Get all the data of a user's incharge duties
    public function get_by_id($id)
    {
        // Get the user info from DB
        $this->Incharge_duty_file->user_id = $id;
        $all_data = $this->Incharge_duty_file->read_row();

        if ($all_data) {
            $data = array();
            while ($row = $all_data->fetch(PDO::FETCH_ASSOC)) {
                $row['incharge_duty_file'] = base64_encode($row['incharge_duty_file']);
                array_push($data, $row);
            }
            echo json_encode($data);
            die();
        } else {
            send(400, 'error', 'no user info about incharge duties found');
            die();
        }
    }

    public function update_by_id($DB_data, $to_update, $update_str)
    {
        if (!$this->Incharge_duty_file->update_row($update_str)) {
            // If can't update_by_id the data, throw an error message
            send(400, 'error', $update_str . ' for ' . $_SESSION['username'] . ' cannot be updated');
            die();
        }
    }

    // UPDATE (PUT) a existing user's info
    public function put()
    {
        if ($_FILES['incharge_duty_file']['size'] >= 1572864) {
            send(400, "error", "file size must be less than 1.5MB");
            die();
        }

        // Clean the data
        $this->Incharge_duty_file->user_id = $_SESSION['user_id'];
        $this->Incharge_duty_file->text = $_FILES['incharge_duty_file']['name'];
        $this->Incharge_duty_file->from = $_FILES['incharge_duty_file']['type'];
        $this->Incharge_duty_file->to = file_get_contents($_FILES['incharge_duty_file']['tmp_name']);

        $new_date = date('Y-m-01', strtotime($_POST['incharge_duty_file_at']));
        $this->Incharge_duty_file->text_int = $new_date;

        if ($this->Incharge_duty_file->post()) {
            $this->get_by_id($_SESSION['user_id']);
        } else {
            send(400, 'error', 'incharge_duty_file cannot be uploaded');
        }
    }

    // Delete a user pic
    public function delete_by_id()
    {
        if (isset($_GET['ID'])) {
            send(400, "error", "provide an ID");
            die();
        }

        $this->Incharge_duty_file->id = $_GET['ID'];
        if ($this->Incharge_duty_file->delete_row()) {
            send(200, 'message', 'image deleted successfully');
        } else {
            send(400, 'error', 'incharge_duty_file cannot deleted');
        }
    }
}

// To check if an user is logged in and verified
loggedin_verified();

// If a user logged in ...

// GET all the user info
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $Incharge_duty_files_api = new Incharge_duty_files_api();
    if (isset($_GET['ID'])) {
        $Incharge_duty_files_api->get_by_id($_GET['ID']);
    } else {
        $Incharge_duty_files_api->get();
    }
}

// POST a new user info
if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'PUT') {
    $Incharge_duty_files_api = new Incharge_duty_files_api();
    $Incharge_duty_files_api->put();
}

// DELETE a user's incharge_duty_file
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $Incharge_duty_files_api = new Incharge_duty_files_api();
    $Incharge_duty_files_api->delete_by_id();
}
