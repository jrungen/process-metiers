<?php

/**
 * @author YSA
 * Classe représentant les mouvements DRH qui pointent anormalement sur la table a05typemouvement.
 */
class MvmtDSI extends Mvmt{
	
	function MvmtDSI($mvmtDrh){
		$this->_mvmtParent = $mvmtDrh;
		$this->_cle = $mvmtDrh->get_cle();
		$this->_personnePhysique =  $mvmtDrh->get_personnePhysique();
		
		//Source Fiche Personne Physique.
		if (is_null($this->_personnePhysique->get_candidat())) {
			//TODO 
		}
		else{ // Creation depuis candidat.
			$this->_societes = $this->_personnePhysique->get_candidat()->get_societes();
			$this->_direction = $this->_personnePhysique->get_candidat()->get_direction();
			$this->_poste = $this->_personnePhysique->get_candidat()->get_poste();
			$this->_typeContrat = $this->_personnePhysique->get_candidat()->get_typecontratGRE();
			$this->_detailMouvement = $this->_personnePhysique->get_candidat()->get_detailMouvement();
			$dar = Dar::findById($this->_personnePhysique->get_candidat()->get_cleDar());
			$this->_nomManager = $dar->get_nomManager();
			$this->_personneRemplacee = $dar->get_personneRemplacee();
		}
		$this->_typeMouvement = $this->_mvmtParent->get_typeMouvement();
	}
	
	/**
	 *  Création mouvement DSI en base.
	 */
	public function create(){
		try{
		
			$query = "INSERT INTO
					a03entreessortiesdsi
						(cle, a00personnephysique, a05typemouvement,
						a03societe, a03direction, a03superieurhierarchique,
						a03poste, a03personneremplacee,
						creation_par, date_creation, heure_creation, modification_par,
						date_modification, heure_modification, a03role )
					values
					('".$this->_cle."_dsi', '".$this->_personnePhysique->get_cle()."', '".$this->_mvmtParent->get_cle()."',
					'".$this->_societes."', '".$this->_direction."', '".$this->_mvmtParent->get_nomManager()."',
					'".$this->_poste."', '".$this->_personneRemplacee."',
					'".$this->_personnePhysique->get_source()."',
					CURDATE(),CURTIME(), 'candidat',
					CURDATE(), CURTIME(), '".$this->_personnePhysique->get_roleTiers()."' )
					";
		
			// on va chercher tous les enregistrements de la requete
			$result=Script::$db->prepare($query);
			$result->execute();
		
		}
		catch(PDOException  $e){
			$errMsg = $e->getMessage();
			echo $errMsg;
		}
		
	}
	
	protected function preCreate(){
	}
	
	protected function postCreate(){
		TacheHelper::createTaches($this->_personnePhysique, $this->_typeMouvement);
	}
}

?>