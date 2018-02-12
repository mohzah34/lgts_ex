<?php
require_once 'model/Client.php';
require_once 'model/Travailleur.php';
require_once 'model/Prestation.php';
require_once 'model/Ferier.php';
require_once 'model/Admin.php';
require_once 'model/Chomage.php';
require_once 'model/Vacance.php';
require_once 'framework/View.php';
require_once 'framework/Controller.php';


class ControllerPrestation extends Controller{
   
    public function index(){
        $conMember = $this->get_user_or_redirect();
        if($conMember->type == 3){
                $this->redirect();
        }
        else{
       
            if($conMember->type == 1){
                 $id = filter_input(INPUT_GET, 'id');
            }
            else if($conMember->type == 2){
                $id = $conMember->id;
            }
//            $year = $this->getCurrentDate($id,"y");
//            $month = $this->getCurrentDate($id,"m");
           
            $month = $this->getMoisCourant($id);
            $year = $this->getYearCourant($id);
            $this->prestations($month, $year, $id,$conMember,false);
        }
   }
   
   public function getMoisCourant($id){
        $t = Travailleur::get_travByID($id);
        $nbStr = strlen($t->moisCourant);   
        if($nbStr == 5){
            return substr($t->moisCourant, 0,1);
           
        }
        else if($nbStr == 6){
            return substr($t->moisCourant, 0,2);
            
        }
   }
   
    public function getYearCourant($id){
        $t = Travailleur::get_travByID($id);
        $nbStr = strlen($t->moisCourant);   
        if($nbStr == 5){
            return substr($t->moisCourant, 1,4);
        }
        else if($nbStr == 6){
            return substr($t->moisCourant, 2,4);
        }
   }
   
   public function edit_pastPrest(){
        $conMember = $this->get_user_or_redirect();
        if($conMember->type == 3 || $conMember->type == 2 ){
                $this->redirect();
        }
        else{
            $id = filter_input(INPUT_POST, 'idT');
            $month = filter_input(INPUT_POST, 'month');
            $year = filter_input(INPUT_POST, 'year');
            $this->prestations($month, $year, $id,$conMember,true);
        }
   }
   
   public function getCurrentDate($idT,$value){
       $date = Prestation::get_travailleur_current_month($idT);
       $m = $date['m'];
       $y = $date['y'];
//       $month = $this->fixed_strftime("%m",mktime(0, 0, 0, $m, 1, $y));
       if($value == "m"){
            if($m == 12){
                return 1;

            }
            else{
                return date( "m", strtotime( $y."-".$m."-1" ) )+1;
            }

       }
       else{
            if($m == 12){
              return date( "Y", strtotime( $y."-".$m."-1" ) ) +1; 
            }
            else {
                return date( "Y", strtotime( $y."-".$m."-1" ) ) ; 
            }
       }
   }
   
