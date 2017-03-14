function onLoad_demande () {
	var thisComponentDemande = this;

	// JR LE 07/02/2017 TEST NOUVEAU CHAMP TYPE CONTRAT
	// thisComponentDemande.ui.find('#COMPLEMENT_r06typecontrat').attr("disabled","disabled").addClass("disabled");
	// $('#COMPLEMENT_r06typecontrat').attr("disabled","disabled").addClass("disabled");
	
	this.ui.find('#COMPLEMENT_r06typecontrat').change(function(){
		alert('test');
		getItem(thisComponentDemande);
	});
	
	return true;
}
function onSave_demande (){
	if (this.isNew()) {
		var cle = Date.now() + '_' + gsUser;
		this.setValue('cle', cle);
	}
	return true;
}

function getItem(itemComponent){
	var type_article = itemComponent.getValue('r06typecontrat');
	
	console.log(type_article);
	$.get( "webservice/item/get-item.php?tableName=r06typecontrat&itemKey="+type_article)
	.done( function( data ) 
	{
		console.log(data.r06codetypecontrat);
		console.log(data.r06libtypecontrat);
	})
	.fail( gopaas.dialog.ajaxFail);
}
