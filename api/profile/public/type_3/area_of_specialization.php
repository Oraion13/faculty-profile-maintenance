<?php

session_start();

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');

require_once '../../../../config/DbConnection.php';
require_once '../../../../models/Type_3.php';
require_once '../../../../utils/send.php';
require_once '../../../api.php';
require_once '../../../../utils/loggedin_verified.php';

// TYPE 3 file
class Area_of_specialization_api extends Type_3 implements api
{
    private $Area_of_specialization;

    // Initialize connection with DB
    public function __construct()
    {
        // Connect with DB
        $dbconnection = new DbConnection();
        $db = $dbconnection->connect();

        // Create an object for users table to do operations
        $this->Area_of_specialization = new Type_3($db);

        // Set table name
        $this->Area_of_specialization->table = 'faculty_area_of_specialization';

        // Set column names
        $this->Area_of_specialization->id_name = 'specialization_id';
        $this->Area_of_specialization->text_name = 'specialization';
    }

    // Get all data
    public function get()
    {
        // Get the user info from DB
        $all_data = $this->Area_of_specialization->read();

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

    // Get all the data of a user's specialization by ID
    public function get_by_id($id)
    {
        // Get the user info from DB
        $this->Area_of_specialization->user_id = $id;
        $all_data = $this->Area_of_specialization->read_row();

        if ($all_data) {
            $data = array();
            while ($row = $all_data->fetch(PDO::FETCH_ASSOC)) {
                array_push($data, $row);
            }
            echo json_encode($data);
            die();
        } else {
            send(400, 'error', 'no user info about Area of specialization found');
            die();
        }
    }
    // POST a new user's specialization
    public function post()
    {
        if (!$this->Area_of_specialization->post()) {
            // If can't post the data, throw an error message
            send(400, 'error', 'specialization cannot be added');
            die();
        }
    }

    // PUT a user's specialization
    public function update_by_id($DB_data, $to_update, $update_str)
    {
        if (strcmp($DB_data, $to_update) !== 0) {
            if (!$this->Area_of_specialization->update_row($update_str)) {
                // If can't update_by_id the data, throw an error message
                send(400, 'error', $update_str . ' for ' . $_SESSION['username'] . ' cannot be updated');
                die();
            }
        }
    }

    // DELETE a user's specialization
    public function delete_by_id()
    {
        if (!$this->Area_of_specialization->delete_row()) {
            // If can't delete the data, throw an error message
            send(400, 'error', 'data cannot be deleted');
            die();
        }
    }

    // POST/UPDATE (PUT)/DELETE a user's Area of specialization
    public function put()
    {
        // // Authorization
        // if ($_SESSION['user_id'] != $_GET['ID']) {
        //     send(401, 'error', 'unauthorized');
        //     die();
        // }

        // Get input data as json
        $data = json_decode(file_get_contents("php://input"));

        // Get all the user's specialization info from DB
        $this->Area_of_specialization->user_id = $_SESSION['user_id'];
        $all_data = $this->Area_of_specialization->read_row();

        // Store all specialization_id's in an array
        $DB_data = array();
        $data_IDs = array();
        while ($row = $all_data->fetch(PDO::FETCH_ASSOC)) {
            array_push($DB_data, $row);
        }

        // Insert the data which has no ID
        $count = 0;
        while ($count < count($data)) {
            // Clean the data
            $this->Area_of_specialization->text = $data[$count]->specialization;

            if ($data[$count]->specialization_id === 0) {
                $this->post();
                array_splice($data, $count, 1);
                continue;
            }

            // Store the IDs
            array_push($data_IDs, $data[$count]->specialization_id);

            ++$count;
        }

        // Delete the data which is abandoned
        $count = 0;
        while ($count < count($DB_data)) {
            if (!in_array($DB_data[$count]['specialization_id'], $data_IDs)) {
                $this->Area_of_specialization->id = (int)$DB_data[$count]['specialization_id'];
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
                if ($element['specialization_id'] == $data[$count]->specialization_id) {
                    $this->Area_of_specialization->id = $element['specialization_id'];
                    $this->Area_of_specialization->text = $data[$count]->specialization;

                    $this->update_by_id($element['specialization'], $data[$count]->specialization, 'specialization');

                    break;
                }
            }

            ++$count;
        }

        $this->get_by_id($_SESSION['user_id']);
    }
}

// GET all the user info
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $Area_of_specialization_api = new Area_of_specialization_api();
    if (isset($_GET['ID'])) {
        $Area_of_specialization_api->get_by_id($_GET['ID']);
    } else {
        $Area_of_specialization_api->get();
    }
}

// To check if an user is logged in and verified
loggedin_verified();

// If a user logged in ...

// POST/UPDATE (PUT) a user's Area_of_specialization
if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'PUT') {
    $Area_of_specialization_api = new Area_of_specialization_api();
    $Area_of_specialization_api->put();
}
