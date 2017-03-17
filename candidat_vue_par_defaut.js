function onLoad_view589() 
{
	var thisComponent = this,
		viewbar = thisComponent.ui.find(".Viewbar");
		
	function addButton(label, cssClass, onClick) {
		if (typeof cssClass === "function") {
			onClick = cssClass;
			cssClass = null;
		}
		// <li><a href="#" class="show-if-can-delete gopaas-delete-item" style=" margin-left:10px;"><span class="glyphicon glyphicon-minus" aria-hidden="true"></span>&nbsp;&nbsp;<span class="trn">Supprimer</span></a></li>
		
		if (IS_MOBILE) {
			var b = $("<button type='button'></button>");
			// console.log(viewbar.find(".gopaas-add-item").length);
			b.addClass(cssClass);
			b.addClass("btn btn-default btn-sm");
			b.css("margin-left","5px");
			b.click(onClick);
			// b.css("color","#777");
			// b.mouseenter(function() { $(this).css("color","#333"); });
			// b.mouseleave(function() { $(this).css("color","#777"); });
			b.html(label);
			b.insertAfter(viewbar.find(".gopaas-add-item"));
			return b;
		} else {
			var a = $("<a></a>");
			a.attr("href","#");
			a.css("margin-left","10px");
			a.click(onClick);
			a.append($("<span>"+label+"</span>"))
			
			var li = $("<li></li>");
			if (cssClass) {
				li.addClass(cssClass);
			}
			li.append(a);
			li.insertAfter(viewbar.find(".gopaas-delete-item").closest("li"));
			return li;
		}
	}
	
	//	viewbar.find(".gopaas-add-menu").addClass("btn-primary").css("padding-left","10px").css("padding-right","10px");
	//	if (!IS_MOBILE) {
	//		viewbar.find(".gopaas-add-item").find("span").css("color","white");
	//	}

	/*
	 * JR le 17/03/2017 Je supprime les bouton Ajouter > demande n°248
	 */
	if (IS_MOBILE) {
		viewbar.find(".gopaas-add-item").hide();
	}else{
		viewbar.find(".gopaas-add-menu").hide();
	}	
	
	// le dernier bouton ajouté, sera le premier affiché
	addButton("Tous les candidats", function() {
		View.open("candidat", "Tous les candidats", "Tableau");
	});
	addButton("En attente de votre validation", function() {
		View.open("candidat", "En attente de votre validation", "Tableau");
	});
	
	thisComponent.ui.find(".datagrid-header-row td > div.datagrid-cell > span").css("font-weight","bold");
	
	if (UTILISATEUR.profil.toLowerCase() !== 'admin') {
		viewbar.find(".gopaas-modify-view,.gopaas-manage-view").hide();
	}
}
