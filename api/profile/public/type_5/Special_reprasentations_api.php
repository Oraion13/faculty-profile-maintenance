<?php

session_start();

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');

require_once '../../../../config/DbConnection.php';
require_once '../../../../models/Type_5.php';
require_once '../../../../utils/send.php';

// TYPE 5 file
class Special_respresentations_api
{
    private $Special_reprasentations;

    // Initialize connection with DB
    public function __construct()
    {
        // Connect with DB
        $dbconnection = new DbConnection();
        $db = $dbconnection->connect();

        // Create an object for users table to do operations
        $this->Special_reprasentations = new Type_5($db);

        // Set table name
        $this->Special_reprasentations->table = 'faculty_special_reprasentations';

        // Set column names
        $this->Special_reprasentations->id_name = 'special_reprasentation_id';
        $this->Special_reprasentations->text_name = 'special_reprasentation';
        $this->Special_reprasentations->from_name = 'special_reprasentation_from';
        $this->Special_reprasentations->to_name = 'special_reprasentation_to';
    }

    // Get all the data of a user's special_reprasentation
    public function get()
    {
        // Get the user info from DB
        $this->Special_reprasentations->user_id = $_GET['ID'];
        $all_data = $this->Special_reprasentations->read_by_id();

        if ($all_data) {
            $data = array();
            while ($row = $all_data->fetch(PDO::FETCH_ASSOC)) {
                array_push($data, $row);
            }
            echo json_encode($data);
            die();
        } else {
            send(400, 'error', 'no user info about Special_reprasentations found');
            die();
        }
    }
    // POST a new user's special_reprasentation
    public function post()
    {
        if (!$this->Special_reprasentations->create()) {
            // If can't post the data, throw an error message
            send(400, 'error', 'special_reprasentation cannot be added');
            die();
        }
    }

    // PUT a user's special_reprasentation
    public function update($DB_data, $to_update, $update_str)
    {
        if (strcmp($DB_data, $to_update) !== 0) {
            if (!$this->Special_reprasentations->update($update_str)) {
                // If can't update the data, throw an error message
                send(400, 'error', $update_str . ' for ' . $_SESSION['username'] . ' cannot be updated');
                die();
            }
        }
    }

    // DELETE a user's special_reprasentation
    public function delete_data()
    {
        if (!$this->Special_reprasentations->delete_row()) {
            // If can't delete the data, throw an error message
            send(400, 'error', 'data cannot be deleted');
            die();
        }
    }

    // POST/UPDATE (PUT)/DELETE a user's Special_reprasentations
    public function put()
    {
        // Authorization
        if ($_SESSION['user_id'] != $_GET['ID']) {
            send(401, 'error', 'unauthorized');
            die();
        }

        // Get input data as json
        $data = json_decode(file_get_contents("php://input"));

        // Get all the user's special_reprasentation info from DB
        $this->Special_reprasentations->user_id = $_SESSION['user_id'];
        $all_data = $this->Special_reprasentations->read_by_id();

        // Store all special_reprasentation_id's in an array
        $DB_data = array();
        $data_IDs = array();
        while ($row = $all_data->fetch(PDO::FETCH_ASSOC)) {
            array_push($DB_data, $row);
        }

        // Insert the data which has no ID
        $count = 0;
        while ($count < count($data)) {
            // Clean the data
            $this->Special_reprasentations->text_title = $data[$count]->special_reprasentation;
            $this->Special_reprasentations->from_text = $data[$count]->special_reprasentation_from;
            $this->Special_reprasentations->to_int = $data[$count]->special_reprasentation_to;

            if ($data[$count]->special_reprasentation_id === 0) {
                $this->post();
                array_splice($data, $count, 1);
                continue;
            }

            // Store the IDs
            array_push($data_IDs, $data[$count]->special_reprasentation_id);

            ++$count;
        }

        // Delete the data which is abandoned
        $count = 0;
        while ($count < count($DB_data)) {
            if (!in_array($DB_data[$count]['special_reprasentation_id'], $data_IDs)) {
                $this->Special_reprasentations->id = (int)$DB_data[$count]['special_reprasentation_id'];
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
                if ($element['special_reprasentation_id'] == $data[$count]->special_reprasentation_id) {
                    $this->Special_reprasentations->id = $element['special_reprasentation_id'];
                    $this->Special_reprasentations->text_title = $data[$count]->special_reprasentation;
                    $this->Special_reprasentations->from_text = $data[$count]->special_reprasentation_from;
                    $this->Special_reprasentations->to_int = $data[$count]->special_reprasentation_to;

                    $this->update($element['special_reprasentation'], $data[$count]->special_reprasentation, 'special_reprasentation');
                    $this->update($element['special_reprasentation_from'], $data[$count]->special_reprasentation_from, 'special_reprasentation_from');
                    $this->update($element['special_reprasentation_to'], $data[$count]->special_reprasentation_to, 'special_reprasentation_to');

                    break;
                }
            }

            ++$count;
        }

        $this->get();
    }
}

// GET all the user's Special_reprasentations
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $Special_respresentations_api = new Special_respresentations_api();
    $Special_respresentations_api->get();
}

// To check if an user is logged in
if (!isset($_SESSION['user_id'])) {
    send(400, 'error', 'no user logged in');
    die();
}

// If a user logged in ...

// POST/UPDATE (PUT) a user's Special_reprasentations
if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'PUT') {
    $Special_respresentations_api = new Special_respresentations_api();
    $Special_respresentations_api->put();
}
