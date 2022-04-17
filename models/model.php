<?php

interface model
{
    // Read all data
    public function read();
    // Read by using a foreign key
    public function read_row();
    // Insert a new row
    public function post();
    // update a row by ID
    public function update_row($to_update);
    // Delete a row by ID
    public function delete_row();
}
