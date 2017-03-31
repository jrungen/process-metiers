<?php

Script::init(array('content'=>'text/plain'));
include dirname(dirname(dirname(__FILE__))).'/asset/class/r03typemouvement.class.php';
include dirname(dirname(dirname(__FILE__))).'/asset/class/r04roletiers.class.php';
include dirname(dirname(dirname(__FILE__))).'/asset/class/candidat.class.php';
include dirname(dirname(dirname(__FILE__))).'/asset/class/a00personnephysique.class.php';
include dirname(dirname(dirname(__FILE__))).'/asset/class/r16statutpap.class.php';
include dirname(dirname(dirname(__FILE__))).'/asset/class/TacheHelper.class.php';
include dirname(dirname(dirname(__FILE__))).'/asset/class/Mvmt.class.php';
include dirname(dirname(dirname(__FILE__))).'/asset/class/MvmtDRH.class.php';
include dirname(dirname(dirname(__FILE__))).'/asset/class/MvmtDRI.class.php';
include dirname(dirname(dirname(__FILE__))).'/asset/class/MvmtDSI.class.php';
include dirname(dirname(dirname(__FILE__))).'/asset/class/MvmtManager.class.php';
include dirname(dirname(dirname(__FILE__))).'/asset/class/demande.class.php';
include dirname(dirname(dirname(__FILE__))).'/asset/class/a07postesbudgetaires.class.php';
include dirname(dirname(dirname(__FILE__))).'/asset/class/actionMvmt.class.php';

// Création fiche PP manuellement
if($_REQUEST['mode']=='getKey'){
	exit(PersonnePhysique::generateKey());
}

//Bouton génération des mvmts depuis la fiche PP
if($_REQUEST['mode']=='mouvement'){
	
	$personnePhysique = PersonnePhysique::findByCle($_REQUEST['cle']);
	$personnePhysique->set_roleTiers( $_REQUEST['role_tiers']);
	$personnePhysique->set_detailMouvement($_REQUEST['detail_mouvement']);
	$personnePhysique->set_dateEffet($_REQUEST['date_effet']);
	$personnePhysique->set_materielInformatique($_REQUEST['materiel_informatique']);
	$personnePhysique->set_bureau($_REQUEST['bureau']);
	$personnePhysique->set_adresseMessagerie($_REQUEST['adresse_messagerie']);
	
	$personnePhysique->generateMvmt($_REQUEST['type_mouvement']);
	// TODO JR le 11/01/2017 : Comment remplir les valeurs r00societes
}

// Création de la personne physique et génération des mouvements depuis le candidat selectioné.
if($_REQUEST['mode']=='createItems'){
	// récupérer les données du candidat sélectionné
	$candidat = Candidat::findById($_REQUEST['idcandidat']);
	
	// Création de la personne physique
	$personnePhysique = new PersonnePhysique($candidat);
	$personnePhysique->create();
}

?>