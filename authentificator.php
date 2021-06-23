<?php

use ReallySimpleJWT\Token;
require_once 'shfConnector.php';

class Authentificator{
    private $secretOptionName = "shf-authentication_secret";
    private $coockieName = "shf-authentication";

    private static $secret;
    private $shfConnector;

    public function __construct() {
        $this->initJwtTokenSecret();
        $this->secret = get_option( $this->secretOptionName );
        $this->shfConnector = new SHFConnector();
    }

    private function initJwtTokenSecret(){
        if(get_option( $this->secretOptionName ) == null){
            $this->resetJwtTokenSecret();
        }
    }

    public function resetJwtTokenSecret(){
        update_option( $this->secretOptionName, wp_generate_password(50, true, false) );
        $this->secret = get_option( $this->secretOptionName );
    }
    
    public function isConnected(){
        $connected = false;
        if(isset($_COOKIE[$this->coockieName])){
            $connected =  Token::validate($_COOKIE[$this->coockieName], $this->secret);
        }
    
        return $connected;
    }

    public function tryConnection($login, $password){
        $connectionStatus = $this->shfConnector->isValidLoginPassword($login, $password);
        if($connectionStatus->connected){
            $this->createConnectionRemember();
        }

        return $connectionStatus;
    }


    private function createConnectionRemember(){
        $userId = 12;
        $expiration = time() + (7 *24 * 60 * 60); //7j
        $issuer = 'authentication_shf';
        
        $token = Token::create($userId, $this->secret, $expiration, $issuer);
        setcookie($this->coockieName,$token, $expiration, "/");
    } 
}
?>