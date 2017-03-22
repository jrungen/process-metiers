<?php

/**
 * @author YSA
 * Classe représentant les mouvements DRH qui pointent anormalement sur la table a05typemouvement.
 */
class MvmtDSI extends Mvmt{
	private $_mvmtDrh;
	private $_direction;
	private $_poste;
	
	function MvmtDSI($mvmtDrh, $dar){
		$this->_mvmtDrh = $mvmtDrh;
		$this->_cle = $mvmtDrh->get_cle();
		$this->_personnePhysique =  $mvmtDrh->get_personnePhysique();
		
		//Source Fiche Personne Physique.
		if (is_null($personnePhysique->get_candidat())) {
			//TODO 
		}
		else{ // Creation depuis candidat.
			$this->_dateEffet = $this->_personnePhysique->get_candidat()->get_dateDebutContrat();
			$this->_societes = $this->_personnePhysique->get_candidat()->get_societes();
			$this->_direction = $this->_personnePhysique->get_candidat()->get_direction();
			$this->_poste = $this->_personnePhysique->get_candidat()->get_poste();
			$this->_typeContrat = $this->_personnePhysique->get_candidat()->get_typecontratGRE();
			$this->_detailMouvement = $this->_personnePhysique->get_candidat()->get_detailMouvement();
		}
		$this->_cochSituationCourant = 'Oui';
		$this->_typeMouvement = $typeMvmt;
		
		$this->_roletiers = $this->_personnePhysique->get_roleTiers();
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
						date_modification, heure_modification )
					values
					('".$mvmtDrhEntree->get_cle()."_dsi', '".$personnePhysique->get_cle()."', '".$mvmtDrhEntree->get_cle()."',
					'".$candidat->get_societes()."', '".$candidat->get_direction()."', '".$data_demande[0]->dar_superieur."',
					'".$candidat->get_poste()."', '".$data_demande[0]->dar_remplace."',
					'candidat', CURDATE(),CURTIME(), 'candidat',
					CURDATE(), CURTIME() )
					";
		
			// on va chercher tous les enregistrements de la requ?te
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
	}
}

?>