<?php

session_start();

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');

require_once '../../../../config/DbConnection.php';
require_once '../../../../models/Type_4.php';
require_once '../../../../utils/send.php';

// TYPE 4 file
class Invited_lectures_api
{
    private $Invited_lectures;

    // Initialize connection with DB
    public function __construct()
    {
        // Connect with DB
        $dbconnection = new DbConnection();
        $db = $dbconnection->connect();

        // Create an object for users table to do operations
        $this->Invited_lectures = new Type_4($db);

        // Set table name
        $this->Invited_lectures->table = 'faculty_invited_lectures';

        // Set column names
        $this->Invited_lectures->id_name = 'invited_lecture_id';
        $this->Invited_lectures->text_name = 'invited_lecture';
        $this->Invited_lectures->from_name = 'invited_lecture_at';
    }

    // Get all the data of a user's invited_lecture
    public function get()
    {
        // Get the user info from DB
        $this->Invited_lectures->user_id = $_GET['ID'];
        $all_data = $this->Invited_lectures->read_by_id();

        if ($all_data) {
            $data = array();
            while ($row = $all_data->fetch(PDO::FETCH_ASSOC)) {
                array_push($data, $row);
            }
            echo json_encode($data);
            die();
        } else {
            send(400, 'error', 'no user info about Invited_lectures found');
            die();
        }
    }
    // POST a new user's invited_lecture
    public function post()
    {
        if (!$this->Invited_lectures->create()) {
            // If can't post the data, throw an error message
            send(400, 'error', 'invited_lecture cannot be added');
            die();
        }
    }

    // PUT a user's invited_lecture
    public function update($DB_data, $to_update, $update_str)
    {
        if (strcmp($DB_data, $to_update) !== 0) {
            if (!$this->Invited_lectures->update($update_str)) {
                // If can't update the data, throw an error message
                send(400, 'error', $update_str . ' for ' . $_SESSION['username'] . ' cannot be updated');
                die();
            }
        }
    }

    // DELETE a user's invited_lecture
    public function delete_data()
    {
        if (!$this->Invited_lectures->delete_row()) {
            // If can't delete the data, throw an error message
            send(400, 'error', 'data cannot be deleted');
            die();
        }
    }

    // POST/UPDATE (PUT)/DELETE a user's Invited_lectures
    public function put()
    {
        // Authorization
        if ($_SESSION['user_id'] != $_GET['ID']) {
            send(401, 'error', 'unauthorized');
            die();
        }

        // Get input data as json
        $data = json_decode(file_get_contents("php://input"));

        // Get all the user's invited_lecture info from DB
        $this->Invited_lectures->user_id = $_SESSION['user_id'];
        $all_data = $this->Invited_lectures->read_by_id();

        // Store all invited_lecture_id	's in an array
        $DB_data = array();
        $data_IDs = array();
        while ($row = $all_data->fetch(PDO::FETCH_ASSOC)) {
            array_push($DB_data, $row);
        }

        // Insert the data which has no ID
        $count = 0;
        while ($count < count($data)) {
            // Clean the data
            $this->Invited_lectures->text = $data[$count]->invited_lecture;
            $this->Invited_lectures->from = $data[$count]->invited_lecture_at;

            if ($data[$count]->invited_lecture_id     === 0) {
                $this->post();
                array_splice($data, $count, 1);
                continue;
            }

            // Store the IDs
            array_push($data_IDs, $data[$count]->invited_lecture_id);

            ++$count;
        }

        // Delete the data which is abandoned
        $count = 0;
        while ($count < count($DB_data)) {
            if (!in_array($DB_data[$count]['invited_lecture_id'], $data_IDs)) {
                $this->Invited_lectures->id = (int)$DB_data[$count]['invited_lecture_id'];
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
                if ($element['invited_lecture_id'] == $data[$count]->invited_lecture_id) {
                    $this->Invited_lectures->id = $element['invited_lecture_id'];
                    $this->Invited_lectures->text = $data[$count]->invited_lecture;
                    $this->Invited_lectures->from = $data[$count]->invited_lecture_at;

                    $this->update($element['invited_lecture'], $data[$count]->invited_lecture, 'invited_lecture');
                    $this->update($element['invited_lecture_at'], $data[$count]->invited_lecture_at, 'invited_lecture_at');

                    break;
                }
            }

            ++$count;
        }

        $this->get();
    }
}

// GET all the user's Invited_lectures
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $Invited_lectures_api = new Invited_lectures_api();
    $Invited_lectures_api->get();
}

// To check if an user is logged in
if (!isset($_SESSION['user_id'])) {
    send(400, 'error', 'no user logged in');
    die();
}

// If a user logged in ...

// POST/UPDATE (PUT) a user's Invited_lectures
if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'PUT') {
    $Invited_lectures_api = new Invited_lectures_api();
    $Invited_lectures_api->put();
}
