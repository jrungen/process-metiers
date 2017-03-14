function onLoad_candidat (){

	var thisComponent = this,
		mpl = "mplalain",
		etape = this.getValue("etape") || "Brouillon",
		type_contrat = this.getValue("type_contrat")
	;

	// JR le 02/02/2017 création du CARTOUCHE
	// var vCartouche = thisComponent.getValue("type_contrat")+' '+thisComponent.getValue("intitule_poste")+' - '+thisComponent.getValue("t01_01_nom_candidat")+' '+thisComponent.getValue("t01_02_prenom_candidat");
	// thisComponent.ui.find('.form-horizontal > h3').html('<i class="fa fa-user" style="color:#8E3557;"></i> '+vCartouche);
	
	// JR le 15/02/2017 MAJ du cartouche avec les nouveaux champs de connexion
	// 1er get-item pour sur type de contrat
	if (thisComponent.getValue('cs00typecontrat')){
		$.get("webservice/item/get-item.php", {
			tableName	: "r06typecontrat",
			itemKey  	: thisComponent.getValue('cs00typecontrat')
		})
		.done(function(data1){
		// je récupère l'intitulé du code type de contrat
			var code_type_contrat = data1.r06codetypecontrat;	
			
			var vCartouche1 = code_type_contrat+' - '+thisComponent.getValue("t01_01_nom_candidat")+' '+thisComponent.getValue("t01_02_prenom_candidat");
			thisComponent.ui.find('.form-horizontal > h3').html('<i class="fa fa-user" style="color:#8E3557;"></i> '+vCartouche1);
			
			// 2ème get-item pour sur intitulé du poste
			if (thisComponent.getValue('cs00postes')){
				$.get("webservice/item/get-item.php", {
					tableName	: "r05postes",
					itemKey  	: thisComponent.getValue('cs00postes')
				})
				.done(function(data2){
				// je récupère l'intitulé du poste
					var intitule_poste = data2.r05libelleposte;	
					
					var vCartouche2 = code_type_contrat+' '+intitule_poste+' - '+thisComponent.getValue("t01_01_nom_candidat")+' '+thisComponent.getValue("t01_02_prenom_candidat");
					thisComponent.ui.find('.form-horizontal > h3').html('<i class="fa fa-user" style="color:#8E3557;"></i> '+vCartouche2);

				}).fail(gopaas.dialog.ajaxFail);
			}// FIN 2ème get-item pour sur intitulé du poste

		}).fail(gopaas.dialog.ajaxFail);
	}// FIN 1er get-item pour sur type de contrat
	
	// Add action in item
	this.addTool("Imprimer PDF","Imprimer PDF", function() { onPrint_candidat(thisComponent); });
	this.addTool("Créer Personne physique","Créer Personne physique", function() { personne_physique(thisComponent); });
	
	calcul_rem_mensuelle(thisComponent);
	//___________________________________________________________________________________________________________
	//___________________________________________________________________________________________________________
	//
	//
	//                      FONCTIONS
	//
	//___________________________________________________________________________________________________________
	//___________________________________________________________________________________________________________

	//-----------------------------------------------
	// cette fonction ajoute un commentaire en haut de la fiche.
	function addComment(text) {
		var span = $("<span>" + text + "</span>");
		var buttonRow = thisComponent.ui.find(".gopaas-button-save-and-close").closest(".row"); // la ligne contenant les boutons Enregistrer, Appliquer, Fermer, etc.
		var row = $("<div></div>").css("text-align","center"); // une nouvelle ligne pour mettre le commentaire
		var col = $("<div></div>").addClass("col-xs-12"); // une nouvelle colonne de largeur 12 pour mettre le commentaire
		col.append(span); // met le commentaire dans la colonne
		row.append(col); // met la colonne dans la ligne
		buttonRow.before(row); // place la nouvelle ligne juste avant la ligne des boutons
	}
	
	//-----------------------------------------------
	thisComponent.getPDFFileName = function() {
		var data = [
			($('#COMPLEMENT_cs00societes').val() || '').replace(/[^\w ]/g,''),
			($('#COMPLEMENT_cs00direction').val() || '').replace(/[^\w ]/g,''),
			($('#COMPLEMENT_cs00typecontrat').val() || '').replace(/[^\w ]/g,''),
			($('#COMPLEMENT_cs00postes').val() || '').replace(/[^\w ]/g,''),
			(this.getValue("t01_01_nom_candidat") || '').replace(/[^\w ]/g,''),
			(this.getValue("t01_02_prenom_candidat") || '').replace(/[^\w ]/g,'')
		];
		return data.join(' - ') + '.pdf';
	};

	
	// AJOUT AKERNECH DU 26/01/2017 Champ Cabinet masqué si origine du recrutement =/= Cabinet de recrutement
	
	function origine_recrutement (thisComponent) { 
		if (thisComponent.getValue ('t01_24_type_recrutement') !== "Cabinet") {
		thisComponent.ui.find("#t01_25_nom_cabinet").closest('.form-group').hide();
		} else {
		thisComponent.ui.find("#t01_25_nom_cabinet").closest('.form-group').show();	
		}
	}
	// Origine recrutement
	thisComponent.ui.find("[name=t01_24_type_recrutement]").on("change", function(){
		origine_recrutement(thisComponent);
	}).trigger("change");

	
	//-----------------------------------------------
	// renvoie un objet JSON contenant les valeurs actuelles des champs à surveiller
	// pour détecter les modifs
	thisComponent.getEtatDemande = function () {
		return {
			t01_24_type_recrutement:	this.getValue("t01_24_type_recrutement"),
			t01_27_commentaire_rh:		this.getValue("t01_27_commentaire_rh"),
			t01_25_nom_cabinet:			this.getValue("t01_25_nom_cabinet")
			// societe:                   this.getValue("societe"),
			// direction:                 this.getValue("direction"),
			// date_arrivee_souhaitee:    this.getValue("t01_30_date_debut_contrat"),
			// salaire:                   this.getValue("salaire"),
			// salaire_fixe_brut_mensuel: this.getValue("salaire_fixe_brut_mensuel"),
			// organigramme             : this.getValue("organigramme")
		};
	};

	//-----------------------------------------------
	// renvoie un tableau JSON avec la liste des différences par rapport à la demande initiale.
	// Renvoie un tableau vide [] si aucun changement
	thisComponent.getChangementDemande = function () {
		var demandeInitiale = JSON.parse(this.getValue("demande_initiale")),
			demandeActuelle = this.getEtatDemande(),
			differences = [],
			fieldName
		;
		for (var fieldName in demandeInitiale) {
			if (demandeInitiale[fieldName] != demandeActuelle[fieldName]) {
				differences.push({
					"name" : fieldName,
					"label" : thisComponent.ui.find("[name=" + fieldName + "]").closest(".form-group").find("label span.trn").html(),
					"before" : demandeInitiale[fieldName],
					"after" : demandeActuelle[fieldName]
				});
			}
		}
		return differences;
	};

	//-----------------------------------------------
	// cette fonction se charge aussi de vérifier la case à cocher de confirmation,
	// et d'enregistrer la fiche.
	thisComponent.accept = function (valideurNumber, valideurAcceptStep) { // pour l'arbitrage le valeurNumber est le 6
		console.log('valideurNumber:'+valideurNumber);
		console.log('valideurAcceptStep:'+valideurAcceptStep);
		
		if (!valideurNumber || !valideurAcceptStep) {
			console.error("paramètre 'valideurNumber' ou 'valideurAcceptStep' manquant");
			return false;
		}
		var thisComponent = this,
			differences = []
		;

		function send() {
			thisComponent.setValue("confirmation_valideur" + valideurNumber, true);
			thisComponent.setValue("etape", valideurAcceptStep);
			thisComponent.setValue("token", gopaas.util.generateRandomString(16));
			thisComponent.saveItem(true);
		}

		// vérifie que la demande initiale n'a pas été modifiée, auquel cas il faut forcer un retour au demandeur
		// sauf si admin
		differences = thisComponent.getChangementDemande();
		if (UTILISATEUR['groupe'] !== "Admin" && UTILISATEUR['groupe'] !== "GRP_ADM_GRE" && UTILISATEUR['groupe'] !== "GRP_RH_CORP") {
			/*
			if (differences.length !== 0) {
				gopaas.dialog.warning("Vous avez modifié la demande, vous devez renvoyer le formulaire au demandeur");
				return;
			}
			*/
		}	

		// confirme et envoie la demande
		/*
		if (!thisComponent.getValue("confirmation_valideur" + valideurNumber)) {
			//gopaas.dialog.confirm("Je confirme avoir pris connaissance de l'organigramme", function(ok) {
			//	if (ok) {
					send();
			//	}
			//});
		} else { // si déjà coché: acceptation directe
			
		}
		*/
		send();
		
	};

	//-----------------------------------------------
	thisComponent.updateValideur3Transverse = function() {
		var transverse = thisComponent.getValue("transverse"),
			metier = thisComponent.getValue("metier"),
			type_contrat = thisComponent.getValue("type_contrat"),
			nb_mois_cdd = thisComponent.getValue("nb_mois_cdd")
		;

		if (thisComponent.ui.find("[name=transverse]").hasClass("disabled")) {
			return; // cette fonction ne s'exécute que si le champ Transverse est modifiable (= édité par mpl)
		}

		if (!thisComponent.onLoadIsFinished) { // il ne faut pas lancer la fonction à l'ouverture de la fiche, mais seulement lorsqu'un champ est réellement modifié, sinon on risque d'appliquer le cas "validation transverse" alors que la fiche a déjà été mise en mode "validation transverse", ce qui a pour effet de retirer à tort le valideur 3)
			return;
		}
		
		$.get("template_auto/candidat/candidat.php", {
			mode     	 : "regle_valideur",
			service      : "get_regle_valideur",
			societe      : thisComponent.getValue("cs00societes"),
			type_contrat : thisComponent.getValue("cs00typecontrat"),
			duree        : thisComponent.getValue("nb_mois_cdd"),
			direction    : thisComponent.getValue("cs00direction"),
			motif_recrutement: thisComponent.getValue("cs00motifentree"),
			metier       : thisComponent.getValue("cs00metiers"),
			transverse   : thisComponent.getValue("transverse")
		}).done(function(result) {
			// setConnectionValue(champ, tableConnectée, clé) . setConnectionValue() est similaire à setValue() sauf qu'il met à jour le complément.
			thisComponent.setConnectionValue("valideur1", "utilisateur", result.valideur1 || "");
			thisComponent.setConnectionValue("valideur2", "utilisateur", result.valideur2 || "");
			thisComponent.setConnectionValue("valideur3", "utilisateur", result.valideur3 || "");
			thisComponent.setConnectionValue("valideur4", "utilisateur", result.valideur4 || "");
			thisComponent.setConnectionValue("valideur5", "utilisateur", result.valideur5 || "");
			thisComponent.setConnectionValue("valideur6", "utilisateur", result.valideur6 || "");
			// setPermission(thisComponent);
			thisComponent.saveItem(clicEnregistrer ? false : true, function() { generatePdf(); } ); // si on vient d'un clic sur le bouton Enregistrer, il faut lancer l'enregistrement sans fermer la fiche (paramètre false)
		}).fail(gopaas.dialog.ajaxFail);
	};

	//-----------------------------------------------
	thisComponent.setPermission = function() { // recalcule l'ensemble des permissions. !! ATTENTION: si cette fonction est modifiée, il faut adapter son équivalent côté PHP dans le script PHP personnalisé de la table demande.
		var demandeur = this.getValue("demandeur");
		var creation_par = this.getValue("creation_par");
		var valideur1 = this.getValue("valideur1");
		var valideur2 = this.getValue("valideur2");
		var valideur3 = this.getValue("valideur3");
		var valideur4 = this.getValue("valideur4");
		var valideur5 = this.getValue("valideur5");
		var valideur6 = this.getValue("valideur6");
		var etape = this.getValue("etape");
		var valideurNumber = null;
		var valideurField = null;
		var permission = [];

		if (etape.indexOf("Attente valideur") === 0) {
			valideurNumber = parseInt(etape.substr("Attente valideur ".length,1));
			valideurField = "valideur" + valideurNumber;
		}

		// demandeur et créateur
		permission.push(demandeur);
		permission.push(creation_par);
		permission.push('akernech');

		// valideurs
		// JR le 21/12/2016 le statut Renvoyer au demandeur n'existe plus pour les candidats, demande d'ADRIEN
		if (etape !== "Brouillon" && etape !== "Annuler" && etape !== "Annulée") {

			if (etape === "Envoi demande" || etape === "Attente valideur 1" || etape === "Valideur 1 refuse") {
				permission.push(valideur1);
			}
			else if (etape === "Valideur 1 accepte" || etape === "Valideur 2 refuse" || etape === "Attente valideur 2") {
				permission.push(valideur1);
				permission.push(valideur2);
			}
			else if (etape === "Valideur 2 accepte" || etape === "Valideur 3 refuse" || etape === "Attente valideur 3") {
				permission.push(valideur1);
				permission.push(valideur2);
				permission.push(valideur3);
			}
			else if (etape === "Valideur 3 accepte" || etape === "Valideur 4 refuse" || etape === "Attente valideur 4") {
				permission.push(valideur1);
				permission.push(valideur2);
				permission.push(valideur3);
				permission.push(valideur4);
			}
			else if (etape === "Valideur 4 accepte" || etape === "Valideur 5 refuse" || etape === "Attente valideur 5") {
				permission.push(valideur1);
				permission.push(valideur2);
				permission.push(valideur3);
				permission.push(valideur4);
				permission.push(valideur5);
			}
			else if (etape === "Valideur 5 accepte" || etape === "Valideur 6 refuse" || etape === "Attente valideur 6" ||
				etape === "Attente validation" || etape === "Arbitrage accepté" || etape === "Arbitrage refusé" )  // arbitrage
			{
				permission.push(valideur1);
				permission.push(valideur2);
				permission.push(valideur3);
				permission.push(valideur4);
				permission.push(valideur5);
				permission.push(valideur6);
			}
			else if (etape === "Acceptée" || etape === "Refusée") {
				if (this.getValue("date_validation1")) { permission.push(valideur1); }
				if (this.getValue("date_validation2")) { permission.push(valideur2); }
				if (this.getValue("date_validation3")) { permission.push(valideur3); }
				if (this.getValue("date_validation4")) { permission.push(valideur4); }
				if (this.getValue("date_validation5")) { permission.push(valideur5); }
				if (this.getValue("date_validation6")) { permission.push(valideur6); }
			}
		}
		this.setValue("permission",permission);
		this.normaliserPermission();

	};

	//-----------------------------------------------
	thisComponent.normaliserPermission = function () { // cette fonction supprime les doublons de permission, enlève les éventuelles valeurs vides, et tri les valeurs, ceci afin d'éviter les pb de calcul de différence à l'enregistrement
		var permission = this.getValue("permission");

		if (!permission) { return; }
		var permission_split = permission.split(",");
		var permission_normal = []; // liste des permissions normalisées (pas de doublon, pas de valeur vide, valeurs triées)
		for (var i=0; i<permission_split.length; i++) {
			var p = permission_split[i];
			if (p && $.inArray(p,permission_normal) === -1) { // on n'ajoute la permission que si elle n'existe pas déjà afin d'éviter les doublon
				permission_normal.push(p);
			}
		}
		permission_normal = permission_normal.sort().join(","); // le tri est important pour s'assurer que 2 combinaisons de permission identique résultent dans un champ permission identique pour éviter que le framework détecte des différences alors qu'il n'y en n'a pas
		this.setValue("permission", permission_normal);
	};
	
	//-----------------------------------------------
	thisComponent.addEuroLabel = function (fieldName) {
		var thisComponent = this;
		$("<label>&euro;</label>")
			.insertAfter(
				thisComponent.ui.find("[name='"+fieldName+"']").closest("div")
					.css("padding-right","6px")
					.removeClass("col-sm-5")
					.addClass("col-sm-4")
			)
			.css("text-align","left")
			.css("padding-left","0")
			.addClass("col-sm-1 control-label")
		;
	};
	
	
	//-----------------------------------------------
	// cette fonction ajoute un bouton à gauche du bouton enregistrer. S'il y a déjà d'autres boutons à gauche de Enregistrer, le nouveau bouton est mis à leur droite.
	// le paramètre title est facultatif.
	function addButton(label, title, onClick) {
		if (typeof title === "function") { // si le paramètre title n'est pas utilisé
			onClick = title;
			title = null;
		}

		var button = $("<button></button>");
		button.attr("type","button");
		button.click(onClick);
		button.html(label);
		button.addClass("btn btn-sm");
		button.css("margin-right","3px");//.css("color","white").css("background-color","#562380");
		if (title) {
			button.attr("title",title);
		}
		thisComponent.ui.find(".gopaas-button-save-and-close").before(button); // insère le nouveau bouton juste à gauche du bouton Enregistrer
		return button;
	}
	
	//___________________________________________________________________________________________________________
	//___________________________________________________________________________________________________________
	//
	//
	//                      EVENEMENTS
	//
	//___________________________________________________________________________________________________________
	//___________________________________________________________________________________________________________	
	
	
	//-----------------------------------------------
	// bloquer les champs confirmation_valideurX
	thisComponent.ui.find("[name^=confirmation_valideur]").on("change", function() {
		this.checked = !this.checked;
	});
	
	
	function calcul_rem_mensuelle(thisComponent){
		var vSalMensuel = thisComponent.getValue('t01_33_remuneration_fixe_annuelle_brute')/13;
		thisComponent.setValue('t01_18_remuneration_fixe_mensuelle_brute',vSalMensuel);
	}

	function calcul_rem_annuelle(thisComponent){
		var vSalAnnuel = thisComponent.getValue('t01_18_remuneration_fixe_mensuelle_brute')*13;
		thisComponent.setValue('t01_33_remuneration_fixe_annuelle_brute',vSalMensuel);
	}	
	
	// Calcul le salaire brut mensuel
	thisComponent.ui.find("[name^=t01_33_remuneration_fixe_annuelle_brute]").on("change", function() {
		calcul_rem_mensuelle(thisComponent);
	});
	
	// Calcul le salaire brut annuel
	thisComponent.ui.find("[name^=t01_18_remuneration_fixe_mensuelle_brute]").on("change", function() {
		calcul_rem_annuelle(thisComponent);
	});
	
	
	//-----------------------------------------------
	// bloquer les champs date_validationx sauf si admin
	// bloquer les champs valideurx sauf si admin
	if (UTILISATEUR['groupe'] !== "Admin" && UTILISATEUR['groupe'] !== "GRP_ADM_GRE" && UTILISATEUR['groupe'] !== "GRP_RH_CORP") {
		thisComponent.ui.find("[name=date_validation]").attr("disabled","disabled").addClass("disabled");
		thisComponent.ui.find("[name=valideur]").siblings("span.input-group-btn").children().attr("disabled","disabled").addClass("disabled");
	}
	
	//___________________________________________________________________________________________________________
	//___________________________________________________________________________________________________________
	//
	//
	//                      PERSONNALISATION STYLE ET BOUTONS
	//
	//___________________________________________________________________________________________________________
	//___________________________________________________________________________________________________________

	// Champ Transverse non éditable
	if (gsUser !== mpl && UTILISATEUR['groupe'] !== "Admin" && UTILISATEUR['groupe'] !== "GRP_ADM_GRE" && UTILISATEUR['groupe'] !== "GRP_RH_CORP" || etape !== "Attente valideur 1" && etape !== "Attente valideur 2" && etape !== "Attente valideur 3") {
		this.ui.find("[name=transverse]").addClass("disabled").on("focus", function() {$(this).blur(); gopaas.dialog.notifyInfo("Ce champ n'est pas modifiable"); });
	}

	// Thème violet sur les onglets, titres, etc
	$( "ul.nav-tabs > li > a" ).css('color','#562380');
	this.ui.find('.fa').css('color','#562380');
	$( "h4" ).css('color','#562380');
	$( "h3" ).css('color','#562380');

	// Précédent/Suivant, Enregistrer
	this.ui.find('.gopaas-button-previous').remove();
	this.ui.find('.gopaas-button-next').remove();
	this.ui.find('.gopaas-button-save-and-close').hide();

	// Appliquer
	this.ui.find('.gopaas-button-save').removeClass('btn-warning').addClass('btn-primary').removeAttr('onclick').off('click').click(function() { thisComponent.clicEnregistrer = true; thisComponent.saveItem(false); }).find('span.trn').html('Enregistrer');

	// Fermer
	this.ui.find('.gopaas-button-close').insertAfter(thisComponent.ui.find(".gopaas-button-save")).removeClass("btn-danger").css("background-color","#562380").css("color","white").find("span.trn").html("Quitter");

	// Boutons d'aide
	/*
	this.addHelpButton("prevu_dernier_pb_actu");
	this.addHelpButton("salaire");
	*/
	
	// Menu outil
	this.ui.find('#btn_action_menu li:eq(0)').hide();
	this.ui.find('#btn_action_menu li:eq(2)').hide();

	// Onglet System
	//thisComponent.ui.find("li a[href=#"+thisComponent.data.formId+"_2455]").closest("li").hide();

	// Impression PDF
	//this.addTool("Imprimer PDF","Imprimer le demande en PDF", function() { onPrint(thisComponent); });

	// Désactiver double clic sur les connexions
	// JE le 15/02/2017 suppression des connexions de la DAR, car le script actuel les rendent déjà innacessible
	thisComponent.ui.find("#COMPLEMENT_valideur1, #COMPLEMENT_valideur2, #COMPLEMENT_valideur3, #COMPLEMENT_valideur4, #COMPLEMENT_valideur5, #COMPLEMENT_valideur6").removeAttr('ondblclick').off('dblclick');

	// Signe euros dans le champ salaire brut annuel / mensuel
	// JE le 15/02/2017 Ajout des champs primes et salaire
	thisComponent.addEuroLabel("salaire");
	thisComponent.addEuroLabel("t01_33_remuneration_fixe_annuelle_brute");
	thisComponent.addEuroLabel("t01_18_remuneration_fixe_mensuelle_brute");
	thisComponent.addEuroLabel("t01primecontractuelle");
	thisComponent.addEuroLabel("t01primediscretionnaire");	
	
	//___________________________________________________________________________________________________________
	//___________________________________________________________________________________________________________
	//
	//
	//                      WORKFLOW
	//
	//___________________________________________________________________________________________________________
	//___________________________________________________________________________________________________________


	// Rappel : Accord de Recrutement EN LECTURE SEUL POUR TOUS LE MONDE ET TOUS LES STATUTS
	// désactive tous les input (ne fonctionne pas totalement pour les listes déroulante)
	this.ui.find("#section_1153 input, select, textarea").attr("readonly","readonly").attr("disabled","disabled").addClass("disabled");
	this.ui.find("#section_1158 input, select, textarea").attr("readonly","readonly").attr("disabled","disabled").addClass("disabled");
	this.ui.find("#section_1159 input, select, textarea").attr("readonly","readonly").attr("disabled","disabled").addClass("disabled");
	
	// this.ui.find("#combo_societe").attr("readonly","readonly").attr("disabled","disabled").addClass("disabled");
	// this.ui.find("[name=societe]").addClass("disabled").on("focus", function() {$(this).blur(); gopaas.dialog.notifyInfo("Ce champ n'est pas modifiable"); });

	// this.ui.find("#combo_motif_recrutement").attr("readonly","readonly").attr("disabled","disabled").addClass("disabled");
	// this.ui.find("[name=motif_recrutement]").addClass("disabled").on("focus", function() {$(this).blur(); gopaas.dialog.notifyInfo("Ce champ n'est pas modifiable"); });
	
	// this.ui.find("#combo_metier").attr("readonly","readonly").attr("disabled","disabled").addClass("disabled");
	// this.ui.find("[name=metier]").addClass("disabled").on("focus", function() {$(this).blur(); gopaas.dialog.notifyInfo("Ce champ n'est pas modifiable"); });
	
	// this.ui.find("#combo_type_contrat").attr("readonly","readonly").attr("disabled","disabled").addClass("disabled");
	// this.ui.find("[name=type_contrat]").addClass("disabled").on("focus", function() {$(this).blur(); gopaas.dialog.notifyInfo("Ce champ n'est pas modifiable"); });
	
	// this.ui.find("#combo_transverse").attr("readonly","readonly").attr("disabled","disabled").addClass("disabled");
	// this.ui.find("[name=transverse]").addClass("disabled").on("focus", function() {$(this).blur(); gopaas.dialog.notifyInfo("Ce champ n'est pas modifiable"); });
	
	// JR le 15/02/2017 les find ci-dessus ne fonctionnait pas sur les connexions, correction avec ceux ci-dessous
	this.ui.find("#COMPLEMENT_demandeur").closest(".input-group").find("button").attr("disabled","disabled").addClass("disabled");
	this.ui.find("#COMPLEMENT_cs00societes").closest(".input-group").find("button").attr("disabled","disabled").addClass("disabled");
	this.ui.find("#COMPLEMENT_cs00direction").closest(".input-group").find("button").attr("disabled","disabled").addClass("disabled");
	this.ui.find("#COMPLEMENT_cs00postes").closest(".input-group").find("button").attr("disabled","disabled").addClass("disabled");
	this.ui.find("#COMPLEMENT_cs00typecontrat").closest(".input-group").find("button").attr("disabled","disabled").addClass("disabled");
	this.ui.find("#COMPLEMENT_cs00motifentree").closest(".input-group").find("button").attr("disabled","disabled").addClass("disabled");
	this.ui.find("#COMPLEMENT_cs00metiers").closest(".input-group").find("button").attr("disabled","disabled").addClass("disabled");
	this.ui.find("#COMPLEMENT_cs00nommanager").closest(".input-group").find("button").attr("disabled","disabled").addClass("disabled");
		
	this.ui.find("#t01_25_nom_cabinet").attr("readonly","readonly").attr("disabled","disabled").addClass("disabled");
	this.ui.find("#combo_t01_24_type_recrutement").attr("readonly","readonly").attr("disabled","disabled").addClass("disabled");
	this.ui.find("#t01_27_commentaire_rh").attr("readonly","readonly").attr("disabled","disabled").addClass("disabled");
	
	// Rappel : Accord de Recrutement EN LECTURE SEUL POUR TOUS LE MONDE ET TOUS LES STATUTS

	//-----------------------------------------------
	// Brouillon
	if (etape === "Brouillon") {
		
		// si ce n'est pas le demandeur qui a ouvert la fiche :
		if (gsUser !== this.getValue("recruteur") && UTILISATEUR['groupe'] !== "Admin" && UTILISATEUR['groupe'] !== "GRP_ADM_GRE" && UTILISATEUR['groupe'] !== "GRP_RH_CORP") {
			
			// désactive tous les input (ne fonctionne pas totalement pour les listes déroulante)
			this.ui.find("input, select, textarea").attr("readonly","readonly").attr("disabled","disabled").addClass("disabled");
			this.ui.find(".gopaas-button-save-and-close, .gopaas-button-save").hide(); // cache les 2 boutons d'enregistrement
			
		} else {
			
			// si c'est le RECRUTEUR ou un ADMIN qui a ouvert la fiche on Active les champs			
			// Onglet PRINCIPAL > Informations Contractuelles			
			this.ui.find("#combo_t01_03_civilite_candidat").removeAttr("readonly","readonly").removeAttr("disabled","disabled").removeClass("disabled");
			this.ui.find("#combo_t01_16_statut").removeAttr("readonly","readonly").removeAttr("disabled","disabled").removeClass("disabled");
			this.ui.find("#combo_t01_19_nb_jours_rtt_monetises").removeAttr("readonly","readonly").removeAttr("disabled","disabled").removeClass("disabled");
			this.ui.find("#combo_t01typecontratGRE").removeAttr("readonly","readonly").removeAttr("disabled","disabled").removeClass("disabled");
			
			// Onglet Developpement RH > Informations Développement RH
			this.ui.find("#t01_25_nom_cabinet").removeAttr("readonly","readonly").removeAttr("disabled","disabled").removeClass("disabled");
			this.ui.find("#combo_t01_24_type_recrutement").removeAttr("readonly","readonly").removeAttr("disabled","disabled").removeClass("disabled");
			this.ui.find("#t01_27_commentaire_rh").removeAttr("readonly","readonly").removeAttr("disabled","disabled").removeClass("disabled");
			
			// Onglet Système > REACTIVE liste (retire les attributs disabled)
			this.ui.find("#combo_etape").removeAttr("disabled","disabled");
			this.ui.find("#demande_initiale").removeAttr("disabled","disabled");
			
			// AKE le 25/01/2017 Retirer le bouton de suppression
			/*addButton("Supprimer", "Supprimer cette demande", function() {
				thisComponent.setValue("etape", "Annuler");
				thisComponent.setValue("token", gopaas.util.generateRandomString(16));
				thisComponent.saveItem(true);
			}).addClass("btn-danger").insertAfter(thisComponent.ui.find(".gopaas-button-save"));
			*/

			// bouton pour envoyer la demande -> Soumettre le candidat
			addButton("Soumettre le candidat", "Soumettre le candidat", function() {
				
				// Cacher les Bouton envoyer et enregistrer
				thisComponent.ui.find("button[title='Soumettre le candidat']").attr("disabled","disabled");
				thisComponent.clicEnregistrer = false;
				
				// Générer PDF
				// console.log('Génère le PDF');
				$.post("file/__pdf__/html2pdf.php", {
					action : "file",
					table  : "candidat",
					modele : "candidat",
					cle    : thisComponent.getValue("cle"),
					nom    : thisComponent.getPDFFileName()
				}).done(function(result){
					// Rempli le champ pdf_candidat
					thisComponent.setValue("pdf_candidat",thisComponent.getPDFFileName());
					// après il enregistre et envoie la demande
					// console.log('Envoi la demande');
					thisComponent.setValue("etape", "Envoi demande");
					thisComponent.setValue("date_demande", gopaas.date.dateFr());
					thisComponent.setValue("token", gopaas.util.generateRandomString(16));
					thisComponent.saveItem(true);
				}).fail(gopaas.dialog.ajaxFail);
				
			}).addClass("btn-success");
		}
			
		// JR le 18/01/2017 Brouillon - Ajout du bouton annulation
		// JR le 31/01/2017 Correction des enchainements de pop-up
		if (UTILISATEUR['groupe'] === "Admin" || UTILISATEUR['groupe'] === "GRP_ADM_GRE" || UTILISATEUR['groupe'] === "GRP_RH_CORP"){
			// bouton Annulation 
			addButton("Annulation","Annulation du candidat", function() {
				// Appeler webservice get-item pour récuperer l'id de la demande
				var cle_demande = thisComponent.getValue('demande');
				if(cle_demande){
					$.get("webservice/item/get-item.php", {
						tableName	: "demande",
						itemKey  	: cle_demande
					})
					.done(function(data){
						// je récupère le champ que je souhaite, ici l'id de la demande
						var iddemande = data.iddemande;
						gopaas.dialog.confirm("Vous allez annuler le candidat N° "+iddemande+". Voulez-vous continuer ?", function(ok) {
							if (ok) {
								// OUI je continue sur l'étape suivante
								gopaas.dialog.confirm("Souhaitez-vous créer une nouvelle fiche candidat ?", function(ok) {

									if (ok) {
										// BOUTON OUI j'ouvre l'autre boite de dialog avec le commentaire à saisir
										gopaas.dialog.prompt('Merci de justifier votre annulation.', '', function (val2) {
										if (val2) {
											// Si OUI : Passage de la fiche actuelle en statut Annulée + création d’une nouvelle fiche candidat pour la même DAR + envoi mail aux personnes étant intervenues dans le circuit de la fiche GRE uniquement.
											thisComponent.setValue('commentaire_refus', val2);
											thisComponent.setValue("etape", "Annuler");
											thisComponent.setValue("token", gopaas.util.generateRandomString(16));

											// Webservice pour les envois emails annulation & création nouvelle fiche candidat
											var cle_candidat = thisComponent.getValue('cle');
											$.get("template_auto/candidat/candidat.php?mode=annulation&cle_candidat="+cle_candidat+"&user="+gsUser+"&commentaire_refus="+val2+"&candidat_a_creer=oui")
											.done(function(data)
											{
												gopaas.dialog.notifyInfo('Mail envoyé au(x) valideur(s)');
												thisComponent.saveItem(true);
											})
											.fail(gopaas.dialog.ajaxFail);

										}
										});
									}else{
										// BOUTON NON j'ouvre l'autre boite de dialog avec le commentaire à saisir
										gopaas.dialog.prompt('Merci de justifier votre annulation.', '', function (val2) {
										if (val2) {
											// Si NON : Passage de la fiche actuelle en statut Annulée + pas de création d’une nouvelle fiche candidat + envoi mail aux personnes étant intervenues dans le circuit de la fiche GRE + demandeur DAR.
											thisComponent.setValue('commentaire_refus', val2);
											thisComponent.setValue("etape", "Annuler");
											thisComponent.setValue("token", gopaas.util.generateRandomString(16));

											// Webservice pour les envois emails annulation & création nouvelle fiche candidat
											var cle_candidat = thisComponent.getValue('cle');
											$.get("template_auto/candidat/candidat.php?mode=annulation&cle_candidat="+cle_candidat+"&user="+gsUser+"&commentaire_refus="+val2+"&candidat_a_creer=non")
											.done(function(data)
											{
												gopaas.dialog.notifyInfo('Mail envoyé au(x) valideur(s)');
												thisComponent.saveItem(true);
											})
											.fail(gopaas.dialog.ajaxFail);
										}
										});
									}

								});
							}
						});
					})
				}
			}).addClass("btn-danger").insertAfter(thisComponent.ui.find(".gopaas-button-save"));
		}
	
	//-----------------------------------------------
	// Attente valideur
	} else if (etape.indexOf("Attente valideur") === 0) {
		// console.log('Etape = '+thisComponent.getValue('etape'));
		// console.log('User = '+gsUser);
		
		var valideurNumber = parseInt(etape.substr("Attente valideur ".length,1)),
			valideurField = "valideur" + valideurNumber,
			valideurAcceptStep = "Valideur " + valideurNumber + " accepte",
			valideurRejectStep = "Valideur " + valideurNumber + " refuse";

		// si ce n'est pas le valideur en cours qui a ouvert la fiche :
		if (gsUser !== this.getValue(valideurField) && gsUser !== this.getValue("recruteur") && UTILISATEUR['groupe'] !== "Admin" && UTILISATEUR['groupe'] !== "GRP_ADM_GRE" && UTILISATEUR['groupe'] !== "GRP_RH_CORP") {
			// désactive tous les input (ne fonctionne pas totalement pour les listes déroulante)
			this.ui.find("input, select, textarea").attr("readonly","readonly").attr("disabled","disabled").addClass("disabled");
			// cache les 2 boutons d'enregistrement
			this.ui.find(".gopaas-button-save-and-close, .gopaas-button-save").hide();
			// ajoute un petit commentaire explicatif en haut de la fiche (voir fonction addComment() plus haut)
			addComment("Cette demande est en attente de validation par <strong>"+this.getValue(valideurField)+"</strong>, vous ne pouvez pas la modifier");

		// si c'est le valideur en cours qui a ouvert la fiche :
		} else {
			// SI c'est le Recruteur ou un Admin on réactive les champs
			if (gsUser === this.getValue("recruteur") || UTILISATEUR['groupe'] === "Admin" || UTILISATEUR['groupe'] === "GRP_ADM_GRE" || UTILISATEUR['groupe'] === "GRP_RH_CORP"){
				// si c'est le RECRUTEUR ou un ADMIN qui a ouvert la fiche on Active les champs			
				// Onglet PRINCIPAL > Informations Contractuelles			
				this.ui.find("#combo_t01_03_civilite_candidat").removeAttr("readonly","readonly").removeAttr("disabled","disabled").removeClass("disabled");
				this.ui.find("#combo_t01_16_statut").removeAttr("readonly","readonly").removeAttr("disabled","disabled").removeClass("disabled");
				this.ui.find("#combo_t01_19_nb_jours_rtt_monetises").removeAttr("readonly","readonly").removeAttr("disabled","disabled").removeClass("disabled");
				
				// Onglet Developpement RH > Informations Développement RH
				this.ui.find("#t01_25_nom_cabinet").removeAttr("readonly","readonly").removeAttr("disabled","disabled").removeClass("disabled");
				this.ui.find("#combo_t01_24_type_recrutement").removeAttr("readonly","readonly").removeAttr("disabled","disabled").removeClass("disabled");
				this.ui.find("#t01_27_commentaire_rh").removeAttr("readonly","readonly").removeAttr("disabled","disabled").removeClass("disabled");
				
				// Onglet Système > REACTIVE liste (retire les attributs disabled)
				this.ui.find("#combo_etape").removeAttr("disabled","disabled");
				this.ui.find("#demande_initiale").removeAttr("disabled","disabled");
			}
			
			if (gsUser === this.getValue(valideurField) ){
				// cache les 2 boutons d'enregistrement
				this.ui.find(".gopaas-button-save-and-close, .gopaas-button-save").hide();
				this.ui.find("#section_1154 input, select, textarea").attr("readonly","readonly").attr("disabled","disabled").addClass("disabled");
				this.ui.find("#combo_etape").removeAttr("disabled","disabled");
			}
			
			// bouton Accepter
			addButton("Accepter", function() {
				thisComponent.accept(valideurNumber, valideurAcceptStep);
				// openActionItem("Demande d'accord de recrutement acceptée par "+thisComponent.getValue(valideurField));
			}).addClass("btn-success");
			// bouton Refuser
			addButton("Refuser", function() {
				
				gopaas.dialog.prompt('Ajoutez un commentaire (Attention ce refus sera définitif et le demandeur devra refaire le processus de demande s’il souhaite réitérer sa demande) :', '', function (val) {
					if (val) {
						thisComponent.setValue('commentaire_refus', val);
						thisComponent.setValue("confirmation_valideur" + valideurNumber, false);
						thisComponent.setValue("etape", valideurRejectStep);
						thisComponent.setValue("token", gopaas.util.generateRandomString(16));
						thisComponent.saveItem(true);

						// Webservice pour mettre à jour la DAR lié
						var demande = thisComponent.getValue('demande');
						$.get("template_auto/candidat/candidat.php?mode=maj_DAR&demande="+demande)
						.done(function(data)
						{
							gopaas.dialog.notifyInfo('Mise à jour DAR !!!');
						})
						.fail(gopaas.dialog.ajaxFail);
						
					}
				});
				// openActionItem("Demande d'accord de recrutement refusée par "+thisComponent.getValue(valideurField));
			}).addClass("btn-danger");
			// JR le 21/12/2016 Supprimer bouton Renvoyer au demandeur - demande d'ADRIEN	
		}
	
		// JR le 18/01/2017 Attente valideur - Ajout du bouton annulation
		// JR le 31/01/2017 Correction des enchainements de pop-up
		if (UTILISATEUR['groupe'] === "Admin" || UTILISATEUR['groupe'] === "GRP_ADM_GRE" || UTILISATEUR['groupe'] === "GRP_RH_CORP"){
			// bouton Annulation 
			addButton("Annulation","Annulation du candidat", function() {
				// Appeler webservice get-item pour récuperer l'id de la demande
				var cle_demande = thisComponent.getValue('demande');
				if(cle_demande){
					$.get("webservice/item/get-item.php", {
						tableName	: "demande",
						itemKey  	: cle_demande
					})
					.done(function(data){
						// je récupère le champ que je souhaite, ici l'id de la demande
						var iddemande = data.iddemande;
						gopaas.dialog.confirm("Vous allez annuler le candidat N° "+iddemande+". Voulez-vous continuer ?", function(ok) {
							if (ok) {
								// OUI je continue sur l'étape suivante
								gopaas.dialog.confirm("Souhaitez-vous créer une nouvelle fiche candidat ?", function(ok) {

									if (ok) {
										// BOUTON OUI j'ouvre l'autre boite de dialog avec le commentaire à saisir
										gopaas.dialog.prompt('Merci de justifier votre annulation.', '', function (val2) {
										if (val2) {
											// Si OUI : Passage de la fiche actuelle en statut Annulée + création d’une nouvelle fiche candidat pour la même DAR + envoi mail aux personnes étant intervenues dans le circuit de la fiche GRE uniquement.
											thisComponent.setValue('commentaire_refus', val2);
											thisComponent.setValue("etape", "Annuler");
											thisComponent.setValue("token", gopaas.util.generateRandomString(16));

											// Webservice pour les envois emails annulation & création nouvelle fiche candidat
											var cle_candidat = thisComponent.getValue('cle');
											$.get("template_auto/candidat/candidat.php?mode=annulation&cle_candidat="+cle_candidat+"&user="+gsUser+"&commentaire_refus="+val2+"&candidat_a_creer=oui")
											.done(function(data)
											{
												gopaas.dialog.notifyInfo('Mail envoyé au(x) valideur(s)');
												thisComponent.saveItem(true);
											})
											.fail(gopaas.dialog.ajaxFail);

										}
										});
									}else{
										// BOUTON NON j'ouvre l'autre boite de dialog avec le commentaire à saisir
										gopaas.dialog.prompt('Merci de justifier votre annulation.', '', function (val2) {
										if (val2) {
											// Si NON : Passage de la fiche actuelle en statut Annulée + pas de création d’une nouvelle fiche candidat + envoi mail aux personnes étant intervenues dans le circuit de la fiche GRE + demandeur DAR.
											thisComponent.setValue('commentaire_refus', val2);
											thisComponent.setValue("etape", "Annuler");
											thisComponent.setValue("token", gopaas.util.generateRandomString(16));

											// Webservice pour les envois emails annulation & création nouvelle fiche candidat
											var cle_candidat = thisComponent.getValue('cle');
											$.get("template_auto/candidat/candidat.php?mode=annulation&cle_candidat="+cle_candidat+"&user="+gsUser+"&commentaire_refus="+val2+"&candidat_a_creer=non")
											.done(function(data)
											{
												gopaas.dialog.notifyInfo('Mail envoyé au(x) valideur(s)');
												thisComponent.saveItem(true);
											})
											.fail(gopaas.dialog.ajaxFail);
										}
										});
									}

								});
							}
						});
					})
				}
			}).addClass("btn-danger").insertAfter(thisComponent.ui.find(".gopaas-button-save"));
		}

	//-----------------------------------------------
	// Arbitrage
	} else if (etape === "Attente validation") {
		// si ce n'est pas le/la responsable de l'arbitrage qui a ouvert la fiche :
		if (gsUser !== mpl && gsUser !== this.getValue("recruteur") && UTILISATEUR['groupe'] !== "Admin" && UTILISATEUR['groupe'] !== "GRP_ADM_GRE" && UTILISATEUR['groupe'] !== "GRP_RH_CORP") {
			// désactive tous les input (ne fonctionne pas totalement pour les listes déroulante)
			this.ui.find("input, select, textarea").attr("readonly","readonly").attr("disabled","disabled").addClass("disabled");
			// cache les 2 boutons d'enregistrement
			this.ui.find(".gopaas-button-save-and-close, .gopaas-button-save").hide();
			// ajoute un petit commentaire explicatif en haut de la fiche (voir fonction addComment() plus haut)
			addComment("Cette demande est en attente de validation, vous ne pouvez pas la modifier");

		// si c'est le valideur en cours qui a ouvert la fiche :
		} else {
			// SI c'est le Recruteur ou un Admin on réactive les champs
			if (gsUser === this.getValue("recruteur") || UTILISATEUR['groupe'] === "Admin" || UTILISATEUR['groupe'] === "GRP_ADM_GRE" || UTILISATEUR['groupe'] === "GRP_RH_CORP"){
				// si c'est le RECRUTEUR ou un ADMIN qui a ouvert la fiche on Active les champs			
				// Onglet PRINCIPAL > Informations Contractuelles			
				this.ui.find("#combo_t01_03_civilite_candidat").removeAttr("readonly","readonly").removeAttr("disabled","disabled").removeClass("disabled");
				this.ui.find("#combo_t01_16_statut").removeAttr("readonly","readonly").removeAttr("disabled","disabled").removeClass("disabled");
				this.ui.find("#combo_t01_19_nb_jours_rtt_monetises").removeAttr("readonly","readonly").removeAttr("disabled","disabled").removeClass("disabled");
				
				// Onglet Developpement RH > Informations Développement RH
				this.ui.find("#t01_25_nom_cabinet").removeAttr("readonly","readonly").removeAttr("disabled","disabled").removeClass("disabled");
				this.ui.find("#combo_t01_24_type_recrutement").removeAttr("readonly","readonly").removeAttr("disabled","disabled").removeClass("disabled");
				this.ui.find("#t01_27_commentaire_rh").removeAttr("readonly","readonly").removeAttr("disabled","disabled").removeClass("disabled");
				
				// Onglet Système > REACTIVE liste (retire les attributs disabled)
				this.ui.find("#combo_etape").removeAttr("disabled","disabled");
				this.ui.find("#demande_initiale").removeAttr("disabled","disabled");
			}
			// bouton Accepter
			addButton("Accepter", function() {
				thisComponent.accept(6, "Arbitrage accepté");
				// openActionItem("Demande d'accord de recrutement acceptée par "+thisComponent.getValue(valideurField));
			}).addClass("btn-success");
			// bouton Refuser
			addButton("Refuser", function() {
				gopaas.dialog.prompt('Ajoutez un commentaire:', '', function (val) {
					if (val) {
						thisComponent.setValue('commentaire_refus', val);
						thisComponent.setValue("etape", "Arbitrage refusé");
						thisComponent.setValue("token", gopaas.util.generateRandomString(16));
						thisComponent.saveItem(true);

						// Webservice pour mettre à jour la DAR lié
						var demande = thisComponent.getValue('demande');
						$.get("template_auto/candidat/candidat.php?mode=maj_DAR&demande="+demande)
						.done(function(data)
						{
							gopaas.dialog.notifyInfo('Mise à jour DAR !!!');
						})
						.fail(gopaas.dialog.ajaxFail);
					}
				});
				
				// openActionItem("Demande d'accord de recrutement refusée par "+thisComponent.getValue(valideurField));
			}).addClass("btn-danger");
		}

	//-----------------------------------------------
	// Acceptée / Refusée
	} else if (etape === "Acceptée" || etape === "Refusée" || etape === "Annulée") {
		this.ui.find("input, select, textarea").attr("readonly","readonly").attr("disabled","disabled").addClass("disabled");
		// pouvoir modifier
		this.ui.find("#contrat_realise").removeAttr("readonly","readonly").removeAttr("disabled","disabled");
		if (UTILISATEUR['groupe'] !== "Admin" && UTILISATEUR['groupe'] !== "GRP_ADM_GRE" && UTILISATEUR['groupe'] !== "GRP_RH_CORP") {
			// cache les 2 boutons d'enregistrement
			this.ui.find(".gopaas-button-save-and-close, .gopaas-button-save").hide();		
		}
		
		// AKERNECH le 26/01/2017 Brouillon - Ajout du bouton annulation
		// JR le 31/01/2017 Correction des enchainements de pop-up
		if (etape !== "Annulée"){
			if (UTILISATEUR['groupe'] === "Admin" || UTILISATEUR['groupe'] === "GRP_ADM_GRE" || UTILISATEUR['groupe'] === "GRP_RH_CORP"){
				// bouton Annulation 
			addButton("Annulation","Annulation du candidat", function() {
				// Appeler webservice get-item pour récuperer l'id de la demande
				var cle_demande = thisComponent.getValue('demande');
				if(cle_demande){
					$.get("webservice/item/get-item.php", {
						tableName	: "demande",
						itemKey  	: cle_demande
					})
					.done(function(data){
						// je récupère le champ que je souhaite, ici l'id de la demande
						var iddemande = data.iddemande;
						gopaas.dialog.confirm("Vous allez annuler le candidat N° "+iddemande+". Voulez-vous continuer ?", function(ok) {
							if (ok) {
								// OUI je continue sur l'étape suivante
								gopaas.dialog.confirm("Souhaitez-vous créer une nouvelle fiche candidat ?", function(ok) {

									if (ok) {
										// BOUTON OUI j'ouvre l'autre boite de dialog avec le commentaire à saisir
										gopaas.dialog.prompt('Merci de justifier votre annulation.', '', function (val2) {
										if (val2) {
											// Si OUI : Passage de la fiche actuelle en statut Annulée + création d’une nouvelle fiche candidat pour la même DAR + envoi mail aux personnes étant intervenues dans le circuit de la fiche GRE uniquement.
											thisComponent.setValue('commentaire_refus', val2);
											thisComponent.setValue("etape", "Annuler");
											thisComponent.setValue("token", gopaas.util.generateRandomString(16));

											// Webservice pour les envois emails annulation & création nouvelle fiche candidat
											var cle_candidat = thisComponent.getValue('cle');
											$.get("template_auto/candidat/candidat.php?mode=annulation&cle_candidat="+cle_candidat+"&user="+gsUser+"&commentaire_refus="+val2+"&candidat_a_creer=oui")
											.done(function(data)
											{
												gopaas.dialog.notifyInfo('Mail envoyé au(x) valideur(s)');
												thisComponent.saveItem(true);
											})
											.fail(gopaas.dialog.ajaxFail);

										}
										});
									}else{
										// BOUTON NON j'ouvre l'autre boite de dialog avec le commentaire à saisir
										gopaas.dialog.prompt('Merci de justifier votre annulation.', '', function (val2) {
										if (val2) {
											// Si NON : Passage de la fiche actuelle en statut Annulée + pas de création d’une nouvelle fiche candidat + envoi mail aux personnes étant intervenues dans le circuit de la fiche GRE + demandeur DAR.
											thisComponent.setValue('commentaire_refus', val2);
											thisComponent.setValue("etape", "Annuler");
											thisComponent.setValue("token", gopaas.util.generateRandomString(16));

											// Webservice pour les envois emails annulation & création nouvelle fiche candidat
											var cle_candidat = thisComponent.getValue('cle');
											$.get("template_auto/candidat/candidat.php?mode=annulation&cle_candidat="+cle_candidat+"&user="+gsUser+"&commentaire_refus="+val2+"&candidat_a_creer=non")
											.done(function(data)
											{
												gopaas.dialog.notifyInfo('Mail envoyé au(x) valideur(s)');
												thisComponent.saveItem(true);
											})
											.fail(gopaas.dialog.ajaxFail);
										}
										});
									}

								});
							}
						});
					})
				}
			}).addClass("btn-danger").insertAfter(thisComponent.ui.find(".gopaas-button-save"));
			}
		}
		
		addComment("Cette demande ne peut plus être modifiée"); // ajoute un petit commentaire explicatif en haut de la fiche (voir fonction addComment() plus haut)

	//-----------------------------------------------
	// Etat invalide   :   si la fiche se trouve dans une étape virtuelle du style "Valideur X accepte" ou "Valideur X refuse" , c'est certainement qu'une des règles ne s'est pas déclenchée à tort.
	} else {
		addComment("Cette demande est dans un état inattendu : '" + etape + "'"); // ajoute un petit commentaire explicatif en haut de la fiche (voir fonction addComment() plus haut)
	}


	thisComponent.onLoadIsFinished = true;	
	
	return true;
}

