function onLoad_a05typemouvement (){
	var thisComponent = this;

	// Vehicule
	thisComponent.ui.find("#COMPLEMENT_a05vehicule").on("change", function() {
		VerifVehicule(thisComponent);
	}).trigger("change");
	
	// JR le 02/02/2017 CARTOUCHE
	// JR le 03/02/2017 Maj avec get-item
	// 1er get-item pour sur a00personnephysique
	if(thisComponent.getValue('a00personnephysique')){
		$.get("webservice/item/get-item.php", {
			tableName	: "a00personnephysique",
			itemKey  	: thisComponent.getValue('a00personnephysique')
		})
		.done(function(data){
		// je récupère nom et prénom
			var nom = data.a00nom;
			var prenom = data.a00prenom;
			
			var vCartouche = nom+' '+prenom;
			thisComponent.ui.find('.form-horizontal > h3').html('<i class="fa fa-user" style="color:#8E3557;"></i> '+vCartouche);
			
			// 2ème get-item pour sur r04roletiers
			if (thisComponent.getValue('r04roletiers')){
				$.get('webservice/item/get-item.php', {
					tableName	: 'r04roletiers',
					itemKey  	: thisComponent.getValue('r04roletiers')
				})
				.done(function(data2){
				// je récupère l'intitulé du roletiers
					var libelle_role_tiers = data2.r04libellerole;
					
					var vCartouche2 = vCartouche+' - '+libelle_role_tiers;
					thisComponent.ui.find('.form-horizontal > h3').html('<i class="fa fa-user" style="color:#8E3557;"></i> '+vCartouche2);
					
					// 3ème get-item pour sur r03typemouvement
					if (thisComponent.getValue('r03typemouvement')){
						$.get('webservice/item/get-item.php', {
							tableName	: 'r03typemouvement',
							itemKey  	: thisComponent.getValue('r03typemouvement')
						})
						.done(function(data3){
						// je récupère l'intitulé du type de mouvement
							var libelle_mouvement = data3.r03libelletypemouvement;
							
							var vCartouche3 = vCartouche2+' - '+libelle_mouvement;
							thisComponent.ui.find('.form-horizontal > h3').html('<i class="fa fa-user" style="color:#8E3557;"></i> '+vCartouche3);

						}).fail(gopaas.dialog.ajaxFail);
					}

				}).fail(gopaas.dialog.ajaxFail);
			}

		}).fail(gopaas.dialog.ajaxFail);
	}	
	
	/*********************************************/
	/* AJOUT JR DU 01/03/2017 VERIF MODIFIDATION */
	/*********************************************/
	
	/*******************************************************************************/
	// renvoie un objet JSON contenant les valeurs actuelles des champs à surveiller
	// pour détecter les modifs ----------------------------------------------------
	/*******************************************************************************/
	thisComponent.getEtatDemandeDRI = function () {
		return {
			r00societes:		this.getValue("r00societes"),
			r07sites:			this.getValue("r07sites"),
			a05typecontrat:		this.getValue("a05typecontrat"),
			a05periodeessai1:	this.getValue("a05periodeessai1"),
			a05periodeessai2:	this.getValue("a05periodeessai2"),
			a05vehicule:		this.getValue("a05vehicule"),
			a05vehiculeperiodeessai:this.getValue("a05vehiculeperiodeessai")
		};
	};
	
	thisComponent.getEtatDemandeDSI = function () {
		return {
			r00societes:		this.getValue("r00societes"),
			r07sites:			this.getValue("r07sites")
		};
	};
	
	/*******************************************************************************/
	// renvoie un tableau JSON avec la liste des différences par rapport à la demande initiale.
	// Renvoie un tableau vide [] si aucun changement
	/*******************************************************************************/
	thisComponent.getChangementDemandeDRI = function () {
		var demandeInitiale = JSON.parse(this.getValue("demande_initiale_dri")),
			demandeActuelle = this.getEtatDemandeDRI(),
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
		// console.log('DRI : '+differences);
		return differences;
	};
	
	thisComponent.getChangementDemandeDSI = function () {
		var demandeInitiale = JSON.parse(this.getValue("demande_initiale_dsi")),
			demandeActuelle = this.getEtatDemandeDSI(),
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
		// console.log('DRI : '+differences);
		return differences;
	};
	// Rempli les champ demande_initiale au chargement
	thisComponent.setValue("demande_initiale_dri", JSON.stringify(thisComponent.getEtatDemandeDRI()));
	thisComponent.setValue("demande_initiale_dsi", JSON.stringify(thisComponent.getEtatDemandeDSI()));
	thisComponent.setValue("lock", "Non" );
	
	/*************************************************/
	/* FIN AJOUT JR DU 01/03/2017 VERIF MODIFIDATION */
	/*************************************************/
	
	/************************************************************************************/
	/* AJOUT AKE DU 27/02/2017 CONDITION D'AFFICHAGE DU CHAMP VEHICULE SI PERIODE DESSAI*/
	/************************************************************************************/

	// lancer la vérification au chargement
	// console.log(thisComponent.ui.find("#a05vehicule").val());
	VerifVehicule(thisComponent);
		
	// SCRIPT avec intervalId
	thisComponent.ui.find("[name=a05vehicule]").data("oldValue", thisComponent.ui.find("[name=a05vehicule]").val());
	var intervalId1 = setInterval(function() {
		if (!thisComponent.ui.closest("body").length) { // si le composant a été supprimé (onglet fermé)
			clearInterval(intervalId1);
			return;
		}
		var a05vehicule = thisComponent.ui.find("[name=a05vehicule]");
		if (a05vehicule.val() !== a05vehicule.data("oldValue")) {
			a05vehicule.data("oldValue", a05vehicule.val());
			VerifVehicule(thisComponent);
		}
	}, 1000);

	
	function VerifVehicule(thisComponent){
		
		var vVehicule = thisComponent.getValue('a05vehicule');
		
		if(vVehicule !== 'VEH001' | vVehicule !== '' ){
			thisComponent.ui.find("#combo_a05vehiculeperiodeessai").closest(".form-group").show();
			
		}
		if(vVehicule === 'VEH001' | vVehicule === '' ){
			thisComponent.ui.find("#combo_a05vehiculeperiodeessai").closest(".form-group").hide();
		}
	}

	/****************************************************************************************/
	/* FIN AJOUT AKE DU 27/02/2017 CONDITION D'AFFICHAGE DU CHAMP VEHICULE SI PERIODE DESSAI*/
	/****************************************************************************************/

	/*************************************************************************************/
	/* AJOUT JR DU 17/03/2017 Remplir le libellé type de mouvement dans l'onglet système */
	/*************************************************************************************/
	
	// lancer la vérification au chargement
	updateLibelleMvt(thisComponent);
		
	// SCRIPT avec intervalId
    thisComponent.ui.find("[name=r03typemouvement]").data("oldValue", thisComponent.ui.find("[name=r03typemouvement]").val());
    var intervalId2 = setInterval(function() {
        if (!thisComponent.ui.closest("body").length) { // si le composant a été supprimé (onglet fermé)
            clearInterval(intervalId2);
            return;
        }
        var r03typemouvement = thisComponent.ui.find("[name=r03typemouvement]");
        if (r03typemouvement.val() !== r03typemouvement.data("oldValue")) {
			r03typemouvement.data("oldValue", r03typemouvement.val());
			updateLibelleMvt(thisComponent);
        }
    }, 1000);
	
	function updateLibelleMvt(thisComponent){
		
	 	// get-item pour sur r03typemouvement
		if (thisComponent.getValue('r03typemouvement')){
			$.get('webservice/item/get-item.php', {
				tableName	: 'r03typemouvement',
				itemKey  	: thisComponent.getValue('r03typemouvement')
			})
			.done(function(data4){
			// je récupère l'intitulé du type de mouvement
				var LibelleMvt = data4.r03libelletypemouvement;
			
				thisComponent.setValue('r03libelle_typemouvement', LibelleMvt);

			}).fail(gopaas.dialog.ajaxFail);
		}

	}

	/***********************************************************************************/
	/* FIN JR DU 17/03/2017 Remplir le libellé type de mouvement dans l'onglet système */
	/***********************************************************************************/

	/***********************************************************************/
	/* AJOUT JR DU 17/03/2017 Remplir le périmètre depuis le champ société */
	/***********************************************************************/
	
	// lancer la vérification au chargement
	updatePerimetre(thisComponent);
		
	// SCRIPT avec intervalId
    thisComponent.ui.find("[name=r00societes]").data("oldValue", thisComponent.ui.find("[name=r00societes]").val());
    var intervalId2 = setInterval(function() {
        if (!thisComponent.ui.closest("body").length) { // si le composant a été supprimé (onglet fermé)
            clearInterval(intervalId2);
            return;
        }
        var r00societes = thisComponent.ui.find("[name=r00societes]");
        if (r00societes.val() !== r00societes.data("oldValue")) {
			r00societes.data("oldValue", r00societes.val());
			updatePerimetre(thisComponent);
        }
    }, 1000);
	
	function updatePerimetre(thisComponent){
		
	 	// get-item pour sur r00societes
		if (thisComponent.getValue('r00societes')){
			$.get('webservice/item/get-item.php', {
				tableName	: 'r00societes',
				itemKey  	: thisComponent.getValue('r00societes')
			})
			.done(function(data5){
			// je récupère la clé du périmètre
				var clePerimetre = data5.r00perimetre;
			
				thisComponent.setConnectionValue('a05perimetre', 'r28perimetre', clePerimetre);
				
/*			 	// get-item pour sur r28perimetre
				if (clePerimetre){
					$.get('webservice/item/get-item.php', {
						tableName	: 'r28perimetre',
						itemKey  	: clePerimetre
					})
					.done(function(data6){
					// je récupère le complément du périmètre
						var complementPerimetre = data6.r28perimetre;
					
						thisComponent.setValue('COMPLEMENT_a05perimetre', complementPerimetre);

					}).fail(gopaas.dialog.ajaxFail);
				}*/

			}).fail(gopaas.dialog.ajaxFail);
		}

	}

	/**********************************************************************/
	/* FIN JR DU 17/03/2017 Remplir le périmètre depuis le champ société  */
	/**********************************************************************/

	return true;
}

