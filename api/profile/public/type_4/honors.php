<?php

session_start();

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');

require_once '../../../../config/DbConnection.php';
require_once '../../../../models/Type_4.php';
require_once '../../../../utils/send.php';
require_once '../../../api.php';

// TYPE 4 file
class Honors_api extends Type_4 implements api
{
    private $Honors;

    // Initialize connection with DB
    public function __construct()
    {
        // Connect with DB
        $dbconnection = new DbConnection();
        $db = $dbconnection->connect();

        // Create an object for users table to do operations
        $this->Honors = new Type_4($db);

        // Set table name
        $this->Honors->table = 'faculty_honors';

        // Set column names
        $this->Honors->id_name = 'honor_id';
        $this->Honors->text_name = 'honor';
        $this->Honors->from_name = 'honored_at';
    }

    // Get all data
    public function get()
    {
        // Get the user info from DB
        $all_data = $this->Honors->read();

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

    // Get all the data of a user's honor by ID
    public function get_by_id()
    {
        // Get the user info from DB
        $this->Honors->user_id = $_GET['ID'];
        $all_data = $this->Honors->read_row();

        if ($all_data) {
            $data = array();
            while ($row = $all_data->fetch(PDO::FETCH_ASSOC)) {
                array_push($data, $row);
            }
            echo json_encode($data);
            die();
        } else {
            send(400, 'error', 'no user info about Honors found');
            die();
        }
    }
    // POST a new user's honor
    public function post()
    {
        if (!$this->Honors->post()) {
            // If can't post the data, throw an error message
            send(400, 'error', 'honor cannot be added');
            die();
        }
    }

    // PUT a user's honor
    public function update_by_id($DB_data, $to_update, $update_str)
    {
        if (strcmp($DB_data, $to_update) !== 0) {
            if (!$this->Honors->update_row($update_str)) {
                // If can't update_by_id the data, throw an error message
                send(400, 'error', $update_str . ' for ' . $_SESSION['username'] . ' cannot be updated');
                die();
            }
        }
    }

    // DELETE a user's honor
    public function delete_by_id()
    {
        if (!$this->Honors->delete_row()) {
            // If can't delete the data, throw an error message
            send(400, 'error', 'data cannot be deleted');
            die();
        }
    }

    // POST/UPDATE (PUT)/DELETE a user's Honors
    public function put()
    {
        // Authorization
        if ($_SESSION['user_id'] != $_GET['ID']) {
            send(401, 'error', 'unauthorized');
            die();
        }

        // Get input data as json
        $data = json_decode(file_get_contents("php://input"));

        // Get all the user's honor info from DB
        $this->Honors->user_id = $_SESSION['user_id'];
        $all_data = $this->Honors->read_row();

        // Store all honor_id	's in an array
        $DB_data = array();
        $data_IDs = array();
        while ($row = $all_data->fetch(PDO::FETCH_ASSOC)) {
            array_push($DB_data, $row);
        }

        // Insert the data which has no ID
        $count = 0;
        while ($count < count($data)) {
            // Clean the data
            $this->Honors->text = $data[$count]->honor;
            $this->Honors->from = $data[$count]->honored_at;

            if ($data[$count]->honor_id     === 0) {
                $this->post();
                array_splice($data, $count, 1);
                continue;
            }

            // Store the IDs
            array_push($data_IDs, $data[$count]->honor_id);

            ++$count;
        }

        // Delete the data which is abandoned
        $count = 0;
        while ($count < count($DB_data)) {
            if (!in_array($DB_data[$count]['honor_id'], $data_IDs)) {
                $this->Honors->id = (int)$DB_data[$count]['honor_id'];
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
                if ($element['honor_id'] == $data[$count]->honor_id) {
                    $this->Honors->id = $element['honor_id'];
                    $this->Honors->text = $data[$count]->honor;
                    $this->Honors->from = $data[$count]->honored_at;

                    $this->update_by_id($element['honor'], $data[$count]->honor, 'honor');
                    $this->update_by_id($element['honored_at'], $data[$count]->honored_at, 'honored_at');

                    break;
                }
            }

            ++$count;
        }

        $this->get_by_id();
    }
}

// GET all the user's Honors
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $Honors_api = new Honors_api();
    if (isset($_GET['ID'])) {
        $Honors_api->get_by_id();
    } else {
        $Honors_api->get();
    }
}

// To check if an user is logged in
if (!isset($_SESSION['user_id'])) {
    send(400, 'error', 'no user logged in');
    die();
}

// If a user logged in ...

// POST/UPDATE (PUT) a user's Honors
if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'PUT') {
    $Honors_api = new Honors_api();
    $Honors_api->put();
}
