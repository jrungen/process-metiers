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
include dirname(dirname(dirname(__FILE__))).'a07postesbudgetaires.class.php';

// Création fiche PP manuellement
if($_REQUEST['mode']=='getKey'){
	exit(PersonnePhysique::generateKey());
}

//@deprecated Création des mvmts depuis la fiche PP
if($_REQUEST['mode']=='mouvement'){
	createPpMvmt();
}

//Création de la fiche PP et de ses mvmts
if($_REQUEST['mode']=='createItems'){
	createPpAndMvmt();
}

function createPpAndMvmt(){

	// récupérer les données du candidat sélectionné
	$idcandidat = $_REQUEST['idcandidat'];
	$candidat = Candidat::findById($idcandidat);
	
	// Création de la personne physique
	$personnePhysique = new PersonnePhysique($candidat);
	$personnePhysique->create();
	
	// récupérer le référentiel des tâches pour savoir quel sont les tâches à créer.
	$data_r02listetaches = TacheHelper::get_refTaches($personnePhysique->get_roleTiers(),TypeMvmt::ARRIVEE);
		
	
	/*-----------------------------------*/
	
	
	/*
	 * Création mouvement DRH
	 */
	$mvmtDrhEntree = new MvmtDRH($personnePhysique,TypeMvmt::ARRIVEE);
	$mvmtDrhEntree->create();
	
	// Sortie si <> CDI
	if ($candidat->get_typecontratGRE() !== 'CTT001'){
			$mvmtDrhSortie = new MvmtDRH($personnePhysique,TypeMvmt::DEPART);
			$mvmtDrhSortie->create();
	}
	
	/*-----------------------------------*/
	
	/*
	 * Création Rôle Tiers
	 */
	/* A SUPPRIMER */
	try{
	
		$getLastKey = Script::$db->prepare("SELECT cle FROM a06roles ORDER BY cle DESC LIMIT 1");
		$getLastKey->execute();
		$lastKey = $getLastKey->fetchColumn();
		$getLastKey->closeCursor();
	
		if($lastKey === FALSE)
		{
			$lastNumber="R00000001";
		}
	
		$lastNumber = (int) substr($lastKey, 1); // retire "R", il reste : [compteur] , qui est une valeur numérique
		$lastNumber = $lastNumber+1;
		$lastNumber = str_pad($lastNumber,8,'0',STR_PAD_LEFT);
		$role_cle = "R".$lastNumber;
	
		// Requete INSERT INTO
		$query = "INSERT INTO
						a06roles
					(cle, a00personnephysique, r04roletiers,
					creation_par, date_creation, heure_creation, modification_par, date_modification, heure_modification)
					values
					('".$role_cle."','".$personnePhysique->get_cle()."', 'ROL001',
					'candidat', CURDATE(), CURTIME(), 'candidat', CURDATE(), CURTIME() )
					";
	
		// on va chercher tous les enregistrements de la requ?te
		$result=Script::$db->prepare($query);
		$result->execute();
	
	}
	catch(PDOException  $e){
		$errMsg = $e->getMessage();
		echo $errMsg;
	}
	
	/*-----------------------------------*/
	
	/*
	 * Création mouvement DRI
	 */
	$mvmtDriEntree = new MvmtDRI($mvmtDrhEntree);
	$mvmtDriEntree->create();
	
	
	/*
	 * Création mouvement DSI
	 */
	$mvmtDsiEntree = new MvmtDSI($mvmtDrhEntree);
	$mvmtDsiEntree->create();
	
	/*-----------------------------------*/
	
	
	/*
	 * Création tâches
	 */
	echo '-> Création tâches='.sizeof($data_r02listetaches);
	try{
	
		$i = 1;
		foreach ($data_r02listetaches as $r02listetache) {
			$getLastKey = Script::$db->prepare("SELECT cle FROM a04taches ORDER BY cle DESC LIMIT 1");
			$getLastKey->execute();
			$lastKey = $getLastKey->fetchColumn();
			$getLastKey->closeCursor();
	
			if($lastKey === FALSE)
			{
				$lastNumber = "T000000001";
			}
	
			$lastNumber = (int) substr($lastKey, 3); // retire "MVT", il reste : [compteur] , qui est une valeur numérique
			$lastNumber = $lastNumber+1;
			$lastNumber = str_pad($lastNumber,9,'0',STR_PAD_LEFT);
			$tache_cle = "T".$lastNumber;
	
			// Creation de la requete
	
			// Associer la tâches suivant l'entrée sortie (DRH, DRI ou DSI)
			$entreesortie = '';
			$allocation = '';
				
			if($r02listetache->r01allocationtache == 'DRH') {
				$entreesortie = 'a05typemouvement';
				$allocation = 'drh';
	
				$query = "	INSERT INTO a04taches
							( cle, r02listetaches, a04statuttache, a00personnephysique, utilisateur, r01allocationtache, ".$entreesortie." , creation_par, date_creation, heure_creation, modification_par, date_modification, heure_modification)
							values ('".$tache_cle."','".$r02listetache->cle."', 'Non Traité','".$personnePhysique->get_cle()."','".$r02listetache->utilisateur."','".$r02listetache->r01allocationtache."','".$mvmtDrhEntree->get_cle()."', 'candidat', CURDATE(),CURTIME(), 'candidat', CURDATE(),  CURTIME() );
							";
			}
				
			if ( ($r02listetache->r01allocationtache == 'DRI') || ($r02listetache->r01allocationtache == 'DSI') ){
				if($r02listetache->r01allocationtache == 'DRI'){
					$entreesortie = 'a02entreessortiesdri';
					$allocation = 'dri';
				}
	
				if($r02listetache->r01allocationtache == 'DSI'){
					$entreesortie = 'a03entreessortiesdsi';
					$allocation = 'dsi';
				}
	
				$query = "	INSERT INTO a04taches
							( cle, r02listetaches, a04statuttache, a00personnephysique, a05typemouvement, utilisateur, r01allocationtache, ".$entreesortie." , creation_par, date_creation, heure_creation, modification_par, date_modification, heure_modification)
							values ('".$tache_cle."','".$r02listetache->cle."', 'Non Traité','".$personnePhysique->get_cle()."','".$mvmtDrhEntree->get_cle()."','".$r02listetache->utilisateur."','".$r02listetache->r01allocationtache."','".$mvmtDrhEntree->get_cle()."_".$allocation."', 'candidat', CURDATE(),CURTIME(), 'candidat', CURDATE(),  CURTIME() );
							";
			}
			// on va chercher tous les enregistrements de la requ?te
			$result=Script::$db->prepare($query);
			$result->execute();
	
			$i++;
		}
	
	}
	catch(PDOException  $e){
		$errMsg = $e->getMessage();
		echo $errMsg;
	}
	/*-----------------------------------*/

}

