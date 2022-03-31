<?php

function send($status, $message)
{
    header($_SERVER["SERVER_PROTOCOL"] . ' ' . $status, true, $status);
    echo json_encode(
        array('message' => $message)
    );
}
