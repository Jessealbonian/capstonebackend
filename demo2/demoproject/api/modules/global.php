<?php
class GlobalMethods{
    public function sendPayload($data, $remarks, $message, $code){
        $status = array("remarks"=>$remarks, "message"=>$message);
        http_response_code($code);
        return array(
            "status"=>$status,
            "payload"=>$data,
            "prepared_by"=>"Jomar",
            "timestamp"=>date_create()
        );
    }

    public function getResponse($data, $remarks, $error, $statusCode) {
        $response = [
            'remarks' => $remarks
        ];

        if ($data !== null) {
            $response['data'] = $data; 
        }

        if ($error !== null) {
            $response['error'] = $error;
        }
        http_response_code($statusCode);
        return $response;
    }
}
?>