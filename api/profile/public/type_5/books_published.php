<?php

session_start();

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');

require_once '../../../../config/DbConnection.php';
require_once '../../../../models/Type_5.php';
require_once '../../../../utils/send.php';
require_once '../../../api.php';
require_once '../../../../utils/loggedin_verified.php';

// TYPE 5 file
class Books_published_api extends Type_5 implements api
{
    private $Books_published;

    // Initialize connection with DB
    public function __construct()
    {
        // Connect with DB
        $dbconnection = new DbConnection();
        $db = $dbconnection->connect();

        // Create an object for users table to do operations
        $this->Books_published = new Type_5($db);

        // Set table name
        $this->Books_published->table = 'faculty_books_published';

        // Set column names
        $this->Books_published->id_name = 'book_published_id';
        $this->Books_published->text_name = 'title';
        $this->Books_published->from_name = 'description';
        $this->Books_published->to_name = 'published_at';
    }

    // Get all data
    public function get()
    {
        // Get the user info from DB
        $all_data = $this->Books_published->read();

        if ($all_data) {
            $data = array();
            while ($row = $all_data->fetch(PDO::FETCH_ASSOC)) {
                array_push($data, $row);
            }
            echo json_encode($data);
            die();
        } else {
            send(400, 'error', 'no info about books published found');
            die();
        }
    }

    // Get all the data of a user's title
    public function get_by_id($id)
    {
        // Get the user info from DB
        $this->Books_published->user_id = $id;
        $all_data = $this->Books_published->read_row();

        if ($all_data) {
            $data = array();
            while ($row = $all_data->fetch(PDO::FETCH_ASSOC)) {
                array_push($data, $row);
            }
            echo json_encode($data);
            die();
        } else {
            send(400, 'error', 'no info about books published found');
            die();
        }
    }
    // POST a new user's title
    public function post()
    {
        if (!$this->Books_published->post()) {
            // If can't post the data, throw an error message
            send(400, 'error', 'title cannot be added');
            die();
        }
    }

    // PUT a user's title
    public function update_by_id($DB_data, $to_update, $update_str)
    {
        if (strcmp($DB_data, $to_update) !== 0) {
            if (!$this->Books_published->update_row($update_str)) {
                // If can't update_by_id the data, throw an error message
                send(400, 'error', $update_str . ' for ' . $_SESSION['username'] . ' cannot be updated');
                die();
            }
        }
    }

    // DELETE a user's title
    public function delete_by_id()
    {
        if (!$this->Books_published->delete_row()) {
            // If can't delete the data, throw an error message
            send(400, 'error', 'data cannot be deleted');
            die();
        }
    }

    // POST/UPDATE (PUT)/DELETE a user's Books_published
    public function put()
    {
        // // Authorization
        // if ($_SESSION['user_id'] != $_GET['ID']) {
        //     send(401, 'error', 'unauthorized');
        //     die();
        // }

        // Get input data as json
        $data = json_decode(file_get_contents("php://input"));

        // Get all the user's title info from DB
        $this->Books_published->user_id = $_SESSION['user_id'];
        $all_data = $this->Books_published->read_row();

        // Store all book_published_id's in an array
        $DB_data = array();
        $data_IDs = array();
        while ($row = $all_data->fetch(PDO::FETCH_ASSOC)) {
            array_push($DB_data, $row);
        }

        // Insert the data which has no ID
        $count = 0;
        while ($count < count($data)) {
            // Clean the data
            $this->Books_published->text_title = $data[$count]->title;
            $this->Books_published->from_text = $data[$count]->description;
            $at = date('Y-m-01', strtotime($data[$count]->published_at));
            $this->Books_published->to_int = $at;

            if ($data[$count]->book_published_id === 0) {
                $this->post();
                array_splice($data, $count, 1);
                continue;
            }

            // Store the IDs
            array_push($data_IDs, $data[$count]->book_published_id);

            ++$count;
        }

        // Delete the data which is abandoned
        $count = 0;
        while ($count < count($DB_data)) {
            if (!in_array($DB_data[$count]['book_published_id'], $data_IDs)) {
                $this->Books_published->id = (int)$DB_data[$count]['book_published_id'];
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
                if ($element['book_published_id'] == $data[$count]->book_published_id) {
                    $this->Books_published->id = $element['book_published_id'];
                    $this->Books_published->text_title = $data[$count]->title;
                    $this->Books_published->from_text = $data[$count]->description;
                    $at = date('Y-m-01', strtotime($data[$count]->published_at));
                    $this->Books_published->to_int = $at;

                    $this->update_by_id($element['title'], $data[$count]->title, 'title');
                    $this->update_by_id($element['description'], $data[$count]->description, 'description');
                    $this->update_by_id($element['published_at'], $at, 'published_at');

                    break;
                }
            }

            ++$count;
        }

        $this->get_by_id($_SESSION['user_id']);
    }
}

// GET all the user's Books_published
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $Books_published_api = new Books_published_api();
    if (isset($_GET['ID'])) {
        $Books_published_api->get_by_id($_GET['ID']);
    } else {
        $Books_published_api->get();
    }
}

// To check if an user is logged in and verified
loggedin_verified();

// If a user logged in ...

// POST/UPDATE (PUT) a user's Books_published
if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'PUT') {
    $Books_published_api = new Books_published_api();
    $Books_published_api->put();
}
