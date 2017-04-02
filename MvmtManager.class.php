<?php


/**
 * @author YSA
* Gestionaire de création des mouvments et tâches.
*/
class MvmtManager {
	private $_source;
	private $_action;
	private $_mvmtDrhEntree;
	private $_mvmtDrhSortie;
	private $_materielInformatique;
	private $_bureau;
	private $_adresseMessagerie;
	
	public function MvmtManager ($mvmt){
		$this->_mvmtDrhEntree = $mvmt;
		$candidat = $mvmt->get_personnePhysique()->get_candidat();
		
		if (is_null($candidat)) {
			$this->_source = 'Persone physique'; // personne physique
		}else{
			$this->_source = 'Candidat'; // candidat seletioné
		}
		
		$this->load_rules();
	}
	
	private function load_rules() {
		try{
			//TODO L'ordre des action doit être gérer avec une colonne ordre.
			$query = "SELECT action 
					FROM regle_mouvement 
					where source='".$this->_source."' 
					and mouvement='".$this->_mvmtDrhEntree->get_typeMouvement()."' and detail_mouvement='".$this->_mvmtDrhEntree->get_detailMouvement()."' 
					and role='".$this->_mvmtDrhEntree->get_roleTiers()."'
					and typecontrat='".$this->_mvmtDrhEntree->get_personnePhysique()->get_typeContrat()."'
					and materielinformatique='".$this->_mvmtDrhEntree->get_personnePhysique()->get_materielInformatique()."'
					and bureau='".$this->_mvmtDrhEntree->get_personnePhysique()->get_bureau()."'
					and adresse_messagerie='".$this->_mvmtDrhEntree->get_personnePhysique()->get_adresseMessagerie()."'
					order by ordre ;
					";
			
			echo 'Requête Action = '.$query."\n";
			
			// on va chercher tous les enregistrements de la requête
			$result=Script::$db->prepare($query);
			$result->execute();
	
			// on dit qu'on veut que le résultat soit récupérable sous forme de tableau
			$this->_data_regles = $result->fetchAll((PDO::FETCH_OBJ));
	
			// on ferme le curseur des résultats
			$result->closeCursor();
	
		}
		catch(PDOException  $e){
			$errMsg = $e->getMessage();
			echo $errMsg;
		}
	}
	
	public function executeActions(){
		
		foreach ($this->_data_regles as $regle){
			switch ($regle->action){
				case(ActionMvmt::DRH_AVENANT):
					// Implicite car mvmt créer depuis la PP avec le bouton générer mouvements.
					break;
				case(ActionMvmt::DRI_AVENANT):
					break;
				case(ActionMvmt::DRI_ENTREE):
					echo "\n".'action à exécuter '.ActionMvmt::DRI_ENTREE;
					$mvmtDriEntree = new MvmtDRI($this->_mvmtDrhEntree,TypeMvmt::ARRIVEE);
					$mvmtDriEntree->create();
					break;
				case(ActionMvmt::DSI_ENTREE):
					echo "\n".'action à exécuter '.ActionMvmt::DSI_ENTREE;
					$mvmtDsiEntree = new MvmtDSI($this->_mvmtDrhEntree,TypeMvmt::ARRIVEE);
					$mvmtDsiEntree->create();
					break;
				case(ActionMvmt::DRH_SORTIE):
					echo "\n".'action à exécuter '.ActionMvmt::DRH_SORTIE;
					$this->_mvmtDrhSortie = new MvmtDRH($this->_mvmtDrhEntree->get_personnePhysique(),TypeMvmt::DEPART);
					$this->_mvmtDrhSortie->create();
					break;
				case(ActionMvmt::DRI_SORTIE):
					echo "\n".'action à exécuter '.ActionMvmt::DRI_SORTIE;
					if (!is_null($this->_mvmtDrhSortie)){
						$mvmtDriSortie = new MvmtDRI($this->_mvmtDrhSortie,TypeMvmt::DEPART);
						$mvmtDriSortie->create();
						break;
					}else{
						//TODO Retourner une erreur dans le journal.
					}					
				case(ActionMvmt::DSI_SORTIE):
					echo "\n".'action à exécuter '.ActionMvmt::DSI_SORTIE;
					if (!is_null($this->_mvmtDrhSortie)){
						$mvmtDsiSortie = new MvmtDSI($this->_mvmtDrhSortie,TypeMvmt::DEPART);
						$mvmtDsiSortie->create();
					}else{
						//TODO Retourner une erreur dans le journal.
					}
					break;
				case(ActionMvmt::DRH_SORTIE_PROLONGATION):
					echo "\n".'action à exécuter '.ActionMvmt::DRH_SORTIE_PROLONGATION;
					if (!is_null($this->_mvmtDrhSortie)){
					
					}else{
						//TODO Retourner une erreur dans le journal.
					}
					break;
				case(ActionMvmt::DRI_SORTIE_PROLONGATION):
					echo "\n".'action à exécuter '.ActionMvmt::DRI_SORTIE_PROLONGATION;
					if (!is_null($this->_mvmtDrhSortie)){
					
					}else{
						//TODO Retourner une erreur dans le journal.
					}
					break;
				case(ActionMvmt::DSI_SORTIE_PROLONGATION):
					echo "\n".'action à exécuter '.ActionMvmt::DSI_SORTIE_PROLONGATION;
					if (!is_null($this->_mvmtDrhSortie)){
					
					}else{
						//TODO Retourner une erreur dans le journal.
					}
					break;
				case(ActionMvmt::PAP_SORTIE_PROLONGATION):
					echo "\n".'action à exécuter '.ActionMvmt::PAP_SORTIE_PROLONGATION;
					if (!is_null($this->_mvmtDrhSortie)){
					
					}else{
						//TODO Retourner une erreur dans le journal.
					}
					break;
				case(ActionMvmt::PAP_VALID_RECRUTEMENT):
					echo "\n".'action à exécuter '.ActionMvmt::PAP_VALID_RECRUTEMENT;
					//TODO depuis mvmtDRH, récupérer date d'arrivée réelle et/ou date depart réelle, salaire réel et prime réelle.
					//		déjà codé en js sur la maj d'un mvmtDRH
					$this->_mvmtDrhEntree->get_personnePhysique()->valideRecrutementPAP();
					break;
				case(ActionMvmt::DRH_ENTREE_PROLONGATION):
					// Implicite car mvmt créer depuis la PP avec le bouton générer mouvements.
					break;
				case(ActionMvmt::DRI_ENTREE_PROLONGATION):
					break;
				case(ActionMvmt::DSI_ENTREE_PROLONGATION):
					break;
				case(ActionMvmt::PAP_ENTREE_PROLONGATION):
					break;
				case(ActionMvmt::PAP_ENTREE_REMPLACEMENT):
					break;
				case(ActionMvmt::DRH_MAIL):
					echo "\n".'action à exécuter '.ActionMvmt::DRH_MAIL;
					//TODO pour CDD->CDI
					break;
				case(ActionMvmt::DRI_MAIL):
					echo "\n".'action à exécuter '.ActionMvmt::DRI_MAIL;
					//TODO
					break;
				case(ActionMvmt::DSI_MAIL):
					echo "\n".'action à exécuter '.ActionMvmt::DSI_MAIL;
					//TODO
					break;
				case(ActionMvmt::PAP_MAIL):
					echo "\n".'action à exécuter '.ActionMvmt::PAP_MAIL;
					break;
			}
		}
	}
}

?>