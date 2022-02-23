<?php


require_once 'authentificator.php'; 
require_once 'csvReader.php'; 

class AdminImportCsvFile{
    private $csvInputName;
    private $csvSeparator = ';';
    private $currentCsvFile = null;
    private $errors = [];
    private $pluginName;
    private $dataName;
    private $describe;

    public function __construct($dataName, $describe, $csvInputName) {
        $this->dataName = $dataName;
        $this->describe = $describe;
        $this->csvInputName = $csvInputName;
        $plugin_data = get_plugin_data( plugin_dir_path( __FILE__ ).'index.php'  );
        $this->pluginName =$plugin_data["Name"];
    }


    public function displayForm(){
        if(isset($_POST["import-".$this->csvInputName])){
            $fileUploaded = $this->uploadFileIfValid();
        }
        ?>
        <form method="post" enctype="multipart/form-data" >
            <fieldset>
                <legend><?php _e("Import ".$this->dataName." manager", $this->pluginName); ?></legend>
                <p><?php _e($this->describe, $this->pluginName); ?></p>

                <div class="messages">
                    <?php if($fileUploaded){ ?>
                        <span class="fas fa-check uploaded"><?php _e("File uploaded", $this->pluginName); ?></span>
                    <?php } ?>
                    <ul class="errors">
                        <?php foreach ($this->errors as $error){    ?>
                            <li class="fas fa-times"><?php echo $error ?></li>
                        <?php }?>
                    </ul>

                    <?php if(!$fileUploaded && $this->fileAlreadyExist()){ ?>
                        <span class="info fas fa-info-circle"><?php _e("The file already existing, it will be overrided.", $this->pluginName); ?></span>
                    <?php }else if(!$fileUploaded){?>
                        <span class="warning fas fa-exclamation-triangle"><?php _e("Warning : no file uploaded, datas is empty.", $this->pluginName); ?></span>
                    <?php }?>
                </div>
                <?php
                if($this->fileAlreadyExist()){
                ?>
                <a href="<?php echo "/wp-content/uploads/".$this->pluginName."/".$this->csvInputName.".csv" ?>" >Fichier courant</a>
                <?php
                }
                ?>
                <div>
                    <label for="file_list_uploaded" class="fas fa-file-csv"><?php _e("Horses", $this->pluginName); ?></label>
                    <input type="file" name="<?php echo $this->csvInputName ?>" id="file_list_uploaded" accept=".csv" />
                </div>

                
                <input type="submit" class="button-primary" value="<?php _e("Import", $this->pluginName); ?>" name="import-<?php echo $this->csvInputName ?>"/>
            </fieldset>
        </form>
        <?php

    }


    private function fileAlreadyExist(){
        $csvReader = new CsvReader(wp_upload_dir()['basedir']."/".$this->pluginName."/".$this->csvInputName.".csv");
        return $csvReader->fileExist();
    }

    private function uploadFileIfValid() {
        if(!$this->existFilesToUpload()) {
            return false;
        }
       
        if(!$this->checkFileType($this->csvInputName, 'text/csv',  __("File type is not csv", $this->pluginName))) {
            return false;
        }
        if(!$this->uploadFile()){
            return false;
        }

        if(!$this->validateFileContent()){
            return false;
        }
        
        $this->copyValidatedFile();
        

        return true;
    }

    private function existFilesToUpload(){
       
        if (empty($_FILES)){
            return false;
        }
        if($this->existFileToUploadWithName($this->csvInputName) ){
            return true;
        }else{
            array_push($this->errors, __("Empty file", $this->pluginName));
            return false;
        }
    }

    private function existFileToUploadWithName($name){
        return isset($name) && ($_FILES[$name]['size'] > 0);
    }

    private function checkFileType($name, $expectedType, $errorMessage){
        $arr_file_type = wp_check_filetype(basename($_FILES[$name]['name']));
        $uploaded_file_type = $arr_file_type['type'];
        $allowed_file_types = array($expectedType);
        if(in_array($uploaded_file_type, $allowed_file_types)){
            return true;
        }else{
            array_push($this->errors, $errorMessage);
            return false;
        }
    }

    private function uploadFile(){

        $upload_overrides = array( 'test_form' => false );

        $movefileCsv = wp_handle_upload( $_FILES[$this->csvInputName], $upload_overrides );
        if ( $movefileCsv && ! isset( $movefileCsv['error']) ) {
            $this->currentCsvFile = $movefileCsv["file"];
        }else{
            array_push($this->errors, $movefileCsv['error']);
            array_push($this->errors, __("Error during file uploading", $this->pluginName));
            return false;
        }

        return true;

    }

    private function validateFileContent(){
        $valid = true;
        if(!$this->checkFileEncoding()){
            $valid = false;
        }
        $csvReader = new CsvReader($this->currentCsvFile);
        $fileValid = $csvReader->check();
        if(!$fileValid){
            $this->errors =  array_merge($this->errors, $csvReader->errors);
            $valid = false;
        }

        return $valid;
    }

    

    private function checkFileEncoding(){
        if (mb_check_encoding(file_get_contents($this->currentCsvFile), 'UTF-8')) {
            return true;
        }else{
            array_push($this->errors, __("File must be encoding in UTF-8", $this->pluginName));
            return false;
        }
    }

    private function copyValidatedFile(){

        if(!is_dir(wp_upload_dir()['basedir']."/".$this->pluginName."/")){
            mkdir(wp_upload_dir()['basedir']."/".$this->pluginName."/", 0700);
        }

        if(!rename($this->currentCsvFile, wp_upload_dir()['basedir']."/".$this->pluginName."/".$this->csvInputName.".csv")){
            array_push($this->errors, __("Error during copie after file validating", $this->pluginName));
        }


    }
}
?>