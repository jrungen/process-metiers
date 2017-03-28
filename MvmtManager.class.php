<?php


/**
 * @author YSA
* Gestionaire de création des mouvments et tâches.
*/
class MvmtManager {
	private $_source;
	private $_action;
	private $_mvmtParent;
	
	public function MvmtManager ($mvmt){
		$this->_mvmtParent = $mvmt;
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
	
			$query = "SELECT action 
					FROM regle_mouvement 
					where source='".$this->_source."' 
					and mouvement='".$this->_mvmtParent->get_typeMouvement()."' and detail_mouvement='".$this->_mvmtParent->get_detailMouvement()."' 
					and role='".$this->_mvmtParent->get_roleTiers()."'
					and typecontrat='".$this->_mvmtParent->get_personnePhysique()->get_typeContrat()."'
					and materielinformatique='".$this->_mvmtParent->get_personnePhysique()->get_materielInformatique()."'
					and bureau='".$this->_mvmtParent->get_personnePhysique()->get_bureau()."'
					and adresse_messagerie='".$this->_mvmtParent->get_personnePhysique()->get_adresseMessagerie()."';
					";
			
			echo 'conditions='.$query;
			
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
		if (!is_null($this->_mvmtParent->get_personnePhysique())){
			$this->_mvmtParent->get_personnePhysique()->valideRecrutementPAP();
		}
		
		foreach ($this->_data_regles as $regle){
			echo '\naction à exécuter : '.$regle->action;
			switch ($regle->action){
				case(ActionMvmt::AVENANT_DRH):
					break;
				case(ActionMvmt::AVENANT_DRI):
					break;
				case(ActionMvmt::AVENANT_DSI):
					break;
				case(ActionMvmt::SORTIE_DRH):
					$mvmtDrhSortie = new MvmtDRH($this->_mvmtParent->get_personnePhysique(),TypeMvmt::DEPART);
					$mvmtDrhSortie->create();
					break;
				case(ActionMvmt::SORTIE_DRI):
					$mvmtDriSortie = new MvmtDRI($this->_mvmtParent,TypeMvmt::DEPART);
					$mvmtDriSortie->create();
					break;
				case(ActionMvmt::SORTIE_DSI):
					$mvmtDsiSortie = new MvmtDSI($this->_mvmtParent,TypeMvmt::DEPART);
					$mvmtDsiSortie->create();
					break;
				case(ActionMvmt::SORTIE_PAP):
					//TODO
					break;
				case(ActionMvmt::ENTREE_DRH):
					// Déjà crée directement après la création PP.
					break;
				case(ActionMvmt::ENTREE_DRI):
					$mvmtDriEntree = new MvmtDRI($this->_mvmtParent,TypeMvmt::ARRIVEE);
					$mvmtDriEntree->create();
					break;
				case(ActionMvmt::ENTREE_DSI):
					$mvmtDsiEntree = new MvmtDSI($this->_mvmtParent,TypeMvmt::ARRIVEE);
					$mvmtDsiEntree->create();
					break;
				case(ActionMvmt::ENTREE_PAP):
					$this->_mvmtParent->get_personnePhysique()->valideRecrutementPAP();
					break;
				case(ActionMvmt::ENTREE_PROLONGATION_DRH):
					break;
				case(ActionMvmt::ENTREE_PROLONGATION_DRI):
					break;
				case(ActionMvmt::ENTREE_PROLONGATION_DSI):
					break;
				case(ActionMvmt::ENTREE_PROLONGATION_PAP):
					break;
				case(ActionMvmt::ENTREE_REMPLACEMENT_PAP):
					break;
				case(ActionMvmt::AJOUT_PP):
					// Implicite car manager appellé depuis le post create de la PP.
					break;
				case(ActionMvmt::MAIL_DRH):
					//TODO pour CDD
					break;
				case(ActionMvmt::MAIL_DRI):
					//TODO pour CDD
					break;
				case(ActionMvmt::MAIL_DSI):
					//TODO pour CDD
					break;
				case(ActionMvmt::MAIL_PAP):
					break;
				case(ActionMvmt::MAIL_CDD_CDI_DRH):
					break;
				case(ActionMvmt::SORTIE_PROLONGATION_DRH):
					break;
				case(ActionMvmt::SORTIE_PROLONGATION_DRI):
					break;
				case(ActionMvmt::SORTIE_PROLONGATION_DSI):
					break;
				case(ActionMvmt::SORTIE_PROLONGATION_PAP):
					break;
			}
		}
	}
}

?>