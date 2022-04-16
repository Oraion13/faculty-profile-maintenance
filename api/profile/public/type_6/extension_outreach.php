<?php

session_start();

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');

require_once '../../../../config/DbConnection.php';
require_once '../../../../models/Type_6.php';
require_once '../../../../utils/send.php';

// TYPE 6 file
class Extension_outreach_api
{
    private $Extension_outreach;

    // Initialize connection with DB
    public function __construct()
    {
        // Connect with DB
        $dbconnection = new DbConnection();
        $db = $dbconnection->connect();

        // Create an object for users table to do operations
        $this->Extension_outreach = new Type_6($db);

        // Set table name
        $this->Extension_outreach->table = 'faculty_extension_outreach';

        // Set column names
        $this->Extension_outreach->id_name = 'extension_outreach_id';
        $this->Extension_outreach->text_name = 'extension_outreach';
        $this->Extension_outreach->from_name = 'extension_outreach_from';
        $this->Extension_outreach->to_name = 'extension_outreach_to';
        $this->Extension_outreach->text_int_name = 'number_of_participants';
    }

    // Get all data
    public function get()
    {
        // Get the user info from DB
        $all_data = $this->Extension_outreach->read();

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

    // Get all the data of a user's extension_outreach
    public function get_by_id()
    {
        // Get the user info from DB
        $this->Extension_outreach->user_id = $_GET['ID'];
        $all_data = $this->Extension_outreach->read_by_id();

        if ($all_data) {
            $data = array();
            while ($row = $all_data->fetch(PDO::FETCH_ASSOC)) {
                array_push($data, $row);
            }
            echo json_encode($data);
            die();
        } else {
            send(400, 'error', 'no user info about Extension_outreach found');
            die();
        }
    }
    // POST a new user's extension_outreach
    public function post()
    {
        if (!$this->Extension_outreach->create()) {
            // If can't post the data, throw an error message
            send(400, 'error', 'extension_outreach cannot be added');
            die();
        }
    }

    // PUT a user's extension_outreach
    public function update($DB_data, $to_update, $update_str)
    {
        if (strcmp($DB_data, $to_update) !== 0) {
            if (!$this->Extension_outreach->update($update_str)) {
                // If can't update the data, throw an error message
                send(400, 'error', $update_str . ' for ' . $_SESSION['username'] . ' cannot be updated');
                die();
            }
        }
    }

    // DELETE a user's extension_outreach
    public function delete_data()
    {
        if (!$this->Extension_outreach->delete_row()) {
            // If can't delete the data, throw an error message
            send(400, 'error', 'data cannot be deleted');
            die();
        }
    }

    // POST/UPDATE (PUT)/DELETE a user's Extension_outreach
    public function put()
    {
        // Authorization
        if ($_SESSION['user_id'] != $_GET['ID']) {
            send(401, 'error', 'unauthorized');
            die();
        }

        // Get input data as json
        $data = json_decode(file_get_contents("php://input"));

        // Get all the user's extension_outreach info from DB
        $this->Extension_outreach->user_id = $_SESSION['user_id'];
        $all_data = $this->Extension_outreach->read_by_id();

        // Store all extension_outreach_id's in an array
        $DB_data = array();
        $data_IDs = array();
        while ($row = $all_data->fetch(PDO::FETCH_ASSOC)) {
            array_push($DB_data, $row);
        }

        // Insert the data which has no ID
        $count = 0;
        while ($count < count($data)) {
            // Clean the data
            $this->Extension_outreach->text = $data[$count]->extension_outreach;
            $this->Extension_outreach->from = $data[$count]->extension_outreach_from;
            $this->Extension_outreach->to = $data[$count]->extension_outreach_to;
            $this->Extension_outreach->text_int = $data[$count]->number_of_participants;

            if ($data[$count]->extension_outreach_id === 0) {
                $this->post();
                array_splice($data, $count, 1);
                continue;
            }

            // Store the IDs
            array_push($data_IDs, $data[$count]->extension_outreach_id);

            ++$count;
        }

        // Delete the data which is abandoned
        $count = 0;
        while ($count < count($DB_data)) {
            if (!in_array($DB_data[$count]['extension_outreach_id'], $data_IDs)) {
                $this->Extension_outreach->id = (int)$DB_data[$count]['extension_outreach_id'];
                $this->delete_data();
            }

            ++$count;
        }

        // Update the data which is available
        $count = 0;
        while ($count < count($data)) {
            // Clean the data
            // print_r($row);
            foreach ($DB_data as $key => $element) {
                if ($element['extension_outreach_id'] == $data[$count]->extension_outreach_id) {
                    $this->Extension_outreach->id = $element['extension_outreach_id'];
                    $this->Extension_outreach->text = $data[$count]->extension_outreach;
                    $this->Extension_outreach->from = $data[$count]->extension_outreach_from;
                    $this->Extension_outreach->to = $data[$count]->extension_outreach_to;
                    $this->Extension_outreach->text_int = $data[$count]->number_of_participants;

                    $this->update($element['extension_outreach'], $data[$count]->extension_outreach, 'extension_outreach');
                    $this->update($element['extension_outreach_from'], $data[$count]->extension_outreach_from, 'extension_outreach_from');
                    $this->update($element['extension_outreach_to'], $data[$count]->extension_outreach_to, 'extension_outreach_to');
                    $this->update($element['number_of_participants'], $data[$count]->number_of_participants, 'number_of_participants');

                    break;
                }
            }

            ++$count;
        }

        $this->get_by_id();
    }
}

// GET all the user's Extension_outreach
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $Extension_outreach_api = new Extension_outreach_api();
    if (isset($_GET['ID'])) {
        $Extension_outreach_api->get_by_id();
    } else {
        $Extension_outreach_api->get();
    }
}

// To check if an user is logged in
if (!isset($_SESSION['user_id'])) {
    send(400, 'error', 'no user logged in');
    die();
}

// If a user logged in ...

// POST/UPDATE (PUT) a user's Extension_outreach
if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'PUT') {
    $Extension_outreach_api = new Extension_outreach_api();
    $Extension_outreach_api->put();
}