   public function pastPrest(){
        setlocale (LC_TIME, 'fr_FR.UTF8');
       $conMember = $this->get_user_or_redirect();
       $date = filter_input(INPUT_POST, 'yearSelect')."-".filter_input(INPUT_POST, 'monthSelect');
       $year = substr($date,0,4);
       $month = substr($date,5,2);
       echo var_dump($year);
       echo var_dump($month);
       $date = $this->fixed_strftime("%Y-%m",mktime(0, 0, 0, $month, 1, $year));
       
        if($conMember->type == 2){
                $id = $conMember->id;
        }
        else{
                $id = filter_input(INPUT_POST, 'idT');
        }
       $nb_heures_elec = Prestation::calc_nb_elec($id, $date);
       $prest = Prestation::getPrestOfMonth($id, $date);
       $moisCourant = $this->fixed_strftime("%B %Y",mktime(0, 0, 0, $month, 1, $year));
	
       $clientS = [];
       $scan = [];
       $lastdate = "";
       $nbPlan = 0;
       $nbPlanPrest = 0;
       $titresM = 0;
       $titresRemis = 0;
       $horsPlan = 0;
       foreach($prest as $p){
            $clientS[$p->id] = Client::getClientByID($p->idC);
            $niss = Travailleur::get_travByID($id)->niss;
            if($p->hPrevue != 0){
                $nbPlan += $p->hPrevue;
                $nbPlanPrest += $p->heures;
                $titresRemis += $p->titresRemis;
                if($p->elec != 1){
                    $titresM += $p->heures - $p->titresRemis;
                }
            }
            else{
                $horsPlan += $p->heures;
                $titresRemis += $p->titresRemis;
                if($p->elec != 1){
                    $titresM += $p->heures - $p->titresRemis;
                }
            }
            $scan[$p->id] = $this->analyse_csv($niss,$p->jour,$p->idT,$clientS[$p->id]);
            $lastdate = $p->jour;
       }
        $trav = Travailleur::get_travByID($id);
        $nbVac = Vacance::countVac();
        $nbChom = Chomage::countChom();
        $nbComs = Travailleur::countComs();
      (new View("pastPrest"))->show(array("nbChom"=>$nbChom,"nbVac"=>$nbVac,"nbComs"=>$nbComs,"month"=>$month,"year"=>$year,"nb_elec"=>$nb_heures_elec,"nbPlanPrest"=>$nbPlanPrest,"nbPlan"=>$nbPlan,"titresM"=>$titresM,"titresRemis"=>$titresRemis,"horsPlan"=>$horsPlan,"trav"=>$trav,"scanner"=>$scan,"prest"=>$prest,"clientS"=>$clientS,"member"=>$conMember,"moisCourant"=>$moisCourant));  
   }
   
   public function analyse_csv($niss,$date,$idT,$client){
       $jour = $this->fixed_strftime("%e%m%g", strtotime($date));
       $prestsDay = Prestation::getPrestDay2($date, $idT);
       $nb = 0;
       $arr = [];
       
       foreach($prestsDay as $p){
           //echo $p->heures;
           $nb = $nb + $p->heures;
       }
       $nbScan = Admin::get_prest_scanner($jour, $niss,$client->numClient);
       $arr["nbHeures"] = $nb;
       $arr["nbTitres"] = $nbScan;
       return $arr;
    }
   
   public function find_trav(){
       $keyword = filter_input(INPUT_POST, 'keyword');
       $results = Travailleur::find_trav($keyword);
       
       return (new View("resultTrav"))->show(array("results"=>$results));
   }
   
   public function find_client(){
       $keyword = filter_input(INPUT_POST, 'keyword');
       $results = Client::find_client($keyword);
       
       return (new View("resultClient"))->show(array("results"=>$results));
   }
   
