function onLoad_r11motifentree (){
	return true;
}
function onSave_r11motifentree (){

var thisComponent = this;

	if (this.isNew()) {
		
		if ( this.getValue('cle') == "" ) // indispensable sinon boucle récursive sur update()
		{
			$.get("template_auto/r11motifentree/r11motifentree.php?mode=getKey")
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