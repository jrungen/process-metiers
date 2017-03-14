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
						// je récupère l'intitulé du roletiers
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
			a05typecontrat:		this.getValue("a05typecontrat"),
			a05localisation:	this.getValue("a05localisation"),
			a05dateeffet:		this.getValue("a05dateeffet"),
			a05periodeessai1:	this.getValue("a05periodeessai1")
		};
	};
	
	thisComponent.getEtatDemandeDSI = function () {
		return {
			a05typecontrat:		this.getValue("a05typecontrat"),
			r03typemouvement:		this.getValue("r03typemouvement"),
			a05localisation:	this.getValue("a05cochsituationcourant")
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
		return differences;
	};
	
	thisComponent.getChangementDemandeDSI = function () {
		var demandeInitiale = JSON.parse(this.getValue("demande_initiale_dsi")),
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
		return differences;
	};
	// Rempli les champ demande_initiale au chargement
	thisComponent.setValue("demande_initiale_dri", JSON.stringify(thisComponent.getEtatDemandeDRI()));
	thisComponent.setValue("demande_initiale_dsi", JSON.stringify(thisComponent.getEtatDemandeDSI()));
	
	/*************************************************/
	/* FIN AJOUT JR DU 01/03/2017 VERIF MODIFIDATION */
	/*************************************************/
	
	/************************************************************************************/
	/* AJOUT AKE DU 27/02/2017 CONDITION D'AFFICHAGE DU CHAMP VEHICULE SI PERIODE DESSAI*/
	/************************************************************************************/

	// lancer la vérification au chargement
	console.log(thisComponent.ui.find("#a05vehicule").val());
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
		
		if(vVehicule !== 'VEH001'){
			thisComponent.ui.find("#combo_a05vehiculeperiodeessai").closest(".form-group").show();
			
		}else{
			thisComponent.ui.find("#combo_a05vehiculeperiodeessai").closest(".form-group").hide();
		}
	}

	/****************************************************************************************/
	/* FIN AJOUT AKE DU 27/02/2017 CONDITION D'AFFICHAGE DU CHAMP VEHICULE SI PERIODE DESSAI*/
	/****************************************************************************************/
	
	return true;
}



function onSave_a05typemouvement (){

	var thisComponent = this;
	
	// a executer uniquement si c'est une fiche existante
	if (!this.isNew()) {
		// on rempli les champs modification
		thisComponent.setValue("demande_modif_dri", JSON.stringify(thisComponent.getEtatDemandeDRI()));	
		thisComponent.setValue("demande_modif_dsi", JSON.stringify(thisComponent.getEtatDemandeDSI()));
		
		// on récupère les champs demande initiale et modification
		var demande_Init_DRI = thisComponent.getValue("demande_initiale_dri");
		var demande_Modif_DRI = thisComponent.getValue("demande_modif_dri");
		var demande_Init_DSI = thisComponent.getValue("demande_initiale_dsi");
		var demande_Modif_DSI = thisComponent.getValue("demande_modif_dsi");

		if ( (demande_Init_DRI !== demande_Modif_DRI) && (demande_Init_DSI !== demande_Modif_DSI) ){
			// Changement détecté dans les 2 cas
			gopaas.dialog.prompt('Vous venez de modifier cette fiche, vous pouvez ajouter un commentaire si nécessaire.', '', function (val1) {
				if (val1) {
					thisComponent.setValue('commentaire_modif_dri', val1);
					thisComponent.setValue('commentaire_modif_dsi', val1);

					// Webservice pour les envois d'emails
					var cle_drh = thisComponent.getValue('cle');
					$.get("template_auto/a05typemouvement/a05typemouvement.php?mode=email_1&cle="+cle_drh+"&user="+gsUser+"&commentaire="+val1)
					.done(function(data)
					{
						gopaas.dialog.notifyInfo('Mail envoyé aux DRI et DSI');
						thisComponent.saveItem(true);
					})
					.fail(gopaas.dialog.ajaxFail);

				}
			});
		}else{
			// Changement détecté dans un cas uniquement
			
			// if (demande_Init_DRI !== demande_Modif_DRI){
			// // Changement DRI
			
				// gopaas.dialog.prompt('Vous venez de modifier cette fiche, vous pouvez ajouter un commentaire si nécessaire.', '', function (val2) {
					// if (val2) {
						// thisComponent.setValue('commentaire_modif_dri', val2);

						// // Webservice pour les envois d'emails
						// var cle_drh = thisComponent.getValue('cle');
						// $.get("template_auto/a05typemouvement/a05typemouvement.php?mode=email_2&cle="+cle_drh+"&user="+gsUser+"&commentaire="+val2)
						// .done(function(data)
						// {
							// gopaas.dialog.notifyInfo('Mail envoyé aux DRI');
							// thisComponent.saveItem(true);
						// })
						// .fail(gopaas.dialog.ajaxFail);

					// }
				// });
			// }// FIN Changement DRI
			
			// if (demande_Init_DSI !== demande_Modif_DSI){
			// // Changement DSI
			
				// gopaas.dialog.prompt('Vous venez de modifier cette fiche, vous pouvez ajouter un commentaire si nécessaire.', '', function (val3) {
					// if (val3) {
						// thisComponent.setValue('commentaire_modif_dsi', val3);

						// // Webservice pour les envois d'emails
						// var cle_drh = thisComponent.getValue('cle');
						// $.get("template_auto/a05typemouvement/a05typemouvement.php?mode=email_3&cle="+cle_drh+"&user="+gsUser+"&commentaire="+val3)
						// .done(function(data)
						// {
							// gopaas.dialog.notifyInfo('Mail envoyé aux DSI');
							// thisComponent.saveItem(true);
						// })
						// .fail(gopaas.dialog.ajaxFail);

					// }
				// });
			// }// FIN Changement DSI
		}// FIN Changement détecté dans un cas uniquement
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