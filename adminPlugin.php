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
}
?>