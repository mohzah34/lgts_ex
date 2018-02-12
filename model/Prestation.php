<?php
require_once "framework/Model.php";


class Prestation extends Model{
    public $id;
    public $heures;
    public $hPrevue;
    public $jour;
    public $debut;
    public $titresRemis;
    public $elec;
    public $absence;
    public $justification;
    public $cloture;
    public $idT;
    public $idC;
    public $status;
    
    

    public function __construct($id, $heures, $hPrevue ,$jour, $debut, $titresRemis,$elec,$status, $absence, $justification, $cloture, $idT, $idC){
        $this->id = $id;
        $this->heures = $heures;
        $this->hPrevue = $hPrevue;
        $this->jour = $jour;
        $this->debut = $debut;
        $this->titresRemis = $titresRemis;
        $this->elec = $elec;
        $this->status = $status;
        $this->absence = $absence;
        $this->justification = $justification;
        $this->cloture = $cloture;
        $this->idT = $idT;
        $this->idC = $idC;
    }
    
    
    public static function get_allPrest(){
        $query = self::execute("SELECT * FROM prestations",array());
        $data = $query->fetchAll(); 
        $results = [];
        foreach($data as $row){
            $results[] = new Prestation($row['id'],$row["heures"], $row["hPrevue"], $row["jour"], $row["debut"], $row["titres_remis"],$row["elec"],$row["status"],$row["absence"], $row["justification"], $row["cloture_mois"], $row["travailleur_id"], $row["client_id"]);
        }
        return $results;
    }
   public static function isPrested($id, $dateP){
       $query = self::execute("SELECT * FROM prested_planning where planID=:id and date LIKE '$dateP'",array("id"=>$id));
       $res = $query->fetchAll();
        $nombre_res = count($res); 
        if($nombre_res < 1){
            return false;
        }
        return true;
   }
   
   
   public static function get_travailleur_current_month($idT){
       $query = self::execute("SELECT annee as y,mois as m FROM clotured where idT=:id order by id desc limit 1 ",array("id"=>$idT));
       $res = $query->fetch();
       return $res;
   }
   
   
   public static function cloturedMonth($month,$year,$idT){
       $query = self::execute("SELECT * FROM clotured where mois=:m and annee=:a and idT=:id ",array("m"=>$month,"a"=>$year,"id"=>$idT));
       $res = $query->fetchAll();
        $nombre_res = count($res); 
        if($nombre_res < 1){
            return false;
        }
        else{
            return true;
        }
   }
   
   public static function getPrestPlanning($idD){
//       echo var_dump($idD);
       $query = self::execute("SELECT * FROM prested_planning where id=:id",array("id"=>$idD));
       $res = $query->fetch(PDO::FETCH_ASSOC);
       return $res;
   }
   
   public static function getPrestedID($id,$date){
       $query = self::execute("SELECT * FROM prested_planning where planID=:id AND date LIKE '$date'",array("id"=>$id));
       $res = $query->fetch(PDO::FETCH_ASSOC);
       return $res['id'];
   }
  
   
   public static function getPrestOfMonth($id,$date){
        $query = self::execute("SELECT * FROM prestations where travailleur_id=:id AND jour LIKE '$date-%' ORDER by jour",array("id"=>$id));
        $data = $query->fetchAll(); 
        $results = [];
        foreach($data as $row){
            $results[] = new Prestation($row['id'],$row["heures"], $row["hPrevue"], $row["jour"], $row["debut"], $row["titres_remis"],$row["elec"],$row["status"],$row["absence"], $row["justification"], $row["cloture_mois"], $row["travailleur_id"], $row["client_id"]);
        }
        return $results;
   }
    
