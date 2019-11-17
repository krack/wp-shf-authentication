<?php

class SHFConnector{


    public function isValidLoginPassword($login, $password){
        $connectionStatus = new ConnectionStatus();
        if(strtolower($login) == "anne" || strtolower($login) == "lucile" || strtolower($login) == "lucille"){
            $connectionStatus->connected = true;
        }else{
            $connectionStatus->connected = false;
            array_push($connectionStatus->errors, __("login failed", "shf-authentication"));
        }

        return $connectionStatus;
    }   
}

class ConnectionStatus {
    public $connected;
    public $errors;

    function __construct(){
        $this->connected = false;
        $this->errors = [];
    }
}
?>