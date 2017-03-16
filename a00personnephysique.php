<?php

Script::init(array('content'=>'text/plain'));
include dirname(dirname(dirname(__FILE__))).'/asset/class/r03typemouvement_class.php';
include dirname(dirname(dirname(__FILE__))).'/asset/class/r04roletiers_class.php';
include dirname(dirname(dirname(__FILE__))).'/asset/class/candidat_class.php';
include dirname(dirname(dirname(__FILE__))).'/asset/class/a00personnephysique_class.php';
include dirname(dirname(dirname(__FILE__))).'/asset/class/r16statutpap_class.php';

if($_REQUEST['mode']=='getKey'){
	$getLastKey = Script::$db->prepare("SELECT cle FROM a00personnephysique ORDER BY cle DESC LIMIT 1");
	$getLastKey->execute();
	$lastKey = $getLastKey->fetchColumn();
	$getLastKey->closeCursor();

	if($lastKey === FALSE)
	{
	exit( "AC00000001" );
	}

	$lastNumber = (int) substr($lastKey, 2); // retire "AC", il reste : [compteur] , qui est une valeur numérique
	$lastNumber = $lastNumber+1;
	$lastNumber = str_pad($lastNumber,8,'0',STR_PAD_LEFT);
	exit("AC".$lastNumber);
}


