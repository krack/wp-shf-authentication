<div class="shf-authentication-login <?php if(count($connectionStatus->errors) > 0) { echo "shf-authentication-login-in-error"; } ?>">
    <h1><?php _e("Connection", "shf-authentication") ?></h1>
    <div class="highlighted">
        <form method="post"> 
            <ul class="errors">
            <?php foreach($connectionStatus->errors as $error){ ?>
                <li class="fas fa-times"><?php echo $error ?></li>
            <?php } ?>
            </ul>
            <div>
                <label for="login"><?php _e("Login", "shf-authentication") ?></label>
                <input type="text" id="login" name="login"/>
            </div>
            <div>
                <label for="password"><?php _e("Password", "shf-authentication") ?></label>
                <input type="password" id="password" name="password"/>
            </div>
            <input type="submit" value="<?php _e("Connection", "shf-authentication") ?>"/>
        </form>
    </div>
</div>