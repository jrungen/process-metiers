<?php

/**
 * @author YSA
 * Classe abstraite parente de toutes les classe mouvements.
 */
abstract class Mvmt {
	
	private $_cle;
	private $_dateEffet;
	private $_personnePhysique;
	private $_roleTiers;
	private $_typeMouvement;
	private $_detailMouvement;
	private $_societes;
	
	
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