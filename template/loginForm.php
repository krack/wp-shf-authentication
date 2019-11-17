<div class="shf-authentication-login">
    <h2><?php _e("Restricted content", "shf-authentication") ?></p>
    <p><?php _e("This content required a SHF valid subscribe for selle Francais. ", "shf-authentication") ?></p>

    <form method="post"> 
        <ul class="errors">
        <?php foreach($connectionStatus->errors as $error){ ?>
            <li class="fas fa-times"><?php echo $error ?></li>
        <?php } ?>
        </ul>
        <div>
            <label for="login"><?php _e("login", "shf-authentication") ?></label>
            <input type="text" id="login" name="login"/>
        </div>
        <div>
            <label for="password"><?php _e("password", "shf-authentication") ?></label>
            <input type="password" id="password" name="password"/>
        </div>
        <input type="submit" value="<?php _e("connection", "shf-authentication") ?>"/>
    </form>
</div>