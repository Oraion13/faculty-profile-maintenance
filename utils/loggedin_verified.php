<?php
// To check if an user is logged in and verified
function loggedin_verified()
{
    // To check if an user is logged in
    if (!isset($_SESSION['user_id'])) {
        send(400, 'error', 'no user logged in');
        die();
    }

    // Check if the user is verified
    if ($_SESSION['is_verified'] === 0) {
        send(400, 'error', 'account not verified');
        die();
    }
}
