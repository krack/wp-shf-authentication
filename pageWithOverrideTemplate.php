<?php

class PageWithOverrideTemplate{
    private $pageName;
    private $template;
    private $cssFiles;
    public function __construct($pageName, $templatePath, $cssFiles){
        $this->pageName = $pageName;
        $this->templatePath = $templatePath;
        $this->cssFiles = $cssFiles;

        

        if(!$this->isPageExist()){
            $this->createPage();
        }

        $this->registerTemplateOverride();
        $this->add();
    }

    private function registerTemplateOverride(){
        add_action( 'template_include', function($template){
            if ( is_page( $this->pageName ) ) {
                $new_template = plugin_dir_path( __FILE__ ).$this->templatePath;
                if ( !empty( $new_template ) ) {
                    return $new_template;
                }
            }
            return $template;
        } );
    }

    private function add(){
        add_action( 'wp_enqueue_scripts', array( $this,'wptuts_scripts_basicAppointmentPlugin') );
        
        
    }

    function wptuts_scripts_basicAppointmentPlugin(){
        wp_register_style( 'fontawesome', 'https://use.fontawesome.com/releases/v5.6.3/css/all.css',  array(), null, 'all');
        wp_enqueue_style( 'fontawesome' );
       
        foreach($this->cssFiles as $cssFile ){
            wp_register_style( 'custom-style', plugins_url( "/css/".$cssFile, __FILE__ ), array(), null, 'all' );
        }

        wp_enqueue_style( 'custom-style' );

        
    }



    private function isPageExist(){
	
        $pages = get_pages(array(
            'post_status'  => array('publish', 'private')
        )); 
        foreach ( $pages as $page ) {
            if($page->post_title === $this->pageName){
                return true;
            };
        }
        return false;
    }

    private function createPage(){
        $my_post = array(
            'post_title' => $this->pageName,
            'post_content' => '',
            'post_status' => 'publish',
            'post_author' => 1,
            'post_type' => 'page'
        );
        wp_insert_post( $my_post );
    }


}


?>