    public static function getPrestDay($date,$id){
        $query = self::execute("SELECT * FROM prestations where travailleur_id=:id AND jour LIKE :date ",array("id"=>$id,"date"=>$date));
        $data = $query->fetchAll(); 
        $results = [];
        foreach($data as $row){
            $results[] = new Prestation($row['id'],$row["heures"], $row["hPrevue"], $row["jour"], $row["debut"], $row["titres_remis"],$row["elec"],$row["status"],$row["absence"], $row["justification"], $row["cloture_mois"], $row["travailleur_id"], $row["client_id"]);
        }
        return $results;
    }
    public static function getPrestDay2($date,$id,$idC){
        $query = self::execute("SELECT * FROM prestations where travailleur_id=:id AND jour = :date AND elec = 0 ",array("id"=>$id,"date"=>$date));
        $data = $query->fetchAll(); 
        $results = [];
        foreach($data as $row){
            $results[] = new Prestation($row['id'],$row["heures"], $row["hPrevue"], $row["jour"], $row["debut"], $row["titres_remis"],$row["elec"],$row["status"],$row["absence"], $row["justification"], $row["cloture_mois"], $row["travailleur_id"], $row["client_id"]);
        }
        return $results;
    }
    
	
	public static function getPrestDayNoPlan($date,$id){
        $query = self::execute("SELECT * FROM prestations where travailleur_id=:id AND jour LIKE :date AND cloture_mois=:cloture AND id not in (SELECT prestID from prested_planning)",array("id"=>$id,"date"=>$date,"cloture"=>0));
        $data = $query->fetchAll(); 
        $results = [];
        foreach($data as $row){
            $results[] = new Prestation($row['id'],$row["heures"], $row["hPrevue"], $row["jour"], $row["debut"], $row["titres_remis"],$row["elec"],$row["status"],$row["absence"], $row["justification"], $row["cloture_mois"], $row["travailleur_id"], $row["client_id"]);
        }
        return $results;
    }
    
    public static function getPlanning($id){
        $query = self::execute("Select * FROM planning_mensuel_travailleur where travailleur_id=:id",array("id"=>$id));
        $data = $query->fetchAll(); 
        $results = [];
        foreach($data as $row){
            $results[$row['jourSemaine']] = $row;
        }
        return $results;
    }
    
    public static function calc_nb_elec($id,$date){
        $query = self::execute("Select sum(heures) as tot_elec FROM prestations where travailleur_id=:id and elec = 1 and jour LIKE '$date-%' ",array("id"=>$id));
        $data = $query->fetch(); 
        return $data['tot_elec'];
    }
    
    
    public static function get_prest($id_p){
        $query = self::execute("select * from prestations where id = :id", array("id"=>$id_p));
        $row =  $query->fetch();
        return new Prestation($row['id'],$row["heures"], $row["hPrevue"], $row["jour"], $row["debut"], $row["titres_remis"],$row["elec"],$row["status"],$row["absence"], $row["justification"], $row["cloture_mois"], $row["travailleur_id"], $row["client_id"]);

    }
    
    public static function getPrestTrav($id){
        $query = self::execute("SELECT * FROM prestations where travailleur_id = :id",array("id"=>$id));
        $data = $query->fetchAll(); 
        $results = [];
        foreach($data as $row){
            $results[] = new Prestation($row['id'],$row["heures"], $row["hPrevue"], $row["jour"], $row["debut"], $row["titres_remis"],$row["elec"],$row["status"],$row["absence"], $row["justification"], $row["cloture_mois"], $row["travailleur_id"], $row["client_id"]);
        }
        return $results;
    }
    
    public static function getPrestTravNoClot($id){
        $query = self::execute("SELECT * FROM prestations where travailleur_id = :id AND cloture_mois = 0",array("id"=>$id));
        $data = $query->fetchAll(); 
        $results = [];
        foreach($data as $row){
            $results[] = new Prestation($row['id'],$row["heures"], $row["hPrevue"], $row["jour"], $row["debut"], $row["titres_remis"],$row["elec"],$row["status"],$row["absence"], $row["justification"], $row["cloture_mois"], $row["travailleur_id"], $row["client_id"]);
        }
        return $results;
    }
    
