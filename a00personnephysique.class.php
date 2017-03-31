<?php

/**
 * @author JRU
 *
 */
class PersonnePhysique {
	private $_cle;
	private $_roleTiers;
	private $_civilite;
	private $_nom;
	private $_prenom;
	private $_adresse;
	private $_complement;
	private $_codepostal;
	private $_ville;
	private $_nationalite;
	private $_datenaissance;
	private $_departementNaissance;
	private $_lieuNaissance;
	private $_numSecuriteSociale;
	private $_clesecu;
	private $_actif;
	private $_candidat;
	private $_typeContrat;
	private $_materielInformatique;
	private $_bureau;
	private $_adresseMessagerie;
	private $_nomManager;
	private $_source;
	private $_detailMouvement;
	private $_dateEffet;
	private $_a00matricule;
	private $_a00matriculeunique;
	private $_utilisateur; // Pour lier un user gopaas au PP courant

	/**
	 * Génére clé personne physique
	 */
	public static function generateKey(){ 
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
	
	/**
	 * Création d'une personne physique à partir du candidat.
	 * @param unknown $candidat
	 */
	function PersonnePhysique ($candidat){
		$this->_cle = PersonnePhysique::generateKey();
		
		if (!is_null($candidat)){
			$this->_source='candidat';
			$this->_civilite = $candidat->get_civilite();
			$this->_nom = $candidat->get_nom();
			$this->_prenom = $candidat->get_prenom();
			$this->_adresse = $candidat->get_adresse();
			$this->_complement = $candidat->get_complement();
			$this->_codepostal = $candidat->get_codePostal();
			$this->_ville = $candidat->get_ville();
			$this->_nationalite = $candidat->get_nationalite();
			$this->_datenaissance = $candidat->get_dateNaissance();
			$this->_departementNaissance = $candidat->get_departementNaissance();
			$this->_lieuNaissance = $candidat->get_lieuNaissance();
			$this->_numSecuriteSociale = $candidat->get_numSecuriteSociale();
			$this->_clesecu = $candidat->get_cleSecuriteSociale();
			$this->_typeContrat = $candidat->get_typeContratGRE();
			$this->_detailMouvement = $candidat->get_detailMouvement();
			$this->_dateEffet = $candidat->get_dateArriveeSouhaitee();
			
			// Depuis Candidat seléctioné, valeurs par defauts.
			$this->_materielInformatique = true;
			$this->_bureau = true;
			$this->_adresseMessagerie =true;
			
			//On stock le candidat dans la personne physique pour accéder aux champs du candidat.
			$this->_candidat = $candidat;
			
			// Pour un candidat le rôle tiers est systématiquement salarié.
			$this->_roleTiers = RolesTiers::SALARIE;
			
		}else{
			$this->_source='personnephysique';
			//TODO Création directe depuis fiche Personne physique.
		}
	}
	
	public static function findByCle($clePP){
		try{
			// Creation de la requete
			$query = "SELECT * FROM a00personnephysique WHERE cle = '".$clePP."'";
		
			// on va chercher tous les enregistrements de la requete
			$result=Script::$db->prepare($query);
			$result->execute();
		
			// on dit qu'on veut que le resultat soit recuperable sous forme de tableau
			$data_pp = $result->fetchAll((PDO::FETCH_OBJ));
		
			// on ferme le curseur des r?sultats
			$result->closeCursor();
		
		}
		catch(PDOException  $e){
			$errMsg = $e->getMessage();
			echo $errMsg;
		}
		
		$pp = new PersonnePhysique(null);
		
		$pp->_cle = $clePP;
		$pp->_civilite = $data_pp[0]->a00civilite;
		$pp->_nom = $data_pp[0]->a00nom;
		$pp->_prenom = $data_pp[0]->a00prenom;
		$pp->_adresse = $data_pp[0]->a00adresse;
		$pp->_complement = $data_pp[0]->a00complement;
		$pp->_codepostal = $data_pp[0]->a00codepostal;
		$pp->_nationalite = $data_pp[0]->a00nationalite;
		$pp->_datenaissance = $data_pp[0]->a00datenaissance;
		$pp->_departementNaissance = $data_pp[0]->a00departementnaissance;
		$pp->_lieuNaissance = $data_pp[0]->a00lieunaissance;
		$pp->_numSecuriteSociale = $data_pp[0]->a00numerosecu;
		$pp->_clesecu = $data_pp[0]->a00clesecu;
		$pp->_actif = $data_pp[0]->a00actif;
		$pp->_typeContrat = $data_pp[0]->a00typecontrat;
		$pp->_materielInformatique = $data_pp[0]->a00materielinformatique;
		$pp->_bureau = $data_pp[0]->a00bureau;
		$pp->_adresseMessagerie = $data_pp[0]->a00adressemail;
		//TODO $pp->_nomManager = $data_pp[0]->;
		
		$pp->_source='personnephysique';
		
		return $pp;
	}
	
	public function  get_matricule(){
		return $this->_a00matricule;
	}
	
	public function set_matricule($_a00matricule){
		$this->_a00matricule = $_a00matricule;
	}
	
	public function get_matriculeunique(){
		return $this->_a00matriculeunique;
	}
	
	public function set_matriculeunique($_a00matriculeunique){
		$this->_a00matriculeunique = $_a00matriculeunique;
	}
	
	public function get_dateEffet(){
		return $this->_dateEffet;
	}
	
	public function set_dateEffet($dateEffet){
		$this->_dateEffet = $dateEffet;
	}
	
	public function get_detailMouvement(){
		return $this->_detailMouvement;
	}
	
	public function set_detailMouvement($detailMouvement){
		$this->_detailMouvement = $detailMouvement;
	}
	
	public function get_source(){
		return $this->_source;
	}
	
	public function set_source($_source){
		$this->_source = $_source;
	}
	public function get_cle(){
		return $this->_cle;
	}

	public function get_roleTiers(){
		return $this->_roleTiers;
	}

	public function set_roleTiers($_roleTiers){	
		$this->_roleTiers = $_roleTiers;
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
		return $this->_departementNaissance;
	}

	public function set_departementnaissance($_departementnaissance){
		$this->_departementNaissance = $_departementnaissance;
	}

	public function get_lieunaissance(){
		return $this->_lieuNaissance;
	}

	public function set_lieunaissance($_lieunaissance){
		$this->_lieuNaissance = $_lieunaissance;
	}

	public function get_numerosecu(){
		return $this->_numSecuriteSociale;
	}

	public function set_numerosecu($_numerosecu){
		$this->_numSecuriteSociale = $_numerosecu;
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
	
	public function get_candidat(){
		return $this->_candidat;
	}
	
	public function get_typeContrat(){
		return $this->_typeContrat;
	}
	
	public function set_typeContrat($_typeContrat){
		$this->_typeContrat = $_typeContrat;
	}
	
	public function get_materielInformatique(){
		return $this->_materielInformatique;
	}
	
	public function set_materielInformatique($_materielInformatique){
		$this->_materielInformatique = $_materielInformatique;
	}
	
	public function get_bureau(){
		return $this->_bureau;
	}
	
	public function set_bureau($_bureau){
		$this->_bureau = $_bureau;
	}
	
	public function get_adresseMessagerie(){
		return $this->_adresseMessagerie;
	}
	
	public function set_adresseMessagerie($_adresseMessagerie){
		$this->_adresseMessagerie = $_adresseMessagerie;
	}
	
	public function get_nomManager(){
		return $this->_nomManager;
	}
	
	public function set_nomManager($_nomManager){
		$this->_nomManager = $_nomManager;
	}
	
	/**
	 * Création de la personne physique en base.
	 */
	public function create(){
		try{
	
			// Requete INSERT INTO
			$query = "INSERT INTO
					a00personnephysique
					(cle, r04roletiers, r03typemouvement, a00civilite, a00nom, a00prenom, a00adresse, a00complement, a00codepostal, a00ville,
					a00nationalite, a00datenaissance, a00departementnaissance, a00lieunaissance, a00numerosecu,a00clesecu, a00actif,
					creation_par, date_creation, heure_creation, modification_par, date_modification, heure_modification, candidat,
					a00typecontrat, a00superieurhierarchique, r32detailmouvement)
					values
					('".$this->_cle."', 
							'".RolesTiers::SALARIE."', 
							'".TypeMvmt::ARRIVEE."',
							'".$this->_civilite."',
							'".$this->_nom."',
							'".$this->_prenom."',
							'".$this->_adresse."',
							'".$this->_complement."',
							'".$this->_codepostal."',
							'".$this->_ville."',
							'".$this->_nationalite."',
							'".$this->_datenaissance."',
							'".$this->_departementNaissance."',
							'".$this->_lieuNaissance."',
							'".$this->_numSecuriteSociale."',
							'".$this->_clesecu."', 
							'Oui',
							'" . $this->_source."',
							CURDATE(), CURTIME(), 'candidat', CURDATE(), CURTIME(), 
							'".$this->_candidat->get_cle()."',
							'". $this->_typeContrat."',
							'".$this->_candidat->get_nomManager()."',
							'".$this->_candidat->get_detailMouvement()."'
							)";
	
			// on va chercher tous les enregistrements de la requ?te
			$result=Script::$db->prepare($query); 
			$result->execute();
			}
			catch(PDOException  $e){
				$errMsg = $e->getMessage();
				echo $errMsg;
			}
		$this->postCreate();
	}
	
	private function postCreate(){
		// Dans tout les cas de création de personne physique, on crée un mouvment DRH d'arrivée.
		$this->generateMvmt(TypeMvmt::ARRIVEE);	
	}
	
	public function generateMvmt($typeMouvment){
		//TODO Trouver la condition fonctionelle pour spécifier le type de mvmt Avenant dans la classe postcreate de la PP
		$mvmtDrhPp = new MvmtDRH($this, $typeMouvment);
		$mvmtDrhPp->create();
		$mvmtManager = new MvmtManager($mvmtDrhPp);
		$mvmtManager->executeActions();
	}
	
	/**
	 * Requete pour mettre à jour le statut du PAP (validation du recrutement)
	 */
	public function valideRecrutementPAP(){
		Pap::valideRecrutement($this);
	}
	
	function getJsonData(){
		$var = get_object_vars($this);
		foreach ($var as &$value) {
			if (is_object($value) && method_exists($value,'getJsonData')) {
				$value = $value->getJsonData();
			}
		}
		return $var;
	}
	
}
?>