<?php

interface api
{
    // GET all data
    public function get();
    // GET the data for ID which is provided in the URL
    public function get_by_id($id);

    // Only authenticated users

    // POST a new data
    public function post();
    // UPDATE a existing data
    public function update_by_id($DB_data, $to_update, $update_str);
    // DELETE the data which is missing in input
    public function delete_by_id();
    // Do all the operations - INSERT, DELETE, PUT
    public function put();
}
