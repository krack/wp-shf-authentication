<?php

require_once plugin_dir_path( __DIR__ ).'people.php'; 
$authentificator = new Authentificator();
$connectionStatus = $authentificator->isConnected();
?>

<div id="login-header" class="<?php if(!$connectionStatus){ echo "disconnected"; }else{echo "connected";} ?>">
    <a href="/user-detail/">
    <?php
    if(!$connectionStatus){
    ?>
        Mon profil
    <?php
    }else{
        $userId =$authentificator->getCurrentId();
        $people = new Peoples(); 
        $user = $people->get($userId);
        echo "$user->lastname $user->firstame";
    }
    ?>
</a>
    <a class="benevol" href="/benevoles/">Devenir bénévole</a>
</div>