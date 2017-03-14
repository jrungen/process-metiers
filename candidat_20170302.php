<?php // a supprimer

Script::init(array(
	'content' => 'application/json',
	'session' => false, // l'authentification doit être contrôlée au cas par cas en fonction du service demandé. certains services doivent être accessibles sans authentification, notamment le service 'validate'.
));
$mode = $_REQUEST['mode'];

if ($mode === 'maj_DAR'){
	$cle_demande = $_REQUEST['demande'];
	$result=Script::$db->prepare("UPDATE demande SET candidat_cree = 0 WHERE cle = '".$cle_demande."'"); 
	$result->execute();
	
	echo json_encode('OK');
}

if ($mode === 'annulation'){
	$cle_candidat = $_REQUEST['cle_candidat'];
	$gsUser = $_REQUEST['user'];
	$var_commentaire_refus = addslashes($_REQUEST['commentaire_refus']);
	$candidat_a_creer = $_REQUEST['candidat_a_creer'];
	
	try{	
		// Creation de la requete
		$query_candidat = "SELECT c.valideur1, c.valideur2, c.valideur3, c.valideur4, c.valideur5, c.valideur6, c.recruteur, c.idcandidat, d.iddemande, c.demandeur, c.t01_32_poste
		FROM candidat c
		LEFT OUTER JOIN demande d on d.cle = c.demande
		WHERE c.cle = '".$cle_candidat."'";
		
		// on va chercher tous les enregistrements de la requete
		$result_candidat=Script::$db->prepare($query_candidat); 
		$result_candidat->execute();
		
		// on dit qu'on veut que le resultat soit recuperable sous forme de tableau
		$data_candidat = $result_candidat->fetchAll((PDO::FETCH_OBJ));
		
		// on ferme le curseur des resultats			
		$result_candidat->closeCursor(); 

	}
	catch(PDOException  $e){
		$errMsg = $e->getMessage();
		echo $errMsg;
	}
	
	// création du candidat à faire, je décoche la case candidat_cree dans la demande lié
	if ($candidat_a_creer == 'oui'){
		$update_demande=Script::$db->prepare("UPDATE demande SET candidat_cree = 0 WHERE iddemande = '".$data_candidat[0]->iddemande."'"); 
		$update_demande->execute();
	}
	
	// La demande est annulé et pas de création de candidat donc envoi un mail au demandeur
	if ($candidat_a_creer == 'non'){
		// je vérifie si le demandeur est différent de la personne qui annule pour envoyer un mail
		if ($data_candidat[0]->demandeur != $gsUser){
			// echo 'entré dans demandeur - ';
			// je récupère l'email du demandeur
			try{	
				// Creation de la requete				
				$query_user1 = "SELECT utilisateur.email, candidat.t01_32_poste
				FROM utilisateur, candidat
				WHERE utilisateur.cle = '".$data_candidat[0]->demandeur."' AND candidat.idcandidat = '".$data_candidat[0]->idcandidat."'";

				// on va chercher tous les enregistrements de la requete
				$result_user1=Script::$db->prepare($query_user1); 
				$result_user1->execute();				
				// on dit qu'on veut que le resultat soit recuperable sous forme de tableau
				$data_user1 = $result_user1->fetchAll((PDO::FETCH_OBJ));				
				// on ferme le curseur des resultats			
				$result_user1->closeCursor(); 
			}
			catch(PDOException  $e){
				$errMsg = $e->getMessage();
				echo $errMsg;
			}

			$sujet1 = '';
			$message1 = '';
			
			// Modèle email au demandeur
			$sujet1 = "ANNULÉ - Candidat sélectionné pour le poste : ".$data_user1[0]->t01_32_poste." | DAR N°".$data_candidat[0]->iddemande;
			
			$message1 = "<div style=\'color:#1f497d;\'>
				<img height=\'160\' src=\'https://www.gopaas.net/qualif/altarea/processus-metiers/asset/bandeau_email.jpg\'>
				<p>Bonjour,</p>
				
				<p>La fiche candidat pour le poste de <b>".$data_user1[0]->t01_32_poste."</b> a été <span style=\'color:red; font-weight:bold;\'>annulée</span> par <b>".$gsUser."</b>.</p>
				
				<p><span style=\'text-decoration:underline;font-weight:bold;\'>L\'administrateur n’a pas recréé de fiche candidat pour cette demande d’accord de recrutement.</span></p>
				
				<p>Commentaire :<br>
				".$var_commentaire_refus."</p>
				
				<p>Pour toute question concernant cette annulation, merci de vous adresser aux administrateurs à l\'adresse <a href=\'mailto:adm-candidat-selectionne@altareacogedim.com\'>adm-candidat-selectionne@altareacogedim.com</a>.</p>
				
				<p>Cordialement,</p>
				<p>L\'&eacute;quipe RH</p>
				</div>";
				
			// Requete INSERT INTO
			$query1 = "INSERT INTO email(
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
							'".$data_user1[0]->email."',
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
			// echo "insert Recruteur : ".$query1."<br/>";
			$result1=Script::$db->prepare($query1); 
			$result1->execute();
		}
	}

	// création d'un fonction pour envoyer les mails d'annulation au valideur
	function mailvalideur($num_valideur, $cle_valideur, $cle_user, $idcandidat, $commentaire_refus, $iddemande){
		if ($cle_valideur != $cle_user){
			// echo 'entré dans valideur mail - ';
			// je récupère l'email du valideur
			try{	
				// Creation de la requete
				$query_user = "SELECT utilisateur.email, candidat.date_validation".$num_valideur." as date_validation, candidat.t01_32_poste
				FROM utilisateur, candidat
				WHERE utilisateur.cle = '".$cle_valideur."' AND candidat.idcandidat = '".$idcandidat."'";
				
				// on va chercher tous les enregistrements de la requete
				$result_user=Script::$db->prepare($query_user); 
				$result_user->execute();				
				// on dit qu'on veut que le resultat soit recuperable sous forme de tableau
				$data_user = $result_user->fetchAll((PDO::FETCH_OBJ));				
				// on ferme le curseur des resultats			
				$result_user->closeCursor(); 
			}
			catch(PDOException  $e){
				$errMsg = $e->getMessage();
				echo $errMsg;
			}
			$sujet2 = '';
			$message2 = '';
			// Modèle email pour les valideurs
			$sujet2 = "ANNULÉ - Candidat sélectionné pour le poste : ".$data_user[0]->t01_32_poste." | DAR N°".$iddemande;
			$message2 = "<div style=\'color:#1f497d;\'>
				<img height=\'160\' src=\'https://www.gopaas.net/qualif/altarea/processus-metiers/asset/bandeau_email.jpg\'>
				<p>Bonjour,</p>
				
				<p>La fiche candidat pour le poste de <b>".$data_user[0]->t01_32_poste."</b> a été <span style=\'color:red; font-weight:bold;\'>annulée</span> par <b>".$cle_user."</b>.</p>
				
				<p>Commentaire :<br>
				".$commentaire_refus."</p>
				
				<p>Pour toute question concernant cette annulation, merci de vous adresser aux administrateurs à l\'adresse <a href=\'mailto:adm-candidat-selectionne@altareacogedim.com\'>adm-candidat-selectionne@altareacogedim.com</a>.</p>
				
				<p>Cordialement,</p>
				<p>L\'&eacute;quipe RH</p>
				</div>
				";

			if ($data_user[0]->date_validation != ''){
			// echo 'Envoi OK '.$cle_valideur.' - ';
				// Requete INSERT INTO
				$query2 = "INSERT INTO email(
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
								'".$data_user[0]->email."',
								'".$sujet2."',
								'".$message2."',
								'Planifié',
								'candidat',
								'html',
								'".$cle_user."',
								CURDATE(),
								CURTIME(),
								'".$cle_user."',
								CURDATE(),
								CURTIME()
								)";
				// echo "insert Valideur : ".$query2."<br/>";
				$result2=Script::$db->prepare($query2); 
				$result2->execute();
			}
		}
		return "OK";
	}
	
	// envoi d'un mail au recruteur en charge
	// je vérifie si le recruteur est différent de la personne qui annule pour envoyer un mail
	if ( ($data_candidat[0]->recruteur != $gsUser) && ($data_candidat[0]->recruteur != $data_candidat[0]->demandeur) ){
		
		// je récupère l'email du recruteur
		try{	
			// Creation de la requete
			$query_user = "SELECT utilisateur.email, candidat.t01_32_poste
			FROM utilisateur, candidat
			WHERE utilisateur.cle = '".$data_candidat[0]->recruteur."' AND candidat.idcandidat = '".$data_candidat[0]->idcandidat."';";

			// on va chercher tous les enregistrements de la requete
			$result_user=Script::$db->prepare($query_user); 
			$result_user->execute();				
			// on dit qu'on veut que le resultat soit recuperable sous forme de tableau
			$data_user = $result_user->fetchAll((PDO::FETCH_OBJ));				
			// on ferme le curseur des resultats			
			$result_user->closeCursor(); 
		}
		catch(PDOException  $e){
			$errMsg = $e->getMessage();
			echo $errMsg;
		}

		$sujet3 = '';
		$message3 = '';
		// Modèle email pour le recruteur
		$sujet3 = "ANNULÉ - Candidat sélectionné pour le poste : ".$data_user[0]->t01_32_poste." | DAR N°".$data_candidat[0]->iddemande;
		$message3 = "<div style=\'color:#1f497d;\'>
			<img height=\'160\' src=\'https://www.gopaas.net/qualif/altarea/processus-metiers/asset/bandeau_email.jpg\'>
			<p>Bonjour,</p>
			
			<p>La fiche candidat pour le poste de <b>".$data_user[0]->t01_32_poste."</b> a été <span style=\'color:red; font-weight:bold;\'>annulée</span> par <b>".$gsUser."</b>.</p>
			
			<p>Commentaire :<br>
			".$var_commentaire_refus."</p>
				
			<p>Pour toute question concernant cette annulation, merci de vous adresser aux administrateurs à l\'adresse <a href=\'mailto:adm-candidat-selectionne@altareacogedim.com\'>adm-candidat-selectionne@altareacogedim.com</a>.</p>
			
			<p>Cordialement,</p>
			<p>L\'&eacute;quipe RH</p>
			</div>";

		// Requete INSERT INTO
		$query3 = "INSERT INTO email(
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
						'".$data_user[0]->email."',
						'".$sujet3."',
						'".$message3."',
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
		// echo "insert Recruteur : ".$query3."<br/>";
		$result3=Script::$db->prepare($query3); 
		$result3->execute();
	}
	
	if ($data_candidat[0]->valideur1 != ''){
		mailvalideur('1', $data_candidat[0]->valideur1, $gsUser, $data_candidat[0]->idcandidat, $var_commentaire_refus, $data_candidat[0]->iddemande);
	}
	if ($data_candidat[0]->valideur2 != ''){
		mailvalideur('2', $data_candidat[0]->valideur2, $gsUser, $data_candidat[0]->idcandidat, $var_commentaire_refus, $data_candidat[0]->iddemande);
	}
	if ($data_candidat[0]->valideur3 != ''){
		mailvalideur('3', $data_candidat[0]->valideur3, $gsUser, $data_candidat[0]->idcandidat, $var_commentaire_refus, $data_candidat[0]->iddemande);
	}
	if ($data_candidat[0]->valideur4 != ''){
		mailvalideur('4', $data_candidat[0]->valideur4, $gsUser, $data_candidat[0]->idcandidat, $var_commentaire_refus, $data_candidat[0]->iddemande);
	}
	if ($data_candidat[0]->valideur5 != ''){
		mailvalideur('5', $data_candidat[0]->valideur5, $gsUser, $data_candidat[0]->idcandidat, $var_commentaire_refus, $data_candidat[0]->iddemande);
	}
	if ($data_candidat[0]->valideur6 != ''){
		mailvalideur('6', $data_candidat[0]->valideur6, $gsUser, $data_candidat[0]->idcandidat, $var_commentaire_refus, $data_candidat[0]->iddemande);
	}
	
	// Envoi email aux users PAP (Groupe GRP_ADM_DAR)	
	$query_grp = "SELECT * FROM utilisateur WHERE groupe = 'GRP_ADM_DAR' AND actif = '1';";
	$result_grp = Script::$db->query($query_grp);
	while( $row_grp = $result_grp->fetch(PDO::FETCH_OBJ) )
	{
		// je vérifie que l'utilisateur du groupe GRP_ADM_DAR est différent de l'utilisateur connecté
		if ($row_grp->cle !== $gsUser){
			$sujet4 = '';
			$message4 = '';
			// Modèle email pour le recruteur
			$sujet4 = "ANNULÉ - Candidat sélectionné pour le poste : ".$data_candidat[0]->t01_32_poste." | DAR N°".$data_candidat[0]->iddemande;		
			$message4 = "<div style=\'color:#1f497d;\'>
				<img height=\'160\' src=\'https://www.gopaas.net/qualif/altarea/processus-metiers/asset/bandeau_email.jpg\'>
				<p>Bonjour,</p>
				
				<p>La fiche candidat pour le poste de <b>".$data_candidat[0]->t01_32_poste."</b> a été <span style=\'color:red; font-weight:bold;\'>annulée</span> par <b>".$gsUser."</b>.</p>
				
				<p>Commentaire :<br>
				".$var_commentaire_refus."</p>
					
				<p>Pour toute question concernant cette annulation, merci de vous adresser aux administrateurs à l\'adresse <a href=\'mailto:adm-candidat-selectionne@altareacogedim.com\'>adm-candidat-selectionne@altareacogedim.com</a>.</p>
				
				<p>Cordialement,</p>
				<p>L\'&eacute;quipe RH</p>
				</div>";

			// Requete INSERT INTO
			$query4 = "INSERT INTO email(
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
							'".$sujet4."',
							'".$message4."',
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
			$result4=Script::$db->prepare($query4); 
			$result4->execute();
		}
	}
	
	echo json_encode('OK');
}