   public function prestations($month,$year,$id,$conMember,$isClotured){
	  // setlocale(LC_TIME, 'FR_fr');    
	   //setlocale(LC_ALL, 'fr_FR');
	   setlocale (LC_TIME, 'fr_FR.UTF8');	  
       $nbDay = cal_days_in_month(CAL_GREGORIAN, $month, $year);
       $format = '%e-%a';
       $format2 = "%w";
       $planning = [];
       $lesDates = [];
       $prests =[];
       $nbPlans = 0;
       $nbNoPlans = 0;
       $clientsP = [];
       $prestNoPlan = [];
       $nbHeuresPrestPlan = 0;
       $jFerier = Ferier::getAllJours();
       $ferier = [];
       $nbHeures = 0;
       $nbTitresE = 0;
       $nbTitres = 0;
       $prestation = [];
       for($myDay=1; $myDay <= $nbDay; ++$myDay){
            $jour = $this->fixed_strftime($format2, mktime(0, 0, 0, $month, $myDay, $year));
            $dateP = $this->fixed_strftime("%Y-%m-%d", mktime(0, 0, 0, $month, $myDay, $year));
            
            if($jour != 0 && !Prestation::isFerier($myDay,$month,$year)){
                $planning[$myDay] = Prestation::getDayPlanning($id, $jour);
            }
            $prestation[$myDay] = Prestation::getPrestDay($dateP, $id);
            if(!Prestation::isFerier($myDay,$month,$year)){ 
                $nbPlans += $planning[$myDay];
            }
            
            foreach($prestation[$myDay] as $p){
                $nbHeures += $p->heures;
                $clientsP[$p->id] = Client::getClientByID($p->idC);
                if(!$p->elec){
                    $nbTitres += $p->titresRemis;
                }
                else{
                   $nbTitresE += $p->heures;
                }
            }
            
            $d = $this->fixed_strftime($format, mktime(0, 0, 0, $month, $myDay, $year));
            $lesDates[] = $d;
          
            foreach($jFerier as $jf){
                if($dateP == $jf->date){
                    $ferier[$d] = $jf;
                }
                
            }
       }
       $clotured = Prestation::getClotured($id);
       $clientsT = Client::get_clientsTrav($id);
       $trav = Travailleur::get_travByID($id);
       $comments = Travailleur::commentaires($trav->id);
       $moisCourant = $this->fixed_strftime("%B %Y", mktime(0, 0, 0, $month, 1, $year));
       
       $nbH = $nbHeures - $nbTitresE;
       $nbTitresM = $nbH - $nbTitres;
       $nbVac = Vacance::countVac();
        $nbChom = Chomage::countChom();
        $nbComs = Travailleur::countComs();
     
      (new View("prestation"))->show(array("nbChom"=>$nbChom,"nbVac"=>$nbVac,"nbComs"=>$nbComs,"prestation"=>$prestation,"nbTitresE"=>$nbTitresE,"isclotured"=>$isClotured,"heurePlanPrest"=>$nbHeuresPrestPlan,"nbNoPlans"=>$nbNoPlans,"nbPlans"=>$nbPlans,"totalTitresM"=>$nbTitresM,"totalTitres"=>$nbTitres,"totalHeures"=>$nbHeures,"ferier"=>$ferier,"prestNoPlan"=>$prestNoPlan,"clientP"=>$clientsP,"prest"=>$prests,"dates"=>$lesDates,"myClients"=>$clientsT,"idT"=>$id,"year"=>$year,"month"=>$month,"nbDay"=>$nbDay,"planning"=>$planning,"member"=>$conMember,"trav"=>$trav,"comments"=>$comments,"clotured"=>$clotured,"moisCourant"=>$moisCourant));
   }
   
  
   
    public function getTotalTitresE($prest){
       $nbHeures = 0;
       foreach($prest as $p1){
           if($p1->elec == 1){
               $nbHeures += $p1->heures;
           }
           
       }
       return $nbHeures;
    }
   public function getTotalHeures($prest){
       $nbHeures = 0;
       foreach($prest as $p1){
           $nbHeures += $p1->heures;
           
       }
    
       return $nbHeures;
   }
   
   public function getTotalHeuresPlanning($prest){
       $nbHeures = 0;
       foreach($prest as $p1){
           $nbHeures += $p1->heures;
           
       }
       return $nbHeures;
   }
   
   public function getTotalTitresM($prest){
       $nbTitresM = 0;
       foreach($prest as $p1){
           if($p1->elec == 0){
                $tot = $p1->heures - $p1->titresRemis;
                if($tot != 0){
                    $nbTitresM += $tot;
                }
           }
       }
       return $nbTitresM;
   }
   
   public function getTotalTitres($prest){
       $nbTitres = 0;
       foreach($prest as $p1){
           if(!$p1->elec){
            $nbTitres += $p1->titresRemis;
           }
       }
       return $nbTitres;
   }
   
