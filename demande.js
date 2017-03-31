function onLoad_demande () {
	var thisComponent = this,
		etape = this.getValue("etape") || "Brouillon",
		clicEnregistrer = thisComponent.clicEnregistrer || false,
		type_contrat = this.getValue("r06typecontrat")
	;
	var salaire_reference = 100000;	
	
	// JR le 03/02/2017 maj CARTOUCHE avec get-item
	// 1er get-item pour sur direction
	if (thisComponent.getValue('d00direction')){
		$.get("webservice/item/get-item.php", {
			tableName	: "r15direction",
			itemKey  	: thisComponent.getValue('d00direction')
		})
		.done(function(data){
		// je récupère l'intitulé de la direction
			var direction_intitule = data.r15direction;
			
			var vCartouche = gopaas.date.dateFr(thisComponent.getValue('date_demande'))+' '+direction_intitule;
			thisComponent.ui.find('.form-horizontal > h3').html('<i class="fa fa-user" style="color:#8E3557;"></i> '+vCartouche);
			
			// 2ème get-item pour sur type de contrat
			if (thisComponent.getValue('r06typecontrat')){
				$.get("webservice/item/get-item.php", {
					tableName	: "r06typecontrat",
					itemKey  	: thisComponent.getValue('r06typecontrat')
				})
				.done(function(data2){
				// je récupère l'intitulé du code type de contrat
					var code_type_contrat = data2.r06codetypecontrat;	
					
					var vCartouche2 = gopaas.date.dateFr(thisComponent.getValue('date_demande'))+' '+direction_intitule+' '+code_type_contrat;
					thisComponent.ui.find('.form-horizontal > h3').html('<i class="fa fa-user" style="color:#8E3557;"></i> '+vCartouche2);
					
					// 3ème get-item pour sur motif recrutement
					if (thisComponent.getValue('d00motifpap')){
						$.get("webservice/item/get-item.php", {
							tableName	: "r14motifpap",
							itemKey  	: thisComponent.getValue('d00motifpap')
						})
						.done(function(data3){
						// je récupère l'intitulé du motif recrutement
							var motif_recrutement = data3.r14libmotifpap;	
							
							var vCartouche3 = gopaas.date.dateFr(thisComponent.getValue('date_demande'))+' '+direction_intitule+' '+code_type_contrat+' '+motif_recrutement;
							thisComponent.ui.find('.form-horizontal > h3').html('<i class="fa fa-user" style="color:#8E3557;"></i> '+vCartouche3);
							
							// 4ème get-item pour sur intitulé du poste
							if (thisComponent.getValue('d00postes')){
								$.get("webservice/item/get-item.php", {
									tableName	: "r05postes",
									itemKey  	: thisComponent.getValue('d00postes')
								})
								.done(function(data4){
								// je récupère l'intitulé du poste
									var intitule_poste = data4.r05libelleposte;	
									
									var vCartouche4 = gopaas.date.dateFr(thisComponent.getValue('date_demande'))+' '+direction_intitule+' '+code_type_contrat+' '+motif_recrutement+' '+intitule_poste;
									thisComponent.ui.find('.form-horizontal > h3').html('<i class="fa fa-user" style="color:#8E3557;"></i> '+vCartouche4);

								}).fail(gopaas.dialog.ajaxFail);
							} // FIN 4ème get-item pour sur intitulé du poste

						}).fail(gopaas.dialog.ajaxFail);
					}// FIN 3ème get-item pour sur motif recrutement

				}).fail(gopaas.dialog.ajaxFail);
			}// FIN 2ème get-item pour sur type de contrat

		}).fail(gopaas.dialog.ajaxFail);
	}// FIN 1er get-item pour sur direction

	// JR le 30/08/2016 ajouter un candidat
	this.addTool("Ajouter un candidat",null, function() { create_candidat(thisComponent); });

	if (this.isNew()) {
		this.setValue("transverse","Non");
		this.setConnectionValue("demandeur", "utilisateur", gsUser); // setConnectionValue(champConnexion,tableConnectée,clé).  setConnectionValue() est similaire à setValue() sauf qu'il met à jour le complément.
		this.setValue("date_demande", gopaas.date.dateFr()); // ce champ devrait peut-être n'être initialisé qu'à l'envoi de la demande ?? sinon il ne sert à rien, il sera toujours égal à date_creation
		this.setValue("etape", "Brouillon");
		this.setValue("nb_mois_cdd", "NA");
		this.setValue("cle", "demande." + gopaas.date.dateFr().replace(/\//g,".") + "." + gsUser + "." + gopaas.util.generateRandomString(4));
	}else{
		$( "h3" ).first().append( "<span>N° "+thisComponent.getValue('iddemande')+"</span>" );
	}

	// JR le 14/02/2017 à voir si c'est toujours utile ou pas > vu avec adrien on met en commentaire car ce n'est plus utilisé
	// Initialise la liste des services pour l'évènement onChange du champ Direction
	// $.get("template_auto/demande/demande.php", {service:"get_liste_service"}
	// ).done(function (liste) {
		// thisComponent.listeService = liste; // la liste de tous les services de la base de données
		// thisComponent.afficherMasquerService();
	// }).fail(gopaas.dialog.ajaxFail);

	//___________________________________________________________________________________________________________
	//___________________________________________________________________________________________________________
	//
	//
	//                      FONCTIONS
	//
	//___________________________________________________________________________________________________________
	//___________________________________________________________________________________________________________

	//-----------------------------------------------
	// renvoie un objet JSON contenant les valeurs actuelles des champs à surveiller
	// pour détecter les modifs
	// JR le 10/02/2017 mise à jour des valeurs connexion
	thisComponent.getEtatDemande = function () {
		return {
			r06typecontrat:				this.getValue("r06typecontrat"),
			d00motifpap:				this.getValue("d00motifpap"),
			prevu_dernier_pb_actu:		this.getValue("prevu_dernier_pb_actu"),
			d00societes:				this.getValue("d00societes"),
			d00direction:				this.getValue("d00direction"),
			date_arrivee_souhaitee:		this.getValue("date_arrivee_souhaitee"),
			salaire:					this.getValue("salaire"),
			salaire_fixe_brut_mensuel:	this.getValue("salaire_fixe_brut_mensuel"),
			organigramme:				this.getValue("organigramme")
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
	
	/***********************************************/

	//-----------------------------------------------
	// cette fonction se charge aussi de vérifier la case à cocher de confirmation,
	// et d'enregistrer la fiche.
	thisComponent.accept = function (valideurNumber, valideurAcceptStep) { // pour l'arbitrage le valeurNumber est le 6
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

		// confirme et envoie la demande
		if (!thisComponent.getValue("confirmation_valideur" + valideurNumber)) {
			gopaas.dialog.confirm("Je confirme avoir pris connaissance de l'organigramme", function(ok) {
				if (ok) {
					send();
				}
			});
		} else { // si déjà coché: acceptation directe
			send();
		}
	};

	//-----------------------------------------------
	// JR le 14/02/2017 correction connexion type contrat
	thisComponent.updateValideur3Transverse = function() {
		var transverse = thisComponent.getValue("transverse"),
			metier = thisComponent.getValue("metier"),
			type_contrat = thisComponent.getValue("r06typecontrat"),
			nb_mois_cdd = thisComponent.getValue("nb_mois_cdd")
		;

		if (thisComponent.ui.find("[name=transverse]").hasClass("disabled")) {
			return; // cette fonction ne s'exécute que si le champ Transverse est modifiable
		}

		if (!thisComponent.onLoadIsFinished) { // il ne faut pas lancer la fonction à l'ouverture de la fiche, mais seulement lorsqu'un champ est réellement modifié, sinon on risque d'appliquer le cas "validation transverse" alors que la fiche a déjà été mise en mode "validation transverse", ce qui a pour effet de retirer à tort le valideur 3)
			return;
		}
		
		// JR Le 07/02/2017 mise à jour des nouvelles connexion
		$.get("template_auto/demande/demande.php", {
			service      		: "get_regle_valideur",
			societe      		: thisComponent.getValue("d00societes"),
			type_contrat 		: thisComponent.getValue("r06typecontrat"),
			duree        		: thisComponent.getValue("nb_mois_cdd"),
			direction    		: thisComponent.getValue("d00direction"),
			motif_recrutement	: thisComponent.getValue("d00motifpap"),
			metier       		: thisComponent.getValue("d00metiers"),
			transverse   		: thisComponent.getValue("transverse"),
			valeur_salaire		: thisComponent.getValue("d00valeursalaire")
		}).done(function(result) {
			// setConnectionValue(champ, tableConnectée, clé) .setConnectionValue() est similaire à setValue() sauf qu'il met à jour le complément.
			thisComponent.setConnectionValue("valideur1", "utilisateur", result.valideur1 || "");
			thisComponent.setConnectionValue("valideur2", "utilisateur", result.valideur2 || "");
			thisComponent.setConnectionValue("valideur3", "utilisateur", result.valideur3 || "");
			thisComponent.setConnectionValue("valideur4", "utilisateur", result.valideur4 || "");
			thisComponent.setConnectionValue("valideur5", "utilisateur", result.valideur5 || "");
			thisComponent.setConnectionValue("valideur6", "utilisateur", result.valideur6 || "");
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

		// valideurs
		if (etape === "Renvoyer au demandeur") { // pour ce cas particulier, le champ permission est utilisé pour savoir à qui envoyer l'email, le champ permission sera ensuite écrasé par la rules
			if (valideur1 && this.getValue("date_validation1")) { permission.push(valideur1); }
			if (valideur2 && this.getValue("date_validation2")) { permission.push(valideur2); }
			if (valideur3 && this.getValue("date_validation3")) { permission.push(valideur3); }
			if (valideur4 && this.getValue("date_validation4")) { permission.push(valideur4); }
			if (valideur5 && this.getValue("date_validation5")) { permission.push(valideur5); }
			if (valideur6 && this.getValue("date_validation6")) { permission.push(valideur6); }

		} else if (etape !== "Brouillon" && /* etape !== "Renvoyer au demandeur" && */ etape !== "Annuler" && etape !== "Annulée") {

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
	thisComponent.addHelpButton = function (fieldName) {
		var thisComponent = this,
			field = thisComponent.ui.find("[name="+fieldName+"]"),
			helpButton = $("<a></a>").attr("href","#").css("margin-left","7px").data("fieldName", fieldName).data("field", field).append($("<span class='glyphicon glyphicon-question-sign'></span>"))
		;

		thisComponent.tooltipVisible = thisComponent.tooltipVisible || {};
		if (!thisComponent.tooltipVisible.hasOwnProperty(fieldName)) {
			thisComponent.tooltipVisible[fieldName] = false;
		}

		field.tooltip({trigger:"hover focus manual", title:field.attr("title")});
		helpButton.click( function() {
			var fieldName = $(this).data("fieldName"),
				field = $(this).data("field")
			;
			if (thisComponent.tooltipVisible[fieldName]) {
				field.tooltip("hide");
				thisComponent.tooltipVisible[fieldName] = false;
			} else {
				field.tooltip("show");
				thisComponent.tooltipVisible[fieldName] = true;
			}
		});
		field.closest(".form-group").find("label").append(helpButton);
	};

	//-----------------------------------------------
	// JR 07/02/2017 mise à jour des champs connexion
	// JR 10/02/2017 mise à jour champ connexion motif_recrutement
	thisComponent.getPDFFileName = function() {
		// je récupère la 2ème valeur dans le complément contrat qui est le code, exemple : CDI
		var complement_contrat = $('#COMPLEMENT_r06typecontrat').val();
		var code_contrat = complement_contrat.substring(complement_contrat.lastIndexOf("|")+1, complement_contrat.length);
	
		var data = [
			gopaas.date.toSql(this.getValue("date_demande") || ''),
			($('#COMPLEMENT_d00direction').val() || '').replace(/[^\w ]/g,''),
			(code_contrat || '').replace(/[^\w ]/g,''),
			($('#COMPLEMENT_d00motifpap').val() || '').replace(/[^\w ]/g,''),
			($('#COMPLEMENT_d00postes').val() || '').replace(/[^\w ]/g,'')
		];
		return data.join(' - ') + '.pdf';

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
	// JR le 31/12/2017 - Création du bouton documentation
	thisComponent.ui.find("[name=salaire_fixe_brut_mensuel]").closest(".form-group").append( "<button id='btn_open_documentation' type='button' class='btn btn-info btn-sm' style='float: right; margin-right: 15px; margin-top: 5px;' title='Documentation'> <span class='glyphicon glyphicon-file'></span>&nbsp;<span class='hidden-xs trn'>Documentation</span></button>" );

	thisComponent.ui.find("#btn_open_documentation").on('click', function(){
		// action à faire
		View.open('documentation','Par défaut','Tableau');	
	});

	//-----------------------------------------------
	// Masquer les champs stagiaire/contrat pro
	// JR le 31/12/2017 - Je masque ou affiche le bouton documentation dans la fonction masquer_stagiaire
	function masquer_stagiaire(thisComponent){
		thisComponent.ui.find("#salaire_fixe_brut_mensuel").closest('.form-group').hide();
		thisComponent.ui.find("#cv").closest('.form-group').hide();
		thisComponent.ui.find("#nom_ecole").closest('.form-group').hide();
		thisComponent.ui.find("#nom_diplome").closest('.form-group').hide();
		thisComponent.ui.find("#cout_formation").closest('.form-group').hide();
		thisComponent.ui.find("#btn_open_documentation").closest(".form-group").hide();
	}

	function afficher_stagiaire(thisComponent){
		thisComponent.ui.find("#salaire_fixe_brut_mensuel").closest('.form-group').show();
		thisComponent.ui.find("#cv").closest('.form-group').show();
		thisComponent.ui.find("#nom_ecole").closest('.form-group').show();
		thisComponent.ui.find("#nom_diplome").closest('.form-group').show();
		thisComponent.ui.find("#cout_formation").closest('.form-group').show();		
		thisComponent.ui.find("#btn_open_documentation").closest(".form-group").show();
	}

	//-----------------------------------------------
	// Masquer les champs CDI/CDD
	function masquer_cdi(thisComponent){
		thisComponent.ui.find("#motivation_demande").closest('.form-group').hide();
		thisComponent.ui.find("#motivation_demande").closest('.form-group').prev().hide();
		thisComponent.ui.find("#combo_prevu_dernier_pb_actu").closest('.form-group').hide();
		thisComponent.ui.find("#prevu_dernier_pb_actu_commentaire").closest('.form-group').hide();
		thisComponent.ui.find("#impact_budgetaire_annee_n").closest('.form-group').hide();
		thisComponent.ui.find("#impact_budgetaire_annee_n_plus_1").closest('.form-group').hide();
		thisComponent.ui.find("#organigramme").closest('.form-group').hide();
		thisComponent.ui.find("#salaire").closest('.form-group').hide();
		// JR 07/02/2017 MAJ connexion metier
		thisComponent.ui.find("#COMPLEMENT_d00metiers").closest('.form-group').hide();
		thisComponent.ui.find("#combo_metier").closest('.form-group').hide();
		thisComponent.ui.find("#personne_remplacee").closest('.form-group').hide();
	}

	//-----------------------------------------------
	function afficher_cdi(thisComponent){
		thisComponent.ui.find("#motivation_demande").closest('.form-group').show();
		thisComponent.ui.find("#motivation_demande").closest('.form-group').prev().show();
		thisComponent.ui.find("#combo_prevu_dernier_pb_actu").closest('.form-group').show();
		thisComponent.ui.find("#prevu_dernier_pb_actu_commentaire").closest('.form-group').show();
		thisComponent.ui.find("#impact_budgetaire_annee_n").closest('.form-group').show();
		thisComponent.ui.find("#impact_budgetaire_annee_n_plus_1").closest('.form-group').show();
		thisComponent.ui.find("#organigramme").closest('.form-group').show();
		thisComponent.ui.find("#salaire").closest('.form-group').show();
		// JR 07/02/2017 MAJ connexions motif_recrutement et metier
		thisComponent.ui.find("#combo_motif_recrutement").closest('.form-group').show();
		thisComponent.ui.find("#COMPLEMENT_d00motifpap").closest('.form-group').show();
		// JR 07/02/2017 MAJ connexions motif_recrutement et valeur retourné MOTPAP002 = Remplacement
		// JR 10/02/2017 correction  condition motif_recrutement - j'ai supprimé les LOWERCASE les valeurs sont en majuscule.
		// var motif_recrutement = $('#COMPLEMENT_d00motifpap').val();
		var motif_recrutement = thisComponent.getValue("d00motifpap");
		// console.log("script exec : "+motif_recrutement);
		if (motif_recrutement === 'MOTPAP002') {
			thisComponent.ui.find("#COMPLEMENT_d00personneremplacee").closest('.form-group').show();
		} else {
			thisComponent.ui.find("#COMPLEMENT_d00personneremplacee").closest('.form-group').hide();
		}
		// JR le 14/02/2017 afficher le champ métier
		thisComponent.ui.find("#COMPLEMENT_d00metiers").closest('.form-group').show();
	}

	//-----------------------------------------------
	// Masquer les champs date_fin et durée si cdi
	// SI CDI
	function masquer_date_fin_duree(thisComponent){
		var type_contrat = thisComponent.getValue("r06typecontrat");
		// console.log(type_contrat);
		if(type_contrat==="CTT001"){
			thisComponent.ui.find("#nb_mois_cdd").closest('.form-group').hide();
			thisComponent.ui.find("#date_fin").removeClass("gopaas-field-date datepicker").closest('.form-group').hide();
			thisComponent.ui.find("#duree").removeClass("gopaas-field-number").closest('.form-group').hide();
		}else{
			thisComponent.ui.find("#nb_mois_cdd").closest('.form-group').show();
			thisComponent.ui.find("#date_fin").addClass("gopaas-field-date datepicker").closest('.form-group').show();
			thisComponent.ui.find("#duree").addClass("gopaas-field-number").closest('.form-group').show();
		}
	}
	//-----------------------------------------------
	// Entrée prévue au BP d'avril 2016
	// update-mgh : 25/05/2016
	function masquer_prevue_bp_avril_2016(thisComponent){
		thisComponent.ui.find("#combo_prevu_dernier_pb_actu").closest('.form-group').hide();
	}
			
	function afficher_prevue_bp_avril_2016(thisComponent){
		thisComponent.ui.find("#combo_prevu_dernier_pb_actu").closest('.form-group').show();
	}	
	
	//-----------------------------------------------
	// Met à jour le champ nb_mois_cdd
	// JR 07/02/2017 MAJ connexions type_contrat et valeur CTT001 = CDI
	function calcul_duree(thisComponent){
		if(thisComponent.getValue('r06typecontrat')!=='CTT001'){
			var date2 = new Date(thisComponent.getValue('date_fin').substr(6,4)+'-'+thisComponent.getValue('date_fin').substr(3,2)+'-'+thisComponent.getValue('date_fin').substr(0,2));
			var date1 = new Date(thisComponent.getValue('date_arrivee_souhaitee').substr(6,4)+'-'+thisComponent.getValue('date_arrivee_souhaitee').substr(3,2)+'-'+thisComponent.getValue('date_arrivee_souhaitee').substr(0,2));
			var diff = dateDiff(date1, date2);
			//alert(diff.day);
			if(Number(diff.day)<=150){
				thisComponent.setValue('nb_mois_cdd','moins de 5 mois');
			}

			if(Number(diff.day)>150){
				thisComponent.setValue('nb_mois_cdd','plus de 5 mois');
			}
			if (isNaN(Number(diff.day))) {
				thisComponent.ui.find("#duree").val("");
			} else {
				thisComponent.ui.find("#duree").val(Math.floor( Number(diff.day)/30*10 ) / 10);
			}
		} else {
			thisComponent.setValue('nb_mois_cdd','NA');
		}
	}

	//___________________________________________________________________________________________________________
	//___________________________________________________________________________________________________________
	//
	//
	//                      EVENEMENTS
	//
	//___________________________________________________________________________________________________________
	//___________________________________________________________________________________________________________
	
	/***********************************************************/
	// JR LE 10/02/2017 NOUVEAU SCRIPT POUR CHAMP TYPE CONTRAT //
	/***********************************************************/
	// lancer la vérification au chargement
	verifTypeContrat(thisComponent);
	/***********************************************************/
    // on ne peut pas utiliser les évènements sur un champ de type connexion,
    // il faut faire un contrôle toutes les x secondes pour détecter les modifications faites par JavaScript.
	/***********************************************************/
    thisComponent.ui.find("[name=r06typecontrat]").data("oldValue", thisComponent.ui.find("[name=r06typecontrat]").val());
    var intervalId2 = setInterval(function() {
        if (!thisComponent.ui.closest("body").length) { // si le composant a été supprimé (onglet fermé)
            clearInterval(intervalId2);
            return;
        }
        var r06typecontrat = thisComponent.ui.find("[name=r06typecontrat]");
        if (r06typecontrat.val() !== r06typecontrat.data("oldValue")) {
			r06typecontrat.data("oldValue", r06typecontrat.val());
			verifTypeContrat(thisComponent);			
        }
    }, 1000);

	function verifTypeContrat(thisComponent){
		var motif_recrutement = thisComponent.ui.find('[name=d00motifpap]').val();
		// console.log(motif_recrutement);
		if(thisComponent.getValue('r06typecontrat')==='CTT001'){
			masquer_stagiaire(thisComponent);
			afficher_cdi(thisComponent);
			masquer_date_fin_duree(thisComponent);
			// Update MGH 25/05/2016
			if(motif_recrutement!=null){
				if (motif_recrutement === 'MOTPAP002') {
					masquer_prevue_bp_avril_2016(thisComponent);
				}
			}
		}

		if(thisComponent.getValue('r06typecontrat')==='CTT002'){
			masquer_stagiaire(thisComponent);
			afficher_cdi(thisComponent);
			masquer_date_fin_duree(thisComponent);
			// Update MGH 25/05/2016
			if(motif_recrutement!=null){
				if (motif_recrutement === 'MOTPAP002') {
					masquer_prevue_bp_avril_2016(thisComponent);
				}
			}
		}

		// Stagiaire / Contrat pro
		if((thisComponent.getValue('r06typecontrat')!=='CTT002')&&(thisComponent.getValue('r06typecontrat')!=='CTT001')){
			masquer_cdi(thisComponent);
			afficher_stagiaire(thisComponent);
			masquer_date_fin_duree(thisComponent);
			afficher_prevue_bp_avril_2016(thisComponent);
		}
		calcul_duree(thisComponent);
	}

	/****************************************************************/
	// JR LE 10/02/2017 NOUVEAU SCRIPT POUR CHAMP MOTIF RECRUTEMENT //
	/****************************************************************/
	// lancer la vérification au chargement
	verifMotifRecrut(thisComponent);
	// SCRIPT avec intervalId
	thisComponent.ui.find("[name=d00motifpap]").data("oldValue", thisComponent.ui.find("[name=d00motifpap]").val());
    var intervalId1 = setInterval(function() {
        if (!thisComponent.ui.closest("body").length) { // si le composant a été supprimé (onglet fermé)
            clearInterval(intervalId1);
            return;
        }
        var d00motifpap = thisComponent.ui.find("[name=d00motifpap]");
        if (d00motifpap.val() !== d00motifpap.data("oldValue")) {
			d00motifpap.data("oldValue", d00motifpap.val());
			verifMotifRecrut(thisComponent);
        }
    }, 1000);
	
	function verifMotifRecrut (thisComponent){
		var motif_recrutement = thisComponent.getValue('d00motifpap');
		var type_contrat = thisComponent.getValue('r06typecontrat');
		
		if (motif_recrutement === 'MOTPAP002') {
			thisComponent.ui.find("#COMPLEMENT_d00personneremplacee").closest(".form-group").show();
			// Update MGH 25/05/2016
			if(type_contrat==='CTT001'){
				masquer_prevue_bp_avril_2016(thisComponent);
			}
		} else {
			thisComponent.ui.find("#COMPLEMENT_d00personneremplacee").closest(".form-group").hide();
			// Update MGH 25/05/2016
			if(type_contrat==='CTT001'){
				afficher_prevue_bp_avril_2016(thisComponent);
			}
		}
	}
	/************************************************************************************/
	// JR LE 10/02/2017 FIN NOUVEAU SCRIPT POUR CHAMP MOTIF RECRUTEMENT et TYPE CONTRAT //
	/************************************************************************************/
	
	/*******************************************************************/
	// JR LE 22/02/2017 NOUVEAU SCRIPT POUR CHAMP a07postesbudgetaires //
	/*******************************************************************/
	// lancer la vérification au chargement
	// verifPAP(thisComponent);
	// SCRIPT avec intervalId
	thisComponent.ui.find("[name=a07postesbudgetaires]").data("oldValue", thisComponent.ui.find("[name=a07postesbudgetaires]").val());
    var intervalId3 = setInterval(function() {
        if (!thisComponent.ui.closest("body").length) { // si le composant a été supprimé (onglet fermé)
            clearInterval(intervalId3);
            return;
        }
        var a07postesbudgetaires = thisComponent.ui.find("[name=a07postesbudgetaires]");
        if (a07postesbudgetaires.val() !== a07postesbudgetaires.data("oldValue")) {
			a07postesbudgetaires.data("oldValue", a07postesbudgetaires.val());
			verifPAP(thisComponent);
        }
    }, 1000);
	
	function verifPAP (thisComponent){
		var cle_pap = thisComponent.getValue('a07postesbudgetaires');
		// console.log(cle_pap);
		if (cle_pap !== ''){
			// JR 22/02/2017 rempli la fiche PAP		
			$.get("template_auto/demande/demande.php", {
				service		: "autre",
				mode		: "update_pap",
				cle_pap		: cle_pap
			}).done(function(result) {
				// console.log('Fiche '+cle_pap+' mise à jour');			
			}).fail(gopaas.dialog.ajaxFail);
			
			// Je remet la valeur dans le champ pap_initiale au cas ou l'on vide la connexion le onSave saura qu'il faut remettre à jour le PAP 
			thisComponent.setValue('pap_initiale',$('#ID_a07postesbudgetaires').val());
		}
	}
	/*******************************************************************/
	// JR LE 22/02/2017 FIN SCRIPT POUR CHAMP a07postesbudgetaires //
	/*******************************************************************/
	
	// lancer la vérification au chargement
	verifMotifRecrut(thisComponent);
	// SCRIPT avec intervalId
	thisComponent.ui.find("[name=d00motifpap]").data("oldValue", thisComponent.ui.find("[name=d00motifpap]").val());
    var intervalId1 = setInterval(function() {
        if (!thisComponent.ui.closest("body").length) { // si le composant a été supprimé (onglet fermé)
            clearInterval(intervalId1);
            return;
        }
        var d00motifpap = thisComponent.ui.find("[name=d00motifpap]");
        if (d00motifpap.val() !== d00motifpap.data("oldValue")) {
			d00motifpap.data("oldValue", d00motifpap.val());
			verifMotifRecrut(thisComponent);
        }
    }, 1000);

	//-----------------------------------------------
	// Date début/fin
	thisComponent.ui.find("#date_fin, #date_arrivee_souhaitee").on("change", function(){
		calcul_duree(thisComponent);
		calculImpactBudgetaire(thisComponent);
		// thisComponent.updateValideur3Transverse(); // dans le cas où on est en CDD, les dates de début/fin vont changer le champ nb_mois_cdd, ce qui peut avoir un impact sur le valideur transverse
	}).trigger("change");

	//-----------------------------------------------
	// Nb mois CDD
	thisComponent.ui.find("[name=nb_mois_cdd]").on("change", function(){
		// thisComponent.updateValideur3Transverse();
	}).trigger("change");

	//-----------------------------------------------
	// Salaire annuel
	thisComponent.ui.find("#salaire").on("change", function() {
		calculImpactBudgetaire(thisComponent);
		if(thisComponent.getValue("salaire") > salaire_reference){
			thisComponent.setValue("d00valeursalaire", "Supérieur au salaire de référence");
		}else{
			thisComponent.setValue("d00valeursalaire", "Inférieur ou égal au salaire de référence");
		}
	}).trigger("change");

	//-----------------------------------------------
	// Salaire mensuel
	thisComponent.ui.find("#salaire_fixe_brut_mensuel").on("change", function() {
	});

	//-----------------------------------------------
	// Direction
	// JR le 14/02/2017 je reviens sur une connexion standard
	thisComponent.ui.find("[name=d00direction]").closest(".input-group").find(".input-group-btn button:first").removeAttr("onclick").off("click").click(function() { // removeAttr("onclick") + off("click") désactive le click s'il a été assigné par JQuery ET s'il a été assigné directement en HTML. http://stackoverflow.com/questions/785147/jquery-unbind-click
		if (!thisComponent.getValue("d00societes")) {
			gopaas.dialog.info("Vous devez d'abord sélectionner une société");
			return;
		}
		View.open(
			'r15direction',
			'Connexion',
			'Tableau',
			new Link( Component.find('Item', this), Link.type.SET_CONNECTED_ITEM, {"fieldName":"d00direction"} ),
			{"showViewMenu":false,"showManageView":false,"showModifyView":false,"showRefreshView":false,"showFilter":false,"showAddItem":true,"showToolMenu":false,"showSelectItem":true,"showCancelView":true,"moreFilter":"[{\"field\":\"r15societe\",\"source_field\":\"d00societes\"}]"}
		);
	});

	//-----------------------------------------------
	// Transverse
	thisComponent.ui.find("[name=transverse]").on("change", function(){
		thisComponent.updateValideur3Transverse();
	}).trigger("change");

	//-----------------------------------------------
	// bloquer les champs confirmation_valideurX
	thisComponent.ui.find("[name=confirmation_valideur]").on("change", function() {
		this.checked = !this.checked;
	});
	
	//-----------------------------------------------
	// bloquer les champs date_validationx & valideurx sauf si admin
	if (UTILISATEUR['profil'].toLowerCase() !== 'admin') {
		thisComponent.ui.find("[name^=date_validation]").attr("disabled","disabled").addClass("disabled");
		thisComponent.ui.find("[name^=valideur]").siblings("span.input-group-btn").children().attr("disabled","disabled").addClass("disabled");
	}
	
	/*
	 * JR le 27/03/2017
	 * Rempli le champ pap_initiale au démarrage de l'application
	 * Je le fait via un timer de 5 secondes pour laisser les get-item de GoPaaS remplir le champ ID_a07postesbudgetaires
	 */
    var timerID1 = setTimeout(function() {
		//console.log('timer lancé');
		thisComponent.setValue('pap_initiale',$('#ID_a07postesbudgetaires').val());
		clearInterval(timerID1);
    }, 5000);
    
	//___________________________________________________________________________________________________________
	//___________________________________________________________________________________________________________
	//
	//
	//                      PERSONNALISATION STYLE ET BOUTONS
	//
	//___________________________________________________________________________________________________________
	//___________________________________________________________________________________________________________

	// Champ Transverse non éditable
	if ( UTILISATEUR['groupe']!== "GRP_ADM_DAR" && UTILISATEUR['groupe']!== "Admin"|| etape !== "Attente valideur 1" && etape !== "Attente valideur 2" && etape !== "Attente valideur 3") {
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
	this.addHelpButton("prevu_dernier_pb_actu");
	this.addHelpButton("salaire");

	// Menu outil
	this.ui.find('#btn_action_menu li:eq(0)').hide();
	this.ui.find('#btn_action_menu li:eq(2)').hide();

	// Impression PDF
	this.addTool("Imprimer PDF","Imprimer le demande en PDF", function() { onPrint(thisComponent); });

	// Désactiver double clic sur les connexions
	thisComponent.ui.find("#COMPLEMENT_d00societes, #COMPLEMENT_d00direction, #COMPLEMENT_sous_service, #COMPLEMENT_demandeur, #COMPLEMENT_valideur1, #COMPLEMENT_valideur2, #COMPLEMENT_valideur3, #COMPLEMENT_valideur4, #COMPLEMENT_valideur5, #COMPLEMENT_valideur6").removeAttr('ondblclick').off('dblclick');

	// Signe euros dans le champ salaire brut annuel / mensuel
	thisComponent.addEuroLabel("salaire");
	thisComponent.addEuroLabel("impact_budgetaire_annee_n");
	thisComponent.addEuroLabel("impact_budgetaire_annee_n_plus_1");
	thisComponent.addEuroLabel("cout_formation");
	thisComponent.addEuroLabel("salaire_fixe_brut_mensuel");

	//___________________________________________________________________________________________________________
	//___________________________________________________________________________________________________________
	//
	//
	//                      WORKFLOW
	//
	//___________________________________________________________________________________________________________
	//___________________________________________________________________________________________________________

	//-----------------------------------------------
	// Brouillon
	if (etape === "Brouillon" || etape === null) {

		// si ce n'est pas le demandeur ou un admin qui a ouvert la fiche :
		if (gsUser !== this.getValue("demandeur") && (UTILISATEUR['groupe'].toLowerCase() !== "admin") && (UTILISATEUR['groupe'].toLowerCase() !== "grp_adm_dar")) {
			this.ui.find("input, select, textarea").attr("readonly","readonly").attr("disabled","disabled").addClass("disabled"); // désactive tous les input (ne fonctionne pas totalement pour les listes déroulante)
			this.ui.find(".gopaas-button-save-and-close, .gopaas-button-save").hide(); // cache les 2 boutons d'enregistrement
			addComment("Cette demande est en cours de création par <strong>"+this.getValue("demandeur")+"</strong>, vous ne pouvez pas la modifier"); // ajoute un petit commentaire explicatif en haut de la fiche (voir fonction addComment() plus haut)

		// si c'est le demandeur ou un admin qui a ouvert la fiche :
		} else {
			addButton("Supprimer", "Supprimer cette demande", function() {
				thisComponent.setValue("etape", "Annuler");
				thisComponent.setValue("token", gopaas.util.generateRandomString(16));
				thisComponent.saveItem(true);
			}).addClass("btn-danger").insertAfter(thisComponent.ui.find(".gopaas-button-save"));
			
			// bouton pour envoyer la demande
			addButton("Envoyer la demande","Envoyer la demande (DAR)", function() {

				// Cacher les Bouton envoyer et enregistrer
				thisComponent.ui.find("button[title='Envoyer la demande (DAR)']").attr("disabled","disabled");
				thisComponent.clicEnregistrer = false;
				
				// rempli le circuit de validation
				// JR 07/02/2017 MAJ des champs connexions
				$.get("template_auto/demande/demande.php", {
					service				: "get_regle_valideur",
					societe     		: thisComponent.getValue("d00societes"),
					type_contrat 		: thisComponent.getValue("r06typecontrat"),
					duree        		: thisComponent.getValue("nb_mois_cdd"),
					direction			: thisComponent.getValue("d00direction"),
					motif_recrutement	: thisComponent.getValue("d00motifpap"),
					metier				: thisComponent.getValue("d00metiers"),
					transverse			: thisComponent.getValue("transverse"),
					valeur_salaire		: thisComponent.getValue("d00valeursalaire")
				}).done(function(result) {
					// setConnectionValue(champ, tableConnectée, clé) . setConnectionValue() est similaire à setValue() sauf qu'il met à jour le complément.
					thisComponent.setConnectionValue("valideur1", "utilisateur", result.valideur1 || "");
					thisComponent.setConnectionValue("valideur2", "utilisateur", result.valideur2 || "");
					thisComponent.setConnectionValue("valideur3", "utilisateur", result.valideur3 || "");
					thisComponent.setConnectionValue("valideur4", "utilisateur", result.valideur4 || "");
					thisComponent.setConnectionValue("valideur5", "utilisateur", result.valideur5 || "");
					thisComponent.setConnectionValue("valideur6", "utilisateur", result.valideur6 || "");
					// setPermission(thisComponent);
					// thisComponent.saveItem(clicEnregistrer ? false : true, function() { generatePdf(); } ); // si on vient d'un clic sur le bouton Enregistrer, il faut lancer l'enregistrement sans fermer la fiche (paramètre false)

					// vérifie d'abord que tous les champs des onglets 1 et 2 sont renseignés
					var champNonRenseigne = [], libelleChampNonRenseigne = [];
					function verifier_onglet(panel) {
						panel.find("input, textarea, select").each(function() {
							var $this = $(this);
							if ($this.closest(".form-group").find("[name=sous_service],[name=prevu_dernier_pb_actu_commentaire],[name=recruteur]").length !== 0) { return; } // cas particuliers : champs ignorés non obligatoires, à ignorer dans la vérification
							
							if ($this.closest(".form-group").find("[name=d00societes],[name=d00direction],[name=d00metiers],[name=d00nommanager],[name=d00materielinformatique],[name=r06typecontrat],[name=d00motifpap]").length !== 0) { return; } // JR le 14/02/2017 cas particuliers : Ce sont les nouveaux champs connexion liste que je n'arrive pas à tester
							
							if ($this.closest(".form-group").find(".datepicker").length === 1) { if ($this.val() === '00/00/0000') { $this.val(""); } } // cas particulier : les dates sont assimilées à vide si elle vaut 00/00/0000
							if ($this.closest(".form-group").css("display") !== "none" && !$this.hasClass("gopaas-file-selector") && ($this.val() === "" || typeof $this.val() === undefined || $this.val() === null)) { // element visible et non renseigné . On exclue gopaas-file-selector car c'est un champ temporaire, vidé à chaque fois qu'on sélectionne un fichier joint.
								champNonRenseigne.push($this);
								var libelle = $this.closest(".form-group").find("label span.trn").html();
								if ($.inArray(libelle,libelleChampNonRenseigne) === -1) {
									libelleChampNonRenseigne.push(libelle);
								}
							}
						});
					}
					verifier_onglet(thisComponent.ui.find(".tab-content .tab-pane:eq(0)"));
					verifier_onglet(thisComponent.ui.find(".tab-content .tab-pane:eq(1)"));
					if (champNonRenseigne.length) {
						gopaas.dialog.info("Champs non renseignés:<br>"+libelleChampNonRenseigne.join("<br>"));
						
						// Afficher les Boutons envoyer et enregistrer
						thisComponent.ui.find("button[title='Envoyer la demande (DAR)']").removeAttr("disabled","disabled");				
						thisComponent.clicEnregistrer = true;
						return;
					}

					// JR le 28/09/2016 Ajout d'une vérification supplémentaire avant l'envoi
					if (thisComponent.getValue('valideur1') == '' ) {
						gopaas.dialog.info("Votre demande présente une anomalie. Merci de bien vouloir enregistrer en brouillon votre demande et tenter de nouveau l'envoi. Si le problème persiste, contacter le support à l’adresse : adm-demande-embauche@altareacogedim.com");

						// Afficher les Boutons envoyer et enregistrer
						thisComponent.ui.find("button[title='Envoyer la demande (DAR)']").removeAttr("disabled","disabled");				
						thisComponent.clicEnregistrer = true;				
						return;
					}

					// enregistre et envoie la demande
					thisComponent.setValue("etape", "Envoi demande");
					thisComponent.setValue("date_demande", gopaas.date.dateFr());
					thisComponent.setValue("token", gopaas.util.generateRandomString(16));

					// Afficher les Boutons envoyer et enregistrer
					//thisComponent.ui.find("button[title='Envoyer la demande (DAR)']").removeAttr("disabled","disabled");					
					thisComponent.saveItem(true);
					
				}).fail(gopaas.dialog.ajaxFail);
			}).addClass("btn-success");
		}

	//-----------------------------------------------
	// Attente valideur
	} else if (etape.indexOf("Attente valideur") === 0) {
		var valideurNumber = parseInt(etape.substr("Attente valideur ".length,1)),
			valideurField = "valideur" + valideurNumber,
			valideurAcceptStep = "Valideur " + valideurNumber + " accepte",
			valideurRejectStep = "Valideur " + valideurNumber + " refuse";

		// si ce n'est pas le valideur en cours ou un admin qui a ouvert la fiche :
		if (gsUser !== this.getValue(valideurField) && (UTILISATEUR['groupe'].toLowerCase() !== "admin") && (UTILISATEUR['groupe'].toLowerCase() !== "grp_adm_dar")) {
			this.ui.find("input, select, textarea").attr("readonly","readonly").attr("disabled","disabled").addClass("disabled"); // désactive tous les input (ne fonctionne pas totalement pour les listes déroulantes)
			this.ui.find(".gopaas-button-save-and-close, .gopaas-button-save").hide(); // cache les 2 boutons d'enregistrement
			addComment("Cette demande est en attente de validation par <strong>"+this.getValue(valideurField)+"</strong>, vous ne pouvez pas la modifier"); // ajoute un petit commentaire explicatif en haut de la fiche (voir fonction addComment() plus haut)

		// si c'est le valideur en cours ou un admin qui a ouvert la fiche :
		} else {

			this.ui.find("#valideur1").removeAttr("readonly","readonly");

			// bouton Accepter
			addButton("Accepter", function() {
				thisComponent.accept(valideurNumber, valideurAcceptStep);
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
					}
				});
			}).addClass("btn-danger");
			addButton("Renvoyer au demandeur", "Signale au demandeur que vous avez modifié sa demande, en précisant ce qui a changé. Le processus de validation est repris depuis le début", function() {
				var differences = thisComponent.getChangementDemande(),
					listeModif = ""
				;
				if (differences.length === 0) {
					gopaas.dialog.info("Vous n'avez fait aucune modification");
					return;
				}

				gopaas.dialog.prompt('Ajoutez un commentaire pour le demandeur:', '', function (val) {
					if (val) {						
						for (var i=0; i<differences.length; i++) {
							var d = differences[i];
							listeModif += "<b>" + d.label + "</b> : <b>" + d.before + "</b> =&gt; <b>" + d.after + "</b><br>\n";
						}
						thisComponent.setValue('commentaire_renvoi_demandeur', val);
						thisComponent.setValue("etape", "Renvoyer au demandeur");
						thisComponent.setValue("modification",listeModif);
						thisComponent.saveItem("true");
					}
				});
			}).addClass("btn-warning");
		}


	//-----------------------------------------------
	// Arbitrage
	} else if (etape === "Attente validation") {
		// si ce n'est pas le/la responsable de l'arbitrage qui a ouvert la fiche :
		if ((UTILISATEUR['groupe'].toLowerCase() !== "admin") && (UTILISATEUR['groupe'].toLowerCase() !== "grp_adm_dar")) {
			this.ui.find("input, select, textarea").attr("readonly","readonly").attr("disabled","disabled").addClass("disabled"); // désactive tous les input (ne fonctionne pas totalement pour les listes déroulante)
			this.ui.find(".gopaas-button-save-and-close, .gopaas-button-save").hide(); // cache les 2 boutons d'enregistrement
			addComment("Cette demande est en attente de validation, vous ne pouvez pas la modifier"); // ajoute un petit commentaire explicatif en haut de la fiche (voir fonction addComment() plus haut)

		// si c'est le valideur en cours qui a ouvert la fiche :
		} else {
			// bouton Accepter
			addButton("Accepter", function() {
				thisComponent.accept(6, "Arbitrage accepté");
			}).addClass("btn-success");
			// bouton Refuser
			addButton("Refuser", function() {
				gopaas.dialog.prompt('Ajoutez un commentaire:', '', function (val) {
					if (val) {
						thisComponent.setValue('commentaire_refus', val);
						thisComponent.setValue("etape", "Arbitrage refusé");
						thisComponent.setValue("token", gopaas.util.generateRandomString(16));
						thisComponent.saveItem(true);
					}
				});

			}).addClass("btn-danger");
		}

	//-----------------------------------------------
	// Acceptée / Refusée
	} else if (etape === "Acceptée" || etape === "Refusée" || etape === "Annulée") {
		//this.ui.find("input, select, textarea").attr("readonly","readonly").attr("disabled","disabled").addClass("disabled"); // désactive tous les input (ne fonctionne pas totalement pour les listes déroulante)
		
		// this.ui.find("#contrat_realise").removeAttr("readonly","readonly").removeAttr("disabled","disabled"); // JR le 22/11/2016 il manquait la remove Class Disabled
		if ((UTILISATEUR['groupe'].toLowerCase() !== "admin") && (UTILISATEUR['groupe'].toLowerCase() !== "grp_adm_dar")) {
			this.ui.find("input, select, textarea").attr("readonly","readonly");
			this.ui.find("select, textarea").attr("readonly","readonly").attr("disabled","disabled").addClass("disabled");
			this.ui.find("#contrat_realise").attr("readonly","readonly").attr("disabled","disabled").addClass("disabled");
			this.ui.find(".gopaas-button-save-and-close, .gopaas-button-save").hide(); // cache les 2 boutons d'enregistrement
		}else{
			this.ui.find("#contrat_realise").removeAttr("readonly","readonly").removeAttr("disabled","disabled").removeClass("disabled"); // pouvoir modifier
			this.ui.find("#combo_transverse").removeAttr("readonly","readonly").removeAttr("disabled","disabled").removeClass("disabled"); // pouvoir modifier // JR le 22/11/2016
			this.ui.find("#valideur1").removeAttr("readonly","readonly");
		
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

function onSave_demande(close) {
	var thisComponent = this,
		etape = this.getValue("etape"),
		clicEnregistrer = thisComponent.clicEnregistrer || false,
		dateDemande,
		dateDebut,
		dateFin,
		typeContrat = thisComponent.getValue("r06typecontrat"),
		differences = thisComponent.computeDifferences(),
		listeModif = "";

	/*
	 * JR le 27/03/2017 Exécution de la fonction
	 */
	var papActuel = thisComponent.getValue('a07postesbudgetaires');
	var papInitiale = thisComponent.getValue('pap_initiale');

	// get-item pour récupérer l'ID de la papInitiale
	if (papInitiale){
		if (papActuel !== papInitiale) {
			//console.log('ID PAP '+papInitiale);
			/*
			 * FAIRE un UPDATE ITEM
			 */
			var formData = {};
			formData = [{"name":"tableName","value":"a07postesbudgetaires"},{"name":"ida07postesbudgetaires","value":papInitiale},{"name":"dar_attribuee","value":"0"}];
			formData.push(); // ajoute le nom de la table aux paramètres du formulaire

			$.post( gopaas.url.webservice("item","update-item"), formData )
			.done(function(updatedItem) {
			       // lance le callback onSuccess
			       onSuccess && onSuccess.call(this, updatedItem, close, onSuccess, differences, onFail);

			}).fail(gopaas.dialog.ajaxFail).fail(function() { onFail && onFail.call(thisComponent, close, onSuccess, differences, onFail); });
		}
	
	}
	
	/************************************************/
	
	if (etape === "Brouillon" || etape === "Envoi demande") {
		thisComponent.setValue("demande_initiale", JSON.stringify(thisComponent.getEtatDemande()));
	}

	// vérifier les champs date_arrivee_souhaitee et date_fin pour qu'ils soient >= date_demande
	if (etape === "Brouillon" || etape === "Envoi demande") {
		dateDemande = new Date(gopaas.date.toSql(this.getValue("date_demande") || this.getValue("date_creation")));
		dateDebut = new Date(gopaas.date.toSql(this.getValue("date_arrivee_souhaitee") || gopaas.date.dateFr()));
		if (dateDebut < dateDemande) {
			gopaas.dialog.info("Date d'arrivée souhaitée doit être supérieure ou égale à la date de la demande");
			// Afficher les Boutons envoyer et enregistrer
			thisComponent.ui.find("button[title='Envoyer la demande (DAR)']").removeAttr("disabled","disabled");
			thisComponent.ui.find("button[title='Enregistrer sans fermer la fiche']").removeAttr("disabled","disabled");
			return false;
		}

		if (typeContrat !== "CTT001") { // inutile de contrôler la date de fin en CDI
			dateFin = new Date(gopaas.date.toSql(this.getValue("date_fin") || gopaas.date.dateFr()));
			if (dateFin < dateDemande) {
				gopaas.dialog.info("Date de fin doit être supérieure ou égale à la date de la demande");
				// Afficher les Boutons envoyer et enregistrer
				thisComponent.ui.find("button[title='Envoyer la demande (DAR)']").removeAttr("disabled","disabled");
				thisComponent.ui.find("button[title='Enregistrer sans fermer la fiche']").removeAttr("disabled","disabled");
				return false;
			}
			if (dateFin < dateDebut) {
				gopaas.dialog.info("Date de fin doit être supérieure ou égale à la date d'arrivée souhaitée");
				// Afficher les Boutons envoyer et enregistrer
				thisComponent.ui.find("button[title='Envoyer la demande (DAR)']").removeAttr("disabled","disabled");
				thisComponent.ui.find("button[title='Enregistrer sans fermer la fiche']").removeAttr("disabled","disabled");
				return false;
			}
		}
	}

	thisComponent.clicEnregistrer = false;

	function generatePdf() {
		// Remplir commentaire
		var tous_les_commentaires = "";
		var availableItems = thisComponent.ui.find("#LinkDemandeCommentaire .datagrid-f:first").datagrid("getData").rows;

		$.each(availableItems,function(index,item){
			var date_creation = String(item.date_creation);
			var parts = date_creation.split('-');
			tous_les_commentaires += "Commentaire créé le " + parts[2]+"/"+parts[1]+"/"+parts[0] + " par " + item.creation_par + " :<br>" + item.commentaire + "<br><br>";
		});
		thisComponent.setValue('commentaire',tous_les_commentaires);
		$.post("webservice/pdf/html2pdf.php", {
			action : "file",
			table  : "demande",
			modele : "demande",
			cle    : thisComponent.getValue("cle"),
			nom    : thisComponent.getPDFFileName()
		}).fail(gopaas.dialog.ajaxFail);
	}

	// si on est passé par le webservice 'get_regle_valideur' ou 'html2pdf', alors il ne faut pas re-exécuter le callback onSave()
	if (thisComponent.flag) {
		thisComponent.setPermission();
		return true;
	}

	//-----------------------------------------------
	// empêcher l'enregistrement si le user connecté n'est pas le valideur requis
	if (etape === "Brouillon") {
		// si ce n'est pas le demandeur qui a ouvert la fiche :
		if (gsUser !== this.getValue("demandeur") && (UTILISATEUR['groupe'].toLowerCase() !== "admin") && (UTILISATEUR['groupe'].toLowerCase() !== "grp_adm_dar")) {
			gopaas.dialog.warning("Cette demande est en cours de création par <strong>"+this.getValue("demandeur")+"</strong>, vous ne pouvez pas la modifier");
			return false;
		}
	} else if (etape.indexOf("Attente valideur") === 0) {
		var valideurNumber = parseInt(etape.substr("Attente valideur ".length,1)),
			valideurField = "valideur" + valideurNumber
		;
		// si ce n'est pas le valideur en cours qui a ouvert la fiche :
		if (gsUser !== this.getValue(valideurField) && (UTILISATEUR['groupe'].toLowerCase() !== "admin") && (UTILISATEUR['groupe'].toLowerCase() !== "grp_adm_dar")) {
			gopaas.dialog.warning("Cette demande est en attente de validation par <strong>"+this.getValue(valideurField)+"</strong>, vous ne pouvez pas la modifier");
			return false;
		} else {
		}
	} else if (etape.indexOf("Attente validation") === 0) { // = attente arbitrage
		if ((UTILISATEUR['groupe'].toLowerCase() !== "admin") && (UTILISATEUR['groupe'].toLowerCase() !== "grp_adm_dar")) {
			gopaas.dialog.warning("Cette demande est en attente de validation, vous ne pouvez pas la modifier");
			return false;
		}
	} else if (etape === "Acceptée" || etape === "Refusée" || etape === "Annulée") {
		// console.log("Entrée dans Acceptée/ Refusée / Annulée");
		// JR le 22/11/2016 j'ai testé de rajouter les profils user ici mais ça ne fonctionne pas
		 if((UTILISATEUR['groupe'].toLowerCase() !== "admin") && (UTILISATEUR['groupe'].toLowerCase() !== "grp_adm_dar")){
			 gopaas.dialog.warning("Cette demande ne peut plus être modifiée");
			 return false;
		 }else{
		 return true;
		 }
		//gopaas.dialog.warning("Cette demande ne peut plus être modifiée");
		//return false;
	}

	thisComponent.setValue("pdf",thisComponent.getPDFFileName());

	thisComponent.flag = true; // attention ce flag est indispensable, sinon on va appeler en boucle le callback onSave() et les webservice 'html2pdf' et 'get_regle_valideur'
	if (thisComponent.getValue("etape") === "Brouillon" || thisComponent.getValue("etape") === "Envoi demande") {
		// JR 07/02/2017 MAJ des champs connexions
		$.get("template_auto/demande/demande.php", {
			service				: "get_regle_valideur",
			societe      		: thisComponent.getValue("d00societes"),
			type_contrat		: thisComponent.getValue("r06typecontrat"),
			duree				: thisComponent.getValue("nb_mois_cdd"),
			direction			: thisComponent.getValue("d00direction"),
			motif_recrutement	: thisComponent.getValue("d00motifpap"),
			metier				: thisComponent.getValue("d00metiers"),
			transverse			: thisComponent.getValue("transverse"),
			valeur_salaire		: thisComponent.getValue("d00valeursalaire")
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
	} else {
		// setPermission(thisComponent);
		thisComponent.saveItem(clicEnregistrer ? false : true, function() { generatePdf(); } ); // si on vient d'un clic sur le bouton Enregistrer, il faut lancer l'enregistrement sans fermer la fiche (paramètre false)
	}
	return false;
}

function dateDiff(date1, date2){
    var diff = {};                           // Initialisation du retour
    var tmp = date2 - date1;

    tmp = Math.floor(tmp/1000);             // Nombre de secondes entre les 2 dates
    diff.sec = tmp % 60;                    // Extraction du nombre de secondes

    tmp = Math.floor((tmp-diff.sec)/60);    // Nombre de minutes (partie entière)
    diff.min = tmp % 60;                    // Extraction du nombre de minutes

    tmp = Math.floor((tmp-diff.min)/60);    // Nombre d'heures (entières)
    diff.hour = tmp % 24;                   // Extraction du nombre d'heures

    tmp = Math.floor((tmp-diff.hour)/24);   // Nombre de jours restants
    diff.day = tmp;

    return diff;
}

function onPrint(thisComponent)
{
	window.open('webservice/pdf/html2pdf.php?modele=demande&cle='+thisComponent.getValue('cle')+'&table=demande&action=print');
}

function calculImpactBudgetaire(thisComponent){
	var impact_budgetaire_annee_n_plus_1 = 0;
	var impact_budgetaire_annee_n = 0;
	var impact_budgetaire_annee_pleine = 0;
	var salaire = thisComponent.getValue("salaire");
	var date_arrivee_souhaitee = thisComponent.getValue("date_arrivee_souhaitee");
	var endOfYear = new Date((new Date()).getFullYear()+'-12-31');
	var endOfNextYear = new Date((Number((new Date()).getFullYear())+1)+'-12-31');
	var startOfNextYear = new Date((Number((new Date()).getFullYear())+1)+'-01-01');
	var TxActivite = 1.48;
	//var calcul_n = (31/12 - date_arrivee_souhaitee) /365;

	// date de début
	var date1 = new Date(thisComponent.getValue('date_arrivee_souhaitee').substr(6,4)+'-'+thisComponent.getValue('date_arrivee_souhaitee').substr(3,2)+'-'+thisComponent.getValue('date_arrivee_souhaitee').substr(0,2));

	// date de fin
	var date2 = thisComponent.getValue("date_fin");
	if (!date2 || date2 === '00/00/0000') { // si date de fin n'est pas encore renseignée
		date2 = endOfNextYear;
	} else {
		date2 = new Date(date2.substr(6,4)+'-'+date2.substr(3,2)+'-'+date2.substr(0,2));
	}
	var date2_annee_n = date2 < endOfYear ? date2 : endOfYear; // si le contrat se termine avant la fin de l'année, on utilise la date de fin, sinon on utilise le dernier jour de l'année
	var date2_annee_n_plus_1 = date2 < endOfNextYear ? date2 : endOfNextYear; // si le contrat se termine avant la fin de l'année prochaine, on utilise la date de fin, sinon on utilise le dernier jour de l'année prochaine

	// différence des dates
	var diff_annee_n = dateDiff(date1, date2_annee_n);
	var diff_annee_n_plus_1 = dateDiff(startOfNextYear, date2_annee_n_plus_1);

	// budget
	impact_budgetaire_annee_n = Number(salaire) * (Math.max(Number(diff_annee_n.day)+1,0)/365) * TxActivite; // +1 car date_diff ne comptabilise pas le dernier jour
	impact_budgetaire_annee_n_plus_1 = Number(salaire) * (Math.max(Number(diff_annee_n_plus_1.day+1),0)/365) * TxActivite; // +1 car date_diff ne comptabilise pas le dernier jour
	impact_budgetaire_annee_pleine = Number(salaire) * TxActivite;

	thisComponent.setValue("impact_budgetaire_annee_n", impact_budgetaire_annee_n.toFixed(0));
	// thisComponent.setValue("impact_budgetaire_annee_n_plus_1", impact_budgetaire_annee_n_plus_1.toFixed(2));
	thisComponent.setValue("impact_budgetaire_annee_n_plus_1", impact_budgetaire_annee_pleine.toFixed(0));
}

// JR le 30/08/2016 ajouter un candidat
// JR le 07/02/2017 MAJ champs connexions
function create_candidat(thisComponent)
{
	ItemGeneric.open('candidat', null, null, {
		"demande":thisComponent.getValue("cle"),
		"demandeur":thisComponent.getValue("demandeur"),
		"transverse":thisComponent.getValue("transverse"),
		"salaire":thisComponent.getValue("salaire"),
		"t01_33_remuneration_fixe_annuelle_brute":thisComponent.getValue("salaire"),
		"t01_18_remuneration_fixe_mensuelle_brute":thisComponent.getValue("salaire_fixe_brut_mensuel"),
		"date_arrivee_souhaitee":thisComponent.getValue("date_arrivee_souhaitee"),
		"date_fin":thisComponent.getValue("date_fin"),
		"t01_30_date_debut_contrat":thisComponent.getValue("date_arrivee_souhaitee"),
		"t01_31_date_fin_contrat":thisComponent.getValue("date_fin"),
		"nb_mois_cdd":thisComponent.getValue("nb_mois_cdd"),
		"cs00societes":thisComponent.getValue("d00societes"),
		"cs00direction":thisComponent.getValue("d00direction"),
		"cs00postes":thisComponent.getValue("d00postes"),
		"cs00typecontrat":thisComponent.getValue("r06typecontrat"),
		"cs00motifentree":thisComponent.getValue("d00motifpap"),
		"cs00metiers":thisComponent.getValue("d00metiers"),
		"cs00nommanager":thisComponent.getValue("d00nommanager"),
		"recruteur":thisComponent.getValue("d00recruteur"),
		"etape":"Brouillon",
		"organigramme":"../demande/"+thisComponent.getValue('organigramme'),
		"pdf":"../demande/"+thisComponent.getValue('pdf'),
		"permission":thisComponent.getValue('permission')+","+thisComponent.getValue('recruteur')
	});
}