     public static function isFerier($day,$month,$year){
        $query = self::execute("SELECT * FROM ferier where date = STR_TO_DATE('$day,$month,$year', '%d,%m,%Y' ) ",array());
        $data = $query->fetchAll(); 
        if(!empty($data)){
            return true;
        }
        else{
            return false;
        }
    }
    
    public static function getPrestOfMonth2($month,$year,$id){
        $query = self::execute("SELECT * FROM prestations where travailleur_id = :id AND jour LIKE '$year-$month-%'",array("id"=>$id));
        $data = $query->fetchAll(); 
        $results = [];
        foreach($data as $row){
            $results[] = new Prestation($row['id'],$row["heures"], $row["hPrevue"], $row["jour"], $row["debut"], $row["titres_remis"],$row["elec"],$row["status"],$row["absence"], $row["justification"], $row["cloture_mois"], $row["travailleur_id"], $row["client_id"]);
        }
        return $results;
    }
    
    public static function getPrestClient($id,$date){
        $query = self::execute("SELECT * FROM prestations where client_id = :id and jour LIKE '$date-%' ",array("id"=>$id));
        $data = $query->fetchAll(); 
        $results = [];
        foreach($data as $row){
            $results[] = new Prestation($row['id'],$row["heures"], $row["hPrevue"], $row["jour"], $row["debut"], $row["titres_remis"],$row["elec"],$row["status"],$row["absence"], $row["justification"], $row["cloture_mois"], $row["travailleur_id"], $row["client_id"]);
        }
        return $results;
    }
    
    public static function getPrestClient_fromTravailleur($idC,$idT,$month,$year){
        $query = self::execute("SELECT month(jour) as month, year(jour) as year, day(jour) as day FROM prestations where client_id = :idC and travailleur_id=:idT and jour LIKE '$year-$month-%' ",array("idC"=>$idC,"idT"=>$idT));
        $data = $query->fetchAll(); 
        return $data;
    }
    
    public static function getJoursFerierOfMonth($month,$year){
        $query = self::execute("SELECT month(date) as month, year(date) as year, day(date) as day FROM ferier where date LIKE '$year-$month-%' ",array());
        $data = $query->fetchAll(); 
        return $data;
    }
    
    public static function get_nbHeuresPlan($id){
        $query = self::execute("SELECT heures FROM planning_mensuel_travailleur where id = :id ",array("id"=>$id));
        $data = $query->fetch(); 
        return $data['heures'];
       
    }
    
  
    public static function add_prest($prest,$idPL = ""){
        self::execute("INSERT INTO prestations(heures,hPrevue,jour,debut,titres_remis,absence,justification,cloture_mois,travailleur_id,client_id,elec)
                       VALUES(:heures,:hPrevue,:jour,:debut,:titres,:absence,:justification,:cloture,:idT,:idC,:elec)", array(
                           "heures"=>$prest->heures,
                           "hPrevue"=>$prest->hPrevue,
                           "jour"=>$prest->jour,
                           "debut"=>$prest->debut,
                           "titres"=>$prest->titresRemis,
                           "absence"=>$prest->absence,
                           "elec"=>$prest->elec,
                           "justification"=>$prest->justification,
                           "cloture"=>$prest->cloture,
                           "idT"=>$prest->idT,
                           "idC"=>$prest->idC,
                        ));   
        $id = Model::lastInsertId();
        
        if($idPL != ""){
             Prestation::addPrestedPlan($id, $idPL, $prest->jour);
        }
        if($prest->titresRemis == "E"){
            Prestation::updateTitresE($id);
        }
        