function onSave_a05typemouvement (){

	var thisComponent = this;
	
	// a executer uniquement si c'est une fiche existante
	if (!this.isNew()) {
		// on rempli les champs modification
		var lock1 = $('#lock').val();
		 if (lock1 === "Non"){
			// Execute les fonctions pour tester les changements
			var differences_dri = thisComponent.getChangementDemandeDRI(),
					listeModif_dri = ""
				;
			var differences_dsi = thisComponent.getChangementDemandeDSI(),
					listeModif_dsi = ""
				;
			
			// Créer des tableaux avec les modifications
			for (var i=0; i<differences_dri.length; i++) {
				var dri = differences_dri[i];
				listeModif_dri += "<b>" + dri.label + "</b> : <b>" + dri.before + "</b> -> <b>" + dri.after + "</b><br>\n";
			}
			console.log(listeModif_dri);
			
			for (var j=0; j<differences_dsi.length; j++) {
				var dsi = differences_dsi[j];
				listeModif_dsi += "<b>" + dsi.label + "</b> : <b>" + dsi.before + "</b> -> <b>" + dsi.after + "</b><br>\n";
			}
			console.log(listeModif_dsi);
		}
		
		if ($('#lock').val() === 'Non'){
			if ( (listeModif_dri !== '') && (listeModif_dsi !== '') ){
				console.log('Changement sur les 2');
				gopaas.dialog.prompt('Vous venez de modifier cette fiche, vous pouvez ajouter un commentaire si nécessaire.', '', function (val1) {
					if (val1) {

						thisComponent.setValue('commentaire_modif_dri', val1);
						thisComponent.setValue('commentaire_modif_dsi', val1);
						thisComponent.setValue('demande_modif_dri', listeModif_dri);
						thisComponent.setValue('demande_modif_dsi', listeModif_dsi);

						// Webservice pour les envois d'emails
						var cle_drh = thisComponent.getValue('cle');
						$.get("template_auto/a05typemouvement/a05typemouvement.php?mode=email_1&cle="+cle_drh+"&user="+gsUser+"&commentaire="+val1+"&listeModif_dri="+listeModif_dri+"&listeModif_dsi="+listeModif_dsi)
						.done(function(data)
						{
							gopaas.dialog.notifyInfo('Mail envoyé aux DRI et DSI');
							thisComponent.setValue("lock", "Oui");
							thisComponent.saveItem(false);
							
							var societe = thisComponent.getValue("r00societes");
							var site = thisComponent.getValue("r07sites");
							var typecontrat = thisComponent.getValue("a05typecontrat");
							var periodeessai1 = thisComponent.getValue("a05periodeessai1");
							var periodeessai2 = thisComponent.getValue("a05periodeessai2");
							var vehicule = thisComponent.getValue("a05vehicule");
							var vehiculeperiodeessai = thisComponent.getValue("a05vehiculeperiodeessai");
							
							// webservice pour mettre à jour DRI
							$.get("template_auto/a05typemouvement/a05typemouvement.php?mode=update_dri&cle="+cle_drh+"&societe="+societe+"&site="+site+"&typecontrat="+typecontrat+"&periodeessai1="+periodeessai1+"&periodeessai2="+periodeessai2+"&vehicule="+vehicule+"&vehiculeperiodeessai="+vehiculeperiodeessai)
							.done(function(data2)
							{
								gopaas.dialog.notifyInfo('Mise à jour fiche DRI effectué');					
								
								// webservice pour mettre à jour DSI
								$.get("template_auto/a05typemouvement/a05typemouvement.php?mode=update_dsi&cle="+cle_drh+"&societe="+societe+"&site="+site)
								.done(function(data3)
								{
									gopaas.dialog.notifyInfo('Mise à jour fiche DSI effectué');
									thisComponent.saveItem(true);
								})
								.fail(gopaas.dialog.ajaxFail);
							})
							.fail(gopaas.dialog.ajaxFail);
						})
						.fail(gopaas.dialog.ajaxFail);

					}
				});
			}else{
				// Changement détecté dans un cas uniquement
				if (listeModif_dri !== ''){
				// Changement DRI
				console.log('Changement sur DRI seulement');
				
					gopaas.dialog.prompt('Vous venez de modifier cette fiche, vous pouvez ajouter un commentaire si nécessaire.', '', function (val2) {
						if (val2) {
							
							thisComponent.setValue('commentaire_modif_dri', val2);
							thisComponent.setValue('demande_modif_dri', listeModif_dri);

							// Webservice pour les envois d'emails
							var cle_drh = thisComponent.getValue('cle');
							$.get("template_auto/a05typemouvement/a05typemouvement.php?mode=email_2&cle="+cle_drh+"&user="+gsUser+"&commentaire="+val2+"&listeModif_dri="+listeModif_dri)
							.done(function(data)
							{
								gopaas.dialog.notifyInfo('Mail envoyé aux DRI');
								thisComponent.setValue("lock", "Oui");
								thisComponent.saveItem(false);
								
								var societe = thisComponent.getValue("r00societes");
								var site = thisComponent.getValue("r07sites");
								var typecontrat = thisComponent.getValue("a05typecontrat");
								var periodeessai1 = thisComponent.getValue("a05periodeessai1");
								var periodeessai2 = thisComponent.getValue("a05periodeessai2");
								var vehicule = thisComponent.getValue("a05vehicule");
								var vehiculeperiodeessai = thisComponent.getValue("a05vehiculeperiodeessai");
								
								// webservice pour mettre à jour DRI
								$.get("template_auto/a05typemouvement/a05typemouvement.php?mode=update_dri&cle="+cle_drh+"&societe="+societe+"&site="+site+"&typecontrat="+typecontrat+"&periodeessai1="+periodeessai1+"&periodeessai2="+periodeessai2+"&vehicule="+vehicule+"&vehiculeperiodeessai="+vehiculeperiodeessai)
								.done(function(data2)
								{
									gopaas.dialog.notifyInfo('Mise à jour fiche DRI effectué');		
									thisComponent.saveItem(true);
								})
								.fail(gopaas.dialog.ajaxFail);
							})
							.fail(gopaas.dialog.ajaxFail);

						}
					});
				}// FIN Changement DRI
				
				if (listeModif_dsi !== ''){
				// Changement DSI
				console.log('Changement sur DSI seulement');
				
					gopaas.dialog.prompt('Vous venez de modifier cette fiche, vous pouvez ajouter un commentaire si nécessaire.', '', function (val3) {
						if (val3) {
							
							thisComponent.setValue('commentaire_modif_dsi', val3);
							thisComponent.setValue('demande_modif_dsi', listeModif_dsi);

							// Webservice pour les envois d'emails
							var cle_drh = thisComponent.getValue('cle');
							$.get("template_auto/a05typemouvement/a05typemouvement.php?mode=email_3&cle="+cle_drh+"&user="+gsUser+"&commentaire="+val3+"&listeModif_dsi="+listeModif_dsi)
							.done(function(data)
							{
								gopaas.dialog.notifyInfo('Mail envoyé aux DSI');
								thisComponent.setValue("lock", "Oui");
								thisComponent.saveItem(false);
								
								var societe = thisComponent.getValue("r00societes");
								var site = thisComponent.getValue("r07sites");
								// webservice pour mettre à jour DSI
								$.get("template_auto/a05typemouvement/a05typemouvement.php?mode=update_dsi&cle="+cle_drh+"&societe="+societe+"&site="+site)
								.done(function(data3)
								{
									gopaas.dialog.notifyInfo('Mise à jour fiche DSI effectué');
									thisComponent.saveItem(true);
								})
								.fail(gopaas.dialog.ajaxFail);
							})
							.fail(gopaas.dialog.ajaxFail);

						}
					});
				}// FIN Changement DSI
			}// FIN Changement détecté dans un cas uniquement
		}
	}
		
	if (this.isNew()) {
		//var cle = 'AC'+ Date.now() ;
		if ( this.getValue('cle') == "" ) // indispensable sinon boucle récursive sur update()
		{
			$.get("template_auto/a05typemouvement/a05typemouvement.php?mode=numerotation")
			.done(function(cle) 
			{
				thisComponent.setValue('cle', cle);
				thisComponent.saveItem(false);
			})
			.fail(gopaas.dialog.ajaxFail);
			return false; // car on relance le update en retour de la requête Ajax de cle_devis.php . si on retourne true maintenant on va lancer l'enregistrement avant d'avoir récupéré la clé
		}
	}
	return true;
}