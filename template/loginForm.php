
<?php
$connectionStatus = new ConnectionStatus();
// register connection
if(isset($_POST["login"])){
    $authentificator = new Authentificator();
    $connectionStatus = $authentificator->tryConnection($_POST["login"], $_POST["password"]);
}

?>
<div class="shf-authentication-login <?php if(count($connectionStatus->errors) > 0) { echo "shf-authentication-login-in-error"; } ?> <?php if($displayForm) { echo "shf-authentication-login-in-error"; } ?>">
    <?php
    if($_SESSION["LOGIN_TITLE_OVERRIDE"] != null){
    ?>
        <h1><?php echo $_SESSION["LOGIN_TITLE_OVERRIDE"] ?></h1>
    <?php
    }else{
    ?>
     <h1><?php _e("This part is reserved for judges who are members or members of the Young Breeders program.<br />Access your profile with your SHF or Young Breeders ID.", "shf-authentication") ?></h1>
    <?php
    }
    ?>
    <div class="highlighted">

        <form method="post"> 
           
            <div>
                <label for="login"><?php _e("Login", "shf-authentication") ?></label><input type="text" id="login" name="login"/>
            </div>
            <div>
                <label for="password"><?php _e("Password", "shf-authentication") ?></label><input type="password" id="password" name="password"/>
            </div>
            <input type="submit" value="<?php _e("Connection", "shf-authentication") ?>"/>
            <ul class="errors">
                <?php foreach($connectionStatus->errors as $error){ ?>
                    <li><?php echo $error ?></li>
                <?php } ?>
            </ul>
        </form>

        <div class="rights">
            <b><?php _e("Once connected, benefit from additional content!", "shf-authentication") ?></b>
        </div>
    </div>
</div>
<script>
    document.querySelector('#main-header').style.display = 'block';
    jQuery(document).ready(function() {
        document.querySelector('#login-header').style.display = 'none';
        jQuery('.protected').remove();

    });
</script>