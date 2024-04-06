<?php

function sendJson($data, $status = 200)
{
    abort(response()->json($data, $status));
}

function sendMessageJson($message, $status = 200)
{
    abort(response()->json([
        'message' => $message
    ], $status));
}

