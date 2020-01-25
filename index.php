<?php
/*
 * Plugin Name: shf-authentication
 * Text Domain: shf-authentication
 * Domain Path: /languages
 * Plugin URI: 
 * Description: Possibility for checking is user is subscribe at SHF services.
 * Author: Sylvain Gandon
 * Version: 0.1
 * Author URI: 
*/
require 'vendor/autoload.php';
require_once(ABSPATH . '/wp-includes/pluggable.php');


require_once 'authentificator.php'; 
require_once 'adminPlugin.php'; 

function configure_admin_menu_sfh_authentication(){
    $admin = new AdminPluginSHFAuthentication();
}

add_action( 'admin_menu', 'configure_admin_menu_sfh_authentication' );


wp_register_style( 'custom-style-shf-authentication', plugins_url( "/css/login.css", __FILE__ ), array(), null, 'all' );
wp_enqueue_style( 'custom-style-shf-authentication' );

function shf_authentication_plugin_textdomain() {
	load_plugin_textdomain( 'shf-authentication', FALSE, basename( dirname( __FILE__ ) ) . "/languages/" );
}
add_action( 'plugins_loaded', 'shf_authentication_plugin_textdomain' );


// connection 
$connectionStatus = new ConnectionStatus();
// register connection
if(isset($_POST["login"])){
    $authentificator = new Authentificator();
    $connectionStatus = $authentificator->tryConnection($_POST["login"], $_POST["password"]);
}

function shf_connected_block(){
    global $connectionStatus;
    $connected = false;

    //get valid message
    if(isset($_POST["login"])){
        $authentificator = new Authentificator();
        $connectionStatus = $authentificator->tryConnection($_POST["login"], $_POST["password"]);
    }
    
    if($connectionStatus->connected){
        $connected = true;
    }else{
        $authentificator = new Authentificator();
        $connected = $authentificator->isConnected();
    }

    if(!$connected){
        include "template/loginForm.php";
    }
    return $connected;
}



?>