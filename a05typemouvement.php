<?php //a supprimer

Script::init(array('content'=>'application/json'));

if ($_REQUEST['mode'] === 'numerotation'){
	$getLastKey = Script::$db->prepare("SELECT cle FROM a05typemouvement ORDER BY cle DESC LIMIT 1");
	$getLastKey->execute();
	$lastKey = $getLastKey->fetchColumn();
	$getLastKey->closeCursor();

	if($lastKey === FALSE)
	{
	exit( "MVT0000001" );
	}

	$lastNumber = (int) substr($lastKey, 3); // retire "MVT", il reste : [compteur] , qui est une valeur numérique
	$lastNumber = $lastNumber+1;
	$lastNumber = str_pad($lastNumber,7,'0',STR_PAD_LEFT);
	// exit( "MVT".($lastNumber) );
	exit( json_encode("MVT".($lastNumber)) );
}

if ($_REQUEST['mode'] === 'email_1'){
	$cle = $_REQUEST['cle'];
	$commentaire = $_REQUEST['commentaire'];
	$gsUser = $_REQUEST['user'];
	$listeModif_dri = addslashes($_REQUEST['listeModif_dri']);
	$listeModif_dsi = addslashes($_REQUEST['listeModif_dsi']);

	try{
		// Creation de la requete
		$query_mouvement = "SELECT *
		FROM a05typemouvement
		WHERE cle = '".$cle."';";
		
		// on va chercher tous les enregistrements de la requete
		$result_mouvement=Script::$db->prepare($query_mouvement); 
		$result_mouvement->execute();

		// on dit qu'on veut que le resultat soit recuperable sous forme de tableau
		$data_mouvement = $result_mouvement->fetchAll((PDO::FETCH_OBJ));

		// on ferme le curseur des resultats			
		$result_mouvement->closeCursor(); 

	}
	catch(PDOException  $e){
		$errMsg = $e->getMessage();
		echo $errMsg;
	}
	
	// Envoi email aux users du Groupe GRP_DRI 
	$query_grp1 = "SELECT * FROM utilisateur WHERE groupe REGEXP 'GRP_DRI' AND actif = '1';";
	$result_grp1 = Script::$db->query($query_grp1);
	while( $row_grp1 = $result_grp1->fetch(PDO::FETCH_OBJ) )
	{
		// je vérifie que l'utilisateur du groupe est différent de l'utilisateur connecté
		if ($row_grp1->cle !== $gsUser){
			$sujet1 = '';
			$message1 = '';
			// Modèle email pour le recruteur
			$sujet1 = "MODIFICATION - Entrées/Sorties DRH : ".$cle;
			$message1 = "<div style=\'color:#1f497d;\'>
				<img height=\'160\' src=\'https://www.gopaas.net/qualif/altarea/processus-metiers/asset/bandeau_email.jpg\'>
				<p>Bonjour,</p>
				
				<p>La fiche Entrées/Sorties DRH <b>".$cle."</b> a été modifiée par <b>".$gsUser."</b>.</p>
				
				<p>Liste des modifications :<br>
				".$listeModif_dri."</p>
				
				<p>Commentaire :<br>
				".$commentaire."</p>
				
				<p>Connectez-vous à l\'adresse suivante pour consulter la demande :<br><a href=\'https://www.gopaas.net/prod/altarea/processus-metiers\'>https://www.gopaas.net/prod/altarea/processus-metiers</a>.</p>
				
				<p>Cordialement,</p>
				<p>L\'&eacute;quipe RH</p>
				</div>";

			// Requete INSERT INTO
			$query = "INSERT INTO email(
							cle,
							expediteur,
							destinataire,
							objet,
							message,
							statut,
							source,
							format,
							creation_par,
							date_creation,
							heure_creation,
							modification_par,
							date_modification,
							heure_modification
						)values(
							CONCAT('".$cle."','-',CURDATE(),'-',CURTIME()),
							'adm-candidat-selectionne@altareacogedim.com',
							'".$row_grp1->email."',
							'".$sujet1."',
							'".$message1."',
							'Planifié',
							'candidat',
							'html',
							'".$gsUser."',
							CURDATE(),
							CURTIME(),
							'".$gsUser."',
							CURDATE(),
							CURTIME()
							)";
			$result=Script::$db->prepare($query); 
			$result->execute();
		}
	}
	
	// Envoi email aux users du Groupe GRP_DSI 
	$query_grp2 = "SELECT * FROM utilisateur WHERE groupe REGEXP 'GRP_DSI' AND actif = '1';";
	$result_grp2 = Script::$db->query($query_grp2);
	while( $row_grp2 = $result_grp2->fetch(PDO::FETCH_OBJ) )
	{
		// je vérifie que l'utilisateur du groupe est différent de l'utilisateur connecté
		if ($row_grp2->cle !== $gsUser){
			$sujet2 = '';
			$message2 = '';
			// Modèle email pour le recruteur
			$sujet2 = "MODIFICATION - Entrées/Sorties DRH : ".$cle;
			$message2 = "<div style=\'color:#1f497d;\'>
				<img height=\'160\' src=\'https://www.gopaas.net/qualif/altarea/processus-metiers/asset/bandeau_email.jpg\'>
				<p>Bonjour,</p>
				
				<p>La fiche Entrées/Sorties DRH <b>".$cle."</b> a été modifiée par <b>".$gsUser."</b>.</p>
				
				<p>Liste des modifications :<br>
				".$listeModif_dsi."</p>
				
				<p>Commentaire :<br>
				".$commentaire."</p>
				
				<p>Connectez-vous à l\'adresse suivante pour consulter la demande :<br><a href=\'https://www.gopaas.net/prod/altarea/processus-metiers\'>https://www.gopaas.net/prod/altarea/processus-metiers</a>.</p>
				
				<p>Cordialement,</p>
				<p>L\'&eacute;quipe RH</p>
				</div>";

			// Requete INSERT INTO
			$query = "INSERT INTO email(
							cle,
							expediteur,
							destinataire,
							objet,
							message,
							statut,
							source,
							format,
							creation_par,
							date_creation,
							heure_creation,
							modification_par,
							date_modification,
							heure_modification
						)values(
							CONCAT('".$cle."','-',CURDATE(),'-',CURTIME()),
							'adm-candidat-selectionne@altareacogedim.com',
							'".$row_grp2->email."',
							'".$sujet2."',
							'".$message2."',
							'Planifié',
							'candidat',
							'html',
							'".$gsUser."',
							CURDATE(),
							CURTIME(),
							'".$gsUser."',
							CURDATE(),
							CURTIME()
							)";
			$result=Script::$db->prepare($query); 
			$result->execute();
		}
	}
	
	echo json_encode('OK');
}