if($_REQUEST['mode']=='mouvement'){
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

	// récupérer les données r02listetaches
	try{
	
		// Requete INSERT INTO
		$query = "SELECT * FROM r02listetaches where r03typemouvement = '".$mouvement."' and r04roletiers = '".$role_tiers."' ";

		// on va chercher tous les enregistrements de la requ?te
		$result=Script::$db->prepare($query); 
		$result->execute();
		
		// on dit qu'on veut que le r?sultat soit r?cup?rable sous forme de tableau
		$data_r02listetaches = $result->fetchAll((PDO::FETCH_OBJ));
		
		// on ferme le curseur des r?sultats			
		$result->closeCursor(); 

	}
	catch(PDOException  $e){
		$errMsg = $e->getMessage();
		echo $errMsg;
	}

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

if($_REQUEST['mode']=='createItems'){

	// récupérer les données du candidat sélectionné
	$idcandidat = $_REQUEST['idcandidat'];	
	$candidat = Candidat::findById($idcandidat);

	// récupérer les données r02listetaches
	try{	
		// Requete INSERT INTO
		$query = "SELECT * FROM r02listetaches where r03typemouvement = '".TypeMvmt::ENTREE."'";

		// on va chercher tous les enregistrements de la requ?te
		$result=Script::$db->prepare($query); 
		$result->execute();
		
		// on dit qu'on veut que le r?sultat soit r?cup?rable sous forme de tableau
		$data_r02listetaches = $result->fetchAll((PDO::FETCH_OBJ));
		
		// on ferme le curseur des r?sultats			
		$result->closeCursor();
	}
	catch(PDOException  $e){
		$errMsg = $e->getMessage();
		echo $errMsg;
	}
	
	// Création de la personne physique
	$personnePhysique = new PersonnePhysique($candidat);
	$personnePhysique->create();

	// Requete demande (DAR)
	try{
	$query_demande = "SELECT
						demande.a07postesbudgetaires as dar_cle_pap,
						demande.d00nommanager as dar_superieur,
						demande.d00personneremplacee as dar_remplace
					FROM demande
					where cle = '".$candidat->get_demande()."';
					";
	// on va chercher tous les enregistrements de la requ?te
	$result_demande=Script::$db->prepare($query_demande); 
	$result_demande->execute();
	
	// on dit qu'on veut que le r?sultat soit r?cup?rable sous forme de tableau
	$data_demande = $result_demande->fetchAll((PDO::FETCH_OBJ));
	
	// on ferme le curseur des r?sultats			
	$result_demande->closeCursor();
	}
	catch(PDOException  $e){
		$errMsg = $e->getMessage();
		echo $errMsg;
	}
	/*-----------------------------------*/

	// Requete pour mettre à jour le statut du PAP
	$update_pap = Script::$db->prepare("UPDATE a07postesbudgetaires
				SET a07statutpap = '".StatutPAP::RECRUTEMENT_VALIDE."',
				a07salariePAP = '".$personnePhysique->get_cle()."',
				a07originerecrutement = '".$candidat->get_type_recrutement()."'
				where cle = '".$data_demande[0]->dar_cle_pap."'");
	$update_pap->execute();
	/*-----------------------------------*/
	
	$personnePhysique->set_departementnaissance('');

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
		$lastNumber_entree = $lastNumber+1;
		$lastNumber_sortie = $lastNumber_entree+1;
		
		// N° mvt entrée
		$lastNumber_entree = str_pad($lastNumber_entree,7,'0',STR_PAD_LEFT);
		$mvt_cle = "MVT".$lastNumber_entree;

		// Requete INSERT INTO
		// Entrée
		$query = "INSERT INTO 
						a05typemouvement
					(cle, a05dateeffet, a05cochsituationcourant, a00personnephysique, r03typemouvement, r00societes, r04roletiers, creation_par, date_creation, heure_creation, modification_par, date_modification, heure_modification, a05typecontrat, a05rttmonetises)
					values
					('".$mvt_cle."', '".$candidat->get_date_debut_contrat()."', 'Oui', '".$personnePhysique->get_cle()."', '".TypeMvmt::ENTREE."', '".$candidat->get_societes()."', '".Roles::SALARIE."', 'candidat', CURDATE(),CURTIME(), 'candidat', CURDATE(), CURTIME(), '".$candidat->get_typecontratGRE()."', '".$candidat->get_nb_jours_rtt_monetises()."');";

		// on va chercher tous les enregistrements de la requ?te
		$result=Script::$db->prepare($query); 
		$result->execute();
		
		// Sortie si <> CDI
		if ($candidat->get_typecontratGRE() !== 'CTT001'){
			// N° mvt sortie
			$lastNumber_sortie = str_pad($lastNumber_sortie,7,'0',STR_PAD_LEFT);
			$mvt_cle_sortie = "MVT".$lastNumber_sortie;
			
			$query = "INSERT INTO 
						a05typemouvement
						(cle, a05dateeffet, a05cochsituationcourant, a00personnephysique, r03typemouvement, r00societes, r04roletiers, creation_par, date_creation, heure_creation, modification_par, date_modification, heure_modification, a05typecontrat, a05rttmonetises)
						values
						('".$mvt_cle_sortie."', '".$candidat->get_date_fin_contrat()."', 'Oui', '".$personnePhysique->get_cle()."', '".TypeMvmt::SORTIE."', '".$candidat->get_societes()."', '".Roles::SALARIE."', 'candidat', CURDATE(),CURTIME(), 'candidat', CURDATE(), CURTIME(), '".$candidat->get_typecontratGRE()."', '".$candidat->get_nb_jours_rtt_monetises()."');";

			// on va chercher tous les enregistrements de la requ?te
			$result=Script::$db->prepare($query); 
			$result->execute();		
		}
	}
	catch(PDOException  $e){
		$errMsg = $e->getMessage();
		echo $errMsg;
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
	/* A SUPPRIMER */

	/*-----------------------------------*/
	/*
     * Création mouvement DRI
	 */
	try{

		// Requete INSERT INTO
		$query = "INSERT INTO 
					a02entreessortiesdri
						(cle, a00personnephysique, a05typemouvement,
					a02societe, a02direction, a02superieurhierarchique,
					a02poste, a02personneremplacee, a02typecontrat,
					creation_par, date_creation, heure_creation, modification_par, 	
					date_modification, heure_modification)
					values
					('".$mvt_cle."_dri', '".$personnePhysique->get_cle()."', '".$mvt_cle."',
					'".$candidat->get_societes()."', '".$candidat->get_direction()."', '".$data_demande[0]->dar_superieur."',
					'".$candidat->get_poste()."', '".$data_demande[0]->dar_remplace."', '".$candidat->get_typecontratGRE()."',
					'candidat', CURDATE(), CURTIME(), 'candidat',
					CURDATE(),  CURTIME()  )
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
						(cle, a00personnephysique, a05typemouvement,						
						a03societe, a03direction, a03superieurhierarchique,
						a03poste, a03personneremplacee,
						creation_par, date_creation, heure_creation, modification_par,
						date_modification, heure_modification ) 
					values
					('".$mvt_cle."_dsi', '".$personnePhysique->get_cle()."', '".$mvt_cle."',
					'".$candidat->get_societes()."', '".$candidat->get_direction()."', '".$data_demande[0]->dar_superieur."',
					'".$candidat->get_poste()."', '".$data_demande[0]->dar_remplace."',
					'candidat', CURDATE(),CURTIME(), 'candidat',
					CURDATE(), CURTIME() )
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
				
				$query = "	INSERT INTO a04taches 
							( cle, r02listetaches, a04statuttache, a00personnephysique, utilisateur, r01allocationtache, ".$entreesortie." , creation_par, date_creation, heure_creation, modification_par, date_modification, heure_modification) 
							values ('".$tache_cle."','".$r02listetache->cle."', 'Non Traité','".$personnePhysique->get_cle()."','".$r02listetache->utilisateur."','".$r02listetache->r01allocationtache."','".$mvt_cle."', 'candidat', CURDATE(),CURTIME(), 'candidat', CURDATE(),  CURTIME() );
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
							values ('".$tache_cle."','".$r02listetache->cle."', 'Non Traité','".$personnePhysique->get_cle()."','".$mvt_cle."','".$r02listetache->utilisateur."','".$r02listetache->r01allocationtache."','".$mvt_cle."_".$allocation."', 'candidat', CURDATE(),CURTIME(), 'candidat', CURDATE(),  CURTIME() );
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