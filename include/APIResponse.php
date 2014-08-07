<?php
/**
 * Project : wp-gcm
 * User: thuytien
 * Date: 8/6/2014
 * Time: 10:23 PM
 */

class APIResponse {
    private $response = array();

    public function put($key, $value)
    {
        $this->response[$key] = $value;
        return $this->response;
    }
    public function setMessage($message)
    {
        $this->response["message"] = $message;
    }
    public function toJson()
    {
        return json_encode($this->response);
    }
} 