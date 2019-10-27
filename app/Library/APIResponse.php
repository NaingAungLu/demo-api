<?php

namespace App\Library;

class APIResponse {

    private $errorCode;
    private $message;
    private $data;

    public function __construct($errorCode, $message, $data = false) {
        $this->errorCode = $errorCode;
        $this->message = $message;
        $this->data = $data;
    }

    public function getJson() {
        
        $response = array(
            'errorCode' => $this->errorCode,
            'message' => $this->message
        );

        if($this->data) {
            $response = array_merge($response, ['data' => $this->data]);
        }

        return response()->json($response)->setStatusCode(200, $this->message);
    }
}