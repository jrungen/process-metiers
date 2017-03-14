<?php // a supprimer

Script::init(array(
	'content' => 'application/json',
	'param_not_empty' => array("service"),
	'session' => false, // l'authentification doit être contrôlée au cas par cas en fonction du service demandé. certains services doivent être accessibles sans authentification, notamment le service 'validate'.
));

require_once Script::buildPath('class','RulesManager.php');

function normaliserPermission($permission) { // cette fonction supprime les doublons de permission, enlève les éventuelles valeurs vides, et tri les valeurs, ceci afin d'éviter les pb de calcul de différence à l'enregistrement
	if (!$permission) return $permission;
	$permission = array_unique(array_filter(explode(',', $permission),"strlen"));
	sort($permission);
	return implode(',', $permission);
}

//-------------------------------------------------------
if ($_REQUEST['service'] === 'autre') {
	if ($_REQUEST['mode'] === 'update_pap') {
			Script::$db->exec("
			UPDATE a07postesbudgetaires SET 
				dar_attribuee = '1'
			WHERE cle = '".$_REQUEST['cle_pap']."';");
		echo json_encode('OK');
	}
}else if ($_REQUEST['service'] === 'get_regle_valideur') {
	
	if (!Script::isAuthentified()) { // on est obligé de contrôler manuellement l'authentification, qui a été désactivée dans Script::init() . Voir plus haut l'appel à Script::init(), paramètre 'session'.
		throw new GPSessionException();
	}

	$regle_valideur = Script::$db->fetch("
		SELECT valideur1, valideur2, valideur3, valideur4, valideur5, valideur6, valideur7
		FROM regle_validateur
		WHERE
			fichier = :fichier AND
			salaire = :valeur_salaire AND
			societe = :societe AND
			IFNULL(direction,'') = :direction AND
			type_contrat = :type_contrat AND
			(
				type_contrat IN ('CTT002','CTT001') AND
				(
					transverse = :transverse AND
					(duree = :duree OR type_contrat = 'CTT001') AND
					metier = :metier AND
					motif_recrutement = :motif_recrutement
				)
				OR type_contrat NOT IN ('CTT002','CTT001')
			)
	", array(
		"societe" => $_REQUEST["societe"],
		"type_contrat" => $_REQUEST["type_contrat"],
		"duree" => $_REQUEST["duree"],
		"metier" => $_REQUEST["metier"],
		"motif_recrutement" => $_REQUEST["motif_recrutement"],
		"direction" => $_REQUEST["direction"],
		"transverse" => $_REQUEST["transverse"],
		"fichier" => 'demande',
		"valeur_salaire" => $_REQUEST["valeur_salaire"]
	));
	if (!$regle_valideur) {
		echo "{}";
		// TEST DE LA REQUETE
		// echo "SELECT valideur1, valideur2, valideur3, valideur4, valideur5, valideur6, valideur7
		// FROM regle_validateur
		// WHERE
			// fichier = 'demande' AND
			// societe = '".$_REQUEST['societe']."' AND
			// IFNULL(direction,'') = '".$_REQUEST['direction']."' AND
			// type_contrat = '".$_REQUEST['type_contrat']."' AND
			// (
				// type_contrat IN ('CTT002','CTT001') AND
				// (
					// transverse = '".$_REQUEST['transverse']."' AND
					// (duree = '".$_REQUEST['duree']."' OR type_contrat = 'CTT001') AND
					// metier = '".$_REQUEST['metier']."' AND
					// motif_recrutement = '".$_REQUEST['motif_recrutement']."'
				// )
				// OR type_contrat NOT IN ('CTT002','CTT001')
			// )";
		// TEST DE LA REQUETE
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

	$demande = Script::$db->fetch("SELECT etape, cle, permission, valideur1, valideur2, valideur3, valideur4, valideur5, valideur6, pdf FROM demande WHERE iddemande = ? AND token = ?", array($_REQUEST['id'],$_REQUEST['token']));
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
		/* Http::post(Script::getApplicationURL()."/webservice/pdf/html2pdf.php", array(
				'action' => "file",
				'table'  => "demande",
				'modele' => "demande",
				'cle'    => $demande->cle,
				'nom'    => $demande->pdf,
		)); */
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
	// JR le 15/02/2017 mise à jour le la requette avec les nouveaux champs de connexion
	$demande = Script::$db->fetch("
		SELECT
			r15direction.r15direction AS direction_intitule,
			r00societes.r00libsociete AS societe_intitule,
			r05postes.r05libelleposte AS poste_intitule,
			r06typecontrat.r06codetypecontrat as contrat_intitule,
			r11motifentree.r11libmotifentree as motif_recrut_intitule,
			demande.*
		FROM demande 
			LEFT JOIN r15direction ON r15direction.cle = demande.d00direction
			LEFT JOIN r00societes ON r00societes.cle = demande.d00societes
			LEFT JOIN r05postes ON r05postes.cle = demande.d00postes
			LEFT JOIN r06typecontrat ON r06typecontrat.cle = demande.r06typecontrat
			LEFT JOIN r11motifentree ON r11motifentree.cle = demande.d00motifrecrutement
		WHERE iddemande = ?", 
	$_REQUEST['id']);
	$demande->isStagiaire = !($demande->type_contrat === 'CTT002' || $demande->type_contrat === 'CTT001');


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
	Intitulé du poste:           <span class="gopaas-value"><?php echo $demande->poste_intitule; ?></span><br>
	Société:                     <span class="gopaas-value"><?php echo $demande->societe_intitule; ?></span><br>
	Direction:                   <span class="gopaas-value"><?php echo $demande->direction_intitule; ?></span><br>
	Type de contrat:             <span class="gopaas-value"><?php echo $demande->contrat_intitule; ?></span><br>
	<?php if ($demande->isStagiaire) { ?>
	Salaire de base brut mensuel: <span class="gopaas-value"><?php echo number_format($demande->salaire_fixe_brut_mensuel,0,',',' ').' €'; ?></span><br>
	<?php } else { ?>
	Motif de recrutement:        <span class="gopaas-value gopaas-red"><?php echo $demande->motif_recrut_intitule; ?></span><br>
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
		Vous avez donné votre <strong class="gopaas-accepter">accord</strong> pour cette demande d'embauche
	<?php } else { ?>
		Vous avez <strong class="gopaas-refuser">refusé</strong> cette demande d'embauche
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
			>Je confirme <strong>refuser</strong> cette demande d'embauche</button>
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