<?php

/**
 * @author YSA
 * Classe représentant les mouvements DRH qui pointent anormalement sur la table a05typemouvement.
 */
class MvmtDRH {
	
	private $_cle;
	private $_dateEffet;
	private $_cochSituationCourant;
	private $_personnePhysique;
	private $_typeMouvement;
	private $_societes;
	private $_roletiers;
	private $_typeContrat;
	private $_rttMonetises;
	
	
	function MvmtDRH($personnePhysique, $typeMvmt){
		$this->_cle = generateKey();
		$this->_dateEffet = $this->_personnePhysique->get_candidat()->get_date_debut_contrat();
		$this->_cochSituationCourant = 'Oui';
		$this->_personnePhysique = $personnePhysique;
		$this->_typeMouvement = $typeMvmt;
		$this->_societes = $this->_personnePhysique->get_candidat()->get_societes();
		$this->_roletiers = $this->_personnePhysique->get_candidat()->get_roles();
		$this->_typeContrat = $this->_personnePhysique->get_candidat()->get_typecontratGRE();
		$this->_rttMonetises = $this->_personnePhysique->get_candidat()->get_nbJoursRttMonetisess();
	}
	
	public function get_cle(){
		return $this->_cle;
	}
	
	public function set_cle($_cle){
		$this->_cle = $_cle;
	}
	
	public function get_dateEffet(){
		return $this->_dateEffet;
	}
	
	public function set_dateEffet($_dateEffet){
		$this->_dateEffet = $_dateEffet;
	}
	
	public function get_cochSituationCourant(){
		return $this->_cochSituationCourant;
	}
	
	public function set_cochSituationCourant($_cochSituationCourant){
		$this->_cochSituationCourant = $_cochSituationCourant;
	}
	
	public function get_personnePhysique(){
		return $this->_personnePhysique;
	}
	
	public function set_personnePhysique($_personnePhysique){
		$this->_personnePhysique = $_personnePhysique;
	}
	
	public function get_typeMouvement(){
		return $this->_typeMouvement;
	}
	
	public function set_typeMouvement($_typeMouvement){
		$this->_typeMouvement = $_typeMouvement;
	}
	
	public function get_societes(){
		return $this->_societes;
	}
	
	public function set_societes($_societes){
		$this->_societes = $_societes;
	}
	
	public function get_roletiers(){
		return $this->_roletiers;
	}
	
	public function set_roletiers($_roletiers){
		$this->_roletiers = $_roletiers;
	}
	
	public function get_typeContrat(){
		return $this->_typeContrat;
	}
	
	public function set_typeContrat($_typeContrat){
		$this->_typeContrat = $_typeContrat;
	}
	
	public function get_rttMonetises(){
		return $this->_rttMonetises;
	}
	
	public function set_rttMonetises($_rttMonetises){
		$this->_rttMonetises = $_rttMonetises;
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
					('".$this->_cle."', '".$this->_dateEffet."', 'Oui', '".$this->_personnePhysique->get_clef()."', '".$this->_typeMouvement."', '".$this->_societes."', '".$this->_roletiers."', 'candidat', CURDATE(),CURTIME(), 'candidat', CURDATE(), CURTIME(), '".$this->_typeContrat."', '".$this->_rttMonetises."');";
		
			// on va chercher tous les enregistrements de la requ?te
			$result=Script::$db->prepare($query);
			$result->execute();
			
		}
		catch(PDOException  $e){
			$errMsg = $e->getMessage();
			echo $errMsg;
		}
		
	}
	
	
}

?>