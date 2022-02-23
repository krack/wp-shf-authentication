<?php

class People{
    public $id;
    public $noshf =false;
    public $firstame;
    public $lastname;
    public $email;
    public $phone;
    public $address;
    public $region;
    public $level;
    public $compte;
    public $roles;
    public $website;
    public $establishments;


    public function __construct($rawData) {
        $this->id = $rawData["N° ADHERENT"];
        if($this->id == null || $this->id=="X"){
            $this->id = $rawData["id"];
             $this->noshf = true;
        }
        $this->firstame = $rawData["Prénom"];
        $this->lastname = $rawData["Nom"];
        $this->email = $rawData["email"];
        $this->phone = $rawData["Portable"];
        $this->address = $rawData["Adresse"];
        $this->postalCode = $rawData["CP"];
        $this->city = $rawData["COMMUNE"];
        $this->region = $rawData["Région"];
        $this->level = $rawData["Type de juge"];
        $this->password = $rawData["password"];
        $this->compte = $rawData["compte"];
        $this->website = $rawData["site internet"];
        $this->establishments = $rawData["Etablissement"];
        $this->roles = [];
    }

}
?>