if ($mode == 'regle_valideur'){
	
	require_once Script::buildPath('class','RulesManager.php');
	
	// script poçur les règles valideur
	function normaliserPermission($permission) { // cette fonction supprime les doublons de permission, enlève les éventuelles valeurs vides, et tri les valeurs, ceci afin d'éviter les pb de calcul de différence à l'enregistrement
		if (!$permission) return $permission;
		$permission = array_unique(array_filter(explode(',', $permission),"strlen"));
		sort($permission);
		return implode(',', $permission);
	}
	//-------------------------------------------------------
	if ($_REQUEST['service'] === 'get_regle_valideur') {
		if (!Script::isAuthentified()) { // on est obligé de contrôler manuellement l'authentification, qui a été désactivée dans Script::init() . Voir plus haut l'appel à Script::init(), paramètre 'session'.
			throw new GPSessionException();
		}

		$regle_valideur = Script::$db->fetch("
			SELECT valideur1, valideur2, valideur3, valideur4, valideur5, valideur6
			FROM regle_validateur
			WHERE
				societe = :societe AND
				IFNULL(direction,'') = :direction AND fichier='candidat'
				AND type_contrat = :type_contrat AND
				(
					type_contrat IN ('CDD','CDI') AND
					(
						transverse = :transverse AND
						(duree = :duree OR type_contrat = 'CDI') AND
						metier = :metier AND
						motif_recrutement = :motif_recrutement
					)
					OR type_contrat NOT IN ('CDD','CDI')
				)
		", array(
			"societe" => $_REQUEST["societe"],
			"type_contrat" => $_REQUEST["type_contrat"],
			"duree" => $_REQUEST["duree"],
			"metier" => $_REQUEST["metier"],
			"motif_recrutement" => $_REQUEST["motif_recrutement"],
			"direction" => $_REQUEST["direction"],
			"transverse" => $_REQUEST["transverse"],
		));
		if (!$regle_valideur) {
			echo "{}";
		} else {
			echo json_encode($regle_valideur);
		}

	//-------------------------------------------------------
	} else if ($_REQUEST['service'] === 'validate' || $_REQUEST['service'] === 'confirm') {

		header("Content-type: text/html; charset=utf-8", true);

		if (empty($_REQUEST['answer']) || empty($_REQUEST['id']) || empty($_REQUEST['token'])) {
			throw new GPUserException("Au moins l'un des paramètres obligatoires est manquant: answer, id, token");
		}

		if (!in_array($_REQUEST['answer'], array('yes','no'))) {
			throw new GPUserException("La réponse '$_REQUEST[answer]' est invalide, seules sont acceptées 'yes' ou 'no'");
		}

		// cas particulier: si on est dans un refus: pas besoin de confirmation
		// if ($_REQUEST['answer'] === 'no' && $_REQUEST['service'] === 'validate') {
			// $_REQUEST['service'] = 'confirm';
		// }

		$demande = Script::$db->fetch("SELECT etape, cle, permission, valideur1, valideur2, valideur3, valideur4, valideur5, valideur6, pdf FROM candidat WHERE idcandidat = ? AND token = ?", array($_REQUEST['id'],$_REQUEST['token']));
		if (!$demande) { // soit le token n'est plus valide, soit la demande est introuvable :
			throw new GPUserException("Ce lien est expiré");
		}

		$accepterOuRefuser = ( $_REQUEST['answer'] === 'yes' ? "accepte" : "refuse" );
		switch ($demande->etape) {
			case "Attente valideur 1": $nextStep = "Valideur 1 $accepterOuRefuser"; $numCurrentValidation = 1; break;
			case "Attente valideur 2": $nextStep = "Valideur 2 $accepterOuRefuser"; $numCurrentValidation = 2; break;
			case "Attente valideur 3": $nextStep = "Valideur 3 $accepterOuRefuser"; $numCurrentValidation = 3; break;
			case "Attente valideur 4": $nextStep = "Valideur 4 $accepterOuRefuser"; $numCurrentValidation = 4; break;
			case "Attente valideur 5": $nextStep = "Valideur 5 $accepterOuRefuser"; $numCurrentValidation = 5; break;
			case "Attente valideur 6": $nextStep = "Valideur 6 $accepterOuRefuser"; $numCurrentValidation = 6; break;
			case "Attente validation" : $nextStep = "Arbitrage ".( $_REQUEST['answer'] === 'yes' ? "accepté" : "refusé" ); $numCurrentValidation = 6; break;
			default : throw new GPUserException("La demande est dans un état [$demande->etape] qui ne permet pas de valider");
		}
		// calcul les permissions
		$permission = $demande->permission;
		if (substr($demande->etape,0,16) === "Attente valideur") {
			$nextValideur = ((int) substr($nextStep,9,1)) +1;
			if ($nextValideur <= 6) {
				$permission = normaliserPermission( ($permission ? "$permission," : '').($demande->{"valideur$nextValideur"})); // on ajoute le prochain valideur à la liste des permissions
			}
		}
		if ($_REQUEST['service'] === 'confirm') {
			// met à jour la fiche
			// Rmq : la date de validation est normalement mise à jour par les rules, ici on force la date pour que le fichier PDF généré juste après ait sa dernière validation à jour
			Script::$db->exec("
				UPDATE demande SET 
					etape = ?, 
					token = ?, 
					permission = ?, 
					`date_validation$numCurrentValidation` = NOW(), 
					`confirmation_valideur$numCurrentValidation` = ? ,
					commentaire_refus = ?
				WHERE iddemande = ?", 
				array(
					$nextStep, 
					Script::generateRandomString(16), 
					$permission, 
					$_REQUEST['answer'] === 'yes' ? 1 : 0, 
					isset($_REQUEST['commentaire_refus']) ? $_REQUEST['commentaire_refus'] : null,
					$_REQUEST['id'],
				));
			// met à jour le fichier PDF
			// Rmq 1 : pour simplifier le processus le nom du fichier PDF n'est pas mis à jour, en théorie le nom ne change car les champs dont il dépend : date_demande, direction, type_contrat... ne changent pas dans ce webservice)
			// Rmq 2 : utiliser le webservice en mode HTTP ne fonctionne pas car ce dernier attend un user authentifié, ce qui n'est pas nécessairement le cas ici (et de toute façon on ne transmet pas le cookie de session)

			ob_start();
			$_REQUEST['cle'] = $demande->cle; // uniquement pour le modèle PDF
			include Script::buildPath('file','__pdf__','demande.php'); // le modèle PDF
			$content = ob_get_clean();
			require_once Script::buildPath('lib','pdf','html2pdf','html2pdf.class.php');
			try {
				$html2pdf = new HTML2PDF('P','A4', 'fr', true, 'UTF-8', array(15, 5, 15, 5));
				$html2pdf->pdf->SetDisplayMode('fullpage');
				$html2pdf->writeHTML($content, isset($_GET['vuehtml']));
				$html2pdf->Output(Script::buildPath('file','demande',$demande->pdf),'F');
			}
			catch(HTML2PDF_exception $e) { exit($e); }
			// déclenche les rules
			RulesManager::executeOnSave('demande', $demande->cle, false);
		}
		$demande = Script::$db->fetch("
			SELECT candidat.*, direction.intitule AS direction_intitule 
			FROM candidat 
			LEFT JOIN direction ON direction.cle = candidat.direction
			WHERE idcandidat = ?", 
		$_REQUEST['id']);
		$demande->isStagiaire = !($demande->type_contrat === 'CDD' || $demande->type_contrat === 'CDI');


		?><!DOCTYPE html>
	<html><head>
		<title>Validation demande | Recrutement</title>
		<link rel="stylesheet" href="<?php echo Script::getApplicationURL(); ?>/lib/bootstrap/css/bootstrap.min.css" >
		<link rel="stylesheet" href="<?php echo Script::getApplicationURL(); ?>/lib/bootstrap/css/bootstrap-theme.min.css" >
		<style>
			body { font-size: larger; }
			.gopaas-accepter { color: #088A08; }
			.gopaas-refuser { color: #C12E2A; }
			.gopaas-value { font-weight: bold; }
			.gopaas-red { color: #C12E2A; }
		</style>
		<script>
			function find(id) { return document.getElementById(id); }
			function fixedEncodeURIComponent (str) { // https://developer.mozilla.org/en/docs/Web/JavaScript/Reference/Global_Objects/encodeURIComponent
				return encodeURIComponent(str).replace(/[!'()*]/g, function(c) {
					return '%' + c.charCodeAt(0).toString(16);
				});
			}
		</script>
	</head><body>
		<img src="../../asset/fond_token.png"><br><br>
	<div style="margin-left:15px;">
		Intitulé du poste:           <span class="gopaas-value"><?php echo $demande->intitule_poste; ?></span><br>
		Société:                     <span class="gopaas-value"><?php echo $demande->societe; ?></span><br>
		Direction:                   <span class="gopaas-value"><?php echo $demande->direction_intitule; ?></span><br>
		Type de contrat:             <span class="gopaas-value"><?php echo $demande->type_contrat; ?></span><br>
		<?php if ($demande->isStagiaire) { ?>
		Salaire de base brut mensuel: <span class="gopaas-value"><?php echo number_format($demande->salaire_fixe_brut_mensuel,0,',',' ').' €'; ?></span><br>
		<?php } else { ?>
		Motif de recrutement:        <span class="gopaas-value gopaas-red"><?php echo $demande->motif_recrutement; ?></span><br>
		Salaire de base brut annuel: <span class="gopaas-value"><?php echo number_format($demande->salaire,0,',',' ').' €'; ?></span><br>
		Budgété:                     <span class="gopaas-value gopaas-red"><?php echo $demande->prevu_dernier_pb_actu; ?></span> <?php echo $demande->prevu_dernier_pb_actu_commentaire; ?><br>
		<?php } ?>
		Date souhaitée d'arrivée:    <span class="gopaas-value"><?php echo ($demande->date_arrivee_souhaitee && $demande->date_arrivee_souhaitee !== "0000-00-00" ? date_format(new DateTime($demande->date_arrivee_souhaitee), "d/m/Y") : ""); ?></span><br>
		Date de fin:                 <span class="gopaas-value"><?php echo ($demande->date_fin && $demande->date_fin !== "0000-00-00" ? ($demande->date_fin === 'NA' ? 'NA' : date_format(new DateTime($demande->date_fin), "d/m/Y")) : ""); ?></span><br>
		Durée:                       <span class="gopaas-value"><?php echo $demande->duree; ?></span><br>
		Mission principale:
		<p class="gopaas-value" style="margin-left: 50px;">
			<?php echo $demande->mission_principale; ?>
		</p>
	</div>
	<div style="margin-left:15px;">

	<?php if ($_REQUEST['service'] === 'confirm') { ?>
		<span style="">
		<?php if ($_REQUEST['answer'] === 'yes') { ?>
			Vous avez donné votre <strong class="gopaas-accepter">accord</strong> pour ce candidat sélectionné
		<?php } else { ?>
			Vous avez <strong class="gopaas-refuser">refusé</strong> ce candidat sélectionné
		<?php } ?>
		</span>


	<?php } else { ?>
			<?php if ($_REQUEST['answer'] === 'yes') { ?>
			<!--
				<input id="confirmCheckbox" type="checkbox" onchange="find('confirmButton').disabled = !this.checked;">
				<span style="cursor: default;" onclick="var cb = find('confirmCheckbox'); cb.checked = !cb.checked; find('confirmButton').disabled = !cb.checked;">
					Je confirme donner mon <strong class="gopaas-accepter">accord</strong> pour cette demande d'embauche
				</span><br>
				<button id="confirmButton" type="button" disabled
					onclick="document.location.href = '<?php echo Script::getApplicationURL().
							"/template_auto/demande/demande.php?service=confirm&answer=$_REQUEST[answer]&id=$_REQUEST[id]&token=$_REQUEST[token]"
						; ?>';
					"
				>Envoyer</button>
			-->
				<button id="confirmButton" type="button" class="btn btn-danger"
					onclick="document.location.href = '<?php echo Script::getApplicationURL().
							"/template_auto/demande/demande.php?service=confirm&answer=$_REQUEST[answer]&id=$_REQUEST[id]&token=$_REQUEST[token]"
						; ?>';
					"
				>Je confirme avoir pris connaissance de l'organigramme</button>


			<?php } else { ?>
				<br>
				<p>
					<span>Commentaire (obligatoire)</span><br>
					<input type="text" id="commentaire_refus" name="commentaire_refus" style="width: 400px;" oninput="find('confirmButton').disabled = (this.value ? false : true);"
						onkeypress="if (event.which === 13) { var e = document.createEvent('Event'); e.initEvent('click',true,true); find('confirmButton').dispatchEvent(e); return false; } return true;"
					><br>
				</p>
				<button id="confirmButton" type="button" class="btn btn-danger" disabled
					onclick="document.location.href = '<?php echo Script::getApplicationURL().
							"/template_auto/demande/demande.php?service=confirm&answer=$_REQUEST[answer]&id=$_REQUEST[id]&token=$_REQUEST[token]&commentaire_refus="
						; ?>' + fixedEncodeURIComponent(find('commentaire_refus').value);
					"
				>Je confirme <strong>refuser</strong> ce candidat sélectionné</button>
			<?php } ?>
	<?php } ?>
	</div>
	</body></html><?php

	//-------------------------------------------------------
	} else if ($_REQUEST['service'] === 'get_liste_service') { // renvoie la liste des fiches "sous_service" de la base de données
		if (!Script::isAuthentified()) { // on est obligé de contrôler manuellement l'authentification, qui a été désactivée dans Script::init() . Voir plus haut l'appel à Script::init(), paramètre 'session'.
			throw new GPSessionException();
		}
		echo json_encode(Script::$db->fetchAll("select cle, intitule, societe, direction from sous_service"));

	//-------------------------------------------------------
	} else {
		throw new GPFatalException("Le service demandé '$_REQUEST[service]' est inconnu");
	}
}