<?php
/*
Plugin Name: shf-authentication
Text Domain: shf-authentication
Domain Path: /languages
Plugin URI: 
Description: Possibility for checking is user is subscribe at SHF services.
Author: Sylvain Gandon
Version: 0.2
Author URI: 
*/
require 'vendor/autoload.php';
require_once(ABSPATH . '/wp-includes/pluggable.php');

require_once 'authentificator.php'; 
require_once 'adminPlugin.php'; 
require_once 'pageWithOverrideTemplate.php'; 

function configure_admin_menu_sfh_authentication(){
    $admin = new AdminPluginSHFAuthentication();
}

add_action( 'admin_menu', 'configure_admin_menu_sfh_authentication' );


wp_register_style( 'custom-style-shf-authentication', plugins_url( "/css/login.css", __FILE__ ), array(), null, 'all' );
wp_enqueue_style( 'custom-style-shf-authentication' );


wp_enqueue_script('jquery');
wp_enqueue_script( 'shf-authentifcation-login', plugins_url( "/js/".'login.js', __FILE__ ), array(), null, true);

function shf_authentication_plugin_textdomain() {
	load_plugin_textdomain( 'shf-authentication', FALSE, basename( dirname( __FILE__ ) ) . "/languages/" );
}
add_action( 'plugins_loaded', 'shf_authentication_plugin_textdomain' );

// connection 
$connectionStatus = new ConnectionStatus();
//diconnect
if(isset($_POST["disconnect"])){
    $authentificator = new Authentificator();
    $authentificator->disconnect();

}

// register connection
if(isset($_POST["login"])){
    $authentificator = new Authentificator();
    $connectionStatus = $authentificator->tryConnection($_POST["login"], $_POST["password"]);

}

function shf_connected_block($displayLogin = true, $right = "shf", $displayForm =false){
    global $connectionStatus;
    $hasRight = true;
    $authentificator = new Authentificator();

    if($connectionStatus->connected){
        $connected = true;
        $hasRight = $authentificator->hasRight($right);
    }else{
        $authentificator = new Authentificator();
        $connected = $authentificator->isConnected();
        if($connected){
            $hasRight = $authentificator->hasRight($right);
        }
    }
    

    if(!$connected && $displayLogin){
        $message = __("Log in<br /> to learn more", "shf-authentication");
        include "template/connectionMessage.php";
    }
    if(!$connected || !$hasRight){
        include "template/loginForm.php";
    }
    return $connected && $hasRight;
}

function shf_login_block(){
    global $connectionStatus;
    $connected = false;

  
    
    if($connectionStatus->connected){
        $connected = true;
    }else{
        $authentificator = new Authentificator();
        $connected = $authentificator->isConnected();
    }

    if(!$connected){
        include "template/loginForm.php";
    }
}


function shf_connected_class(){
    global $connectionStatus;
    $connected = false;


    
    if($connectionStatus->connected){
        $connected = true;
    }else{
        $authentificator = new Authentificator();
        $connected = $authentificator->isConnected();
    }

    if(!$connected){
        echo "shf-disconnected";
    }
}

function shf_add_fixed_connection_button(){
    global $connectionStatus;
    $connected = false;


    
    if($connectionStatus->connected){
        $connected = true;
    }else{
        $authentificator = new Authentificator();
        $connected = $authentificator->isConnected();
    }

    if(!$connected){
        include "template/connectionFixedButton.php";
    }
}


/**
 * protect media with change of path
 */

function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function changePathToProtect($attachment_ID)
{          
    $oldFilePath = get_attached_file($attachment_ID); // Gets path to attachment

    $path_parts = pathinfo($oldFilePath);

    if(strtolower($path_parts['extension']) == "mp4" || strtolower($path_parts['extension']) == "pdf"){
        $random = generateRandomString();
        $newfilePath = $path_parts['dirname'].'/'.$random.'/'.$path_parts['basename'];  

        mkdir($path_parts['dirname'].'/'.$random.'/', 0777);
        rename( $oldFilePath, $newfilePath);
        update_attached_file( $attachment_ID, $newfilePath );
    }
   
}
add_action("add_attachment", 'changePathToProtect');
add_action("edit_attachment", 'changePathToProtect');

function return_output($file, $message){
    ob_start();
    include $file;
    return ob_get_clean();
}
function protectedBlock($atts, $content){
    $atts = shortcode_atts(array('visible' => false, 'form'=> false, 'right' => 'shf', 'login'=>'', 'message' => __("Log in<br /> to learn more", "shf-authentication"), 'class'=>''), $atts);
    if($atts['login'] != ""){
        $_SESSION["LOGIN_TITLE_OVERRIDE"]=$atts['login'];
   }    
    if(shf_connected_block(false, $atts['right'], $atts['form'] )){
        return $content;
    }else{
        if($atts["visible"]=="true"){
            $message = $atts["message"];
            return return_output("template/connectionMessage.php", $message);
        }elseif($atts["class"] != ''){
           
            return '<div class="'.$atts["class"].' openLoginButton"></div>';
        }
        else{
            return '';
        }
    }
}

add_shortcode('protectedESF', 'protectedBlock');

function loginBlock(){
    return return_output("template/loginForm.php");
}
if (empty($_POST) && !is_admin()){
    include "template/loginheader.php" ;
}


// profile page

new PageWithOverrideTemplate("user-detail", "template/user-detail.php", ["user-detail.css"]);


function wpse_298888_posts_where( $where, $query ) {
    global $wpdb;
    
    $starts_with = $query->get( 'starts_with' );
    
    if ( $starts_with ) {
            $where .= " AND $wpdb->posts.post_title LIKE '$starts_with%'";
    }
    
    $exact = $query->get( 'exact' );
    
    if ( $exact ) {
            $where .= " AND $wpdb->posts.post_title = '$exact'";
    }
    
    
    return $where;
}
add_filter( 'posts_where', 'wpse_298888_posts_where', 10, 2 );
function wpb_custom_new_menu() {
    register_nav_menus(
        array(
        'connected_menu_juges' => __( 'Connected menu judges' ),
        'connected_menu_yb' => __( 'Connected menu Yb' ),
        )
    );
}
add_action( 'init', 'wpb_custom_new_menu' );
?>
