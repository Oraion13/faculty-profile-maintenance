<?php

session_start();

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');

require_once '../../../../config/DbConnection.php';
require_once '../../../../models/Type_2.php';
require_once '../../../../utils/send.php';

// TYPE 2 file
class Positions_api
{
    private $Positions;

    // Initialize connection with DB
    public function __construct()
    {
        // Connect with DB
        $dbconnection = new DbConnection();
        $db = $dbconnection->connect();

        // Create an object for faculty_positions table to do operations
        $this->Positions = new Type_2($db);

        // Set table name
        $this->Positions->table = 'faculty_positions';

        // Set column names
        $this->Positions->id_name = 'position_id';
        $this->Positions->text_name = 'position';
    }

    // Get all data
    public function get()
    {
        // Get the positions from DB
        $all_data = $this->Positions->read();

        if ($all_data) {
            $data = array();
            while ($row = $all_data->fetch(PDO::FETCH_ASSOC)) {
                array_push($data, $row);
            }
            echo json_encode($data);
            die();
        } else {
            send(400, 'error', 'no positions found');
            die();
        }
    }
}

// GET all the user info
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $Positions_api = new Positions_api();
    $Positions_api->get();
}
