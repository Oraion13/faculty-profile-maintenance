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
class Research_degree_api extends Type_6 implements api
{
    private $Research_degree;

    // Initialize connection with DB
    public function __construct()
    {
        // Connect with DB
        $dbconnection = new DbConnection();
        $db = $dbconnection->connect();

        // Create an object for users table to do operations
        $this->Research_degree = new Type_6($db);

        // Set table name
        $this->Research_degree->table = 'faculty_research_degree';

        // Set column names
        $this->Research_degree->id_name = 'research_degree_id';
        $this->Research_degree->text_name = 'research_degree';
        $this->Research_degree->from_name = 'research_degree_from';
        $this->Research_degree->to_name = 'research_degree_to';
        $this->Research_degree->text_int_name = 'title';
    }

    // Get all data
    public function get()
    {
        // Get the user info from DB
        $all_data = $this->Research_degree->read();

        if ($all_data) {
            $data = array();
            while ($row = $all_data->fetch(PDO::FETCH_ASSOC)) {
                array_push($data, $row);
            }
            echo json_encode($data);
            die();
        } else {
            send(400, 'error', 'no info about research degree found');
            die();
        }
    }

    // Get all the data of a user's research_degree
    public function get_by_id($id)
    {
        // Get the user info from DB
        $this->Research_degree->user_id = $id;
        $all_data = $this->Research_degree->read_row();

        if ($all_data) {
            $data = array();
            while ($row = $all_data->fetch(PDO::FETCH_ASSOC)) {
                array_push($data, $row);
            }
            echo json_encode($data);
            die();
        } else {
            send(400, 'error', 'no info about research degree found');
            die();
        }
    }
            
    // Get all data by dates
    public function get_by_date($start, $end)
    {
        // Get data from DB
        $from = date('Y-m-01', strtotime($start));
        $this->Research_degree->start = $from;
        $to = date('Y-m-01', strtotime($end));
        $this->Research_degree->end = $to;
        $all_data = $this->Research_degree->read_row_date();

        if ($all_data) {
            $data = array();
            while ($row = $all_data->fetch(PDO::FETCH_ASSOC)) {
                array_push($data, $row);
            }
            echo json_encode($data);
            die();
        } else {
            send(400, 'error', 'no info about research degree found');
            die();
        }
    }

    // POST a new user's research_degree
    public function post()
    {
        if (!$this->Research_degree->post()) {
            // If can't post the data, throw an error message
            send(400, 'error', 'research degree cannot be added');
            die();
        }
    }

    // PUT a user's research_degree
    public function update_by_id($DB_data, $to_update, $update_str)
    {
        if (strcmp($DB_data, $to_update) !== 0) {
            if (!$this->Research_degree->update_row($update_str)) {
                // If can't update_by_id the data, throw an error message
                send(400, 'error', $update_str . ' for ' . $_SESSION['username'] . ' cannot be updated');
                die();
            }
        }
    }

    // DELETE a user's research_degree
    public function delete_by_id()
    {
        if (!$this->Research_degree->delete_row()) {
            // If can't delete the data, throw an error message
            send(400, 'error', 'data cannot be deleted');
            die();
        }
    }

    // POST/UPDATE (PUT)/DELETE a user's Research_degree
    public function put()
    {
        // // Authorization
        // if ($_SESSION['user_id'] != $_GET['ID']) {
        //     send(401, 'error', 'unauthorized');
        //     die();
        // }

        // Get input data as json
        $data = json_decode(file_get_contents("php://input"));

        // Get all the user's research_degree info from DB
        $this->Research_degree->user_id = $_SESSION['user_id'];
        $all_data = $this->Research_degree->read_row();

        // Store all research_degree_id's in an array
        $DB_data = array();
        $data_IDs = array();
        while ($row = $all_data->fetch(PDO::FETCH_ASSOC)) {
            array_push($DB_data, $row);
        }

        // Insert the data which has no ID
        $count = 0;
        while ($count < count($data)) {
            // Clean the data
            $this->Research_degree->text = $data[$count]->research_degree;

            $from = date('Y-m-01', strtotime($data[$count]->research_degree_from));
            $this->Research_degree->from = $from;
            $to = date('Y-m-01', strtotime($data[$count]->research_degree_to));
            $this->Research_degree->to = $to;

            $this->Research_degree->text_int = $data[$count]->title;

            if ($data[$count]->research_degree_id === 0) {
                $this->post();
                array_splice($data, $count, 1);
                continue;
            }

            // Store the IDs
            array_push($data_IDs, $data[$count]->research_degree_id);

            ++$count;
        }

        // Delete the data which is abandoned
        $count = 0;
        while ($count < count($DB_data)) {
            if (!in_array($DB_data[$count]['research_degree_id'], $data_IDs)) {
                $this->Research_degree->id = (int)$DB_data[$count]['research_degree_id'];
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
                if ($element['research_degree_id'] == $data[$count]->research_degree_id) {
                    $this->Research_degree->id = $element['research_degree_id'];
                    $this->Research_degree->text = $data[$count]->research_degree;

                    $from = date('Y-m-01', strtotime($data[$count]->research_degree_from));
                    $this->Research_degree->from = $from;
                    $to = date('Y-m-01', strtotime($data[$count]->research_degree_to));
                    $this->Research_degree->to = $to;

                    $this->Research_degree->text_int = $data[$count]->title;

                    $this->update_by_id($element['research_degree'], $data[$count]->research_degree, 'research_degree');
                    $this->update_by_id($element['research_degree_from'], $from, 'research_degree_from');
                    $this->update_by_id($element['research_degree_to'], $to, 'research_degree_to');
                    $this->update_by_id($element['title'], $data[$count]->title, 'title');

                    break;
                }
            }

            ++$count;
        }

        $this->get_by_id($_SESSION['user_id']);
    }
}

// GET all the user's Research_degree
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $Research_degree_api = new Research_degree_api();
    if (isset($_GET['ID'])) {
        $Research_degree_api->get_by_id($_GET['ID']);
    } else if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1 && isset($_GET['from']) && isset($_GET['to'])) {
        $Research_degree_api->get_by_date($_GET['from'], $_GET['to']);
    } else {
        $Research_degree_api->get();
    }
}

// To check if an user is logged in and verified
loggedin_verified();

// If a user logged in ...

// POST/UPDATE (PUT) a user's Research_degree
if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'PUT') {
    $Research_degree_api = new Research_degree_api();
    $Research_degree_api->put();
}
