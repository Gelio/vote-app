<?php

$defaultCodes = [
    400 => "Bad Request",
    401 => "Unauthorised",
    404 => "Not Found",
    500 => "Internal Server Error"
];

class HeadersHandler
{
    protected $headersString;
    protected $dataSent = false;
    protected $bearer = "";

    public function __construct() {
        $this->headersString = file_get_contents("php://input");
        $this->headers = json_decode($this->headersString, true);

        // Check for Authenticated Bearer
        $allHeaders = getallheaders();
        if(isset($allHeaders['Authorization'])) {
            $authorization = $allHeaders['Authorization'];
            $exploded = explode(' ', $authorization);
            if(isset($exploded[1])) {
                // Bearer exists
                $this->bearer = $exploded[1];
            }
        }
    }

    public function getHeader($name) {
        if(isset($this->headers[$name]))
            return $this->headers[$name];

        return false;
    }

    public function getHeaders($asString = false) {
        if($asString)
            return $this->headersString;
        else
            return $this->headers;
    }

    public function sendHeaderCode($code, $message = "") {
        global $defaultCodes;

        if(empty($message) && isset($defaultCodes[$code]))
            $message = $defaultCodes[$code];

        $this->sendHeader("HTTP/1.1 $code $message");
    }

    public function sendHeader($header) {
        if(!$this->dataSent)
            header($header);
        else
            echo "data already sent, can't send header: $header";
    }

    public function sendJSONData($dataArray) {
        if(!$this->dataSent)
            $this->sendHeader("Content-type: application/json");

        $this->dataSent = true;
        echo json_encode($dataArray);
    }

    public function isAuthenticated() {
        return !empty($this->bearer);
    }

    public function getBearer() {
        return $this->bearer;
    }
}
?>
