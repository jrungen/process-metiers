function onLoad_a02entreessortiesdri (){
var thisComponent = this;

	// Appeler webservice get-item pour les salaries
	if(thisComponent.getValue('a00personnephysique')){
		$.get("webservice/item/get-item.php", {
			tableName	: "a00personnephysique",
			itemKey  	: thisComponent.getValue('a00personnephysique')
		})
		.done(function(data){
			// je récupère nom et prénom
			var nom_prenom = data.a00nom+' '+data.a00prenom;
			var vCartouche = nom_prenom;
			thisComponent.ui.find('.form-horizontal > h3').html('<i class="fa fa-user" style="color:#8E3557;"></i> '+vCartouche);
			
			// 2ème get-item pour sur a05typemouvement
			if(thisComponent.getValue('a05typemouvement')){
				$.get("webservice/item/get-item.php", {
					tableName	: "a05typemouvement",
					itemKey  	: thisComponent.getValue('a05typemouvement')
				})
				.done(function(data2){
					// je récupère les clés r04roletiers et r03typemouvement pour les get-item suivant
					var roletiers = data2.r04roletiers;
					var typemouvement = data2.r03typemouvement;
					
					// 3ème get-item pour sur r04roletiers
					if (roletiers){
						$.get('webservice/item/get-item.php', {
							tableName	: 'r04roletiers',
							itemKey  	: roletiers
						})
						.done(function(data3){
							// je récupère l'intitulé du roletiers
							var libelle_role_tiers = data3.r04libellerole;

							var vCartouche2 = vCartouche+' - '+libelle_role_tiers;
							thisComponent.ui.find('.form-horizontal > h3').html('<i class="fa fa-user" style="color:#8E3557;"></i> '+vCartouche2);
							
							// 4ème get-item pour sur r03typemouvement
							if (typemouvement){
								$.get('webservice/item/get-item.php', {
									tableName	: 'r03typemouvement',
									itemKey  	: typemouvement
								})
								.done(function(data4){
									// je récupère l'intitulé du type mouvement
									var libelle_mouvement = data4.r03libelletypemouvement;
							
									var vCartouche3 = vCartouche2+' - '+libelle_mouvement;
									thisComponent.ui.find('.form-horizontal > h3').html('<i class="fa fa-user" style="color:#8E3557;"></i> '+vCartouche3);

								}).fail(gopaas.dialog.ajaxFail);
							}

						}).fail(gopaas.dialog.ajaxFail);
					}
					
				})
				.fail(gopaas.dialog.ajaxFail);
			}
		})
		.fail(gopaas.dialog.ajaxFail);
	}
	
	/*************************************************************************************/
	/* AJOUT JR DU 17/03/2017 Remplir le libellé type de mouvement dans l'onglet système */
	/*************************************************************************************/
	
	// lancer la vérification au chargement
	updateLibelleMvt(thisComponent);
		
	// SCRIPT avec intervalId
    thisComponent.ui.find("[name=a05typemouvement]").data("oldValue", thisComponent.ui.find("[name=a05typemouvement]").val());
    var intervalId2 = setInterval(function() {
        if (!thisComponent.ui.closest("body").length) { // si le composant a été supprimé (onglet fermé)
            clearInterval(intervalId2);
            return;
        }
        var a05typemouvement = thisComponent.ui.find("[name=a05typemouvement]");
        if (a05typemouvement.val() !== a05typemouvement.data("oldValue")) {
			a05typemouvement.data("oldValue", a05typemouvement.val());
			updateLibelleMvt(thisComponent);
        }
    }, 1000);
	
	function updateLibelleMvt(thisComponent){
		
	 	// get-item pour sur a05typemouvement
		if (thisComponent.getValue('a05typemouvement')){
			$.get('webservice/item/get-item.php', {
				tableName	: 'a05typemouvement',
				itemKey  	: thisComponent.getValue('a05typemouvement')
			})
			.done(function(data5){
			// je récupère l'intitulé du type de mouvement
				var LibelleMvt = data5.r03libelle_typemouvement;
			
				thisComponent.setValue('libelle_typemouvement', LibelleMvt);

			}).fail(gopaas.dialog.ajaxFail);
		}

	}

	/***********************************************************************************/
	/* FIN JR DU 17/03/2017 Remplir le libellé type de mouvement dans l'onglet système */
	/***********************************************************************************/
	
	// Add action in item
	this.addTool("Imprimer PDF","Imprimer PDF", function() { onPrint_mvt_dri(thisComponent); });
	
	return true;
}

function onSave_a02entreessortiesdri (){
	if (this.isNew()) {
		var cle = Date.now() + '_' + gsUser;
		this.setValue('cle', cle);
	}
	return true;
}

function onPrint_mvt_dri(thisComponent)
{
	window.open('file/__pdf__/html2pdf.php?modele=mvt_dri&cle='+thisComponent.getValue('cle')+'&table=a02entreessortiesdri&action=print');
}