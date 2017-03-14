<?php //a supprimer

// Script::init(array('content'=>'text/plain'));
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
	
	// Envoi email aux users des Groupes GRP_DRI et GRP_DSI
	$query_grp = "SELECT * FROM utilisateur WHERE groupe REGEXP 'GRP_DRI|GRP_DSI' AND actif = '1';";
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
				
				<p>La fiche Entrées/Sorties DRH a été <span style=\'color:red; font-weight:bold;\'>modifiée</span> par <b>".$gsUser."</b>.</p>
				
				<p>Commentaire :<br>
				".$commentaire."</p>
					
				<p>Pour toute question concernant cette modification, merci de vous adresser aux administrateurs à l\'adresse <a href=\'mailto:adm-candidat-selectionne@altareacogedim.com\'>adm-candidat-selectionne@altareacogedim.com</a>.</p>
				
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
							CONCAT('ANNULATION-',CURDATE(),'-',CURTIME()),
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

if ($_REQUEST['mode'] === 'email_2'){
	$cle = $_REQUEST['cle'];
	$commentaire = $_REQUEST['commentaire'];
	$gsUser = $_REQUEST['user'];

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
				
				<p>La fiche Entrées/Sorties DRH a été <span style=\'color:red; font-weight:bold;\'>modifiée</span> par <b>".$gsUser."</b>.</p>
				
				<p>Commentaire :<br>
				".$commentaire."</p>
					
				<p>Pour toute question concernant cette modification, merci de vous adresser aux administrateurs à l\'adresse <a href=\'mailto:adm-candidat-selectionne@altareacogedim.com\'>adm-candidat-selectionne@altareacogedim.com</a>.</p>
				
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
							CONCAT('ANNULATION-',CURDATE(),'-',CURTIME()),
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
				
				<p>La fiche Entrées/Sorties DRH a été <span style=\'color:red; font-weight:bold;\'>modifiée</span> par <b>".$gsUser."</b>.</p>
				
				<p>Commentaire :<br>
				".$commentaire."</p>
					
				<p>Pour toute question concernant cette modification, merci de vous adresser aux administrateurs à l\'adresse <a href=\'mailto:adm-candidat-selectionne@altareacogedim.com\'>adm-candidat-selectionne@altareacogedim.com</a>.</p>
				
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
							CONCAT('ANNULATION-',CURDATE(),'-',CURTIME()),
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