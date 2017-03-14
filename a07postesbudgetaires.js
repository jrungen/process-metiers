		//**********************************************************************************************
		// VARIABLES PAR DEFAUT
		//**********************************************************************************************
		
		//TAUX DE CHARGE
		var vTxCharge = 1.48;		
		
		
		function onLoad_a07postesbudgetaires (){
	
			var thisComponent = this;
		
			// lancer les vérifications au chargement
			verifMotifPAP(thisComponent);
			verifMouvement(thisComponent);
		
	
	
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
		
		/*********************************************************************/
		/*********************************************************************/
		// 					          EVENEMENTS 						     //
		/*********************************************************************/
		/*********************************************************************/	
		
		
		//CONTROLE SUR LE MOTIF PAP
			thisComponent.ui.find("[name=a07motif]").data("oldValue", thisComponent.ui.find("[name=a07motif]").val());
			var intervalId2 = setInterval(function() {
				if (!thisComponent.ui.closest("body").length) { // si le composant a été supprimé (onglet fermé)
					clearInterval(intervalId2);
					return;
				}
				var a07motif = thisComponent.ui.find("[name=a07motif]");
				if (a07motif.val() !== a07motif.data("oldValue")) {
					a07motif.data("oldValue", a07motif.val());
					verifMotifPAP(thisComponent);			
				}
			}, 1000);
	
		//CONTROLE SUR LE TYPE DE MOUVEMENT PAP
			thisComponent.ui.find("[name=a07mouvement]").data("oldValue", thisComponent.ui.find("[name=a07mouvement]").val());
			var intervalId2 = setInterval(function() {
				if (!thisComponent.ui.closest("body").length) { // si le composant a été supprimé (onglet fermé)
					clearInterval(intervalId2);
					return;
				}
				var a07mouvement = thisComponent.ui.find("[name=a07mouvement]");
				if (a07mouvement.val() !== a07mouvement.data("oldValue")) {
					a07mouvement.data("oldValue", a07mouvement.val());
					verifMouvement(thisComponent);			
				}
			}, 1000);
		
		//CONTROLE SUR LE TYPE DE CONTRAT		
			thisComponent.ui.find("[name=a07typecontrat]").data("oldValue", thisComponent.ui.find("[name=a07typecontrat]").val());
			var intervalId2 = setInterval(function() {
				if (!thisComponent.ui.closest("body").length) { // si le composant a été supprimé (onglet fermé)
					clearInterval(intervalId2);
					return;
				}
				var a07typecontrat = thisComponent.ui.find("[name=a07typecontrat]");
				if (a07typecontrat.val() !== a07typecontrat.data("oldValue")) {
					a07typecontrat.data("oldValue", a07typecontrat.val());
					verifMouvement(thisComponent);
					CleanDatesSortie(thisComponent);
			
					calcul_a07anneepap(thisComponent);
					calcul_a07trimestre(thisComponent);
					calcul_a07forecastdatearrivee(thisComponent);
		
					calcul_a07prevetp(thisComponent);
					calcul_a07reeletp(thisComponent);
					calcul_a07forecastetp(thisComponent);	

					calcul_a07prevvariationeffectif(thisComponent);		
					calcul_a07reelvariationeffectif(thisComponent);
					calcul_a07forecastvariationeffectif(thisComponent);

					//CALCULS DONNEES SALARIALES PREVISIONNELLES
					calcul_a07prevmsanneepleinecc (thisComponent);
					calcul_a07prevmsproratcc (thisComponent);
					calcul_a07prevecosortieetp	 (thisComponent);
					calcul_a07prevecosortiemsproratcc (thisComponent);
					calcul_a07prevecosortiemsanneepleinecc (thisComponent);
					calcul_a07prevsurcoutecoetp (thisComponent);
					calcul_a07prevsurcoutecomsanneepleinecc (thisComponent);
					calcul_a07prevsurcoutecomsproratcc	 (thisComponent);

					//CALCULS DONNEES SALARIALES REELLES
					calcul_a07reelmsanneepleinecc (thisComponent);
					calcul_a07reelmsproratcc (thisComponent);
					calcul_a07reelecosortieetp	 (thisComponent);
					calcul_a07reelecosortiemsproratcc (thisComponent);
					calcul_a07reelecosortiemsanneepleinecc (thisComponent);
					calcul_a07reelsurcoutecoetp (thisComponent);
					calcul_a07reelsurcoutecomsanneepleinecc (thisComponent);
					calcul_a07reelsurcoutecomsproratcc (thisComponent);

					//CALCULS DONNEES SALARIALES FORECAST
					calcul_a07forecastsurcoutecoetp (thisComponent);
					calcul_a07forecastsurcoutecomsanneepleinecc (thisComponent);
					calcul_a07forecastsurcoutecomsproratcc (thisComponent);
					
				}
			}, 1000);

		//Sur le champ Taux d'activité
	
		this.ui.find('#a07tauxactivite').change(function(){
		
			calcul_a07prevvariationeffectif(thisComponent);
			calcul_a07prevetp(thisComponent);		

			calcul_a07reelvariationeffectif(thisComponent);
			calcul_a07reeletp(thisComponent);		
		
			calcul_a07forecastvariationeffectif(thisComponent);
			calcul_a07forecastetp(thisComponent);		

			//CALCULS DONNEES SALARIALES PREVISIONNELLES
			calcul_a07prevmsanneepleinecc (thisComponent);
			calcul_a07prevmsproratcc (thisComponent);
			calcul_a07prevecosortieetp	 (thisComponent);
			calcul_a07prevecosortiemsproratcc (thisComponent);
			calcul_a07prevecosortiemsanneepleinecc (thisComponent);
			calcul_a07prevsurcoutecoetp (thisComponent);
			calcul_a07prevsurcoutecomsanneepleinecc (thisComponent);
			calcul_a07prevsurcoutecomsproratcc	 (thisComponent);

			//CALCULS DONNEES SALARIALES REELLES
			calcul_a07reelmsanneepleinecc (thisComponent);
			calcul_a07reelmsproratcc (thisComponent);
			calcul_a07reelecosortieetp	 (thisComponent);
			calcul_a07reelecosortiemsproratcc (thisComponent);
			calcul_a07reelecosortiemsanneepleinecc (thisComponent);
			calcul_a07reelsurcoutecoetp (thisComponent);
			calcul_a07reelsurcoutecomsanneepleinecc (thisComponent);
			calcul_a07reelsurcoutecomsproratcc (thisComponent);

			//CALCULS DONNEES SALARIALES FORECAST
			calcul_a07forecastsurcoutecoetp (thisComponent);
			calcul_a07forecastsurcoutecomsanneepleinecc (thisComponent);
			calcul_a07forecastsurcoutecomsproratcc (thisComponent);
			
		});	

		//Sur le champ Salaire annuel Prévisionnel

		this.ui.find('#a07prevsalaireannuelhc').change(function(){
		
			//CALCULS DONNEES SALARIALES PREVISIONNELLES
			calcul_a07prevmsanneepleinecc (thisComponent);
			calcul_a07prevmsproratcc (thisComponent);
			calcul_a07prevecosortieetp (thisComponent);
			calcul_a07prevecosortiemsproratcc (thisComponent);
			calcul_a07prevecosortiemsanneepleinecc (thisComponent);
			calcul_a07prevsurcoutecoetp (thisComponent);
			calcul_a07prevsurcoutecomsanneepleinecc (thisComponent);
			calcul_a07prevsurcoutecomsproratcc	 (thisComponent);

			//CALCULS DONNEES SALARIALES FORECAST
			calcul_a07forecastsurcoutecoetp (thisComponent);
			calcul_a07forecastsurcoutecomsanneepleinecc (thisComponent);
			calcul_a07forecastsurcoutecomsproratcc (thisComponent);
		});			
		
		
		//Sur le champ Date entrée Prévisionnelle
	
		this.ui.find('#a07prevdatearrivee').change(function(){
		
			calcul_a07anneepap(thisComponent);
			calcul_a07trimestre(thisComponent);
			calcul_a07forecastdatearrivee(thisComponent);
		
			calcul_a07prevetp(thisComponent);
			calcul_a07forecastetp(thisComponent);		

			calcul_a07prevvariationeffectif(thisComponent);		
			calcul_a07forecastvariationeffectif(thisComponent);

			//CALCULS DONNEES SALARIALES PREVISIONNELLES
			calcul_a07prevmsanneepleinecc (thisComponent);
			calcul_a07prevmsproratcc (thisComponent);
			calcul_a07prevecosortieetp (thisComponent);
			calcul_a07prevecosortiemsproratcc (thisComponent);
			calcul_a07prevecosortiemsanneepleinecc (thisComponent);
			calcul_a07prevsurcoutecoetp (thisComponent);
			calcul_a07prevsurcoutecomsanneepleinecc (thisComponent);
			calcul_a07prevsurcoutecomsproratcc	 (thisComponent);

			//CALCULS DONNEES SALARIALES FORECAST
			calcul_a07forecastsurcoutecoetp (thisComponent);
			calcul_a07forecastsurcoutecomsanneepleinecc (thisComponent);
			calcul_a07forecastsurcoutecomsproratcc (thisComponent);
		});	
	
	
		//Sur le champ Date sortie Prévisionnelle

		this.ui.find('#a07prevdatedepart').change(function(){
	
			calcul_a07anneepap(thisComponent);
			calcul_a07trimestre(thisComponent);
			calcul_a07forecastdatedepart(thisComponent);
		
			calcul_a07prevetp(thisComponent);
			calcul_a07forecastetp(thisComponent);		
		
			calcul_a07prevvariationeffectif(thisComponent);
			calcul_a07forecastvariationeffectif(thisComponent);

			//CALCULS DONNEES SALARIALES PREVISIONNELLES
			calcul_a07prevmsanneepleinecc (thisComponent);
			calcul_a07prevmsproratcc (thisComponent);
			calcul_a07prevecosortieetp (thisComponent);
			calcul_a07prevecosortiemsproratcc (thisComponent);
			calcul_a07prevecosortiemsanneepleinecc (thisComponent);
			calcul_a07prevsurcoutecoetp (thisComponent);
			calcul_a07prevsurcoutecomsanneepleinecc (thisComponent);
			calcul_a07prevsurcoutecomsproratcc	 (thisComponent);

			//CALCULS DONNEES SALARIALES FORECAST
			calcul_a07forecastsurcoutecoetp (thisComponent);
			calcul_a07forecastsurcoutecomsanneepleinecc (thisComponent);
			calcul_a07forecastsurcoutecomsproratcc (thisComponent);			
		});	
	
		//Sur le champ Salaire annuel Réel
	
		this.ui.find('#a07reelsalaireannuelhc').change(function(){
	
			//CALCULS DONNEES SALARIALES REELLES
			calcul_a07reelmsanneepleinecc (thisComponent);
			calcul_a07reelmsproratcc (thisComponent);
			calcul_a07reelecosortieetp (thisComponent);
			calcul_a07reelecosortiemsproratcc (thisComponent);
			calcul_a07reelecosortiemsanneepleinecc (thisComponent);
			calcul_a07reelsurcoutecoetp (thisComponent);
			calcul_a07reelsurcoutecomsanneepleinecc (thisComponent);
			calcul_a07reelsurcoutecomsproratcc (thisComponent);

			//CALCULS DONNEES SALARIALES FORECAST
			calcul_a07forecastsurcoutecoetp (thisComponent);
			calcul_a07forecastsurcoutecomsanneepleinecc (thisComponent);
			calcul_a07forecastsurcoutecomsproratcc (thisComponent);
		});	
		
		//Sur le champ Date entrée Réelle
	
		this.ui.find('#a07reeldatearrivee').change(function(){
	
			calcul_a07anneepap(thisComponent);
			calcul_a07trimestre(thisComponent);
			calcul_a07forecastdatearrivee(thisComponent);
		
			calcul_a07reelvariationeffectif(thisComponent);
			calcul_a07reeletp(thisComponent);
			calcul_a07forecastvariationeffectif(thisComponent);	
			calcul_a07forecastetp(thisComponent);

			//CALCULS DONNEES SALARIALES REELLES
			calcul_a07reelmsanneepleinecc (thisComponent);
			calcul_a07reelmsproratcc (thisComponent);
			calcul_a07reelecosortieetp (thisComponent);
			calcul_a07reelecosortiemsproratcc (thisComponent);
			calcul_a07reelecosortiemsanneepleinecc (thisComponent);
			calcul_a07reelsurcoutecoetp (thisComponent);
			calcul_a07reelsurcoutecomsanneepleinecc (thisComponent);
			calcul_a07reelsurcoutecomsproratcc (thisComponent);

			//CALCULS DONNEES SALARIALES FORECAST
			calcul_a07forecastsurcoutecoetp (thisComponent);
			calcul_a07forecastsurcoutecomsanneepleinecc (thisComponent);
			calcul_a07forecastsurcoutecomsproratcc (thisComponent);
		});	
	
	
	
		//Sur le champ Date sortie Réelle
	
		this.ui.find('#a07reeldatedepart').change(function(){
		
			calcul_a07anneepap(thisComponent);
			calcul_a07trimestre(thisComponent);
			calcul_a07forecastdatedepart(thisComponent);

			calcul_a07reeletp(thisComponent);
			calcul_a07forecastetp(thisComponent);
		
			calcul_a07reelvariationeffectif(thisComponent);		
			calcul_a07forecastvariationeffectif(thisComponent);		


			//CALCULS DONNEES SALARIALES REELLES
			calcul_a07reelmsanneepleinecc (thisComponent);
			calcul_a07reelmsproratcc (thisComponent);
			calcul_a07reelecosortieetp	 (thisComponent);
			calcul_a07reelecosortiemsproratcc (thisComponent);
			calcul_a07reelecosortiemsanneepleinecc (thisComponent);
			calcul_a07reelsurcoutecoetp (thisComponent);
			calcul_a07reelsurcoutecomsanneepleinecc (thisComponent);
			calcul_a07reelsurcoutecomsproratcc (thisComponent);

			//CALCULS DONNEES SALARIALES FORECAST
			calcul_a07forecastsurcoutecoetp (thisComponent);
			calcul_a07forecastsurcoutecomsanneepleinecc (thisComponent);
			calcul_a07forecastsurcoutecomsproratcc (thisComponent);		
		
		});		

	
		/*********************************************************************/
		/*********************************************************************/
		// 					  FONCTIONS POUR LE VISUEL						 //
		/*********************************************************************/
		/*********************************************************************/

		//CLEAN LES DATES DE SORTIE SI TYPE DE CONTRAT = CDI ou CDIOD (CTT001 ou CTT006)
		function CleanDatesSortie(thisComponent){
			if(thisComponent.getValue('a07mouvement')==='MVTPAP001' || thisComponent.getValue('a07mouvement')==='MVTPAP003'){
				if(thisComponent.getValue('a07typecontrat')==='CTT001' || thisComponent.getValue('a07typecontrat')==='CTT006'){
					thisComponent.setValue('a07forecastdatearrivee','00/00/0000');
					thisComponent.setValue('a07prevdatedepart','00/00/0000');
					thisComponent.setValue('a07reeldatedepart','00/00/0000');	
					thisComponent.setValue('a07forecastdatedepart','00/00/0000');
				}
			}
		}

		//AFFICHER/MASQUER LES CHAMPS SALARIE REMPLACE ET TYPE DE RECRUTEMENT SELON MOTIF PAP ENTREE/SORTIE, REMPLACEMENT, ETC.
		function verifMotifPAP(thisComponent){
		var motifPAP = thisComponent.ui.find('[name=d00motifrecrutement]').val();

			if(thisComponent.getValue('a07motif')==='MOTPAP001'){
				thisComponent.ui.find("#COMPLEMENT_a07salarieremplace").closest('.form-group').hide();
				thisComponent.ui.find("#COMPLEMENT_a07typerecrutement").closest('.form-group').show();		
				thisComponent.ui.find("COMPLEMENT_a07originerecrutement").closest('.form-group').show();
			}
		
			if(thisComponent.getValue('a07motif')==='MOTPAP002'){
				thisComponent.ui.find("#COMPLEMENT_a07salarieremplace").closest('.form-group').show();
				thisComponent.ui.find("#COMPLEMENT_a07typerecrutement").closest('.form-group').show();		
				thisComponent.ui.find("COMPLEMENT_a07originerecrutement").closest('.form-group').show();
			}
		
			if(thisComponent.getValue('a07motif')==='MOTPAP003' || thisComponent.getValue('a07motif')==='MOTPAP004' || thisComponent.getValue('a07motif')==='MOTPAP005' ){
				thisComponent.ui.find("#COMPLEMENT_a07salarieremplace").closest('.form-group').hide();
				thisComponent.ui.find("#COMPLEMENT_a07typerecrutement").closest('.form-group').hide();
				thisComponent.ui.find("COMPLEMENT_a07originerecrutement").closest('.form-group').hide();
			}
		}

		//AFFICHER/MASQUER LES CHAMPS DATE DEPART ET ORIGINE RECRUTEMENT SI ENTREE/SORTIE
		function verifMouvement(thisComponent){
			var vMotif = thisComponent.getValue('a07motif');
		
			if (thisComponent.getValue('a07mouvement')!=='MVTPAP001') {					
				// JR le 08/03/2017 vider la connexion a07typerecrutement si sortie
				thisComponent.setConnectionValue("a07typerecrutement", "", ""); // setConnectionValue(champConnexion,tableConnectée,clé)
				thisComponent.ui.find("#a07typerecrutement").closest('.form-group').hide();
			}

			if(thisComponent.getValue('a07mouvement')==='MVTPAP001' || thisComponent.getValue('a07mouvement')==='MVTPAP003'){
			
				// JR le 08/03/2017 cas d'un mouvement MVTPAP001 (entrée)
				if(thisComponent.getValue('a07mouvement')==='MVTPAP001'){				
					// Prérempli le Motif avec Création de poste (MOTPAP001) et le type de recrutment avec "Recrutement externe" (RECRUT001)
					if (vMotif === '' || vMotif === 'MOTPAP003' || vMotif === 'MOTPAP004' || vMotif === 'MOTPAP005'){
						thisComponent.setConnectionValue('a07motif', 'r14motifpap', 'MOTPAP001');
						thisComponent.setConnectionValue('a07typerecrutement', 'r20typerecrutement', 'RECRUT001');
					}
					
					// afficher a07typerecrutement
					thisComponent.ui.find("#a07typerecrutement").closest('.form-group').show();
					// on remplit le statut PAP avec "Recrutement en cours" (STAPAP001)
					thisComponent.setConnectionValue("a07statutpap", "r16statutpap", "STAPAP001");
				}
				
				// JR le 08/03/2017 cas d'un mouvement MVTPAP003 (Refacturation Entrée)
				if(thisComponent.getValue('a07mouvement')==='MVTPAP003'){
					// Prérempli le Motif avec Refacturation entrée (MOTPAP004)
					if (vMotif === '' || vMotif === 'MOTPAP001' || vMotif === 'MOTPAP002' || vMotif === 'MOTPAP003' || vMotif === 'MOTPAP005'){
						thisComponent.setConnectionValue("a07motif", "r14motifpap", "MOTPAP004");
					}
					// on cache et on vide la connexion a07typerecrutement			
					thisComponent.setConnectionValue("a07typerecrutement", "", ""); // setConnectionValue(champConnexion,tableConnectée,clé)
					thisComponent.ui.find("#a07typerecrutement").closest('.form-group').hide();
					// on remplit le statut PAP avec "Refacturation" (STAPAP005)
					thisComponent.setConnectionValue("a07statutpap", "r16statutpap", "STAPAP005");
				}
				
				if (thisComponent.getValue('a07typecontrat')==='CTT001' || thisComponent.getValue('a07typecontrat')==='CTT006') {
					thisComponent.ui.find("#COMPLEMENT_a07originerecrutement").closest('.form-group').show();
					thisComponent.ui.find("#a07prevdatedepart").closest('.form-group').hide();
					thisComponent.ui.find("#a07reeldatedepart").closest('.form-group').hide();
					thisComponent.ui.find("#a07forecastdatedepart").closest('.form-group').hide();
				}
	
				if (thisComponent.getValue('a07typecontrat')!='CTT001' && thisComponent.getValue('a07typecontrat')!='CTT006') {		
					thisComponent.ui.find("#COMPLEMENT_a07originerecrutement").closest('.form-group').show();
					thisComponent.ui.find("#a07prevdatedepart").closest('.form-group').show();		
					thisComponent.ui.find("#a07reeldatedepart").closest('.form-group').show();	
					thisComponent.ui.find("#a07forecastdatedepart").closest('.form-group').show();			
				}
			}
		
			if(thisComponent.getValue('a07mouvement')==='MVTPAP002' || thisComponent.getValue('a07mouvement')==='MVTPAP004'){
				// JR le 08/03/2017 Uniquement si mouvement "Sortie"
				if(thisComponent.getValue('a07mouvement')==='MVTPAP002'){
					// Prérempli le Motif avec Sortie (MOTPAP003)
					if (vMotif === '' || vMotif === 'MOTPAP001' || vMotif === 'MOTPAP002' || vMotif === 'MOTPAP004' || vMotif === 'MOTPAP005'){
						thisComponent.setConnectionValue("a07motif", "r14motifpap", "MOTPAP003");
					}
					// on remplit le statut PAP avec "Sortie" (STAPAP004)
					thisComponent.setConnectionValue("a07statutpap", "r16statutpap", "STAPAP004");
				}
				// JR le 08/03/2017 Uniquement si mouvement "Refacturation Sortie"
				if(thisComponent.getValue('a07mouvement')==='MVTPAP004'){
					// Prérempli le Motif avec Refacturation Sortie (MOTPAP005)
					if (vMotif === '' || vMotif === 'MOTPAP001' || vMotif === 'MOTPAP002' || vMotif === 'MOTPAP003' || vMotif === 'MOTPAP004'){
						thisComponent.setConnectionValue("a07motif", "r14motifpap", "MOTPAP005");
					}
					// On remplit le statut PAP avec "Refacturation" (STAPAP005)
					thisComponent.setConnectionValue("a07statutpap", "r16statutpap", "STAPAP005");
				}
				thisComponent.ui.find("#COMPLEMENT_a07originerecrutement").closest('.form-group').hide();
				thisComponent.ui.find("#a07prevdatedepart").closest('.form-group').show();		
				thisComponent.ui.find("#a07reeldatedepart").closest('.form-group').show();	
				thisComponent.ui.find("#a07forecastdatedepart").closest('.form-group').show();
			}
		}

		/*********************************************************************/
		/*********************************************************************/
		// 					          CALCULS  		    				     //
		/*********************************************************************/
		/*********************************************************************/

		//CALCUL DE LA DATE D'ARRIVEE FORECAST	
		function calcul_a07forecastdatearrivee (thisComponent){
	
		var vDateEntreePrev = thisComponent.getValue('a07prevdatearrivee');
		var vDateEntreeReelle = thisComponent.getValue('a07reeldatearrivee');
		var vDateEntreeForecast = thisComponent.getValue('a07forecastdatearrivee');
	
			if (!vDateEntreeReelle || vDateEntreeReelle === '00/00/0000') {
			thisComponent.setValue('a07forecastdatearrivee',vDateEntreePrev);
			}else{
			thisComponent.setValue('a07forecastdatearrivee',vDateEntreeReelle);
			}
		}
	
		//CALCUL DE LA DATE DE DEPART FORECAST	
		function calcul_a07forecastdatedepart (thisComponent){
	
		var vDateSortiePrev = thisComponent.getValue('a07prevdatedepart');
		var vDateSortieReelle = thisComponent.getValue('a07reeldatedepart');
		var vDateSortieForecast = thisComponent.getValue('a07forecastdatedepart');
	
			if  (!vDateSortieReelle || vDateSortieReelle === '00/00/0000') {
			thisComponent.setValue('a07forecastdatedepart',vDateSortiePrev);
			}else{
			thisComponent.setValue('a07forecastdatedepart',vDateSortieReelle);
			}
		}
	
		//CALCUL DE L'ANNEE PAP	
		function calcul_a07anneepap (thisComponent){
	
		var vDateSortieForecast = thisComponent.getValue('a07forecastdatedepart');
		var vDateEntreeForecast = thisComponent.getValue('a07forecastdatearrivee');
		var vAnneeDateSortieForecast = vDateSortieForecast.substr(vDateSortieForecast.length - 4);
		var vAnneeDateEntreeForecast = vDateEntreeForecast.substr(vDateEntreeForecast.length - 4);

			if(thisComponent.getValue('a07mouvement')==='MVTPAP001' || thisComponent.getValue('a07mouvement')==='MVTPAP003'){
				thisComponent.setValue('a07anneepap',vAnneeDateEntreeForecast);
			}

			if(thisComponent.getValue('a07mouvement')==='MVTPAP002' || thisComponent.getValue('a07mouvement')==='MVTPAP004'){
				thisComponent.setValue('a07anneepap',vAnneeDateSortieForecast);
			}
		}
	
		//CALCUL DU TRIMESTRE PAP	
		function calcul_a07trimestre (thisComponent){

		var vTrimestrePAP = thisComponent.getValue('a07trimestre');
		var vDateSortieForecast = thisComponent.getValue('a07forecastdatedepart');
		var vDateEntreeForecast = thisComponent.getValue('a07forecastdatearrivee');

		var vMoisDateSortieForecast2 = vDateSortieForecast.substring(3,5);
		var vMoisDateEntreeForecast2 = vDateEntreeForecast.substring(3,5);
	
			if(thisComponent.getValue('a07mouvement')==='MVTPAP001' || thisComponent.getValue('a07mouvement')==='MVTPAP003'){
				if (vMoisDateEntreeForecast2 < 4) {
				thisComponent.setValue('a07trimestre','T1');
				}
				if (vMoisDateEntreeForecast2 >3 && vMoisDateEntreeForecast2 < 7) {
				thisComponent.setValue('a07trimestre','T2');
				}
				if (vMoisDateEntreeForecast2 > 6 && vMoisDateEntreeForecast2 < 10) {
				thisComponent.setValue('a07trimestre','T3');
				}
				if (vMoisDateEntreeForecast2 > 9) {
				thisComponent.setValue('a07trimestre','T4');
				}		
			}
	
			if(thisComponent.getValue('a07mouvement')==='MVTPAP002' || thisComponent.getValue('a07mouvement')==='MVTPAP004'){
				if (vMoisDateSortieForecast2 < 4) {
				thisComponent.setValue('a07trimestre','T1');
				}
				if (vMoisDateSortieForecast2 >3 && vMoisDateSortieForecast2 < 7) {
				thisComponent.setValue('a07trimestre','T2');
				}
				if (vMoisDateSortieForecast2 > 6 && vMoisDateSortieForecast2 < 10) {
				thisComponent.setValue('a07trimestre','T3');
				}
				if (vMoisDateSortieForecast2 > 9) {
				thisComponent.setValue('a07trimestre','T4');
				}
			}		
		}	
	
		//CALCUL DE LA VARIATION D'EFFECTIF PREVISIONNELLE
		function calcul_a07prevvariationeffectif (thisComponent){
	
		var vTauxActivite = thisComponent.getValue('a07tauxactivite');
	
			if(thisComponent.getValue('a07mouvement')==='MVTPAP001' || thisComponent.getValue('a07mouvement')==='MVTPAP003'){
	
				thisComponent.setValue('a07prevvariationeffectif', 1* vTauxActivite / 100);
			}
	
			if(thisComponent.getValue('a07mouvement')==='MVTPAP002' || thisComponent.getValue('a07mouvement')==='MVTPAP004'){
	
				thisComponent.setValue('a07prevvariationeffectif', -1* vTauxActivite / 100);
			}	
		}

	
		//CALCUL DE LA VARIATION D'EFFECTIF REELLE	
		function calcul_a07reelvariationeffectif (thisComponent){
	
		var vTauxActivite = thisComponent.getValue('a07tauxactivite');
	
			if(thisComponent.getValue('a07mouvement')==='MVTPAP001' || thisComponent.getValue('a07mouvement')==='MVTPAP003'){
	
				thisComponent.setValue('a07reelvariationeffectif', 1* vTauxActivite / 100);
			}
	
			if(thisComponent.getValue('a07mouvement')==='MVTPAP002' || thisComponent.getValue('a07mouvement')==='MVTPAP004'){
	
				thisComponent.setValue('a07reelvariationeffectif', -1* vTauxActivite / 100);
			}	
		}
	
		//CALCUL DE LA VARIATION D'EFFECTIF FORECAST
		function calcul_a07forecastvariationeffectif (thisComponent){
	
		var vVarEffPrev = thisComponent.getValue('a07prevvariationeffectif');
		var vVarEffReel = thisComponent.getValue('a07reelvariationeffectif');

			if  (!vVarEffReel) {
			thisComponent.setValue('a07forecastvariationeffectif',vVarEffPrev);
			}else{
			thisComponent.setValue('a07forecastvariationeffectif',vVarEffReel);
			}
		}

		// CALCUL DE L'ETP PREVISIONNEL
		function calcul_a07prevetp (thisComponent){
		
		var vDateEntreePrev = thisComponent.getValue('a07prevdatearrivee');
		var vDateSortiePrev = thisComponent.getValue('a07prevdatedepart');	
	
		var vAnneeDateEntreePrev = vDateEntreePrev.substr(vDateEntreePrev.length - 4);	
		var vAnneeDateSortiePrev = vDateSortiePrev.substr(vDateSortiePrev.length - 4);
		var endOfYear = new Date(Number(thisComponent.getValue('a07anneepap'))+'-12-31');
		var startOfYear = new Date(Number(thisComponent.getValue('a07anneepap'))+'-01-01');
		var vTauxActivite = Number(thisComponent.getValue('a07tauxactivite'))/100;

	
		// dates de début et fin reformatées
		var vDateEntreePrev2 = new Date(vDateEntreePrev.substr(6,4)+'-'+ vDateEntreePrev.substr(3,2)+'-'+ vDateEntreePrev.substr(0,2));
		var vDateSortiePrev2 = new Date(vDateSortiePrev.substr(6,4)+'-'+ vDateSortiePrev.substr(3,2)+'-'+ vDateSortiePrev.substr(0,2));

		var DateDiffSansFin = dateDiff(vDateEntreePrev2, endOfYear);
		var DateDiffSansDebut = dateDiff(startOfYear, vDateSortiePrev2);
		var DateDiffDates = dateDiff(vDateEntreePrev2, vDateSortiePrev2);
	
			if(thisComponent.getValue('a07mouvement')==='MVTPAP001' || thisComponent.getValue('a07mouvement')==='MVTPAP003'){		

				if (thisComponent.getValue('a07typecontrat')==='CTT001' || thisComponent.getValue('a07typecontrat')==='CTT006') {
				thisComponent.setValue('a07prevetp', ((Math.max(Number(DateDiffSansFin.day)+1,0)/365) * vTauxActivite));
				}
	
				if (thisComponent.getValue('a07typecontrat')!=='CTT001' && thisComponent.getValue('a07typecontrat')!=='CTT006') {				
					

					console.log(String(vAnneeDateSortiePrev));
					console.log(String(vAnneeDateEntreePrev));
					
					if (!vDateSortiePrev || vDateSortiePrev === '00/00/0000'){
					console.log('1');
						thisComponent.setValue('a07prevetp', ((Math.max(Number(DateDiffSansFin.day)+1,0)/365) * vTauxActivite));
					}
					
					if (vAnneeDateSortiePrev > vAnneeDateEntreePrev) {
					console.log('2');
					thisComponent.setValue('a07prevetp', ((Math.max(Number(DateDiffSansFin.day)+1,0)/365) * vTauxActivite));
					}

					if (vAnneeDateSortiePrev == vAnneeDateEntreePrev) {
					console.log('3');
					thisComponent.setValue('a07prevetp', ((Math.max(Number(DateDiffDates.day)+1,0)/365) * vTauxActivite));
					}
				}
			}
		
			if(thisComponent.getValue('a07mouvement')==='MVTPAP002' || thisComponent.getValue('a07mouvement')==='MVTPAP004'){
				if (!vDateEntreePrev || vDateEntreePrev === '00/00/0000'){	
				thisComponent.setValue('a07prevetp', ((Math.max(Number(DateDiffSansDebut.day)+1,0)/365) * vTauxActivite * -1));	
				}	

				if (vAnneeDateSortiePrev > vAnneeDateEntreePrev) {
				thisComponent.setValue('a07prevetp', ((Math.max(Number(DateDiffSansDebut.day)+1,0)/365) * vTauxActivite* -1));
				}	

				if (vAnneeDateSortiePrev == vAnneeDateEntreePrev) {
				thisComponent.setValue('a07prevetp', ((Math.max(Number(DateDiffDates.day)+1,0)/365) * vTauxActivite * -1));
				}			
			}
		}
	
		//CALCUL DE L'ETP REEL	
		function calcul_a07reeletp (thisComponent){
	
		var vDateEntreeReelle = thisComponent.getValue('a07reeldatearrivee');
		var vDateSortieReelle = thisComponent.getValue('a07reeldatedepart');
	
		var vAnneeDateEntreeReelle = vDateEntreeReelle.substr(vDateEntreeReelle.length - 4);	
		var vAnneeDateSortieReelle = vDateSortieReelle.substr(vDateSortieReelle.length - 4);
		var endOfYear = new Date(Number(thisComponent.getValue('a07anneepap'))+'-12-31');
		var startOfYear = new Date(Number(thisComponent.getValue('a07anneepap'))+'-01-01');
		var vTauxActivite = Number(thisComponent.getValue('a07tauxactivite'))/100;

	
		// dates de début et fin reformatées
		var vDateEntreeReelle2 = new Date(vDateEntreeReelle.substr(6,4)+'-'+ vDateEntreeReelle.substr(3,2)+'-'+ vDateEntreeReelle.substr(0,2));
		var vDateSortieReelle2 = new Date(vDateSortieReelle.substr(6,4)+'-'+ vDateSortieReelle.substr(3,2)+'-'+ vDateSortieReelle.substr(0,2));

		var DateDiffSansFin = dateDiff(vDateEntreeReelle2, endOfYear);
		var DateDiffSansDebut = dateDiff(startOfYear, vDateSortieReelle2);
		var DateDiffDates = dateDiff(vDateEntreeReelle2, vDateSortieReelle2);
	
			if(thisComponent.getValue('a07mouvement')==='MVTPAP001' || thisComponent.getValue('a07mouvement')==='MVTPAP003'){		

				if (thisComponent.getValue('a07typecontrat')==='CTT001' || thisComponent.getValue('a07typecontrat')==='CTT006') {
				thisComponent.setValue('a07reeletp', ((Math.max(Number(DateDiffSansFin.day)+1,0)/365) * vTauxActivite));
				}
	
				if (thisComponent.getValue('a07typecontrat')!=='CTT001' && thisComponent.getValue('a07typecontrat')!=='CTT006') {				
					

					console.log(String(vAnneeDateSortieReelle));
					console.log(String(vAnneeDateEntreeReelle));
					
					if (!vDateSortieReelle || vDateSortieReelle === '00/00/0000'){
					console.log('1');
						thisComponent.setValue('a07reeletp', ((Math.max(Number(DateDiffSansFin.day)+1,0)/365) * vTauxActivite));
					}
					
					if (vAnneeDateSortieReelle > vAnneeDateEntreeReelle) {
					console.log('2');
					//thisComponent.setValue('a07reeletp', ((Math.max(Number(DateDiffSansFin.day)+1,0)/365) * vTauxActivite));
					//console.log(String((Math.max(Number(DateDiffSansFin.day)+1,0)/365) * vTauxActivite));
					}

					if (vAnneeDateSortieReelle == vAnneeDateEntreeReelle) {
					console.log('3');
					thisComponent.setValue('a07reeletp', ((Math.max(Number(DateDiffDates.day)+1,0)/365) * vTauxActivite));
					//console.log(String((Math.max(Number(DateDiffDates.day)+1,0)/365) * vTauxActivite));	

					}
				}
			}
		
			if(thisComponent.getValue('a07mouvement')==='MVTPAP002' || thisComponent.getValue('a07mouvement')==='MVTPAP004'){
				if (!vDateEntreeReelle || vDateEntreeReelle === '00/00/0000'){	
				thisComponent.setValue('a07reeletp', ((Math.max(Number(DateDiffSansDebut.day)+1,0)/365) * vTauxActivite * -1));	
				}	

				if (vAnneeDateSortieReelle > vAnneeDateEntreeReelle) {
				thisComponent.setValue('a07reeletp', ((Math.max(Number(DateDiffSansDebut.day)+1,0)/365) * vTauxActivite* -1));
				}	

				if (vAnneeDateSortieReelle == vAnneeDateEntreeReelle) {
				thisComponent.setValue('a07reeletp', ((Math.max(Number(DateDiffDates.day)+1,0)/365) * vTauxActivite* -1));
				}			
			}
		}
	
	
		//CALCUL DE L'ETP FORECAST	
		function calcul_a07forecastetp (thisComponent){
	
		var vETPPrev = thisComponent.getValue('a07prevetp');
		var vETPReel = thisComponent.getValue('a07reeletp');

			if  (!vETPReel || vETPReel === '0') {
			thisComponent.setValue('a07forecastetp',vETPPrev);
			}else{
			thisComponent.setValue('a07forecastetp',vETPReel);
			}
		}
	
	
	
		//CALCULS DONNEES SALARIALES PREVISIONNELLES
		
		function calcul_a07prevmsanneepleinecc (thisComponent){
		
		var vPrevMSanneepleineHC = thisComponent.getValue('a07prevsalaireannuelhc');
		var vPrevMSAnneePleinCC = Number(vPrevMSanneepleineHC) * Number(vTxCharge);
		
		thisComponent.setValue('a07prevmsanneepleinecc',vPrevMSAnneePleinCC);
		}

		
		function calcul_a07prevmsproratcc (thisComponent){
		
		var vPrevMSanneepleineHC = thisComponent.getValue('a07prevsalaireannuelhc');
		var vETPprev = thisComponent.getValue('a07prevetp');
		var vPrevMSproratCC = Number(vPrevMSanneepleineHC) * Number(vETPprev) * Number(vTxCharge);
		
		thisComponent.setValue('a07prevmsproratcc', vPrevMSproratCC);		
		}

		
		function calcul_a07prevecosortieetp	 (thisComponent){
			
		var vPrevETP = thisComponent.getValue('a07prevetp'); 
		var vPrevEcoSortieETP = vPrevETP - 1;
		
			if(thisComponent.getValue('a07mouvement')==='MVTPAP002' || thisComponent.getValue('a07mouvement')==='MVTPAP004'){
				thisComponent.setValue('a07prevecosortieetp', vPrevEcoSortieETP);	
			}
			
			if(thisComponent.getValue('a07mouvement')==='MVTPAP001' || thisComponent.getValue('a07mouvement')==='MVTPAP003'){	
				thisComponent.setValue('a07prevecosortieetp', 0);	
			}
		}

		
		function calcul_a07prevecosortiemsproratcc (thisComponent){
		
		var vPrevMSanneepleineHC = thisComponent.getValue('a07prevsalaireannuelhc');		
		var vPrevEcoSortieETP =	thisComponent.getValue('a07prevecosortieetp');	
		var vPrevEcoMSproratCC = Number(vPrevMSanneepleineHC) * Number(vPrevEcoSortieETP) * Number(vTxCharge);

			if(thisComponent.getValue('a07mouvement')==='MVTPAP002' || thisComponent.getValue('a07mouvement')==='MVTPAP004'){		
				thisComponent.setValue('a07prevecosortiemsproratcc', vPrevEcoMSproratCC);		
			}
			
			if(thisComponent.getValue('a07mouvement')==='MVTPAP001' || thisComponent.getValue('a07mouvement')==='MVTPAP003'){	
				thisComponent.setValue('a07prevecosortiemsproratcc', 0);	
			}	
		}	

		
		function calcul_a07prevecosortiemsanneepleinecc	 (thisComponent){
		
		var vPrevMSanneepleineHC = thisComponent.getValue('a07prevsalaireannuelhc');		
		var vPrevEcoMSanneepleineCC = Number(vPrevMSanneepleineHC) * Number(vTxCharge);

			if(thisComponent.getValue('a07mouvement')==='MVTPAP002' || thisComponent.getValue('a07mouvement')==='MVTPAP004'){		
				thisComponent.setValue('a07prevecosortiemsanneepleinecc', vPrevEcoMSanneepleineCC);		
			}
			
			if(thisComponent.getValue('a07mouvement')==='MVTPAP001' || thisComponent.getValue('a07mouvement')==='MVTPAP003'){	
				thisComponent.setValue('a07prevecosortiemsanneepleinecc', 0);	
			}
		}

		
		function calcul_a07prevsurcoutecoetp (thisComponent){

		var vPrevEcoSortieETP = thisComponent.getValue('a07prevecosortieetp');
		var vPrevETP = thisComponent.getValue('a07prevetp'); 
		
			if(thisComponent.getValue('a07mouvement')==='MVTPAP002' || thisComponent.getValue('a07mouvement')==='MVTPAP004'){		
				thisComponent.setValue('a07prevsurcoutecoetp', vPrevEcoSortieETP);		
			}
			
			if(thisComponent.getValue('a07mouvement')==='MVTPAP001' || thisComponent.getValue('a07mouvement')==='MVTPAP003'){	
				thisComponent.setValue('a07prevsurcoutecoetp', vPrevETP);	
			}		
		}

		
		function calcul_a07prevsurcoutecomsanneepleinecc (thisComponent){

		var vPrevEcoMSanneepleineCC = thisComponent.getValue('a07prevecosortiemsanneepleinecc');
		var vPrevMSAnneePleinCC = thisComponent.getValue('a07prevmsanneepleinecc'); 
		
			if(thisComponent.getValue('a07mouvement')==='MVTPAP002' || thisComponent.getValue('a07mouvement')==='MVTPAP004'){		
				thisComponent.setValue('a07prevsurcoutecomsanneepleinecc', vPrevEcoMSanneepleineCC);		
			}
			
			if(thisComponent.getValue('a07mouvement')==='MVTPAP001' || thisComponent.getValue('a07mouvement')==='MVTPAP003'){	
				thisComponent.setValue('a07prevsurcoutecomsanneepleinecc', vPrevMSAnneePleinCC);	
			}
		}

		
		function calcul_a07prevsurcoutecomsproratcc	 (thisComponent){

		var vPrevEcoMSproratCC = thisComponent.getValue('a07prevecosortiemsproratcc'); 
		var vPrevMSproratCC = thisComponent.getValue('a07prevmsproratcc');		
		
			if(thisComponent.getValue('a07mouvement')==='MVTPAP002' || thisComponent.getValue('a07mouvement')==='MVTPAP004'){		
				thisComponent.setValue('a07prevsurcoutecomsproratcc', vPrevEcoMSproratCC);		
			}
			
			if(thisComponent.getValue('a07mouvement')==='MVTPAP001' || thisComponent.getValue('a07mouvement')==='MVTPAP003'){	
				thisComponent.setValue('a07prevsurcoutecomsproratcc', vPrevMSproratCC);	
			}
		}

	

		// //CALCULS DONNEES SALARIALES REELLES
		function calcul_a07reelmsanneepleinecc (thisComponent){
		
		var vReelMSanneepleineHC = thisComponent.getValue('a07reelsalaireannuelhc');
		var vReelMSAnneePleinCC = Number(vReelMSanneepleineHC) * Number(TxCharge);
		
		thisComponent.setValue('a07reelmsanneepleinecc',vReelMSAnneePleinCC);
		}

		function calcul_a07reelmsproratcc (thisComponent){

		var vReelMSanneepleineHC = thisComponent.getValue('a07reelsalaireannuelhc');
		var vETPReel = thisComponent.getValue('a07reeletp');
		var vReelMSproratCC = Number(vReelMSanneepleineHC) * Number(vETPReel) * Number(vTxCharge);
		
		thisComponent.setValue('a07reelmsproratcc', vReelMSproratCC);		
		}

		
		function calcul_a07reelecosortieetp	 (thisComponent){
			
		var vreelETP = thisComponent.getValue('a07reeletp'); 
		var vreelEcoSortieETP = vreelETP - 1;
		
			if(thisComponent.getValue('a07mouvement')==='MVTPAP002' || thisComponent.getValue('a07mouvement')==='MVTPAP004'){
				thisComponent.setValue('a07reelecosortieetp', vreelEcoSortieETP);	
			}
			
			if(thisComponent.getValue('a07mouvement')==='MVTPAP001' || thisComponent.getValue('a07mouvement')==='MVTPAP003'){	
				thisComponent.setValue('a07reelecosortieetp', 0);	
			}
		}

		
		function calcul_a07reelecosortiemsproratcc (thisComponent){
		
		var vReelMSanneepleineHC = thisComponent.getValue('a07reelsalaireannuelhc');		
		var vReelEcoSortieETP =	thisComponent.getValue('a07reelecosortieetp');	
		var vReelEcoMSproratCC = Number(vReelMSanneepleineHC) * Number(vReelEcoSortieETP) * Number(vTxCharge);

			if(thisComponent.getValue('a07mouvement')==='MVTPAP002' || thisComponent.getValue('a07mouvement')==='MVTPAP004'){		
				thisComponent.setValue('a07reelecosortiemsproratcc', vReelEcoMSproratCC);		
			}
			
			if(thisComponent.getValue('a07mouvement')==='MVTPAP001' || thisComponent.getValue('a07mouvement')==='MVTPAP003'){	
				thisComponent.setValue('a07reelecosortiemsproratcc', 0);	
			}	
		}	

		
		function calcul_a07reelecosortiemsanneepleinecc	 (thisComponent){
		
		var vReelMSanneepleineHC = thisComponent.getValue('a07reelsalaireannuelhc');		
		var vReelEcoMSanneepleineCC = Number(vReelMSanneepleineHC) * Number(vTxCharge);

			if(thisComponent.getValue('a07mouvement')==='MVTPAP002' || thisComponent.getValue('a07mouvement')==='MVTPAP004'){		
				thisComponent.setValue('a07reelecosortiemsanneepleinecc', vReelEcoMSanneepleineCC);		
			}
			
			if(thisComponent.getValue('a07mouvement')==='MVTPAP001' || thisComponent.getValue('a07mouvement')==='MVTPAP003'){	
				thisComponent.setValue('a07reelecosortiemsanneepleinecc', 0);	
			}
		}

		
		function calcul_a07reelsurcoutecoetp (thisComponent){

		var vReelEcoSortieETP = thisComponent.getValue('a07reelecosortieetp');
		var vReelETP = thisComponent.getValue('a07reeletp'); 
		
			if(thisComponent.getValue('a07mouvement')==='MVTPAP002' || thisComponent.getValue('a07mouvement')==='MVTPAP004'){		
				thisComponent.setValue('a07reelsurcoutecoetp', vReelEcoSortieETP);		
			}
			
			if(thisComponent.getValue('a07mouvement')==='MVTPAP001' || thisComponent.getValue('a07mouvement')==='MVTPAP003'){	
				thisComponent.setValue('a07reelsurcoutecoetp', vReelETP);	
			}		
		}

		
		function calcul_a07reelsurcoutecomsanneepleinecc (thisComponent){

		var vReelEcoMSanneepleineCC = thisComponent.getValue('a07reelecosortiemsanneepleinecc');
		var vReelMSAnneePleinCC = thisComponent.getValue('a07reelmsanneepleinecc'); 
		
			if(thisComponent.getValue('a07mouvement')==='MVTPAP002' || thisComponent.getValue('a07mouvement')==='MVTPAP004'){		
				thisComponent.setValue('a07reelsurcoutecomsanneepleinecc', vReelEcoMSanneepleineCC);		
			}
			
			if(thisComponent.getValue('a07mouvement')==='MVTPAP001' || thisComponent.getValue('a07mouvement')==='MVTPAP003'){	
				thisComponent.setValue('a07reelsurcoutecomsanneepleinecc', vReelMSAnneePleinCC);	
			}
		}

		
		function calcul_a07reelsurcoutecomsproratcc	 (thisComponent){

		var vReelEcoMSproratCC = thisComponent.getValue('a07reelecosortiemsproratcc'); 
		var vReelMSproratCC = thisComponent.getValue('a07reelmsproratcc');		
		
			if(thisComponent.getValue('a07mouvement')==='MVTPAP002' || thisComponent.getValue('a07mouvement')==='MVTPAP004'){		
				thisComponent.setValue('a07reelsurcoutecomsproratcc', vReelEcoMSproratCC);		
			}
			
			if(thisComponent.getValue('a07mouvement')==='MVTPAP001' || thisComponent.getValue('a07mouvement')==='MVTPAP003'){	
				thisComponent.setValue('a07reelsurcoutecomsproratcc', vReelMSproratCC);	
			}
		}

		//CALCULS DONNEES SALARIALES FORECAST	

		function calcul_a07forecastsurcoutecoetp (thisComponent){
		
		var vSurcoutEcoETPPrev = thisComponent.getValue('a07prevsurcoutecoetp');
		var vSurcoutEcoETPReel = thisComponent.getValue('a07reelsurcoutecoetp');

			if  (!vSurcoutEcoETPReel || vSurcoutEcoETPReel === '0') {
			thisComponent.setValue('a07forecastsurcoutecoetp',vSurcoutEcoETPPrev);
			}else{
			thisComponent.setValue('a07forecastsurcoutecoetp',vSurcoutEcoETPReel);
			}
		}

		function calcul_a07forecastsurcoutecomsanneepleinecc (thisComponent){

		var vSurcoutEcoMSanneepleineCCPrev = thisComponent.getValue('a07prevsurcoutecomsanneepleinecc');
		var vSurcoutEcoMSanneepleineCCReel = thisComponent.getValue('a07reelsurcoutecomsanneepleinecc');

			if  (!vSurcoutEcoMSanneepleineCCReel || vSurcoutEcoMSanneepleineCCReel === '0') {
			thisComponent.setValue('a07forecastsurcoutecomsanneepleinecc',vSurcoutEcoMSanneepleineCCPrev);
			}else{
			thisComponent.setValue('a07forecastsurcoutecomsanneepleinecc',vSurcoutEcoMSanneepleineCCReel);
			}
		}

		function calcul_a07forecastsurcoutecomsproratcc (thisComponent){

		var vSurcoutEcoMSproratCCPrev = thisComponent.getValue('a07prevsurcoutecomsproratcc');
		var vSurcoutEcoMSproratCCReel = thisComponent.getValue('a07reelsurcoutecomsproratcc');

			if  (!vSurcoutEcoMSproratCCReel || vSurcoutEcoMSproratCCReel === '0') {
			thisComponent.setValue('a07forecastsurcoutecomsproratcc',vSurcoutEcoMSproratCCPrev);
			}else{
			thisComponent.setValue('a07forecastsurcoutecomsproratcc',vSurcoutEcoMSproratCCReel);
			}
		}
	
		return true;
		}
	
	function onSave_a07postesbudgetaires (){

	var thisComponent = this;

		if (this.isNew()) {
		
			if ( this.getValue('cle') == "" ) // indispensable sinon boucle récursive sur update()
			{
				$.get("template_auto/a07postesbudgetaires/a07postesbudgetaires.php?mode=getKey")
				.done(function(cle) 
				{
					thisComponent.setValue('cle', cle);
					thisComponent.saveItem(false);
				})
				.fail(gopaas.dialog.ajaxFail);
				return false; 
			}
		}
		

		return true;
	}