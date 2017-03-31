<?php

/**
 * @author YSA
 * Classe abstraite parente de toutes les classe mouvements.
 */
abstract class Mvmt {
	
	protected $_cle;
	protected $_dateEffet;
	protected $_personnePhysique;
	protected $_roleTiers;
	protected $_typeMouvement;
	protected $_detailMouvement;
	protected $_societes;
	protected $_mvmtParent;
	protected $_direction;
	protected $_nomManager;
	protected $_personneRemplacee;
	protected $_dar;
	
	public function get_dar(){
		return $this->_dar;
	}
	
	public function set_dar($dar){
		$this->_dar = $dar;
	}
	
	public function get_nomManager(){
		return $this->_nomManager;
	}
	
	public function set_nomManager($_nomManager){
		$this->_nomManager = $_nomManager;
	}
	
	public function get_personneRemplacee(){
		return $this->_personneRemplacee;
	}
	
	public function set_personneRemplacee($_personneRemplacee){
		$this->_personneRemplacee = $_personneRemplacee;
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
	
	public function get_personnePhysique(){
		return $this->_personnePhysique;
	}
	
	public function set_personnePhysique($_personnePhysique){
		$this->_personnePhysique = $_personnePhysique;
	}
	
	public function get_typeMouvement(){
		return $this->_typeMouvement;
	}
	
	public function get_detailMouvement(){
		return $this->_detailMouvement;
	}
	
	public function set_typeMouvement($_typeMouvement){
		$this->_typeMouvement = $_typeMouvement;
	}
	
	public function set_detailMouvement($_typeMouvement){
		$this->_detailMouvement = $_typeMouvement;
	}
	
	public function get_societes(){
		return $this->_societes;
	}
	
	public function set_societes($_societes){
		$this->_societes = $_societes;
	}
	
	public function get_roleTiers(){
		return $this->_roleTiers;
	}
	
	public function set_roleTiers($_roleTiers){
		$this->_roleTiers = $_roleTiers;
	}
	
	/**
	 * Création des tâches du mouvement selon le rôle de la pp et le type de mouvement.
	 */
	protected function createTaches(){
		TacheHelper::createTaches($this->personnePhysique, $this->_typeMouvement);
	}
	
	/**
	 *  Création mouvement en base.
	 */
	abstract protected function create();
	
	/**
	 *  Listener exécuté avant création en base.
	 */
	abstract protected function preCreate();
	
	/**
	 *  *  Listener exécuté après création en base.
	 */
	abstract protected function postCreate();
	
}

?>