if ($_REQUEST['mode'] === 'email_2'){
	$cle = $_REQUEST['cle'];
	$commentaire = $_REQUEST['commentaire'];
	$gsUser = $_REQUEST['user'];
	$listeModif_dri = addslashes($_REQUEST['listeModif_dri']);

	try{
		// Creation de la requete
		$query_mouvement = "SELECT *
		FROM a05typemouvement
		WHERE cle = '".$cle."';";
		
		// on va chercher tous les enregistrements de la requete
		$result_mouvement=Script::$db->prepare($query_mouvement); 
		$result_mouvement->execute();

		// on dit qu'on veut que le resultat soit recuperable sous forme de tableau
		$data_mouvement = $result_mouvement->fetchAll((PDO::FETCH_OBJ));

		// on ferme le curseur des resultats			
		$result_mouvement->closeCursor(); 

	}
	catch(PDOException  $e){
		$errMsg = $e->getMessage();
		echo $errMsg;
	}
	
	// Envoi email aux users du Groupe GRP_DRI 
	$query_grp = "SELECT * FROM utilisateur WHERE groupe REGEXP 'GRP_DRI' AND actif = '1';";
	$result_grp = Script::$db->query($query_grp);
	while( $row_grp = $result_grp->fetch(PDO::FETCH_OBJ) )
	{
		// je vérifie que l'utilisateur du groupe est différent de l'utilisateur connecté
		if ($row_grp->cle !== $gsUser){
			$sujet = '';
			$message = '';
			// Modèle email pour le recruteur
			$sujet = "MODIFICATION - Entrées/Sorties DRH : ".$cle;
			$message = "<div style=\'color:#1f497d;\'>
				<img height=\'160\' src=\'https://www.gopaas.net/qualif/altarea/processus-metiers/asset/bandeau_email.jpg\'>
				<p>Bonjour,</p>
				
				<p>La fiche Entrées/Sorties DRH <b>".$cle."</b> a été modifiée par <b>".$gsUser."</b>.</p>
				
				<p>Liste des modifications :<br>
				".$listeModif_dri."</p>
				
				<p>Commentaire :<br>
				".$commentaire."</p>
				
				<p>Connectez-vous à l\'adresse suivante pour consulter la demande :<br><a href=\'https://www.gopaas.net/prod/altarea/processus-metiers\'>https://www.gopaas.net/prod/altarea/processus-metiers</a>.</p>
				
				<p>Cordialement,</p>
				<p>L\'&eacute;quipe RH</p>
				</div>";

			// Requete INSERT INTO
			$query = "INSERT INTO email(
							cle,
							expediteur,
							destinataire,
							objet,
							message,
							statut,
							source,
							format,
							creation_par,
							date_creation,
							heure_creation,
							modification_par,
							date_modification,
							heure_modification
						)values(
							CONCAT('".$cle."','-',CURDATE(),'-',CURTIME()),
							'adm-candidat-selectionne@altareacogedim.com',
							'".$row_grp->email."',
							'".$sujet."',
							'".$message."',
							'Planifié',
							'candidat',
							'html',
							'".$gsUser."',
							CURDATE(),
							CURTIME(),
							'".$gsUser."',
							CURDATE(),
							CURTIME()
							)";
			$result=Script::$db->prepare($query); 
			$result->execute();
		}
	}
	
	echo json_encode('OK');
}

