function onLoad_r15direction (){
	return true;
}
function onSave_r15direction(){

var thisComponent = this;

	if (this.isNew()) {
		
		if ( this.getValue('cle') == "" ) // indispensable sinon boucle récursive sur update()
		{
			var societe = this.getValue('r15societe');
			$.get("template_auto/r15direction/r15direction.php?mode=getKey&societe="+societe)
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