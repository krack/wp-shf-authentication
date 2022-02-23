<?php


require_once 'authentificator.php'; 
require_once 'adminImportCsvFile.php';

class AdminPluginSHFAuthentication{
    private $judgesForm;
    private $ybForm;
    private $pluginName;

    public function __construct() {
        add_action( 'admin_enqueue_scripts', array( $this,'load_custom_wp_shf_authentication_admin_style') );
        add_menu_page(__("SHF authentication settings", "shf-authentication"), __("SHF authentication", "shf-authentication"), 'manage_options', 'shf-authentication-setting', array( $this,'displaySettingPage'));
        
        $plugin_data = get_plugin_data( plugin_dir_path( __FILE__ ).'index.php'  );
        $this->pluginName =$plugin_data["Name"];

        $this->judgesForm = new AdminImportCsvFile(__('juges', "shf-authentication"), __('List of judges for validate authtentification', "shf-authentication"), 'list_judge');

        $this->ybForm = new AdminImportCsvFile(__('yb', "shf-authentication"), __('List of yb for validate authtentification', "shf-authentication"), 'list_yb');
    }

    public function load_custom_wp_shf_authentication_admin_style() {
        wp_register_style( 'admin-style-sf-authentication', plugins_url( '/css/shf-authentication-admin-style.css', __FILE__ ), array(), null, 'all' );
        wp_enqueue_style( 'admin-style-sf-authentication' );
        wp_register_style( 'fontawesome', 'https://use.fontawesome.com/releases/v5.6.3/css/all.css',  array(), null, 'all');
        wp_enqueue_style( 'fontawesome' );
        wp_enqueue_script( 'shf-authentification-example-mediad', plugins_url( "/js/".'example-media.js', __FILE__ ), array(), null, true);

    }

    public function displaySettingPage(){
       
        $reseted = $this->resetPasswordIfAsked();
        $saved = $this->saveShfConnectionInformation();
        ?>
        <div class="wrap wp-shf-authentication-plugin-admin">
            <h1><?php _e("SHF authentication", $this->pluginName); ?></h1>
            <form method="post">
                <fieldset>
                    <legend><?php _e("Secret manager", $this->pluginName); ?></legend>
                    <div class="messages">
                        <?php if($reseted){ ?>
                            <span class="fas fa-check reseted"><?php _e("Secret reseted", $this->pluginName); ?></span>
                        <?php } ?>

                        <span class="warning fas fa-exclamation-triangle"><?php _e("Warning : with this reset of secret, all connected users need to reconnect them.", $this->pluginName); ?></span> 
                    </div>
                    <input type="submit" class="button-primary" value="<?php _e("Reset secret", $this->pluginName); ?>" name="reset" />                
                </fieldset>

            </form> 

            <form method="post">
                <fieldset>
                    <legend><?php _e("SHF connection", $this->pluginName); ?></legend>
                    <div class="messages">
                        <?php if($saved){ ?>
                            <span class="fas fa-check reseted"><?php _e("saved", $this->pluginName); ?></span>
                        <?php } ?>
                    </div>

                    <div>
                        <label ref="host"><?php _e("host", $this->pluginName); ?></label>
                        <input id="host" placeholder="shf.fr" name="host" value="<?php echo get_option( 'shf_host' ); ?>" /> 
                    </div>
                    <div>
                        <label ref="key-api"><?php _e("key-api", $this->pluginName); ?></label>
                        <input id="key-api" placeholder="myApiKey"  name="key" value="<?php echo get_option( 'shf_key' ); ?>" /> 
                    </div>
                    <div>
                        <label ref="key-secret"><?php _e("key-secret", $this->pluginName); ?></label>
                        <input id="key-secret" placeholder="myApiSecrEt" name="secret" value="<?php echo get_option( 'shf_secret' ); ?>" /> 
                    </div>
                    <div>
                        <label ref="redirect"><?php _e("redirect link", $this->pluginName); ?></label>
                        <input id="redirect" placeholder="https://www.shf.com/mesAdhesion" name="redirect" value="<?php echo get_option( 'shf_redirect' ); ?>" /> 
                    </div>
                    <input type="submit" class="button-primary" value="<?php _e("save", $this->pluginName); ?>" name="save-information" />                
                </fieldset>

            </form> 
            <?php
            $this->judgesForm->displayForm();
            $this->ybForm->displayForm();
            ?>
             <fieldset>
                <legend><?php _e("Shortcode", $this->pluginName); ?></legend> 
                [protectedESF visible="true" right="judge"]
                    <?php _e("Protected content", $this->pluginName); ?>
                [/protectedESF]   
                <p><?php _e("visible (optionnal): <br />true - display login button if not conntect or no right <br />false (default)- display nothing if not conntected or no right", $this->pluginName); ?></p>
                <p><?php _e("right (optionnal): <br />judge - the Protected content, need user with right judge to be see<br />yb - the Protected content, need user with yb judge to be see<br />default - the Protected content, need user just to be connected of shf.", $this->pluginName); ?></p>
            </fieldset>
            <fieldset>
                    <legend><?php _e("Medias name", $this->pluginName); ?></legend> 

                    <p><?php _e("Adding wordpress medias for profile", $this->pluginName); ?></p>
                   
                    <div>
                        <label ref="exemple-id"><?php _e("Example", $this->pluginName); ?></label>
                        <input id="exemple-id" type="text" placeholder="2498" value="2498" />
                    </div>
                    
                    <div>
                        <h3><?php _e("Add a profile photo", $this->pluginName); ?></h3> 
                        <p>profil_(<?php _e("user id", $this->pluginName); ?>)</p>
                        <p>
                        <?php _e("user id is is value of columne 'id' in imported csv file.", $this->pluginName); ?><br />
                        </p>
                        <div class="example">
                            <h4><?php _e("Example", $this->pluginName); ?></h4>
                            <span example="profil_#horse-id#"></span> <span><?php _e("user profile photo", $this->pluginName); ?></span>
                        </div>
                    </div>

                    <div>
                        <h3><?php _e("Add a diploma", $this->pluginName); ?></h3> 
                        <p>diplome_(<?php _e("user id", $this->pluginName); ?>)_*</p>
                        <p>
                        <?php _e("user id is is value of columne 'id' in imported csv file.", $this->pluginName); ?><br />
                        </p>
                        <p>
                        <?php _e("use field \"legend\" to define title of diplome", $this->pluginName); ?><br />
                        </p>
                        <div class="example">
                            <h4><?php _e("Example", $this->pluginName); ?></h4>
                            <span example="diplome_#horse-id#_region"></span> <span><?php _e("one diplome of user", $this->pluginName); ?></span>
                        </div>
                    </div>
            </fieldset>
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