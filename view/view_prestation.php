<?php
setlocale(LC_TIME, 'FR_fr');    
setlocale(LC_ALL, 'fr_FR');
?>
<!DOCTYPE html>
<html lang="fr">
<head>

    <meta charset="UTF-8">
    <base href="<?= $web_root ?>"/>
    <meta name="viewport" content="initial-scale=1.0001, minimum-scale=1.0001, maximum-scale=1.0001, user-scalable=no"/>

    <!-- Bootstrap -->
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">


    <!-- Css -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800" rel="stylesheet">
    
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css"/>
<!--    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/css/bootstrap-datepicker.css"/>-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/css/bootstrap-datepicker3.css"/>
    
    
    <link href="css/style_desktop.css" type="text/css" rel="stylesheet"/>
    <link href="css/style_mobile.css" type="text/css" rel="stylesheet"/>
    <link href="css/style_print.css" type="text/css" rel="stylesheet" media="print">
    

    <title>LGTS - Extranet</title>
   
</head>

<body>
	<div id="main_content">
		<header class="row">
			<div class="bt_open_nav"><i class="fa fa-bars" aria-hidden="true"></i></div>
			<div class="date_content">
				
			</div>
		</header>
		
		<div class="bt_close_nav"></div>
		
		<div id="menu_content">
			<?php if($member->type == 1): ?>
                            <?php require_once "menuEmployeur.php" ?>
                         <?php else: ?>
                            <?php require_once "menuTravailleur.php" ?>
                         <?php endif; ?>
		</div>

		<div class="content">
                    <div class="wrapper">
                        <?php if(!$isclotured): ?>
                    <div class="row no_print">
                        <?php if($member->type == 1):?>
                        <div class="col-xs-12 margin_bottom">
                            <div class="inner">
                                <h2>Clients associés</h2>
                                <ul id="listeCA">
                                    <?php foreach($myClients as $ca): ?>
									
                                        <li>
										<?php if($member->type == 1): ?>
										<i onclick="delAss(<?=$ca->id?>,<?=$trav->id?>,$(this))" style="color:red;" class="fa fa-minus-circle" aria-hidden="true"></i>
										<?php endif; ?>
										<?=$ca->nom?> <?=$ca->prenom?></li>
                                    <?php endforeach;?>
                                </ul>
								<input type="text" placeholder="Recherche par nom / prenom ,n° client" id="seekClient"/>
                            
								<div id="resultClient">
									
								</div>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if(!$isclotured): ?>
                        <div class="col-xs-12 margin_bottom">
                            <div class="inner">
                                <h2>Données du travailleur</h2>
                                <ul>
                                    <li></li>
                                        <li><?=$trav->nom?> <?=$trav->prenom?></li>
                                        <li>NISS : <?=$trav->niss?></li>
                                </ul>
                            </div>
                        </div>
                        <?php endif;?>
                    </div>
                        <?php endif;?>
                            <div class="col-xs-12 margin_bottom">
                                
                                    <h2><?=$moisCourant?> - <?=$trav->nom ?> <?=$trav->prenom ?>  <?php if($isclotured): ?> ( Clôturé )<?php endif;?></h2>
                                   
                              
                            </div>
				
				<div class="row no_print">
					<div class="col-xs-8 margin_bottom col_mobile">
                                          <?php if(!$isclotured): ?>
                                           
                                                
                                                <div class="navigator">
                                                <button id="multiple_terminer" style="margin-top:20px;display:none;background-color:red !important;" class="btn btn-outline-primary"><i class="fa fa-times" aria-hidden="true"></i>Annuler</button> 
                                                <button id="multiple_finish" style="margin-top:20px;display:none;background-color:green !important;" class="btn btn-outline-primary"><i class="fa fa-check" aria-hidden="true"></i>Confirmer</button> 
                                                </div>
						<a class="btn btn-primary" href="Travailleur/modifier/<?=$trav->id?>">Signalétique</a>
						
                                            
                                                    <a class="btn btn-primary" href="Admin/planning/<?=$trav->id?>">Planning</a>
                                                    <form style="display: inline;" method="post" name="clotureMois" action="Prestation/cloture/">
                                                        <input type="hidden" name="travID" value="<?=$trav->id?>"/>
                                                        <input type="hidden" name="month" value="<?=$month?>"/>
                                                        <input type="hidden" name="year" value="<?=$year?>"/>

                                                        <input type="submit" name="cloture" class="btn btn-primary" value="Clôturer le mois">


                                                    </form>
                                                <button id="multiple_btn" class="btn btn-outline-primary"><i class="fa fa-plus" aria-hidden="true"></i>Multiple</button> 
                                                
                                            <?php endif;?>
                                           
                                            <button onclick="printMois()" class="btn btn-outline-primary"><i class="fa fa-print" aria-hidden="true"></i>Imprimer</button>
					</div>
                                    
                                   
					
					
					<div class="col-xs-4 margin_bottom search_month col_mobile">
                                            <?php if(!$isclotured): ?>
						<span>Mois clôturé : </span>
                                                <form method="POST" action="Prestation/pastPrest">
                                                    <input type="hidden" name="idT" value="<?=$trav->id?>"/>
                                                        <select name="monthSelect" class="selectpicker">
                                                            <option disabled selected >Mois</option>
                                                            <option value="1">Janvier</option>
                                                            <option value="2">février</option>
                                                            <option value="3">Mars</option>
                                                            <option value="4">Avril</option>
                                                            <option value="5">Mai</option>
                                                            <option value="6">Juin</option>
                                                            <option value="7">Juillet</option>
                                                            <option value="8">Août</option>
                                                            <option value="9">Septembre</option>
                                                            <option value="10">Octobre</option>
                                                            <option value="11">Novembre</option>
                                                            <option value="12">Décembre</option>
                                                        </select>
                                                        <select name="yearSelect" class="selectpicker">
                                                            <option disabled selected >Année</option>
                                                            <option value="2017">2017</option>
                                                            <option value="2018">2018</option>
                                                            <option value="2019">2019</option>
                                                            <option value="2020">2020</option>
                                                            <option value="2021">2021</option>
                                                            <option value="2022">2022</option>
                                                        </select>
