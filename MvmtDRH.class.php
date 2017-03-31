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
	
	function MvmtDRH($personnePhysique, $typeMouvement){
		$this->_cle = MvmtDRH::generateKey();
		$this->_personnePhysique = $personnePhysique;
		
		$this->_cochSituationCourant = 'Oui'; // Dernier mvmt donc dernière situation de la personne.
		$this->_typeMouvement = $typeMouvement;
		$this->_typeContrat = $this->_personnePhysique->get_typeContrat();
		$this->_detailMouvement = $this->_personnePhysique->get_detailMouvement();
		$this->_roleTiers = $this->_personnePhysique->get_roleTiers();
		$this->_nomManager = $this->_personnePhysique->get_nomManager();
		$this->_dateEffet = $this->_personnePhysique->get_dateEffet();
		
		//Source Fiche Personne Physique.
		if (is_null($personnePhysique->get_candidat())) {
			//TODO
		}
		else{
			// Creation depuis candidat.
			$this->_societes = $this->_personnePhysique->get_candidat()->get_societes();
			$this->_rttMonetises = $this->_personnePhysique->get_candidat()->get_nbJoursRttMonetises();
		}
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
					(cle, a05dateeffet, a05cochsituationcourant, a00personnephysique,
					r03typemouvement, r00societes, r04roletiers, creation_par,
					date_creation, heure_creation, modification_par, date_modification,
					heure_modification, a05typecontrat, a05rttmonetises, a05superieurhierarchique,r32detailmouvement)
					values	(
						'".$this->_cle."', '".$this->_dateEffet."',
						'".$this->_cochSituationCourant."',
						'".$this->_personnePhysique->get_cle()."',
						'".$this->_typeMouvement."',
						'".$this->_societes."',
						'".$this->_roleTiers."',
						'".$this->_personnePhysique->get_source()."',
						CURDATE(),CURTIME(),
						'".$this->_personnePhysique->get_source()."', CURDATE(), CURTIME(),
						'".$this->_typeContrat."',
						'".$this->_rttMonetises."',
						'".$this->_nomManager."',
						'".$this->_detailMouvement."'
					);";
			
			echo "\n".'Insert DRH : '.$query."\n";
		
			// on va chercher tous les enregistrements de la requête
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
	
	protected function preCreate(){
	}
	
	protected function postCreate(){
		//TODO Si dernier mvmt de la PP (cas CDD->CDI ou inverse)
		// alors mail paie
		// findLastMvmtByPP
		TacheHelper::createTaches($this);
	}
	
	public function findLastMvmtByPP(){
		
	}
}

?>