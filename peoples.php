<?php
require_once plugin_dir_path( __FILE__ ).'csvReader.php'; 
require_once "people.php";

class Peoples{
    private $list = null;
    private $map = null;
    private $pluginName;

    public function __construct() {
        if( ! function_exists( 'get_plugin_data' ) ) {
            require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
        }
        $plugin_data = get_plugin_data( plugin_dir_path( __FILE__ ).'index.php'  );
        $this->pluginName =$plugin_data["Name"];
    }

    public function getAll(){
        return $this->$list;
    }
  

    public function get($id){
        if($this->$map == null){
            $this->reinitialisation();
        }

        return $this->$map[$id];
    }
    public function connection($login, $password){
        if($this->$map == null){
            $this->reinitialisation();
        }

        $user = $this->$map[$login];
        if($user != null){
            if($user->password == $password){
                return $user;
            }
        }

        return null;
    }


    public function reinitialisation(){
        $this->$list = [];
        $this->$map = [];
        $this->read("list_judge", "judge");
        $this->read("list_yb", "yb");
       
    }

    public function read($file, $right){
        $csvReader = new CsvReader(wp_upload_dir()['basedir']."/".$this->pluginName."/".$file.".csv");

        $rawDataList = $csvReader->readFile();

        foreach ($rawDataList as $rawData){
            $user = new People($rawData);
            array_push($user->roles, $right);
            $this->$map[$user->id] = $user;
            array_push($this->$list, $user);
        }
    }

}
?>