if ($_REQUEST['mode'] === 'email_3'){
	$cle = $_REQUEST['cle'];
	$commentaire = $_REQUEST['commentaire'];
	$gsUser = $_REQUEST['user'];
	$listeModif_dsi = addslashes($_REQUEST['listeModif_dsi']);

	try{
		// Creation de la requete
		$query_mouvement = "SELECT *
		FROM a05typemouvement
		WHERE cle = '".$cle."';";
		
		// on va chercher tous les enregistrements de la requete
		$result_mouvement=Script::$db->prepare($query_mouvement); 
		$result_mouvement->execute();

		// on dit qu'on veut que le resultat soit recuperable sous forme de tableau
		$data_mouvement = $result_mouvement->fetchAll((PDO::FETCH_OBJ));

		// on ferme le curseur des resultats			
		$result_mouvement->closeCursor(); 

	}
	catch(PDOException  $e){
		$errMsg = $e->getMessage();
		echo $errMsg;
	}
	
	// Envoi email aux users du Groupe GRP_DSI 
	$query_grp = "SELECT * FROM utilisateur WHERE groupe REGEXP 'GRP_DSI' AND actif = '1';";
	$result_grp = Script::$db->query($query_grp);
	while( $row_grp = $result_grp->fetch(PDO::FETCH_OBJ) )
	{
		// je vérifie que l'utilisateur du groupe est différent de l'utilisateur connecté
		if ($row_grp->cle !== $gsUser){
			$sujet = '';
			$message = '';
			// Modèle email pour le recruteur
			$sujet = "MODIFICATION - Entrées/Sorties DRH : ".$cle;
			$message = "<div style=\'color:#1f497d;\'>
				<img height=\'160\' src=\'https://www.gopaas.net/qualif/altarea/processus-metiers/asset/bandeau_email.jpg\'>
				<p>Bonjour,</p>
				
				<p>La fiche Entrées/Sorties DRH <b>".$cle."</b> a été modifiée par <b>".$gsUser."</b>.</p>
				
				<p>Liste des modifications :<br>
				".$listeModif_dsi."</p>
				
				<p>Commentaire :<br>
				".$commentaire."</p>
				
				<p>Connectez-vous à l\'adresse suivante pour consulter la demande :<br><a href=\'https://www.gopaas.net/prod/altarea/processus-metiers\'>https://www.gopaas.net/prod/altarea/processus-metiers</a>.</p>
				
				<p>Cordialement,</p>
				<p>L\'&eacute;quipe RH</p>
				</div>";

			// Requete INSERT INTO
			$query = "INSERT INTO email(
							cle,
							expediteur,
							destinataire,
							objet,
							message,
							statut,
							source,
							format,
							creation_par,
							date_creation,
							heure_creation,
							modification_par,
							date_modification,
							heure_modification
						)values(
							CONCAT('".$cle."','-',CURDATE(),'-',CURTIME()),
							'adm-candidat-selectionne@altareacogedim.com',
							'".$row_grp->email."',
							'".$sujet."',
							'".$message."',
							'Planifié',
							'candidat',
							'html',
							'".$gsUser."',
							CURDATE(),
							CURTIME(),
							'".$gsUser."',
							CURDATE(),
							CURTIME()
							)";
			$result=Script::$db->prepare($query); 
			$result->execute();
		}
	}
	echo json_encode('OK');
}