function onPrint_candidat(thisComponent)
{
	window.open('file/__pdf__/html2pdf.php?modele=candidat&cle='+thisComponent.getValue('cle')+'&table=candidat&action=print');
}

function onSave_candidat (){
	if (this.isNew()) {
		var cle = Date.now() + '_' + gsUser;
		this.setValue('cle', cle);
	}
	var thisComponent = this,
		etape = this.getValue("etape"),
		mpl = "mplalain",
		clicEnregistrer = thisComponent.clicEnregistrer || false,
		dateDemande,
		dateDebut,
		dateFin,
		differences = thisComponent.computeDifferences(),
		listeModif = ""
	;

	if (etape === "Brouillon" || etape === "Envoi demande") {
		thisComponent.setValue("demande_initiale", JSON.stringify(thisComponent.getEtatDemande()));
	}

	// vérifier les champs t01_30_date_debut_contrat et date_fin pour qu'ils soient >= date_demande
	if (etape === "Brouillon" || etape === "Envoi demande") {
		dateDemande = new Date(gopaas.date.toSql(this.getValue("date_demande") || this.getValue("date_creation")));
		dateDebut = new Date(gopaas.date.toSql(this.getValue("t01_30_date_debut_contrat") || gopaas.date.dateFr()));
		if (dateDebut < dateDemande) {
			gopaas.dialog.info("Date d'arrivée souhaitée doit être supérieure ou égale à la date de la demande");
			// Afficher les Boutons envoyer et enregistrer
			thisComponent.ui.find("button[title='Soumettre le candidat']").removeAttr("disabled","disabled");
			thisComponent.ui.find("button[title='Enregistrer sans fermer la fiche']").removeAttr("disabled","disabled");
			return false;
		}
	}

	thisComponent.clicEnregistrer = false;

	// si on est passé par le webservice 'get_regle_valideur' ou 'html2pdf', alors il ne faut pas re-exécuter le callback onSave()
	if (thisComponent.flag) {
		thisComponent.setPermission();
		return true;
	}

	//-----------------------------------------------
	// empêcher l'enregistrement si le user connecté n'est pas le valideur requis
	if (etape === "Brouillon") {
		// si ce n'est pas le demandeur qui a ouvert la fiche :
		if (gsUser !== this.getValue("recruteur") && UTILISATEUR['groupe'] !== "Admin" && UTILISATEUR['groupe'] !== "GRP_ADM_GRE" && UTILISATEUR['groupe'] !== "GRP_RH_CORP") {
			gopaas.dialog.warning("Cette demande est en cours de création par <strong>"+this.getValue("recruteur")+"</strong>, vous ne pouvez pas la modifier");
			return false;
		}
	} else if (etape.indexOf("Attente valideur") === 0) {
		var valideurNumber = parseInt(etape.substr("Attente valideur ".length,1)),
			valideurField = "valideur" + valideurNumber
		;
		// si ce n'est pas le valideur en cours qui a ouvert la fiche :
		if (gsUser !== this.getValue(valideurField) && gsUser !== this.getValue("recruteur") && UTILISATEUR['groupe'] !== "Admin" && UTILISATEUR['groupe'] !== "GRP_ADM_GRE" && UTILISATEUR['groupe'] !== "GRP_RH_CORP") {
			gopaas.dialog.warning("Cette demande est en attente de validation par <strong>"+this.getValue(valideurField)+"</strong>, vous ne pouvez pas la modifier");
			return false;
		} else {
		}
	} else if (etape.indexOf("Attente validation") === 0) { // = attente arbitrage
		if (gsUser !== mpl && UTILISATEUR['groupe'] !== "Admin" && UTILISATEUR['groupe'] !== "GRP_ADM_GRE" && UTILISATEUR['groupe'] !== "GRP_RH_CORP") {
			gopaas.dialog.warning("Cette demande est en attente de validation, vous ne pouvez pas la modifier");
			return false;
		}
	} else if (etape === "Acceptée" || etape === "Refusée" || etape === "Annulée") {
		gopaas.dialog.warning("Cette demande ne peut plus être modifiée");
		return false;
	}

	// $('body').addClass('wait'); // active le curseur sablier. A désactiver avec $('body').removeClass('wait')
	thisComponent.flag = true; // attention ce flag est indispensable, sinon on va appeler en boucle le callback onSave() et les webservice 'html2pdf' et 'get_regle_valideur'
	if (thisComponent.getValue("etape") === "Brouillon" || thisComponent.getValue("etape") === "Envoi demande") {
		$.get("template_auto/candidat/candidat.php", {
			mode	     : "regle_valideur",
			service      : "get_regle_valideur",
			societe      : thisComponent.getValue("cs00societes"),
			type_contrat : thisComponent.getValue("cs00typecontrat"),
			duree        : thisComponent.getValue("nb_mois_cdd"),
			direction    : thisComponent.getValue("cs00direction"),
			motif_recrutement: thisComponent.getValue("cs00motifentree"),
			metier       : thisComponent.getValue("cs00metiers"),
			transverse   : thisComponent.getValue("transverse")
		}).done(function(result) {
			// setConnectionValue(champ, tableConnectée, clé) . setConnectionValue() est similaire à setValue() sauf qu'il met à jour le complément.
			thisComponent.setConnectionValue("valideur1", "utilisateur", result.valideur1 || "");
			thisComponent.setConnectionValue("valideur2", "utilisateur", result.valideur2 || "");
			thisComponent.setConnectionValue("valideur3", "utilisateur", result.valideur3 || "");
			thisComponent.setConnectionValue("valideur4", "utilisateur", result.valideur4 || "");
			thisComponent.setConnectionValue("valideur5", "utilisateur", result.valideur5 || "");
			thisComponent.setConnectionValue("valideur6", "utilisateur", result.valideur6 || "");
			// setPermission(thisComponent);
			thisComponent.saveItem(clicEnregistrer ? false : true, function() {} ); // si on vient d'un clic sur le bouton Enregistrer, il faut lancer l'enregistrement sans fermer la fiche (paramètre false)
			// Générer PDF
			$.post("file/__pdf__/html2pdf.php", {
				action : "file",
				table  : "candidat",
				modele : "candidat",
				cle    : thisComponent.getValue("cle"),
				nom    : thisComponent.getPDFFileName()
			}).done(function(result){
				thisComponent.setValue("pdf_candidat",thisComponent.getPDFFileName());
			}).fail(gopaas.dialog.ajaxFail);

		}).fail(gopaas.dialog.ajaxFail);
	} else {
		// setPermission(thisComponent);
		thisComponent.saveItem(clicEnregistrer ? false : true, function() {} ); // si on vient d'un clic sur le bouton Enregistrer, il faut lancer l'enregistrement sans fermer la fiche (paramètre false)
	}
	return false;	
	//return true;
}

function personne_physique(thisComponent){
	// Webservice pour créer Personne Physique, mouvement entrée et tâches
	var idcandidat = thisComponent.getValue('idcandidat');
	$.get("template_auto/a00personnephysique/a00personnephysique.php?mode=createItems&idcandidat="+idcandidat)
	.done(function(data)
	{
		gopaas.dialog.notifyInfo('Génération des fiches terminée !!!');
		console.log('Fiches créées');
		// Get Data
		//var donnees_releves = JSON.parse(data.donnees_releves);
	})
	.fail(gopaas.dialog.ajaxFail);
}