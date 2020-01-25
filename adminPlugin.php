<?php


require_once 'authentificator.php'; 

class AdminPluginSHFAuthentication{

    public function __construct() {
        add_action( 'admin_enqueue_scripts', array( $this,'load_custom_wp_shf_authentication_admin_style') );
        add_menu_page(__("SHF authentication settings", "shf-authentication"), __("SHF authentication", "shf-authentication"), 'manage_options', 'shf-authentication-setting', array( $this,'displaySettingPage'));
    }

    public function load_custom_wp_shf_authentication_admin_style() {
        wp_register_style( 'admin-style-sf-authentication', plugins_url( '/css/shf-authentication-admin-style.css', __FILE__ ), array(), null, 'all' );
        wp_enqueue_style( 'admin-style-sf-authentication' );
        wp_register_style( 'fontawesome', 'https://use.fontawesome.com/releases/v5.6.3/css/all.css',  array(), null, 'all');
        wp_enqueue_style( 'fontawesome' );
    }

    public function displaySettingPage(){
        $reseted = $this->resetPasswordIfAsked();
        $saved = $this->saveShfConnectionInformation();
        ?>
        <div class="wrap wp-shf-authentication-plugin-admin">
            <h1><?php _e("SHF authentication", 'shf-authentication'); ?></h1>  
            <form method="post">
                <fieldset>
                    <legend><?php _e("Secret manager", 'shf-authentication'); ?></legend>
                    <div class="messages">
                        <?php if($reseted){ ?>
                            <span class="fas fa-check reseted"><?php _e("Secret reseted", 'shf-authentication'); ?></span>
                        <?php } ?>

                        <span class="warning fas fa-exclamation-triangle"><?php _e("Warning : with this reset of secret, all connected users need to reconnect them.", 'shf-authentication'); ?></span> 
                    </div>
                    <input type="submit" class="button-primary" value="<?php _e("Reset secret", 'shf-authentication'); ?>" name="reset" />                
                </fieldset>

            </form> 

            <form method="post">
                <fieldset>
                    <legend><?php _e("SHF connection", 'shf-authentication'); ?></legend>
                    <div class="messages">
                        <?php if($saved){ ?>
                            <span class="fas fa-check reseted"><?php _e("saved", 'shf-authentication'); ?></span>
                        <?php } ?>
                    </div>

                    <div>
                        <label ref="host"><?php _e("host", 'shf-authentication'); ?></label>
                        <input id="host" placeholder="shf.fr" name="host" value="<?php echo get_option( 'shf_host' ); ?>" /> 
                    </div>
                    <div>
                        <label ref="key-api"><?php _e("key-api", 'shf-authentication'); ?></label>
                        <input id="key-api" placeholder="myApiKey"  name="key" value="<?php echo get_option( 'shf_key' ); ?>" /> 
                    </div>
                    <div>
                        <label ref="key-secret"><?php _e("key-secret", 'shf-authentication'); ?></label>
                        <input id="key-secret" placeholder="myApiSecrEt" name="secret" value="<?php echo get_option( 'shf_secret' ); ?>" /> 
                    </div>
                    <div>
                        <label ref="redirect"><?php _e("redirect link", 'shf-authentication'); ?></label>
                        <input id="redirect" placeholder="https://www.shf.com/mesAdhesion" name="redirect" value="<?php echo get_option( 'shf_redirect' ); ?>" /> 
                    </div>
                    <input type="submit" class="button-primary" value="<?php _e("save", 'shf-authentication'); ?>" name="save-information" />                
                </fieldset>

            </form> 
        </div>
        <?php

    }

    private function resetPasswordIfAsked(){
        $reseted = false;
        if(isset($_POST["reset"])){
            $authentificator = new Authentificator();
            $authentificator->resetJwtTokenSecret();
            $reseted = true;
        }
        return $reseted;
    }

    private function saveShfConnectionInformation(){
        $saved = false;
        if(isset($_POST["save-information"])){
            $this->defineOption('shf_host', $_POST["host"]);
            $this->defineOption('shf_key', $_POST["key"]);
            $this->defineOption('shf_secret', $_POST["secret"]);
            $this->defineOption('shf_redirect', $_POST["redirect"]);
            $saved = true;
        }
        return $saved;
    }

    private function defineOption($name, $value){
        if(!get_option($name)){
            add_option( $name, $value );
        }else{
            update_option( $name, $value );
        }
    }
}
?>