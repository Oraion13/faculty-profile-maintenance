<?php

session_start();

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');

require_once '../../../../config/DbConnection.php';
require_once '../../../../models/Type_6.php';
require_once '../../../../utils/send.php';
require_once '../../../api.php';

// TYPE 6 file
class Sponsored_projects_completed_api extends Type_6 implements api
{
    private $Sponsored_projects_completed;

    // Initialize connection with DB
    public function __construct()
    {
        // Connect with DB
        $dbconnection = new DbConnection();
        $db = $dbconnection->connect();

        // Create an object for users table to do operations
        $this->Sponsored_projects_completed = new Type_6($db);

        // Set table name
        $this->Sponsored_projects_completed->table = 'faculty_sponsored_projects_completed';

        // Set column names
        $this->Sponsored_projects_completed->id_name = 'project_id';
        $this->Sponsored_projects_completed->text_name = 'project';
        $this->Sponsored_projects_completed->from_name = 'project_from';
        $this->Sponsored_projects_completed->to_name = 'project_to';
        $this->Sponsored_projects_completed->text_int_name = 'project_cost';
    }

    // Get all data
    public function get()
    {
        // Get the user info from DB
        $all_data = $this->Sponsored_projects_completed->read();

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

    // Get all the data of a user's project
    public function get_by_id()
    {
        // Get the user info from DB
        $this->Sponsored_projects_completed->user_id = $_GET['ID'];
        $all_data = $this->Sponsored_projects_completed->read_row();

        if ($all_data) {
            $data = array();
            while ($row = $all_data->fetch(PDO::FETCH_ASSOC)) {
                array_push($data, $row);
            }
            echo json_encode($data);
            die();
        } else {
            send(400, 'error', 'no user info about Sponsored_projects_completed found');
            die();
        }
    }
    // POST a new user's project
    public function post()
    {
        if (!$this->Sponsored_projects_completed->post()) {
            // If can't post the data, throw an error message
            send(400, 'error', 'project cannot be added');
            die();
        }
    }

    // PUT a user's project
    public function update_by_id($DB_data, $to_update, $update_str)
    {
        if (strcmp($DB_data, $to_update) !== 0) {
            if (!$this->Sponsored_projects_completed->update_row($update_str)) {
                // If can't update_by_id the data, throw an error message
                send(400, 'error', $update_str . ' for ' . $_SESSION['username'] . ' cannot be updated');
                die();
            }
        }
    }

    // DELETE a user's project
    public function delete_by_id()
    {
        if (!$this->Sponsored_projects_completed->delete_row()) {
            // If can't delete the data, throw an error message
            send(400, 'error', 'data cannot be deleted');
            die();
        }
    }

    // POST/UPDATE (PUT)/DELETE a user's Sponsored_projects_completed
    public function put()
    {
        // Authorization
        if ($_SESSION['user_id'] != $_GET['ID']) {
            send(401, 'error', 'unauthorized');
            die();
        }

        // Get input data as json
        $data = json_decode(file_get_contents("php://input"));

        // Get all the user's project info from DB
        $this->Sponsored_projects_completed->user_id = $_SESSION['user_id'];
        $all_data = $this->Sponsored_projects_completed->read_row();

        // Store all project_id's in an array
        $DB_data = array();
        $data_IDs = array();
        while ($row = $all_data->fetch(PDO::FETCH_ASSOC)) {
            array_push($DB_data, $row);
        }

        // Insert the data which has no ID
        $count = 0;
        while ($count < count($data)) {
            // Clean the data
            $this->Sponsored_projects_completed->text = $data[$count]->project;
            $this->Sponsored_projects_completed->from = $data[$count]->project_from;
            $this->Sponsored_projects_completed->to = $data[$count]->project_to;
            $this->Sponsored_projects_completed->text_int = $data[$count]->project_cost;

            if ($data[$count]->project_id === 0) {
                $this->post();
                array_splice($data, $count, 1);
                continue;
            }

            // Store the IDs
            array_push($data_IDs, $data[$count]->project_id);

            ++$count;
        }

        // Delete the data which is abandoned
        $count = 0;
        while ($count < count($DB_data)) {
            if (!in_array($DB_data[$count]['project_id'], $data_IDs)) {
                $this->Sponsored_projects_completed->id = (int)$DB_data[$count]['project_id'];
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
                if ($element['project_id'] == $data[$count]->project_id) {
                    $this->Sponsored_projects_completed->id = $element['project_id'];
                    $this->Sponsored_projects_completed->text = $data[$count]->project;
                    $this->Sponsored_projects_completed->from = $data[$count]->project_from;
                    $this->Sponsored_projects_completed->to = $data[$count]->project_to;
                    $this->Sponsored_projects_completed->text_int = $data[$count]->project_cost;

                    $this->update_by_id($element['project'], $data[$count]->project, 'project');
                    $this->update_by_id($element['project_from'], $data[$count]->project_from, 'project_from');
                    $this->update_by_id($element['project_to'], $data[$count]->project_to, 'project_to');
                    $this->update_by_id($element['project_cost'], $data[$count]->project_cost, 'project_cost');

                    break;
                }
            }

            ++$count;
        }

        $this->get_by_id();
    }
}

// GET all the user's Sponsored_projects_completed
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $Sponsored_projects_completed_api = new Sponsored_projects_completed_api();
    if (isset($_GET['ID'])) {
        $Sponsored_projects_completed_api->get_by_id();
    } else {
        $Sponsored_projects_completed_api->get();
    }
}

// To check if an user is logged in
if (!isset($_SESSION['user_id'])) {
    send(400, 'error', 'no user logged in');
    die();
}

// If a user logged in ...

// POST/UPDATE (PUT) a user's Sponsored_projects_completed
if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'PUT') {
    $Sponsored_projects_completed_api = new Sponsored_projects_completed_api();
    $Sponsored_projects_completed_api->put();
}