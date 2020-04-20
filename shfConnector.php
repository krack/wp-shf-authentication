<?php

class SHFConnector{

    private static $response = null;
    public function isValidLoginPassword($login, $password){
        $connectionStatus = new ConnectionStatus();
        $isValidAccount = false;

        try{
            $isValidAccount = $this->check($login, $password);

            $connectionStatus->connected = $isValidAccount;
            if(!$connectionStatus->connected){
                array_push($connectionStatus->errors, $this->getMessageCheckAccount());
            }            

        } catch (Exception $e) {
            array_push($connectionStatus->errors, __("login failed", "shf-authentication"));
        }

        return $connectionStatus;
    }

    private function getMessageCheckAccount(){
        $link = get_option( 'shf_redirect' );

        $currentYear = date("Y");
        return sprintf(__("error.membership", "shf-authentication"), $link, $currentYear-1, $currentYear); 
    }
    private function check($login, $password){
       
        if(SHFConnector::$response == null){
            $client = new \GuzzleHttp\Client();
            $host= get_option( 'shf_host' );
            $key= get_option( 'shf_key' );
            $secret= get_option( 'shf_secret' );
            $response = $client->request('POST', 'https://'.$host.'/fr/webservice/login.html', 
            [
                'form_params' => [
                    'key-api' => $key,
                    'key-secret' => $secret,
                    'email' => $login,
                    'password' => $password
                ]
            ]);
            SHFConnector::$response = $response;
        }
        
        if(SHFConnector::$response->getStatusCode() != 200){
            throw new Exception('http_error');
        }

        $test = json_decode(SHFConnector::$response->getBody());
        if($test->code_retour == 4 && $test->retour == "Utilisateur non adhérent" ){
            return false;
        } else if($test->code_retour != 0){
            throw new Exception('user_error');

        }
        return $this->checkMembership($test->utilisateur->adhesions);

    }

    private function checkMembership($memberShip){

        $currentYear = date("Y");
        foreach ( $memberShip as $yearMemberShip ) {
            if($yearMemberShip->millesime == $currentYear || $yearMemberShip->millesime == $currentYear-1){
                return true;
            }
        }
        return false;
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