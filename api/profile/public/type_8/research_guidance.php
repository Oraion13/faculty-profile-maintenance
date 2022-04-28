<?php

session_start();

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');

require_once '../../../../config/DbConnection.php';
require_once '../../../../models/Type_8.php';
require_once '../../../../utils/send.php';
require_once '../../../api.php';
require_once '../../../../utils/loggedin_verified.php';

// TYPE 8 file
class Research_guidance_api extends Type_8 implements api
{
    private $Research_guidance;

    // Initialize connection with DB
    public function __construct()
    {
        // Connect with DB
        $dbconnection = new DbConnection();
        $db = $dbconnection->connect();

        // Create an object for users table to do operations
        $this->Research_guidance = new Type_8($db);

        // Set table name
        $this->Research_guidance->table = 'faculty_research_guidance';

        // Set column names
        $this->Research_guidance->id_name = 'research_guidance_id';
        $this->Research_guidance->col1_name = 'phd_guided';
        $this->Research_guidance->col2_name = 'phd_guiding';
        $this->Research_guidance->col3_name = 'me_guided';
        $this->Research_guidance->col4_name = 'me_guiding';
        $this->Research_guidance->col5_name = 'ms_guided';
        $this->Research_guidance->col6_name = 'ms_guiding';
    }

    // Get all data
    public function get()
    {
        // Get the user info from DB
        $all_data = $this->Research_guidance->read();

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

    // Get all the data of a user's research_guidance
    public function get_by_id($id)
    {
        // Get the user info from DB
        $this->Research_guidance->user_id = $id;
        $all_data = $this->Research_guidance->read_row();

        if ($all_data) {
            $data = array();
            while ($row = $all_data->fetch(PDO::FETCH_ASSOC)) {
                array_push($data, $row);
            }
            echo json_encode($data);
            die();
        } else {
            send(400, 'error', 'no user info about Research_guidance found');
            die();
        }
    }
    // POST a new user's research_guidance
    public function post()
    {
        if (!$this->Research_guidance->post()) {
            // If can't post the data, throw an error message
            send(400, 'error', 'research_guidance cannot be added');
            die();
        }
    }

    // PUT a user's research_guidance
    public function update_by_id($DB_data, $to_update, $update_str)
    {
        if (strcmp($DB_data, $to_update) !== 0) {
            if (!$this->Research_guidance->update_row($update_str)) {
                // If can't update_by_id the data, throw an error message
                send(400, 'error', $update_str . ' for ' . $_SESSION['username'] . ' cannot be updated');
                die();
            }
        }
    }

    // DELETE a user's research_guidance
    public function delete_by_id()
    {
        if (!$this->Research_guidance->delete_row()) {
            // If can't delete the data, throw an error message
            send(400, 'error', 'data cannot be deleted');
            die();
        }
    }

    // POST/UPDATE (PUT)/DELETE a user's Research_guidance
    public function put()
    {
        // // Authorization
        // if ($_SESSION['user_id'] != $_GET['ID']) {
        //     send(401, 'error', 'unauthorized');
        //     die();
        // }

        // Get input data as json
        $data = json_decode(file_get_contents("php://input"));

        // Get all the user's research_guidance info from DB
        $this->Research_guidance->user_id = $_SESSION['user_id'];
        $all_data = $this->Research_guidance->read_row();

        // Store all research_guidance_id's in an array
        $DB_data = array();
        $data_IDs = array();
        while ($row = $all_data->fetch(PDO::FETCH_ASSOC)) {
            array_push($DB_data, $row);
        }

        // Insert the data which has no ID
        $count = 0;
        while ($count < count($data)) {
            // Clean the data
            $this->Research_guidance->col1 = $data[$count]->phd_guided;
            $this->Research_guidance->col2 = $data[$count]->phd_guiding;
            $this->Research_guidance->col3 = $data[$count]->me_guided;
            $this->Research_guidance->col4 = $data[$count]->me_guiding;
            $this->Research_guidance->col5 = $data[$count]->ms_guided;
            $this->Research_guidance->col6 = $data[$count]->ms_guiding;

            if ($data[$count]->research_guidance_id === 0) {
                $this->post();
                array_splice($data, $count, 1);
                continue;
            }

            // Store the IDs
            array_push($data_IDs, $data[$count]->research_guidance_id);

            ++$count;
        }

        // Delete the data which is abandoned
        $count = 0;
        while ($count < count($DB_data)) {
            if (!in_array($DB_data[$count]['research_guidance_id'], $data_IDs)) {
                $this->Research_guidance->id = (int)$DB_data[$count]['research_guidance_id'];
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
                if ($element['research_guidance_id'] == $data[$count]->research_guidance_id) {
                    $this->Research_guidance->id = $element['research_guidance_id'];
                    $this->Research_guidance->col1 = $data[$count]->phd_guided;
                    $this->Research_guidance->col2 = $data[$count]->phd_guiding;
                    $this->Research_guidance->col3 = $data[$count]->me_guided;
                    $this->Research_guidance->col4 = $data[$count]->me_guiding;
                    $this->Research_guidance->col5 = $data[$count]->ms_guided;
                    $this->Research_guidance->col6 = $data[$count]->ms_guiding;

                    $this->update_by_id($element['phd_guided'], $data[$count]->phd_guided, 'phd_guided');
                    $this->update_by_id($element['phd_guiding'], $data[$count]->phd_guiding, 'phd_guiding');
                    $this->update_by_id($element['me_guided'], $data[$count]->me_guided, 'me_guided');
                    $this->update_by_id($element['me_guiding'], $data[$count]->me_guiding, 'me_guiding');
                    $this->update_by_id($element['ms_guided'], $data[$count]->ms_guided, 'ms_guided');
                    $this->update_by_id($element['ms_guiding'], $data[$count]->ms_guiding, 'ms_guiding');

                    break;
                }
            }

            ++$count;
        }

        $this->get_by_id($_SESSION['user_id']);
    }
}

// GET all the user's Research_guidance
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $Research_guidance_api = new Research_guidance_api();
    if (isset($_GET['ID'])) {
        $Research_guidance_api->get_by_id($_GET['ID']);
    } else {
        $Research_guidance_api->get();
    }
}

// To check if an user is logged in and verified
loggedin_verified();

// If a user logged in ...

// POST/UPDATE (PUT) a user's Research_guidance
if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'PUT') {
    $Research_guidance_api = new Research_guidance_api();
    $Research_guidance_api->put();
}
