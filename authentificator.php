<?php

use ReallySimpleJWT\Token;
use ReallySimpleJWT\Parse;
use ReallySimpleJWT\Decode;


require_once 'shfConnector.php';
require_once 'peoples.php';

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

    public function hasRight($rights = null){
        $hasRight = false;
        if($rights != null){
            $rightArray = preg_split('/\s*,\s*/', $rights, -1, PREG_SPLIT_NO_EMPTY);
            for($i = 0; $i < count($rightArray); $i++){
                $token = Token::parser($_COOKIE[$this->coockieName], $this->secret);
                $parsed = $token->parse();
                if (in_array($rightArray[$i], $parsed->getPayload()['roles'])) {
                    $hasRight = true;
                }
            }
        }else{
            $hasRight = true;
        }

        return $hasRight;
    }

    public function getCurrentId(){
        $token = Token::parser($_COOKIE[$this->coockieName], $this->secret);
        $parsed = $token->parse();
        return $parsed->getPayload()['sub'];

    }

    public function tryConnection($login, $password){
        $connectionStatus = $this->shfConnector->isValidLoginPassword($login, $password);
        if($connectionStatus->connected){
   
            $rights= [];
            array_push($rights, "shf");
            $people = new Peoples();
            $user = $people->get($connectionStatus->id);
            $noRight = true;
            if (in_array("judge", $user->roles)) {
                array_push($rights, "judge");
                $noRight = false;
            }
            if (in_array("yb", $user->roles)) {
                array_push($rights, "yb");
                $noRight = false;
            }

            if($noRight){
                $connectionStatus->connected = false;
                array_push($connectionStatus->errors, "Vous n'êtes pas autorisé à accéder à ce contenu qui est réservé aux juges ou membres du programme Young Breeders Selle Français.");
            }else{
                $this->createConnectionRemember($connectionStatus, $rights);
            }
        }else{
           
            $rights= [];
            $people = new Peoples();
            $user = $people->connection($login, $password);
            if($user != null){
                $connectionStatus = new ConnectionStatus();
                $connectionStatus->connected = true;
                $connectionStatus->id = $user->id;
                if (in_array("yb", $user->roles)) {
                    array_push($rights, "yb");
                }
                $this->createConnectionRemember($connectionStatus, $rights);
            }
        }

        return $connectionStatus;
    }


    private function createConnectionRemember($connectionStatus, $rights){
        $userId = $connectionStatus->id;
        $expiration = time() + (7 *24 * 60 * 60); //7j
        $issuer = 'authentication_shf';
        
        $payload = [
            'iat' => time(),
            'sub' =>  $userId,
            'exp' => $expiration,
            'iss' => $issuer,
            'roles' =>$rights
        ];

        $token = Token::customPayload($payload, $this->secret);  
        setcookie($this->coockieName,$token, $expiration, "/");
        $_COOKIE[$this->coockieName] = $token;
    } 

    public function disconnect(){
        setcookie($this->coockieName, null, -1, '/');
        wp_redirect( "/" );
        exit;
    }
}
?>