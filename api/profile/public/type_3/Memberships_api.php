<?php

session_start();

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');

require_once '../../../../config/DbConnection.php';
require_once '../../../../models/Type_3.php';
require_once '../../../../utils/send.php';

// TYPE 3 file
class Memberships_api
{
    private $Memberships;

    // Initialize connection with DB
    public function __construct()
    {
        // Connect with DB
        $dbconnection = new DbConnection();
        $db = $dbconnection->connect();

        // Create an object for users table to do operations
        $this->Memberships = new Type_3($db);

        // Set table name
        $this->Memberships->table = 'faculty_memberships';

        // Set column names
        $this->Memberships->id_name = 'membership_id';
        $this->Memberships->text_name = 'membership';
    }

    // Get all the data of a user's membership
    public function get()
    {
        // Get the user info from DB
        $this->Memberships->user_id = $_GET['ID'];
        $all_data = $this->Memberships->read_by_id();

        if ($all_data) {
            $data = array();
            while ($row = $all_data->fetch(PDO::FETCH_ASSOC)) {
                array_push($data, $row);
            }
            echo json_encode($data);
            die();
        } else {
            send(400, 'error', 'no user info about memberships found');
            die();
        }
    }
    // POST a new user's membership
    public function post()
    {
        if (!$this->Memberships->create()) {
            // If can't post the data, throw an error message
            send(400, 'error', 'membership cannot be added');
            die();
        }
    }

    // PUT a user's membership
    public function update($DB_data, $to_update, $update_str)
    {
        if (strcmp($DB_data, $to_update) !== 0) {
            if (!$this->Memberships->update($update_str)) {
                // If can't update the data, throw an error message
                send(400, 'error', $update_str . ' for ' . $_SESSION['username'] . ' cannot be updated');
                die();
            }
        }
    }

    // DELETE a user's membership
    public function delete_data()
    {
        if (!$this->Memberships->delete_row()) {
            // If can't delete the data, throw an error message
            send(400, 'error', 'data cannot be deleted');
            die();
        }
    }

    // POST/UPDATE (PUT)/DELETE a user's memberships
    public function put()
    {
        // Authorization
        if ($_SESSION['user_id'] != $_GET['ID']) {
            send(401, 'error', 'unauthorized');
            die();
        }

        // Get input data as json
        $data = json_decode(file_get_contents("php://input"));

        // Get all the user's membership info from DB
        $this->Memberships->user_id = $_SESSION['user_id'];
        $all_data = $this->Memberships->read_by_id();

        // Store all membership_id's in an array
        $DB_data = array();
        $data_IDs = array();
        while ($row = $all_data->fetch(PDO::FETCH_ASSOC)) {
            array_push($DB_data, $row);
        }

        // Insert the data which has no ID
        $count = 0;
        while ($count < count($data)) {
            // Clean the data
            $this->Memberships->text = $data[$count]->membership;

            if ($data[$count]->membership_id === 0) {
                $this->post();
                array_splice($data, $count, 1);
                continue;
            }

            // Store the IDs
            array_push($data_IDs, $data[$count]->membership_id);

            ++$count;
        }

        // Delete the data which is abandoned
        $count = 0;
        while ($count < count($DB_data)) {
            if (!in_array($DB_data[$count]['membership_id'], $data_IDs)) {
                $this->Memberships->id = (int)$DB_data[$count]['membership_id'];
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
                if ($element['membership_id'] == $data[$count]->membership_id) {
                    $this->Memberships->id = $element['membership_id'];
                    $this->Memberships->text = $data[$count]->membership;

                    $this->update($element['membership'], $data[$count]->membership, 'membership');
                }
            }

            ++$count;
        }

        $this->get();
    }
}

// GET all the user's memberships
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $Memberships_api = new Memberships_api();
    $Memberships_api->get();
}

// To check if an user is logged in
if (!isset($_SESSION['user_id'])) {
    send(400, 'error', 'no user logged in');
    die();
}

// If a user logged in ...

// POST/UPDATE (PUT) a user's memberships
if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'PUT') {
    $Memberships_api = new Memberships_api();
    $Memberships_api->put();
}
