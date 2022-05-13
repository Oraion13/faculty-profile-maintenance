<?php

session_start();

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');

require_once '../../../../config/DbConnection.php';
require_once '../../../../models/Type_4.php';
require_once '../../../../utils/send.php';
require_once '../../../api.php';
require_once '../../../../utils/loggedin_verified.php';

// TYPE 4 file
class Onduty_orders_api extends Type_4 implements api
{
    private $Onduty_orders;

    // Initialize connection with DB
    public function __construct()
    {
        // Connect with DB
        $dbconnection = new DbConnection();
        $db = $dbconnection->connect();

        // Create an object for users table to do operations
        $this->Onduty_orders = new Type_4($db);

        // Set table name
        $this->Onduty_orders->table = 'faculty_onduty_orders';

        // Set column names
        $this->Onduty_orders->id_name = 'onduty_order_id';
        $this->Onduty_orders->text_name = 'onduty_order';
        $this->Onduty_orders->from_name = 'onduty_order_at';
    }

    // Get all data
    public function get()
    {
        // Get the user info from DB
        $all_data = $this->Onduty_orders->read();

        if ($all_data) {
            $data = array();
            while ($row = $all_data->fetch(PDO::FETCH_ASSOC)) {
                array_push($data, $row);
            }
            echo json_encode($data);
            die();
        } else {
            send(400, 'error', 'no info about onduty order found');
            die();
        }
    }

    // Get all the data of a user's onduty order by ID
    public function get_by_id($id)
    {
        // Get the user info from DB
        $this->Onduty_orders->user_id = $id;
        $all_data = $this->Onduty_orders->read_row();

        if ($all_data) {
            $data = array();
            while ($row = $all_data->fetch(PDO::FETCH_ASSOC)) {
                array_push($data, $row);
            }
            echo json_encode($data);
            die();
        } else {
            send(400, 'error', 'no info about onduty order found');
            die();
        }
    }
            
    // Get all data by dates
    public function get_by_date($start, $end)
    {
        // Get data from DB
        $this->Onduty_orders->start = $start;
        $this->Onduty_orders->end = $end;
        $all_data = $this->Onduty_orders->read_row_date();

        if ($all_data) {
            $data = array();
            while ($row = $all_data->fetch(PDO::FETCH_ASSOC)) {
                array_push($data, $row);
            }
            echo json_encode($data);
            die();
        } else {
            send(400, 'error', 'no info about onduty orders found');
            die();
        }
    }

    // POST a new user's onduty order
    public function post()
    {
        if (!$this->Onduty_orders->post()) {
            // If can't post the data, throw an error message
            send(400, 'error', 'onduty order cannot be added');
            die();
        }
    }

    // PUT a user's onduty order
    public function update_by_id($DB_data, $to_update, $update_str)
    {
        if (strcmp($DB_data, $to_update) !== 0) {
            if (!$this->Onduty_orders->update_row($update_str)) {
                // If can't update_by_id the data, throw an error message
                send(400, 'error', $update_str . ' for ' . $_SESSION['username'] . ' cannot be updated');
                die();
            }
        }
    }

    // DELETE a user's onduty order
    public function delete_by_id()
    {
        if (!$this->Onduty_orders->delete_row()) {
            // If can't delete the data, throw an error message
            send(400, 'error', 'data cannot be deleted');
            die();
        }
    }

    // POST/UPDATE (PUT)/DELETE a user's Onduty_orders
    public function put()
    {
        // // Authorization
        // if ($_SESSION['user_id'] != $_GET['ID']) {
        //     send(401, 'error', 'unauthorized');
        //     die();
        // }

        // Get input data as json
        $data = json_decode(file_get_contents("php://input"));

        // Get all the user's onduty order info from DB
        $this->Onduty_orders->user_id = $_SESSION['user_id'];
        $all_data = $this->Onduty_orders->read_row();

        // Store all onduty_order_id 's in an array
        $DB_data = array();
        $data_IDs = array();
        while ($row = $all_data->fetch(PDO::FETCH_ASSOC)) {
            array_push($DB_data, $row);
        }

        // Insert the data which has no ID
        $count = 0;
        while ($count < count($data)) {
            // Clean the data
            $this->Onduty_orders->text = $data[$count]->onduty_order;
            $at = date('Y-m-01', strtotime($data[$count]->onduty_order_at));
            $this->Onduty_orders->from = $at;

            if ($data[$count]->onduty_order_id === 0) {
                $this->post();
                array_splice($data, $count, 1);
                continue;
            }

            // Store the IDs
            array_push($data_IDs, $data[$count]->onduty_order_id);

            ++$count;
        }

        // Delete the data which is abandoned
        $count = 0;
        while ($count < count($DB_data)) {
            if (!in_array($DB_data[$count]['onduty_order_id'], $data_IDs)) {
                $this->Onduty_orders->id = (int)$DB_data[$count]['onduty_order_id'];
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
                if ($element['onduty_order_id'] == $data[$count]->onduty_order_id) {
                    $this->Onduty_orders->id = $element['onduty_order_id'];
                    $this->Onduty_orders->text = $data[$count]->onduty_order;
                    $at = date('Y-m-01', strtotime($data[$count]->onduty_order_at));
                    $this->Onduty_orders->from = $at;

                    $this->update_by_id($element['onduty_order'], $data[$count]->onduty_order, 'onduty_order');
                    $this->update_by_id($element['onduty_order_at'], $at, 'onduty_order_at');

                    break;
                }
            }

            ++$count;
        }

        $this->get_by_id($_SESSION['user_id']);
    }
}

// To check if an user is logged in and verified
loggedin_verified();

// If a user logged in ...

// GET all the user's Onduty_orders
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $Onduty_orders_api = new Onduty_orders_api();
    if (isset($_GET['ID'])) {
        $Onduty_orders_api->get_by_id($_GET['ID']);
    } else if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1 && isset($_GET['from']) && isset($_GET['to'])) {
        $Onduty_orders_api->get_by_date($_GET['from'], $_GET['to']);
    } else {
        $Onduty_orders_api->get();
    }
}

// POST/UPDATE (PUT) a user's Onduty_orders
if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'PUT') {
    $Onduty_orders_api = new Onduty_orders_api();
    $Onduty_orders_api->put();
}
