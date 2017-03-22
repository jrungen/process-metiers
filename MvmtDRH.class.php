<?php

/**
 * @author YSA
 * Classe représentant les mouvements DRH qui pointent anormalement sur la table a05typemouvement.
 */
class MvmtDRH extends Mvmt{
	
	private $_cochSituationCourant;
	private $_rttMonetises;
	
	public function get_cochSituationCourant(){
		return $this->_cochSituationCourant;
	}
	
	public function set_cochSituationCourant($_cochSituationCourant){
		$this->_cochSituationCourant = $_cochSituationCourant;
	}
	
	public function get_rttMonetises(){
		return $this->_rttMonetises;
	}
	
	public function set_rttMonetises($_rttMonetises){
		$this->_rttMonetises = $_rttMonetises;
	}
	
	function MvmtDRH($personnePhysique, $typeMvmt){
		$this->_cle = MvmtDRH::generateKey();
		$this->_personnePhysique = $personnePhysique;
		
		//Source Fiche Personne Physique.
		if (is_null($personnePhysique->get_candidat())) {
			//TODO
		}
		else{ // Creation depuis candidat.
			$this->_dateEffet = $this->_personnePhysique->get_candidat()->get_dateDebutContrat();
			$this->_societes = $this->_personnePhysique->get_candidat()->get_societes();
			$this->_typeContrat = $this->_personnePhysique->get_candidat()->get_typecontratGRE();
			$this->_rttMonetises = $this->_personnePhysique->get_candidat()->get_nbJoursRttMonetisess();
			$this->_detailMouvement = $this->_personnePhysique->get_candidat()->get_detailMouvement();
		}
		$this->_cochSituationCourant = 'Oui';
		$this->_typeMouvement = $typeMvmt;
		
		$this->_role = $this->_personnePhysique->get_roleTiers();
	}
	
	public static function generateKey(){
		
		$getLastKey = Script::$db->prepare("SELECT cle FROM a05typemouvement ORDER BY cle DESC LIMIT 1");
		$getLastKey->execute();
		$lastKey = $getLastKey->fetchColumn();
		$getLastKey->closeCursor();
	
		if($lastKey === FALSE)
		{
			$lastNumber = "MVT0000001";
		}
	
		$lastNumber = (int) substr($lastKey, 3); // retire "MVT", il reste : [compteur] , qui est une valeur numérique
		$lastNumber_entree = $lastNumber+1;
		$lastNumber_sortie = $lastNumber_entree+1;
	
		// N° mvt entrée
		$lastNumber_entree = str_pad($lastNumber_entree,7,'0',STR_PAD_LEFT);
		return "MVT".$lastNumber_entree;
	}
	
	
	/**
	 *  Création mouvement DRH en base.
	 */
	public function create(){
		try{
			$query = "INSERT INTO
						a05typemouvement
					(cle, a05dateeffet, a05cochsituationcourant, a00personnephysique, r03typemouvement, r00societes, r04roletiers, creation_par, date_creation, heure_creation, modification_par, date_modification, heure_modification, a05typecontrat, a05rttmonetises)
					values
					('".$this->_cle."', '".$this->_dateEffet."', 'Oui', '".$this->_personnePhysique->get_cle()."', '".$this->_typeMouvement."', '".$this->_societes."', '".$this->_role."', 'candidat', CURDATE(),CURTIME(), 'candidat', CURDATE(), CURTIME(), '".$this->_typeContrat."', '".$this->_rttMonetises."');";
		
			// on va chercher tous les enregistrements de la requ?te
			$result=Script::$db->prepare($query);
			$result->execute();
			
		}
		catch(PDOException  $e){
			$errMsg = $e->getMessage();
			echo $errMsg;
		}
		
		//Execustion du gestionaire des règles pour créer les mouvements DRI/DSI après la création du mouvement DRH.
		$this->postCreate();
	}
	
	
	private function preCreate(){
	}
	
	private function postCreate(){
		MvmtManager::getInstance($this)->listActions();
	}
}

?>