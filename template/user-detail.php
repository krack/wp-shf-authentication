<?php

require_once plugin_dir_path( __DIR__ ).'people.php'; 
?>
<?php get_header(); ?>
<?php
$authentificator = new Authentificator();
$connectionStatus = $authentificator->isConnected();
function isYb($user){
    return $user->password != null;
}
$forceUserType=$_GET["type"];
if($forceUserType == "judge"){
    $_SESSION["LOGIN_TITLE_OVERRIDE"]= __("This part is reserved for SF member judges.<br /> Access your Judge profile by logging in below with your \"SHF member\" credentials.", "shf-authentication") ;
}else if($forceUserType == "yb"){
    $_SESSION["LOGIN_TITLE_OVERRIDE"]= __("This part is reserved for Young Breeders and partner schools.<br /> Access your Young Breeders / École profile by logging in below with your \"SHF member \" or provisional Young Breeders credentials provided by the Selle Français if you are not yet a member.", "shf-authentication");
}
if(($forceUserType != null && shf_connected_block(false, $forceUserType, true)) || ($forceUserType == null && (shf_connected_block(false, "judge,yb", true)))){
  $userId =$authentificator->getCurrentId();

  if (isset($_GET["user_id"])) {
    $userId = $_GET["user_id"];
  }
  $people = new Peoples(); 
  $user = $people->get($userId);

?>
<div id="profile_page">
    <form method="post" >
        <input name="disconnect" type="submit" class="disconnect" value="<?php _e("Disconnect", "shf-authentication") ?>" /></input>
    </form>
    <a class="back" href="<?php echo site_url(); ?>"><?php _e("Return to the website", "shf-authentication") ?></a>

<h2><?php _e("My profile", "shf-authentication") ?></h2>

<?php
        $query_profile_args = array(
        'post_type'      => 'attachment',
        'post_mime_type' => 'image',
        'post_status'    => 'inherit',
        'posts_per_page' => -1,
        'post_parent'    => 0,
        'starts_with'    => "profil_".$user->id
    );
    $profileUrl = "";
    $query_profile = new WP_Query( $query_profile_args );
    if(count($query_profile->posts) > 0){
        $profileUrl=wp_get_attachment_url( $query_profile->posts[0]->ID );
    }
?>
<div class="info"> 
    <img class="profil" src="<?php echo $profileUrl; ?>" alt="profil <?php echo $user->lastname ?> <?php echo $user->firstame ?>" />
    <div class="fields">
        <div>
            <label><?php _e("lastname", "shf-authentication") ?></label>
            <span><?php echo $user->lastname ; ?></span>
        </div>
        <div>
            <label><?php _e("firstame", "shf-authentication") ?></label>
            <span><?php echo $user->firstame ; ?></span>
        </div>
        <?php
        if($user->phone != null){
        ?>
            <div>
                <label><?php _e("Phone", "shf-authentication") ?></label>
                <span><?php echo $user->phone ; ?></span>
            </div>
        <?php
        }
        ?>
        <div class="long">
            <label><?php _e("email", "shf-authentication") ?></label>
            <span><?php echo $user->email ; ?></span>
        </div>
        <?php
        if($user->establishments != null){
        ?>
            <div>
                <label><?php _e("Establishment", "shf-authentication") ?></label>
                <span><?php echo $user->establishments ; ?></span>
            </div>
        <?php
        }
        ?>
        <?php
        if($user->address != null){
        ?>
            <div  class="long">
                <label><?php _e("address", "shf-authentication") ?></label>
                <span><?php echo $user->address." ".$user->postalCode." ".$user->city; ?></span>
            </div>
        <?php
        }
        ?>
        <?php
        if($user->region != null){
        ?>
            <div>
                <label><?php _e("Home region", "shf-authentication") ?></label>
                <span><?php echo $user->region ; ?></span>
            </div>
        <?php
        }
        ?>

        <?php
        if($user->website != null){
        ?>
            <div  class="long">
                <label><?php _e("website", "shf-authentication") ?></label>
                <span><a href="<?php echo $user->website ; ?>"><?php echo $user->website ; ?></a></span>
            </div>
        <?php
        }
        ?>

        <div>
            <label><?php _e("Membership number", "shf-authentication") ?></label>
            <span><?php 
            if(!$user->noshf){
                echo $user->id ;
            }else{
                _e("Awaiting membership", "shf-authentication");
            }
            ?></span>
        </div>
    </div>
</div>
    <?php
if(!isYb($user)){
?>
    <div class="level">
        <label><?php _e("level", "shf-authentication") ?></label>
        <span><?php echo $user->level ; ?></span>
    </div>
<?php 
}
?>


<?php

function displayDiplome($id, $level, $label){
    $query_fiche_args = array(
        'post_type'      => 'attachment',
        'post_mime_type' => 'application/pdf',
        'post_status'    => 'inherit',
        'posts_per_page' => -1,
        'post_parent'    => 0,
        'starts_with'    =>"diplome_".$id."_".$level
        
        
    );
    $query_fiche = new WP_Query( $query_fiche_args );

    if(count($query_fiche->posts) > 0){
    ?> 
        <a class="diplome" download href="<?php echo wp_get_attachment_url( $query_fiche->posts[0]->ID ) ?>"><?php echo $label; ?></a>
    <?php 
    } else{
    ?>
        <span class="diplome"><?php echo $label ?></span>
    <?php 
    }
    ?>
<?php
}

if(!isYb($user)){
?>
<div class="diplomes">
<label><?php _e("Diplomes", "shf-authentication") ?></label>
<?php
    displayDiplome($user->id, 'departemental', __("Departemental judge", "shf-authentication"));
    displayDiplome($user->id, 'regional', __("Regionnal judge", "shf-authentication"));
    displayDiplome($user->id, 'nationnal', __("Nationnal judge", "shf-authentication"));
?>
</div>
<?php
}
?>

<div class="judges-menu"> 
<?php
if(!isYb($user)){
?>
<h3><?php _e("My judge access", "shf-authentication") ?></h3>
<?php
}else{
?>
<h3><?php _e("Online training", "shf-authentication") ?></h3>
<?php
}


$menu = 'connected_menu_juges';
if(isYb($user)){
    $menu = 'connected_menu_yb';
}
wp_nav_menu( array( 
    'theme_location' => $menu, 
    'container_class' => 'connected_menu-class' ) ); 
}
?>
</div>
<?php
get_footer(); ?>