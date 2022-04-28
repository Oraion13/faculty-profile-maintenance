<?php

session_start();

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');

require_once '../../../../config/DbConnection.php';
require_once '../../../../models/Type_5.php';
require_once '../../../../utils/send.php';
require_once '../../../api.php';
require_once '../../../../utils/loggedin_verified.php';

// TYPE 5 file
class Programme_organized_api extends Type_5 implements api
{
    private $Programme_organized;

    // Initialize connection with DB
    public function __construct()
    {
        // Connect with DB
        $dbconnection = new DbConnection();
        $db = $dbconnection->connect();

        // Create an object for users table to do operations
        $this->Programme_organized = new Type_5($db);

        // Set table name
        $this->Programme_organized->table = 'faculty_programme_organized';

        // Set column names
        $this->Programme_organized->id_name = 'programme_organized_id';
        $this->Programme_organized->text_name = 'programme_organized';
        $this->Programme_organized->from_name = 'programme_organized_from';
        $this->Programme_organized->to_name = 'programme_organized_to';
    }

    // Get all data
    public function get()
    {
        // Get the user info from DB
        $all_data = $this->Programme_organized->read();

        if ($all_data) {
            $data = array();
            while ($row = $all_data->fetch(PDO::FETCH_ASSOC)) {
                array_push($data, $row);
            }
            echo json_encode($data);
            die();
        } else {
            send(400, 'error', 'no info about Area of specialization found');
            die();
        }
    }

    // Get all the data of a user's programme_organized
    public function get_by_id($id)
    {
        // Get the user info from DB
        $this->Programme_organized->user_id = $id;
        $all_data = $this->Programme_organized->read_row();

        if ($all_data) {
            $data = array();
            while ($row = $all_data->fetch(PDO::FETCH_ASSOC)) {
                array_push($data, $row);
            }
            echo json_encode($data);
            die();
        } else {
            send(400, 'error', 'no user info about Programme_organized found');
            die();
        }
    }
    // POST a new user's programme_organized
    public function post()
    {
        if (!$this->Programme_organized->post()) {
            // If can't post the data, throw an error message
            send(400, 'error', 'programme_organized cannot be added');
            die();
        }
    }

    // PUT a user's programme_organized
    public function update_by_id($DB_data, $to_update, $update_str)
    {
        if (strcmp($DB_data, $to_update) !== 0) {
            if (!$this->Programme_organized->update_row($update_str)) {
                // If can't update_by_id the data, throw an error message
                send(400, 'error', $update_str . ' for ' . $_SESSION['username'] . ' cannot be updated');
                die();
            }
        }
    }

    // DELETE a user's programme_organized
    public function delete_by_id()
    {
        if (!$this->Programme_organized->delete_row()) {
            // If can't delete the data, throw an error message
            send(400, 'error', 'data cannot be deleted');
            die();
        }
    }

    // POST/UPDATE (PUT)/DELETE a user's Programme_organized
    public function put()
    {
        // // Authorization
        // if ($_SESSION['user_id'] != $_GET['ID']) {
        //     send(401, 'error', 'unauthorized');
        //     die();
        // }

        // Get input data as json
        $data = json_decode(file_get_contents("php://input"));

        // Get all the user's programme_organized info from DB
        $this->Programme_organized->user_id = $_SESSION['user_id'];
        $all_data = $this->Programme_organized->read_row();

        // Store all programme_organized_id's in an array
        $DB_data = array();
        $data_IDs = array();
        while ($row = $all_data->fetch(PDO::FETCH_ASSOC)) {
            array_push($DB_data, $row);
        }

        // Insert the data which has no ID
        $count = 0;
        while ($count < count($data)) {
            // Clean the data
            $this->Programme_organized->text_title = $data[$count]->programme_organized;
            $this->Programme_organized->from_text = $data[$count]->programme_organized_from;
            $this->Programme_organized->to_int = $data[$count]->programme_organized_to;

            if ($data[$count]->programme_organized_id === 0) {
                $this->post();
                array_splice($data, $count, 1);
                continue;
            }

            // Store the IDs
            array_push($data_IDs, $data[$count]->programme_organized_id);

            ++$count;
        }

        // Delete the data which is abandoned
        $count = 0;
        while ($count < count($DB_data)) {
            if (!in_array($DB_data[$count]['programme_organized_id'], $data_IDs)) {
                $this->Programme_organized->id = (int)$DB_data[$count]['programme_organized_id'];
                $this->delete_by_id();
            }

            ++$count;
        }

        // Update the data which is available
        $count = 0;
        while ($count < count($data)) {
            // Clean the data
            // print_r($row);
            foreach ($DB_data as $key => $element) {
                if ($element['programme_organized_id'] == $data[$count]->programme_organized_id) {
                    $this->Programme_organized->id = $element['programme_organized_id'];
                    $this->Programme_organized->text_title = $data[$count]->programme_organized;
                    $this->Programme_organized->from_text = $data[$count]->programme_organized_from;
                    $this->Programme_organized->to_int = $data[$count]->programme_organized_to;

                    $this->update_by_id($element['programme_organized'], $data[$count]->programme_organized, 'programme_organized');
                    $this->update_by_id($element['programme_organized_from'], $data[$count]->programme_organized_from, 'programme_organized_from');
                    $this->update_by_id($element['programme_organized_to'], $data[$count]->programme_organized_to, 'programme_organized_to');

                    break;
                }
            }

            ++$count;
        }

        $this->get_by_id($_SESSION['user_id']);
    }
}

// GET all the user's Programme_organized
// GET all the user info
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $Programme_organized_api = new Programme_organized_api();
    if (isset($_GET['ID'])) {
        $Programme_organized_api->get_by_id($_GET['ID']);
    } else {
        $Programme_organized_api->get();
    }
}

// To check if an user is logged in and verified
loggedin_verified();

// If a user logged in ...

// POST/UPDATE (PUT) a user's Programme_organized
if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'PUT') {
    $Programme_organized_api = new Programme_organized_api();
    $Programme_organized_api->put();
}
