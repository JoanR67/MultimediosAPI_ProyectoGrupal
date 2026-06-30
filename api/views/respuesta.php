<?php


function convertirJSON($objeto)
{

    header(
        "Content-Type: application/json"
    );

    echo json_encode(
        $objeto,
        JSON_PRETTY_PRINT
    );
}
