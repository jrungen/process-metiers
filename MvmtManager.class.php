<?php


/**
 * @author YSA
* Gestionaire de création des mouvments et tâches.
*/
class MvmtManager {
	private $_source;
	private $_action;
	private $_mvmtParent;
	private $_materielInformatique;
	private $_bureau;
	private $_adresseMessagerie;
	
	public function MvmtManager ($mvmt){
		$this->_mvmtParent = $mvmt;
		$candidat = $mvmt->get_personnePhysique()->get_candidat();
		
		if (is_null($candidat)) {
			$this->_source = 'Persone physique'; // personne physique
		}else{
			$this->_source = 'Candidat'; // candidat seletioné
		}
		
		if (!$this->_mvmtParent->get_personnePhysique()->get_materielInformatique()){
			$this->_materielInformatique=1;
		}else{
			$this->_materielInformatique=0;
		}
		if (!$this->_mvmtParent->get_personnePhysique()->get_bureau()){
			$this->_bureau=1;
		}else{
			$this->_bureau=0;
		}
		if (!$this->_mvmtParent->get_personnePhysique()->get_adresseMessagerie()){
			$this->_adresseMessagerie=1;
		}else{
			$this->_adresseMessagerie=0;
		}
		
		$this->load_rules();
	}
	
	private function load_rules() {
		try{
			//TODO L'ordre des action doit être gérer avec une colonne ordre.
			$query = "SELECT action 
					FROM regle_mouvement 
					where source='".$this->_source."' 
					and mouvement='".$this->_mvmtParent->get_typeMouvement()."' and detail_mouvement='".$this->_mvmtParent->get_detailMouvement()."' 
					and role='".$this->_mvmtParent->get_roleTiers()."'
					and typecontrat='".$this->_mvmtParent->get_personnePhysique()->get_typeContrat()."'
					and materielinformatique='".$this->_materielInformatique."'
					and bureau='".$this->_bureau."'
					and adresse_messagerie='".$this->_adresseMessagerie."'
					order by ordre ;
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
		
		foreach ($this->_data_regles as $regle){
			echo '\naction à exécuter : '.$regle->action;
			switch ($regle->action){
				case(ActionMvmt::DRH_AVENANT):
					// Implicite car mvmt créer depuis la PP avec le bouton générer mouvements.
					break;
				case(ActionMvmt::DRI_AVENANT):
					break;
				case(ActionMvmt::DSI_ENTREE):
					break;
				case(ActionMvmt::DRH_SORTIE):
					echo 'action='.ActionMvmt::DRI_SORTIE;
					$mvmtDrhSortie = new MvmtDRH($this->_mvmtParent->get_personnePhysique(),TypeMvmt::DEPART);
					$mvmtDrhSortie->create();
					break;
				case(ActionMvmt::DRI_ENTREE):
					$mvmtDriSortie = new MvmtDRI($this->_mvmtParent,TypeMvmt::DEPART);
					$mvmtDriSortie->create();
					break;
				case(ActionMvmt::DSI_SORTIE):
					echo 'action='.ActionMvmt::DSI_SORTIE;
					$mvmtDsiSortie = new MvmtDSI($this->_mvmtParent,TypeMvmt::DEPART);
					$mvmtDsiSortie->create();
					break;
				case(ActionMvmt::DRI_ENTREE):
					$mvmtDriEntree = new MvmtDRI($this->_mvmtParent,TypeMvmt::ARRIVEE);
					$mvmtDriEntree->create();
					break;
				case(ActionMvmt::DSI_ENTREE):
					$mvmtDsiEntree = new MvmtDSI($this->_mvmtParent,TypeMvmt::ARRIVEE);
					$mvmtDsiEntree->create();
					break;
				case(ActionMvmt::PAP_VALID_RECRUTEMENT):
					//TODO depuis mvmtDRH, récupérer date d'arrivée réelle et/ou date depart réelle, salaire réel et prime réelle.
					//		déjà codé en js sur la maj d'un mvmtDRH
					$this->_mvmtParent->get_personnePhysique()->valideRecrutementPAP();
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
				case(ActionMvmt::DRH_SORTIE_PROLONGATION):
					break;
				case(ActionMvmt::DRI_SORTIE_PROLONGATION):
					break;
				case(ActionMvmt::DSI_SORTIE_PROLONGATION):
					break;
				case(ActionMvmt::PAP_SORTIE_PROLONGATION):
					break;
				case(ActionMvmt::DRH_MAIL):
					//TODO pour CDD->CDI
					break;
				case(ActionMvmt::DRI_MAIL):
					//TODO
					break;
				case(ActionMvmt::DSI_MAIL):
					//TODO
					break;
				case(ActionMvmt::PAP_MAIL):
					break;
			}
		}
	}
}

?>