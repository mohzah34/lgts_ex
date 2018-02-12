<?php
    
require_once 'Configuration.php';
    
abstract class Controller{
    
    public function __construct(){
        session_start();
    }
        
    //connecte l'utilisateur donné et redirige vers la page d'acceuil   
    protected function log_user($member, $controller="", $action="index"){
        $_SESSION["user"] = $member;
//        if($member->firstConnect == 1){
//              $this->redirect($controller,"editMDP");         
//        }
//        else{
            $this->redirect($controller, $action);
//        }
        //see http://codingexplained.com/coding/php/solving-concurrent-request-blocking-in-php
        session_write_close(); 
    }
        
    //déconnecte l'utilisateur et redirige vers l'accueil
    public function logout(){
        $_SESSION = array();
        session_destroy();
        $this->redirect();
    }
        
        
    //redirige le navigateur vers l'action demandée
    public function redirect($controller = "", $action = "index", $id = "", $statusCode = 303)
    {
        $web_root = Configuration::get("web_root");
        $default_controller  = Configuration::get("default_controller");
        if ($controller == "") {
            $controller = $default_controller;
        }
            
        header('Location: '.$web_root.$controller."/".$action."/".$id, true, $statusCode);
        die();
    }
        
    //indique un l'utilisateur est connecté
    public function user_logged(){
        if (!isset($_SESSION['user'])) {
            return false;
        } else {
            return true;
        }
    }
        
    //renvoie l'utilisateur connecté ou redige vers l'accueil
    public function get_user_or_redirect()
    {
        if (!$this->user_logged()) {
            $this->redirect();
        } else {
            $user = $_SESSION['user'];
        }
            
        return $user;
    }
	
    public function isAdmin_or_redirect()
    {
        if (!$this->user_logged()) {
            $this->redirect();

        } 
        else {
            $user = $_SESSION['user'];
            if($user->type == 2){
                    $this->redirect("travailleur","index");
            }
            else if($user->type == 3){
                    $this->redirect("client","index");
            }
        }
        return false;
    }
	
    public function isClient_or_redirect()
    {
        if (!$this->user_logged()) {
            $this->redirect();
        } else {
            $user = $_SESSION['user'];
            if($user->type == 2){
                    $this->redirect("travailleur","index");
            }
            else if($user->type == 1){
                    $this->redirect("Admin","index");
            }
        }
    }
	
    public function isTravailleur_or_redirect()
    {
        if (!$this->user_logged()) {
            $this->redirect();
        } else {
            $user = $_SESSION['user'];
            if($user->type == 3){
                    $this->redirect("client","index");
            }
            else if($user->type == 1){
                    $this->redirect("Admin","index");
            }
        }
    }
	
	
    public function isTravailleurAdmin_or_redirect()
    {
        if (!$this->user_logged()) {
            $this->redirect();
        } else {
            $user = $_SESSION['user'];
            if($user->type == 3){
                    $this->redirect("client","index");
            }
        }
    }
	
	
	
	
        
    //renvoie le string donné haché.
    public function my_hash($password)
    {
        $prefix_salt = "vJemLnU3";
        $suffix_salt = "QUaLtRs7";
        return md5($prefix_salt.$password.$suffix_salt);        
    }
        
    //indique si un mot de passe correspond à son hash
    public function check_password($password, $hash)
    {
        //return $hash === $this->my_hash($password);
        return $hash === $password;
        var_dump(my_hash($password));
    }
        
    public function get_user_or_false()
    {
        if (!$this->user_logged()) {
            $user = false;
        } else {
            $user = $_SESSION['user'];
        }
        return $user;
    }
    
    function randomPWD() {
        $length = 10;
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
        
        
        
    public function fixed_strftime($format, $unix_timestamp) {
        if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN') {
            $format = preg_replace('#(?<!%)((?:%%)*)%e#', '\1%#d', $format);
        }
        return strftime($format, $unix_timestamp);
    }
        
     public function getMoisCourant($moisCourant){
         
        $nbStr = strlen($moisCourant);   
        if($nbStr == 5){
            return substr($moisCourant, 0,1);
                
        }
        else if($nbStr == 6){
            return substr($moisCourant, 0,2);
                
        }
   }
       
    public function getYearCourant($moisCourant){
        $nbStr = strlen($moisCourant);   
        if($nbStr == 5){
            return substr($moisCourant, 1,4);
        }
        else if($nbStr == 6){
            return substr($moisCourant, 2,4);
        }
   }
       
    //tout controleur doit posséder une méthode index, c'est son action
    //par défaut
    public abstract function index();
        
        
}