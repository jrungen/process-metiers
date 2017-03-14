function onLoad_a00personnephysique (){
	var thisComponent_a00 = this;	

    // JR le 02/02/2017 création du CARTOUCHE
    // JR le 03/02/2017 MAJ avec get-item pour r04roletiers
	var vCartouche = thisComponent_a00.getValue('a00nom')+' '+thisComponent_a00.getValue('a00prenom');
	thisComponent_a00.ui.find('.form-horizontal > h3').html('<i class="fa fa-user" style="color:#562380;"></i> '+vCartouche);
	
	if (thisComponent_a00.getValue('r04roletiers')){
		$.get("webservice/item/get-item.php", {
			tableName	: "r04roletiers",
			itemKey  	: thisComponent_a00.getValue('r04roletiers')
		})
		.done(function(data){
			// je récupère l'intitulé du role tiers
			var libelle_role_tiers = data.r04libellerole;
			
			var vCartouche2 = vCartouche+' - '+libelle_role_tiers;
			thisComponent_a00.ui.find('.form-horizontal > h3').html('<i class="fa fa-user" style="color:#562380;"></i> '+vCartouche2);
			
			// 2ème get-item pour sur r03typemouvement
			if (thisComponent_a00.getValue('r03typemouvement')){
				$.get("webservice/item/get-item.php", {
					tableName	: "r03typemouvement",
					itemKey  	: thisComponent_a00.getValue('r03typemouvement')
				})
				.done(function(data2){
					// je récupère l'intitulé du mouvement
					var libelle_mouvement = data2.r03libelletypemouvement;
					
					var vCartouche3 = vCartouche2+' - '+libelle_mouvement;
					thisComponent_a00.ui.find('.form-horizontal > h3').html('<i class="fa fa-user" style="color:#562380;"></i> '+vCartouche3);
				
				}).fail(gopaas.dialog.ajaxFail);
			}
		
		}).fail(gopaas.dialog.ajaxFail);
	}
	
	// Ajouter bouton Imprimer
	thisComponent_a00.ui.find("#r03typemouvement").closest(".form-group").append( "<button id='btn_generer_mv' type='button' class='btn btn-primary btn-sm' style='float: right; margin-right: 15px; margin-top: 5px;' title='Générer Mouvement'>Générer Mouvement</span></button>" );
	
	thisComponent_a00.ui.find("#btn_generer_mv").on('click', function(){
		generer_mv(thisComponent_a00);
	});
	
	// On masque les onglets pour les utilisateurs Guest"
	/* if (UTILISATEUR.profil === 'Guest') {
		
	//On masque l'onglet Mouvement
		thisComponent_a00.ui.find('#tab2555').hide();
	//On masque l'onglet SYSTEM
		thisComponent_a00.ui.find('#tab2549').hide();
	} */

    //Sur modification Rôle Tiers
	this.ui.find('#COMPLEMENT_r04roletiers').change(function(){
		console.log('change');
         AFFSectionComplementAction(thisComponent_a00);
	});
	return true;

}
function AFFSectionComplementAction (thisComponent_a00){
// ********************************************************************************************************************
// La fonction permet d'afficher le complément d'information à renseigner pour les intérimaire et 
// les consultants
// ********************************************************************************************************************

	//On récupère la valeur du nombre de RTT monétisé
	var vRole = thisComponent_a00.getValue('#COMPLEMENT_r04roletiers');
	console.log('ROLE : ' + vRole);
	if(vRole=='SALARIE'){
			// on rend invisible la section
			thisComponent_a00.ui.find('#section_1293').hide();
	}else{
			// on rend visible la section
			thisComponent_a00.ui.find('#section_1293').show();		
	}
}
function generer_mv (thisComponent_a00){	
	// Sauvegarde la fiche avant de continuer
	
	if (thisComponent_a00.isNew()) {
		//var cle = 'AC'+ Date.now() ;
		if ( thisComponent_a00.getValue('cle') == "" ) // indispensable sinon boucle récursive sur update()
		{
			$.get("template_auto/a00personnephysique/a00personnephysique.php?mode=getKey")
			.done(function(cle) 
			{
				thisComponent_a00.setValue('cle', cle);
				thisComponent_a00.saveItem(false);
				mouvement(thisComponent_a00);
			})
			.fail(gopaas.dialog.ajaxFail);
		}
	}else{
		mouvement(thisComponent_a00);
	}
		
}

function mouvement(thisComponent_a00){
	// Webservice pour créer mouvement dans DRH, DRI et DSI
	var cle = thisComponent_a00.getValue('cle');
	var role_tiers = thisComponent_a00.getValue('r04roletiers');
	var mouvement = thisComponent_a00.getValue('r03typemouvement');

	if ((role_tiers != '') && (mouvement!='')){
		$.get("template_auto/a00personnephysique/a00personnephysique.php?mode=mouvement&cle="+cle+"&role_tiers="+role_tiers+"&mouvement="+mouvement)
		.done(function(data)
		{
			gopaas.dialog.notifyInfo('Génération des fiches terminée !!!');
			// console.log('Fiches créées');
			thisComponent_a00.ui.find('#COMPLEMENT_r04roletiers').closest('.input-group').find('input').val('');
			thisComponent_a00.ui.find('#COMPLEMENT_r03typemouvement').closest('.input-group').find('input').val('');
		})
		.fail(gopaas.dialog.ajaxFail);
	}else{
		gopaas.dialog.info("Vous devez d'abord renseigner les champs Rôle tiers et Mouvement");
	}
}
	
function onSave_a00personnephysique (){

var thisComponent = this;

	if (this.isNew()) {
		//var cle = 'AC'+ Date.now() ;
		if ( this.getValue('cle') == "" ) // indispensable sinon boucle récursive sur update()
		{
			$.get("template_auto/a00personnephysique/a00personnephysique.php?mode=getKey")
			.done(function(cle) 
			{
				thisComponent.setValue('cle', cle);
				thisComponent.saveItem(false);
			})
			.fail(gopaas.dialog.ajaxFail);
		}
	}
	return true;
}