if ($_REQUEST['mode'] === 'update_dri'){
	function dateUS($date){
		$date_us = substr($date,6,4).'-'.substr($date,3,2).'-'.substr($date,0,2);
		return $date_us;
	}
	$cle = $_REQUEST['cle'];
	$gsUser = Script::$user['cle'];
	$societe = $_REQUEST['societe'];
	$site = $_REQUEST['site'];
	$typecontrat = $_REQUEST['typecontrat'];
	$periodeessai1 = dateUS($_REQUEST['periodeessai1']);
	$periodeessai2 = dateUS($_REQUEST['periodeessai2']);
	$vehicule = $_REQUEST['vehicule'];
	$vehiculeperiodeessai = addslashes($_REQUEST['vehiculeperiodeessai']);
	/*
	* Requete pour mettre à jour les champs sur DRI
	*/
	$update_dri = Script::$db->prepare("UPDATE a02entreessortiesdri
				SET a02societe = '".$societe."',
				a02site = '".$site."',
				a02typecontrat = '".$typecontrat."',
				a02finperiodeessai1 = '".$periodeessai1."',
				a02finperiodeessai2 = '".$periodeessai2."',
				a02vehicule = '".$vehicule."',
				a02vehiculeperiodeessai = '".$vehiculeperiodeessai."',
				date_modification = CURDATE(), heure_modification = CURTIME(), modification_par = '".$gsUser."'
				where a05typemouvement = '".$cle."'");
	$update_dri->execute();
	/*-----------------------------------*/	
	echo json_encode('OK');
	
}

if ($_REQUEST['mode'] === 'update_dsi'){
	$cle = $_REQUEST['cle'];
	$gsUser = Script::$user['cle'];
	$societe = $_REQUEST['societe'];
	$site = $_REQUEST['site'];
	/*
	* Requete pour mettre à jour les champs sur DRI
	*/
	$update_dsi = Script::$db->prepare("UPDATE a03entreessortiesdsi	
				SET a03societe = '".$societe."',
				a03site = '".$site."',
				date_modification = CURDATE(), heure_modification = CURTIME(), modification_par = '".$gsUser."'
				where a05typemouvement = '".$cle."'");
	$update_dsi->execute();
	/*-----------------------------------*/	
	echo json_encode('OK');
	
}