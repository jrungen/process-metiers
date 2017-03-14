<?php

Script::init(array('content'=>'text/plain'));

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
				
				$query = "	INSERT INTO a04taches 
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
	
	try{
		// Creation de la requ?te
		$query = "SELECT * FROM candidat WHERE idcandidat = '".$idcandidat."'";

		// on va chercher tous les enregistrements de la requ?te
		$result=Script::$db->prepare($query); 
		$result->execute();

		// on dit qu'on veut que le resultat soit recuperable sous forme de tableau
		$data_candidat = $result->fetchAll((PDO::FETCH_OBJ));

		// on ferme le curseur des r?sultats			
		$result->closeCursor(); 
	}
	catch(PDOException  $e){
		$errMsg = $e->getMessage();
		echo $errMsg;
	}
	// Champ type de contrat dans candidat selectionné = t01typecontratGRE
	// CDI = CTT001 / CDD = CTT002 / ST = CTT003 / CAPP = CTT004 / CPROF = CTT005 / CDIOD = CTT006

	// récupérer les données r02listetaches
	try{	
		// Requete INSERT INTO
		$query = "SELECT * FROM r02listetaches where r03typemouvement = 'TM001'";

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
     * Création de la personne physique
	 */
	try{
		/*
		 * DEBUT - Nouveau numéro
		 */

		$getLastKey = Script::$db->prepare("SELECT cle FROM a00personnephysique ORDER BY cle DESC LIMIT 1");
		$getLastKey->execute();
		$lastKey = $getLastKey->fetchColumn();
		$getLastKey->closeCursor();

		if($lastKey === FALSE)
		{
		$lastNumber = "AC00000001";
		}

		$lastNumber = (int) substr($lastKey, 2); // retire "AC", il reste : [compteur] , qui est une valeur numérique
		$lastNumber = $lastNumber+1;
		$lastNumber = str_pad($lastNumber,8,'0',STR_PAD_LEFT);
		$personnephysique_cle = 'AC'.$lastNumber;
		/**/
	
		// Requete INSERT INTO
		$query = "INSERT INTO
				a00personnephysique
				(cle, r04roletiers, r03typemouvement, a00civilite, a00nom, a00prenom, a00adresse, a00complement, a00codepostal, a00ville,
				a00nationalite, a00datenaissance, a00departementnaissance, a00lieunaissance, a00numerosecu,a00clesecu, a00actif,
				creation_par, date_creation, heure_creation, modification_par, date_modification, heure_modification)
				values
				('".$personnephysique_cle."',  'ROL001',  'TM001', '".$data_candidat[0]->t01_03_civilite_candidat."', '".$data_candidat[0]->t01_01_nom_candidat."', '".$data_candidat[0]->t01_02_prenom_candidat."','".$data_candidat[0]->t01adresse."','".$data_candidat[0]->t01complement."','".$data_candidat[0]->t01codepostal."','".$data_candidat[0]->t01ville."','".$data_candidat[0]->t01_22_nationalite_candidat."','".$data_candidat[0]->t01_20_date_naissance."','".$data_candidat[0]->t01departementnaissance."','".$data_candidat[0]->t01_21_lieu_naissance."','".$data_candidat[0]->t01_23_num_securite_sociale."','".$data_candidat[0]->t01clesecuritesociale."', 'Oui', 'candidat', CURDATE(),CURTIME(), 'candidat', CURDATE(),  CURTIME() )
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

	// Requete pour trouver le PAP en passant par la demande
	try{
	$query_pap = "SELECT demande.a07postesbudgetaires as cle_pap
				FROM demande
				where cle = '".$data_candidat[0]->demande."';
				";
	// on va chercher tous les enregistrements de la requ?te
	$result_pap=Script::$db->prepare($query_pap); 
	$result_pap->execute();
	
	// on dit qu'on veut que le r?sultat soit r?cup?rable sous forme de tableau
	$data_pap = $result_pap->fetchAll((PDO::FETCH_OBJ));
	
	// on ferme le curseur des r?sultats			
	$result_pap->closeCursor();
	}
	catch(PDOException  $e){
		$errMsg = $e->getMessage();
		echo $errMsg;
	}
	/*-----------------------------------*/

				
	// Requete pour mettre à jour le statut du PAP
	$update_pap = Script::$db->prepare("UPDATE a07postesbudgetaires
				SET a07statutpap = 'STAPAP002',
				a07salariePAP = '".$personnephysique_cle."',
				a07originerecrutement = '".$data_candidat[0]->t01_24_type_recrutement."'
				where cle = '".$data_pap[0]->cle_pap."'");
	$update_pap->execute();
	/*-----------------------------------*/

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
					('".$mvt_cle."', '".$data_candidat[0]->t01_30_date_debut_contrat."', 'Oui', '".$personnephysique_cle."', 'TM001', '".$data_candidat[0]->cs00societes."', 'ROL001', 'candidat', CURDATE(),CURTIME(), 'candidat', CURDATE(), CURTIME(), '".$data_candidat[0]->t01typecontratGRE."', '".$data_candidat[0]->t01_19_nb_jours_rtt_monetises."');";

		// on va chercher tous les enregistrements de la requ?te
		$result=Script::$db->prepare($query); 
		$result->execute();
		
		// Sortie si <> CDI
		if ($data_candidat[0]->t01typecontratGRE !== 'CTT001'){
			// N° mvt sortie
			$lastNumber_sortie = str_pad($lastNumber_sortie,7,'0',STR_PAD_LEFT);
			$mvt_cle_sortie = "MVT".$lastNumber_sortie;
			
			$query = "INSERT INTO 
						a05typemouvement
						(cle, a05dateeffet, a05cochsituationcourant, a00personnephysique, r03typemouvement, r00societes, r04roletiers, creation_par, date_creation, heure_creation, modification_par, date_modification, heure_modification, a05typecontrat, a05rttmonetises)
						values
						('".$mvt_cle_sortie."', '".$data_candidat[0]->t01_31_date_fin_contrat."', 'Oui', '".$personnephysique_cle."', 'TM002', '".$data_candidat[0]->cs00societes."', 'ROL001', 'candidat', CURDATE(),CURTIME(), 'candidat', CURDATE(), CURTIME(), '".$data_candidat[0]->t01typecontratGRE."', '".$data_candidat[0]->t01_19_nb_jours_rtt_monetises."');";

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
					('".$role_cle."','".$personnephysique_cle."', 'ROL001',
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
					a02societe, a02direction, a02superieurhierarchique, a02poste, a02personneremplacee, a02typecontrat,
					a02finperiodeessai2, a02dateeffetauplustard, a02finperiodeessai1, a02site, a02vehicule, a02vehiculeperiodeessai, creation_par, date_creation, heure_creation, modification_par, 	
date_modification, heure_modification)
					values
					('".$mvt_cle."_dri', '".$personnephysique_cle."', '".$mvt_cle."',
					'".$data_candidat[0]->cs00societes."', '".$data_candidat[0]->cs00direction."', '', '".$data_candidat[0]->t01_32_poste."', '', '".$data_candidat[0]->t01typecontratGRE."',
					'', '', '', '', '', '',
					'candidat', CURDATE(),CURTIME(), 'candidat', CURDATE(),  CURTIME()  )
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
				
				$query = "	INSERT INTO a04taches 
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