   public function workit($arr,$myDay,$dateP){
       $prests = [];
       for($i = 0; $i < sizeOf($arr); ++$i){
            $pl = $arr[$i];
            if(Prestation::isPrested($pl['id'], $dateP)){
                if(!empty($pl)){
                    $arr[$i] = intval($pl['id']);
                    $idPlan = Prestation::getPrestedID($arr[$i],$dateP);
                    $prestIDs = Prestation::getPrestPlanning($idPlan);
                    $prests[$idPlan] = Prestation::get_prest($prestIDs['prestID']);
                     
                }
            }
        }
        return $prests;
   }
   
   
    

   public function newP(){
        $day = $_POST['day'];
        $month = $_POST['month'];
        $year = $_POST['year'];
        $client = $_POST['client'];
        $heures = $_POST['heures'];
        $debut = $_POST['debuts'];
        $titresR = $_POST['titreR'];
        $idT = $_POST['idT'];
        $hPrevue = $_POST['hPrevue'];
        $idPL = $_POST['idPL'];
        $dateS = $this->fixed_strftime("%Y-%m-%d",mktime(0, 0, 0, (int)$month, $day, (int)$year));
        if($titresR == "E"){
            $newID = Prestation::add_prest(new Prestation("",$heures,$hPrevue,$dateS,$debut,0,1,0,0,"",0,$idT,$client),$idPL);
        }
        else{
            $newID = Prestation::add_prest(new Prestation("",$heures,$hPrevue,$dateS,$debut,$titresR,0,0,0,"",0,$idT,$client),$idPL);
        }
        
        
        echo $newID;
    }
   
   
     public function newPAbsence(){
        $day = $_POST['day'];
        $month = $_POST['month'];
        $year = $_POST['year'];
        $client = $_POST['client'];
        $idT = $_POST['idT'];
        $idPL = $_POST['idPL'];
        $hPrevue = $_POST['hPrevue'];
        $dateS = $this->fixed_strftime("%Y-%m-%d",mktime(0, 0, 0, (int)$month, $day, (int)$year));
        $newID = Prestation::add_prest(new Prestation("",0,$hPrevue,$dateS,0,0,0,0,1,$client,0,$idT,0),$idPL);
      
        echo $newID;
   }
   
   
   public function get_nbHeures_plan(){
       $id = $_POST['idP'];
       echo Prestation::get_nbHeuresPlan($id);
   }
   
   
   public function prest_exist(){
        $day = $_POST['day'];
        $month = $_POST['month'];
        $year = $_POST['year'];
        $client = $_POST['client'];
        $trav = $_POST['trav'];
        if( Prestation::prest_exist($day,$month,$year,$client,$trav) ){
            echo "true";
        }
        else{
            echo "false";
        }
   }
   
   public function getDayPrest(){
       $day = $_POST['day'];
       $idT = $_POST['idT'];
       $date = $this->fixed_strftime("%Y-%m-%d",mktime(0, 0, 0, 1, $day, 2017));
       $prests = Prestation::getPrestDay($date, $idT);
       $clientsT = Client::get_clientsTrav($idT);
       $rows = [];
       
       $firstEntry = false;
       $nb = 1;
       foreach($prests as $p){
           if($nb == 1){
               $firstEntry = true;
           }
           $clientS = Client::getClientByID($p->idC);
           $rows[] = (new View("prestedRow"))->show(array("p"=>$p,"day"=>$day,"month"=>1,"year"=>2017,"myClients"=>$clientsT,"idP"=>$p->id,"clientS"=>$clientS,"firstEntry"=>$firstEntry));
           ++$nb;
           if($firstEntry){
               $firstEntry = false;
           }
       }
       return json_encode($rows); 
   }
   