function createPpMvmt(){

	$personnephysique_cle = $_REQUEST['cle'];
	$role_tiers = $_REQUEST['role_tiers'];
	$mouvement = $_REQUEST['mouvement'];
	
	try{
		// Creation de la requete
		$query = "SELECT * FROM a00personnephysique WHERE cle = '".$personnephysique_cle."'";
	
		// on va chercher tous les enregistrements de la requete
		$result=Script::$db->prepare($query);
		$result->execute();
	
		// on dit qu'on veut que le resultat soit recuperable sous forme de tableau
		$data_pp = $result->fetchAll((PDO::FETCH_OBJ));
	
		// on ferme le curseur des r?sultats
		$result->closeCursor();
	
	}
	catch(PDOException  $e){
		$errMsg = $e->getMessage();
		echo $errMsg;
	}
	
	$data_r02listetaches = TacheHelper::get_refTaches($role_tiers, $mouvement);
	
	/*
	 * Création mouvement DRH
	 */
	try{
	
		$getLastKey = Script::$db->prepare("SELECT cle FROM a05typemouvement ORDER BY cle DESC LIMIT 1");
		$getLastKey->execute();
		$lastKey = $getLastKey->fetchColumn();
		$getLastKey->closeCursor();
	
		if($lastKey === FALSE)
		{
			$lastNumber = "MVT0000001";
		}
	
		$lastNumber = (int) substr($lastKey, 3); // retire "MVT", il reste : [compteur] , qui est une valeur numérique
		$lastNumber = $lastNumber+1;
		$lastNumber = str_pad($lastNumber,7,'0',STR_PAD_LEFT);
		$mvt_cle = "MVT".$lastNumber;
	
		// Requete INSERT INTO
		/*
			* JR le 11/01/2017 : Comment remplir les valeurs r00societes
			*/
		$query = "INSERT INTO
						a05typemouvement
					(cle, a05cochsituationcourant, a00personnephysique, r03typemouvement, r04roletiers, a05dateeffet, creation_par, date_creation, heure_creation, modification_par,
date_modification, heure_modification)
					values
					('".$mvt_cle."', 'Oui', '".$personnephysique_cle."', '".$mouvement."', '".$role_tiers."', '".$data_pp[0]->a00dateeffet."', 'candidat', CURDATE(),CURTIME(), 'candidat', CURDATE(),  CURTIME()  )
					";
	
		// on va chercher tous les enregistrements de la requ?te
		$result=Script::$db->prepare($query);
		$result->execute();
	
	}
	catch(PDOException  $e){
		$errMsg = $e->getMessage();
		echo $errMsg;
	}
	/*-----------------------------------*/
	
	/*
	 * Création Rôle Tiers
	 */
	try{
	
		$getLastKey = Script::$db->prepare("SELECT cle FROM a06roles ORDER BY cle DESC LIMIT 1");
		$getLastKey->execute();
		$lastKey = $getLastKey->fetchColumn();
		$getLastKey->closeCursor();
	
		if($lastKey === FALSE)
		{
			$lastNumber="R00000001";
		}
	
		$lastNumber = (int) substr($lastKey, 1); // retire "R", il reste : [compteur] , qui est une valeur numérique
		$lastNumber = $lastNumber+1;
		$lastNumber = str_pad($lastNumber,8,'0',STR_PAD_LEFT);
		$role_cle = "R".$lastNumber;
	
		// Requete INSERT INTO
		$query = "INSERT INTO
						a06roles
					(cle, a00personnephysique, r04roletiers,  creation_par, date_creation, heure_creation, modification_par, date_modification, heure_modification)
					values
					('".$role_cle."','".$personnephysique_cle."',  '".$role_tiers."', 'candidat', CURDATE(),CURTIME(), 'candidat', CURDATE(),  CURTIME()  )
					";
	
		// on va chercher tous les enregistrements de la requ?te
		$result=Script::$db->prepare($query);
		$result->execute();
	
	}
	catch(PDOException  $e){
		$errMsg = $e->getMessage();
		echo $errMsg;
	}
	/*-----------------------------------*/
	/*
	 * Création mouvement DRI
	 */
	try{
	
		// Requete INSERT INTO
		$query = "INSERT INTO
					a02entreessortiesdri
						(cle, a00personnephysique, a05typemouvement , creation_par, date_creation, heure_creation, modification_par, date_modification, heure_modification)
					values
					('".$mvt_cle."_dri', '".$personnephysique_cle."', '".$mvt_cle."', 'candidat', CURDATE(),CURTIME(), 'candidat', CURDATE(),  CURTIME()  )
					";
	
		// on va chercher tous les enregistrements de la requ?te
		$result=Script::$db->prepare($query);
		$result->execute();
	
	}
	catch(PDOException  $e){
		$errMsg = $e->getMessage();
		echo $errMsg;
	}
	/*-----------------------------------*/
	
	/*
	 * Création mouvement DSI
	 */
	try{
	
		// Requete INSERT INTO
		$query = "INSERT INTO
					a03entreessortiesdsi
						(cle, a00personnephysique, a05typemouvement, creation_par, date_creation, heure_creation, modification_par, date_modification, heure_modification )
					values
					('".$mvt_cle."_dsi', '".$personnephysique_cle."', '".$mvt_cle."', 'candidat', CURDATE(),CURTIME(), 'candidat', CURDATE(),  CURTIME()  )
					";
	
		// on va chercher tous les enregistrements de la requ?te
		$result=Script::$db->prepare($query);
		$result->execute();
	
	}
	catch(PDOException  $e){
		$errMsg = $e->getMessage();
		echo $errMsg;
	}
	/*-----------------------------------*/
	
	/*
	 * Création tâches
	 */
	try{
	
		$i = 1;
		foreach ($data_r02listetaches as $r02listetache) {
			$getLastKey = Script::$db->prepare("SELECT cle FROM a04taches ORDER BY cle DESC LIMIT 1");
			$getLastKey->execute();
			$lastKey = $getLastKey->fetchColumn();
			$getLastKey->closeCursor();
	
			if($lastKey === FALSE)
			{
				$lastNumber = "T000000001";
			}
	
			$lastNumber = (int) substr($lastKey, 3); // retire "MVT", il reste : [compteur] , qui est une valeur numérique
			$lastNumber = $lastNumber+1;
			$lastNumber = str_pad($lastNumber,9,'0',STR_PAD_LEFT);
			$tache_cle = "T".$lastNumber;
	
			// Creation de la requete
	
			// Associer la tâches suivant l'entrée sortie (DRH, DRI ou DSI)
			$entreesortie = '';
			$allocation = '';
				
			if($r02listetache->r01allocationtache == 'DRH') {
				$entreesortie = 'a05typemouvement';
				$allocation = 'drh';
	
				$query = "INSERT INTO a04taches
								( cle, r02listetaches, a04statuttache, a00personnephysique, utilisateur, r01allocationtache, ".$entreesortie." , creation_par, date_creation, heure_creation, modification_par, date_modification, heure_modification)
								values ('".$tache_cle."','".$r02listetache->cle."', 'Non Traité','".$personnephysique_cle."','".$r02listetache->utilisateur."','".$r02listetache->r01allocationtache."','".$mvt_cle."', 'candidat', CURDATE(),CURTIME(), 'candidat', CURDATE(),  CURTIME() );
								";
			}
				
			if ( ($r02listetache->r01allocationtache == 'DRI') || ($r02listetache->r01allocationtache == 'DSI') ){
				if($r02listetache->r01allocationtache == 'DRI'){
					$entreesortie = 'a02entreessortiesdri';
					$allocation = 'dri';
				}
	
				if($r02listetache->r01allocationtache == 'DSI'){
					$entreesortie = 'a03entreessortiesdsi';
					$allocation = 'dsi';
				}
	
				$query = "	INSERT INTO a04taches
								( cle, r02listetaches, a04statuttache, a00personnephysique, a05typemouvement, utilisateur, r01allocationtache, ".$entreesortie." , creation_par, date_creation, heure_creation, modification_par, date_modification, heure_modification)
								values ('".$tache_cle."','".$r02listetache->cle."', 'Non Traité','".$personnephysique_cle."','".$mvt_cle."','".$r02listetache->utilisateur."','".$r02listetache->r01allocationtache."','".$mvt_cle."_".$allocation."', 'candidat', CURDATE(),CURTIME(), 'candidat', CURDATE(),  CURTIME() );
								";
			}
			// on va chercher tous les enregistrements de la requ?te
			$result=Script::$db->prepare($query);
			$result->execute();
	
			$i++;
		}
	
	}
	catch(PDOException  $e){
		$errMsg = $e->getMessage();
		echo $errMsg;
	}
	/*-----------------------------------*/
	
}

?>