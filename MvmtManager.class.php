<?php


/**
 * @author YSA
* Gestionaire de création des mouvments et tâches.
*/
class MvmtManager {
	private $_source;
	private $_typeMouvement;
	private $_detailMouvement;
	private $_action;
	private $_roleTiers;
	private $_typeContrat;
	private $_materielInformatique;
	private $_bureau;
	private $_adresseMessagerie;
	private $_data_regles;
	
	public function MvmtManager ($mvmt){
		$pp = $mvmt->get_personnePhysique();
		$candidat = $pp->get_candidat();
		
		if (is_null($candidat)) {
			$this->_source = 'Persone physique'; // personne physique
		}else{
			$this->_source = 'Candidat'; // candidat seletioné
		}
		$this->_typeMouvement = $mvmt->get_typeMouvement();
		$this->_detailMouvement = $mvmt->get_detailMouvement();
		$this->_roleTiers = $mvmt->get_roleTiers();
		$this->_typeContrat = $mvmt->get_personnePhysique()->get_typeContrat();
		$this->_materielInformatique = $mvmt->get_personnePhysique()->get_materielInformatique();
		$this->_bureau = $mvmt->get_personnePhysique()->get_bureau();
		$this->_adresseMessagerie = $mvmt->get_personnePhysique()->get_adresseMessagerie();
		$this->load_rules();
	}
	
	private function load_rules() {
		try{
	
			$query = "SELECT action 
					FROM regle_mouvement 
					where source='".$this->_source."' 
					and mouvement='".$this->_typeMouvement."' and detail_mouvement='".$this->_detailMouvement."' 
					and role='".$this->_roleTiers."'
					and typecontrat='".$this->_typeContrat."'
					and materielinformatique='".$this->_materielInformatique."'
					and bureau='".$this->_bureau."'
					and adresse_messagerie='".$this->_adresseMessagerie."';
					";
			
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
	
	public function listActions(){
		foreach ($this->_data_regles as $regle){
			echo '\naction à exécuter : '.$regle->action;
		}
	}
}

?>