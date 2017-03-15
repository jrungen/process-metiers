<?php

// include 'r03typemouvement_class.php'; // Type mouvement
// include 'r04roletiers_class.php'; // Role tiers

class PersonnePhysique {
	private $_cle;
	private $_roletiers;
	private $_typemouvement;
	private $_civilite;
	private $_nom;
	private $_prenom;
	private $_adresse;
	private $_complement;
	private $_codepostal;
	private $_ville;
	private $_nationalite;
	private $_datenaissance;
	private $_departementnaissance;
	private $_lieunaissance;
	private $_numerosecu;
	private $_clesecu;
	private $_actif;

	private static function generateKey(){ 
		/*
		 * DEBUT - Nouveau numéro
		 */

		$getLastKey = Script::$db->prepare("SELECT cle FROM a00personnephysique ORDER BY cle DESC LIMIT 1");
		$getLastKey->execute();
		$lastKey = $getLastKey->fetchColumn();
		$getLastKey->closeCursor();

		if($lastKey === FALSE)
		{
		$lastNumber = "AC00000001";
		}

		$lastNumber = (int) substr($lastKey, 2); // retire "AC", il reste : [compteur] , qui est une valeur numérique
		$lastNumber = $lastNumber+1;
		$lastNumber = str_pad($lastNumber,8,'0',STR_PAD_LEFT);
		$personnephysique_cle = 'AC'.$lastNumber;
		
		return $personnephysique_cle;
		
	}

	function PersonnePhysique ($candidat){
		/* Générer clé personne physique */
		$getLastKey = Script::$db->prepare("SELECT cle FROM a00personnephysique ORDER BY cle DESC LIMIT 1");
		$getLastKey->execute();
		$lastKey = $getLastKey->fetchColumn();
		$getLastKey->closeCursor();

		if($lastKey === FALSE)
		{
		$lastNumber = "AC00000001";
		}

		$lastNumber = (int) substr($lastKey, 2); // retire "AC", il reste : [compteur] , qui est une valeur numérique
		$lastNumber = $lastNumber+1;
		$lastNumber = str_pad($lastNumber,8,'0',STR_PAD_LEFT);
		$personnephysique_cle = 'AC'.$lastNumber;
		/**********************************/		
		
		$this->_civilite = $candidat->get_civilite();
		$this->_nom = $candidat->get_nom();
		$this->_prenom = $candidat->get_prenom();
		$this->_adresse = $candidat->get_adresse();
		$this->_complement = $candidat->get_complement();
		$this->_codepostal = $candidat->get_codepostal();
		$this->_ville = $candidat->get_ville();
		$this->_nationalite = $candidat->get_nationalite();
		$this->_datenaissance = $candidat->get_date_naissance();
		$this->_departementnaissance = $candidat->get_departementnaissance();
		$this->_lieunaissance = $candidat->get_lieu_naissance();
		$this->_numerosecu = $candidat->get_num_securite_sociale();
		$this->_clesecu = $candidat->get_clesecuritesociale();
		$this->_cle = $personnephysique_cle;
	}

	public function get_cle(){
		return $this->_cle;
	}

	public function get_roletiers(){
		return $this->_roletiers;
	}

	public function set_roletiers($_roletiers){	
		$this->_roletiers = $_roletiers;
	}

	public function get_typemouvement(){
		return $this->_typemouvement;
	}

	public function set_typemouvement($_typemouvement){
		$this->_typemouvement = $_typemouvement;
	}

	public function get_civilite(){
		return $this->_civilite;
	}

	public function set_civilite($_civilite){
		$this->_civilite = $_civilite;
	}

	public function get_nom(){
		return $this->_nom;
	}

	public function set_nom($_nom){
		$this->_nom = $_nom;
	}

	public function get_prenom(){
		return $this->_prenom;
	}

	public function set_prenom($_prenom){
		$this->_prenom = $_prenom;
	}

	public function get_adresse(){
		return $this->_adresse;
	}

	public function set_adresse($_adresse){
		$this->_adresse = $_adresse;
	}

	public function get_complement(){
		return $this->_complement;
	}

	public function set_complement($_complement){
		$this->_complement = $_complement;
	}

	public function get_codepostal(){
		return $this->_codepostal;
	}

	public function set_codepostal($_codepostal){
		$this->_codepostal = $_codepostal;
	}

	public function get_ville(){
		return $this->_ville;
	}

	public function set_ville($_ville){
		$this->_ville = $_ville;
	}

	public function get_nationalite(){
		return $this->_nationalite;
	}

	public function set_nationalite($_nationalite){
		$this->_nationalite = $_nationalite;
	}

	public function get_datenaissance(){
		return $this->_datenaissance;
	}

	public function set_datenaissance($_datenaissance){
		$this->_datenaissance = $_datenaissance;
	}

	public function get_departementnaissance(){
		return $this->_departementnaissance;
	}

	public function set_departementnaissance($_departementnaissance){
		$this->_departementnaissance = $_departementnaissance;
	}

	public function get_lieunaissance(){
		return $this->_lieunaissance;
	}

	public function set_lieunaissance($_lieunaissance){
		$this->_lieunaissance = $_lieunaissance;
	}

	public function get_numerosecu(){
		return $this->_numerosecu;
	}

	public function set_numerosecu($_numerosecu){
		$this->_numerosecu = $_numerosecu;
	}

	public function get_clesecu(){
		return $this->_clesecu;
	}

	public function set_clesecu($_clesecu){
		$this->_clesecu = $_clesecu;
	}

	public function get_actif(){
		return $this->_actif;
	}

	public function set_actif($_actif){
		$this->_actif = $_actif;
	}
	
	public function create(){

		/*
     * Création de la personne physique
	 */
	try{

		// Requete INSERT INTO
		$query = "INSERT INTO
				a00personnephysique
				(cle, r04roletiers, r03typemouvement, a00civilite, a00nom, a00prenom, a00adresse, a00complement, a00codepostal, a00ville,
				a00nationalite, a00datenaissance, a00departementnaissance, a00lieunaissance, a00numerosecu,a00clesecu, a00actif,
				creation_par, date_creation, heure_creation, modification_par, date_modification, heure_modification)
				values
				('".$this->_cle."',  '".Roles::SALARIE."',  '".TypeMvmt::ENTREE."', '".$this->_civilite."', '".$this->_nom."', '".$this->_prenom."','".$this->_adresse."','".$this->_complement."','".$this->_codepostal."','".$this->_ville."','".$this->_nationalite."','".$this->_datenaissance."','".$this->_departementnaissance."','".$this->_lieunaissance."','".$this->_numerosecu."','".$this->_clesecu."', 'Oui', 'candidat', CURDATE(), CURTIME(), 'candidat', CURDATE(), CURTIME() )
				";

		// on va chercher tous les enregistrements de la requ?te
		$result=Script::$db->prepare($query); 
		$result->execute();
	}
	catch(PDOException  $e){
		$errMsg = $e->getMessage();
		echo $errMsg;
	}
	/*-----------------------------------*/

	}
	
}
?>