        return $id;
    }
    
    public static function prest_exist($day,$month,$year,$client,$idT){
        $query = self::execute("SELECT * FROM prestations where client_id = :id and travailleur_id = :idT and jour LIKE '$year-$month-$day' ",array("id"=>$client,"idT"=>$idT));
        $data = $query->fetchAll(); 
        if(empty($data)){
            return false;
        }
        else{
            return true;
        }
        
    }
    
    public static function get_plan_multiple($jour,$date,$idT){
        $query = self::execute("SELECT * FROM planning_mensuel_travailleur where travailleur_id = :idT and (jourSemaine = :jour OR jourSemaine ='v') and actif = 1 AND id not in (Select planID from prested_planning where date LIKE '$date')",array("jour"=>$jour,"idT"=>$idT));
        $data = $query->fetchAll(); 
        return $data;
    }
    
    public static function addPrestedPlan($id,$idPL,$date){
        self::execute("INSERT INTO prested_planning(prestID,planID,date)
                           VALUES(:idP,:idPL,:date)", array(
                               "idP"=>$id,
                               "idPL"=>$idPL,
                               "date"=>$date
                            ));  
        
    }
    
//    public static function getDayPlanning($idT,$jour,$dateP){
//        $query = self::execute("Select * FROM planning_mensuel_travailleur where jourSemaine = :day AND travailleur_id=:idT AND actif=1",array("idT"=>$idT,"day"=>$jour));
//        $data = $query->fetchAll(); 
//        
//        return $data;
//    }
//    
       public static function getDayPlanning($idT,$jour){
        $days = [1=>"lundi",2=>"mardi",3=>"mercredi",4=>"jeudi",5=>"vendredi",6=>"samedi"];
        
        $query = self::execute("Select $days[$jour] as nbH FROM contrat where idT = :idT",array("idT"=>$idT));
        $data = $query->fetch(); 
        
        return $data["nbH"];
    }
    
    
            
    public static function updateClient($idP,$idC){
        self::execute("UPDATE prestations SET client_id=:idC,absence=0,justification='' where id=:idP",array("idP"=>$idP,"idC"=>$idC));
        return true;
    }
    
    public static function cloture_mois($idT,$month,$year){
        self::execute("UPDATE prestations SET cloture_mois=:clot where travailleur_id=:idT and jour LIKE '$year-$month-%'",array("idT"=>$idT,"clot"=>1));
        self::execute("INSERT INTO clotured(mois,annee,idT) VALUES(?,?,?)",array($month,$year,$idT));
        $month2 = intval($month)+1;
        if($month == 12){
            $month = 1;
            $year += 1;
        }
        else{
            ++$month;
        }
        $moisCourant = $month.$year;
        self::execute("UPDATE travailleur SET moisCourant=:clot where id=:idT",array("idT"=>$idT,"clot"=>$moisCourant));
        
        return true;
    }
    
   
    
    public static function updateHeures($idP,$newH){
        self::execute("UPDATE prestations SET heures=:newH where id=:idP",array("idP"=>$idP,"newH"=>$newH));
        return true;
    }
    
    public static function getClotured($idT){
        $query = self::execute("SELECT * FROM clotured where idT = :id",array("id"=>$idT));
        $data = $query->fetchAll(); 
        return $data;
    }
    
    
    public static function updateTitres($idP,$nbTR){
        self::execute("UPDATE prestations SET titres_remis=:nbTR,elec=0 where id=:idP",array("idP"=>$idP,"nbTR"=>$nbTR));
        return true;
    }
	
	public static function updateTitresE($idP){
        self::execute("UPDATE prestations SET titres_remis=0,elec=1 where id=:idP",array("idP"=>$idP));
        return true;
    }
    
    public static function updateDebut($idP,$hDebut){
        self::execute("UPDATE prestations SET debut=:hDebut where id=:idP",array("idP"=>$idP,"hDebut"=>$hDebut));
        return true;
    }
	
	public static function updateStatus($idP,$idC){
        self::execute("UPDATE prestations SET status=:idC where id=:idP",array("idP"=>$idP,"idC"=>$idC));
        return true;
    }
	
	public static function prestToAbsence($idP,$just){
        self::execute("UPDATE prestations SET heures=0,titres_remis=0,elec=0,status=0,absence=1,justification=:just,client_id=17 where id=:idP",array("idP"=>$idP,"just"=>$just));
        return true;
    }
    
    public static function deletePrest($idP){
        self::execute("DELETE FROM prestations where id=:idP",array("idP"=>$idP));
        return true;
    }
}