<!--                                                    <select name="pastPrest" class="selectpicker">
                                                        <option>-</option>
                                                        <?php //if(!empty($clotured)):?>
                                                            <?php //foreach($clotured as $pp): ?>
                                                                <option value="<?=$pp['annee']?>-<?=$pp['mois']?>"><?=$pp['annee']?>-<?=$pp['mois']?></option>
                                                            <?php //endforeach;?>
                                                        <?php //endif;?>
                                                    </select>-->
                                                    <input type="submit" value="Voir" class="btn btn-outline-primary">
                                                </form>
                                             <?php else: ?>
                                                 <form method="POST" action="Prestation/pastPrest">
                                                    <input type="hidden" name="idT" value="<?=$trav->id?>"/>
                                                    <input type="hidden" name="monthSelect" value="<?=$month?>"/>
                                                    <input type="hidden" name="yearSelect" value="<?=$year?>"/>
                                                    
                                                    
                                                    <input type="submit" value="Retour" class="btn btn-outline-primary">
                                                </form>
                                             <?php endif;?>
					</div>
                                    
				</div>
                         
                                <div style="display:none;margin-left: 30%;width: 50%;text-align:center;" class="row multiple_div">
                                    <div style="display:block;" class="col-xs-6 step-1" >
                                        <p>Veuillez séléctionner le client ou un code et les dates pour l'encodage multiple :</p>
                                        <select id='multiple_client'>
                                            <optgroup label="Clients">
                                                <option value='0'>Sélectionner un client ou un code  </option>
                                                <?php foreach($myClients as $c): ?>
                                                    <option value="<?=$c->id?>"><?=$c->nom?> <?=$c->prenom?></option>
                                                <?php endforeach; ?>   
                                            </optgroup>
                                            <optgroup label="Absences">
                                                <option value="AJ">absence justifié</option>
                                                <option value="M">maladie</option>
                                                <option value="V">vacances</option>
                                                <option value="CE">chômage économique</option>
                                                <option value="AU">absence utilisateur</option>
                                                <option value="JF">jour ferié ou de remplacement</option>
                                                <option value="AT">autre</option>
                                            </optgroup>
                                        </select>
                                        <div id="dates_multiple" data-date="12/03/2017"></div>
                                        <div id="result_multiple">
                                            
                                        </div>
                                        
                                        <button id="multiple_confirm" style="margin-top:20px;background-color:green !important;" class="btn btn-outline-primary"><i class="fa fa-arrow-right" aria-hidden="true"></i>Continuer</button> 
                                        
                                    </div>
                                    
                                </div>
                        
                            <div id="multiple_table" style="display:none;" class="table_content margin_bottom">
                                    <div class="row t_body">
                                            
                                            <div class="col-xs-1 column">
                                                    <h5>Date</h5>
                                            </div>
                                            <div class="col-xs-2 column">
                                                    <h5>Client</h5>
                                            </div>
                                            <div class="col-xs-1 column">
                                                    <h5>Nb. Heures</h5>
                                            </div>
                                            <div class="col-xs-2 column">
                                                    <h5>Debut</h5>
                                            </div>
                                            <div class="col-xs-1 column">
                                                    <h5>Titres remis</h5>
                                            </div>
                                            <div class="col-xs-2 column">
                                                    <h5>Planning</h5>
                                            </div>
                                            
											
                                    </div><!--- ROW T BODY END -->

                                    <div class="row t_content">
                                        <?php include 'multiple_row.php'; ?>
                                       
                                      
                                    </div><!---row T content end-->
                                    
					
					
				</div><!-- TABLE CONTENT END -->
                                
				<div id="current_table" class="table_content margin_bottom">
                                    <div class="row t_body main_body">
                                            
                                            <div class="col-xs-1 column">
                                                    <h5>Date</h5>
                                            </div>
                                            <div class="col-xs-2 column">
                                                    <h5>Client</h5>
                                            </div>
                                            <div class="col-xs-1 column">
                                                    <h5>Nb. Heures</h5>
                                            </div>
                                            <div class="col-xs-2 column">
                                                    <h5>Plage horaire</h5>
                                            </div>
                                            <div class="col-xs-1 column">
                                                    <h5>Titres remis</h5>
                                            </div>
                                            <div class="col-xs-2 column">
                                                    <h5>Titres manquantes</h5>
                                            </div>
											
                                            <div class="col-xs-1 column">
                                                    <h5>Status</h5>
                                            </div>
											
                                    </div><!--- ROW T BODY END -->

                                    <div class="row t_content">
                                        
                                       
                                        <?php $cpt = 1; $rowId = 45;  $nbDay = 1;?>
                                        <?php foreach($dates as $d): ?>
                                            
                                            <?php 
                                                
                                                $planDay = strftime("%w",mktime(0, 0, 0, $month, $cpt, $year));
                                                $firstEntry = true;  
                                                
                                            ?>
                                            <?php if(!empty($prestation[$nbDay])) : ?>
                                                <?php foreach($prestation[$nbDay] as $prestJour):?>
                                                    <?php $idP = $prestJour->id;  ?>
                                                    <?php include 'prestedROWS.php'; ?>
                                                    <?php $firstEntry = false;  ?>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <?php include 'blankRow.php'; ?>
                                            <?php endif; ?>
                                           <?php ++$cpt;  ++$nbDay;?>
                                        <?php endforeach; ?>
                                         
                                     
                                     
                                    <input hidden id="idT" type="text" value="<?=$idT?>"/>
                                    <input hidden id="nbDay" type="text" value="<?=$nbDay?>"/>
                                    <input hidden id="typeM" type="text" value="<?=$member->type?>"/>
                                    <div class="col-xs-12 total_table">
                                        <p>Total Heures Planning : <strong> <?= $nbPlans ?>  </strong>&#124; Total heures prestées : <strong><?= $totalHeures ?></strong> &#124; Titres remis : <strong><?= $totalTitres ?></strong> &#124; Titres manquants : <strong><?= $totalTitresM ?></strong> &#124; Électronique : <strong><?= $nbTitresE ?></strong></p>
                                    </div>
                                    </div><!---row T content end-->
                                    
					
					
				</div><!-- TABLE CONTENT END -->
                                <?php if($member->type ==2): ?>
                                    <div class="col-xs-12 margin_bottom no_print comment">
                                        <div class="inner comment_bloc al_right no_padding comment_list">
                                            <div class="padding">
                                                <form method="post" action="Travailleur/add_comment">
                                                    <h2>Rédiger un commentaire</h2>
                                                    <textarea placeholder="Insérez votre commentaire..." name="texte"></textarea>
                                                    <input type="hidden" name="idTrvC" value="<?=$trav->id?>"/>
                                                    <input type="submit" name="commentaire_prestation" value="Envoyer commentaire" class="btn btn-primary">
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
				
				<div class="col-xs-12 margin_bottom no_print">
                                    <div class="inner no_padding comment_list no_print">
                                        <?php if(!empty($comments)):?>
                                            <?php foreach($comments as $com): ?>
                                                <div class='comment'>
                                                    <p><?=$com['texte']?></p>
                                                    <p class="title_comment">Commentaire du <?=$com['ladate']?></p>
                                                </div>

                                            <?php endforeach;?>
                                        <?php endif;?>
                                    </div>
                                </div>
                                
                         
                           <div     
                                
			</div><!-- WRAPPER END -->
		</div><!-- CONTENT END -->
	</div><!-- MAIN CONTENT END -->

     
           

                     

                

<script>
	function printMois() {
		window.print();
	}
</script>
<!-- External libraries jQuery -->
    <script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
    <script src="js/encodage_multiple.js"></script>
    
	
<!-- Bootstrap -->


<!--


<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.min.js"></script>
<script src="js/bootstrap-datepicker.fr.min.js"></script>



	
<!-- DATATABLE FUNC -->
	<script src="js/custom-datatable.js"></script>
	<script src="https://cdn.rawgit.com/leafo/sticky-kit/v1.1.2/jquery.sticky-kit.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/4.4.0/bootbox.min.js"></script>
        

<!-- Height window -->

 <style>
        
        
    </style>
  

<!-- Main js -->
    <script type="text/javascript" src="js/main.js"></script>

<!-- Placeholder -->
    <script src="js/placeholders.min.js"></script>


</body>

</html>