   public function cloture(){
        $conMember = $this->get_user_or_redirect();
        if($conMember->type == 3){
                $this->redirect();
        }
        else{
            $id = filter_input(INPUT_POST, 'travID');
            $month = filter_input(INPUT_POST, 'month');
            $year = filter_input(INPUT_POST, 'year');

            Prestation::cloture_mois($id, $month, $year);
            
            if($conMember->type == 2){
                $this->index();
            }
            else if($conMember->type == 1){
                $year = $this->getCurrentDate($id,"y");
                $month = $this->getCurrentDate($id,"m");
                $this->prestations($month, $year, $id,$conMember);
            }
            
        }
        
       
   }
   
   
  
   public function getPlanning($idT,$jour,$dateP){
      return Prestation::getDayPlanning($idT, $jour,$dateP);
   }
   
   public function updateC(){
       $idC = filter_input(INPUT_POST, 'idC');
       $idP = filter_input(INPUT_POST, 'idP');
       Prestation::updateClient($idP, $idC);
   }
   
   
   public function updateH(){
       $newH = filter_input(INPUT_POST, 'newH');
       $idP = filter_input(INPUT_POST, 'idP');
       Prestation::updateHeures($idP, $newH);

   }
   
   public function updateD(){
       $hDebut = filter_input(INPUT_POST, 'hDebut');
       $idP = filter_input(INPUT_POST, 'idP');
       Prestation::updateDebut($idP, $hDebut);

   }
   
   public function updateT(){
       $nbTR = filter_input(INPUT_POST, 'nbTR');
       $idP = filter_input(INPUT_POST, 'idP');
       Prestation::updateTitres($idP, $nbTR);
	   //$oldT = filter_input(INPUT_POST, 'oldTitreR');
	   //$prest = Prestation::get_prest($idP);
	   //$titresC = Client::getClientByID($prest->idC)->titres;
	   
	   //if($oldT > $nbTR){
		//  $nbTR = $nbTR - $oldT;
	   //}
	   //else{
		//   $nbTR = $oldT - $nbTR;
	   //}
       //$val = $titresC - $nbTR;
       //Client::updateTitres($prest->idC, $val);
   }
   public function updateTE(){
       $idP = filter_input(INPUT_POST, 'idP');
       Prestation::updateTitresE($idP);
   }
   
   public function updateS(){
       $idC = filter_input(INPUT_POST, 'idC');
       $idP = filter_input(INPUT_POST, 'idP');
       Prestation::updateStatus($idP, $idC);
   }
   
    public function prestToAbsence(){
       $idC = filter_input(INPUT_POST, 'idC');
       $idP = filter_input(INPUT_POST, 'idP');
       Prestation::prestToAbsence($idP, $idC);
   }
   
   
   
   public function deletePrest(){
       $idP = filter_input(INPUT_POST, 'idP');
       Prestation::deletePrest($idP);
   }
    
  public function get_planning_multiple(){
   
    $idT = filter_input(INPUT_POST, 'trav');
    $day = filter_input(INPUT_POST, 'day');
    $month = filter_input(INPUT_POST, 'month');
    $year = filter_input(INPUT_POST, 'year');
    $date = $year."-".$month."-".$day;
    $jour = $this->fixed_strftime("%u",mktime(0, 0, 0, $month, $day, $year));
    
    echo json_encode(Prestation::get_plan_multiple($jour,$date,$idT));
    
  }
  
  
  public function get_disabled_dates(){
    $idT = filter_input(INPUT_POST, 'trav');
    $month = filter_input(INPUT_POST, 'month');
    $year = filter_input(INPUT_POST, 'year');
    $idC = filter_input(INPUT_POST, 'client');
    $prest =  Prestation::getPrestClient_fromTravailleur($idC,$idT,$month,$year);
    $ferier = Prestation::getJoursFerierOfMonth($month,$year);
    $dates = [];
    foreach($prest as $p){
        $dates[] = $p["day"].'-'.$p["month"].'-'.$p["year"];
    }
    foreach($ferier as $f){
        $dates[] = $f["day"].'-'.$f["month"].'-'.$f["year"];
    }
    
    echo json_encode($dates);
  }
  
}  
