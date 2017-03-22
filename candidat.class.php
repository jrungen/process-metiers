<?php
// Table
// candidat
class Candidat{
	
	private $_civilite;
	private $_nom;
	private $_prenom;
	private $_adresse;
	private $_complement;
	private $_codepostal;
	private $_ville;
	private $_nationalite;
	private $_dateNaissance;
	private $_departementNaissance;
	private $_lieu_naissance;
	private $_numSecuriteSociale;
	private $_cleSecuriteSociale;
	private $_cleDar;
	private $_typeRecrutement;
	private $_dateDebutContrat;
	private $_societes;
	private $_typeContratGRE;
	private $_nbJoursRttMonetises;
	private $_dateFinContrat;
	private $_direction;
	private $_poste;
	private $_detailMouvement;
	
	public function get_detailMouvement(){
		return $this->_detailMouvement;
	}
	
	public function set_detailMouvement($_detailMouvement){
		$this->_detailMouvement = $_detailMouvement;
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

	public function get_date_naissance(){
		return $this->_dateNaissance;
	}

	public function set_date_naissance($_date_naissance){
		$this->_dateNaissance = $_date_naissance;
	}

	public function get_departementnaissance(){
		return $this->_departementNaissance;
	}

	public function set_departementnaissance($_departementnaissance){
		$this->_departementNaissance = $_departementnaissance;
	}

	public function get_lieu_naissance(){
		return $this->_lieu_naissance;
	}

	public function set_lieu_naissance($_lieu_naissance){
		$this->_lieu_naissance = $_lieu_naissance;
	}

	public function get_num_securite_sociale(){
		return $this->_numSecuriteSociale;
	}

	public function set_num_securite_sociale($_num_securite_sociale){
		$this->_numSecuriteSociale = $_num_securite_sociale;
	}

	public function get_clesecuritesociale(){
		return $this->_cleSecuriteSociale;
	}

	public function set_clesecuritesociale($_clesecuritesociale){
		$this->_cleSecuriteSociale = $_clesecuritesociale;
	}
	
	public function get_cleDar(){
		return $this->_cleDar;
	}

	public function set_cleDar($cle_dar){
		$this->_cleDar = $cle_dar;
	}

	public function get_type_recrutement(){
		return $this->_typeRecrutement;
	}

	public function set_type_recrutement($_type_recrutement){
		$this->_typeRecrutement = $_type_recrutement;
	}

	public function get_dateDebutContrat(){
		return $this->_dateDebutContrat;
	}

	public function set_dateDebutContrat($_date_debut_contrat){
		$this->_dateDebutContrat = $_date_debut_contrat;
	}

	public function get_societes(){
		return $this->_societes;
	}

	public function set_societes($_societes){
		$this->_societes = $_societes;
	}

	public function get_typecontratGRE(){
		return $this->_typeContratGRE;
	}

	public function set_typecontratGRE($_typecontratGRE){
		$this->_typeContratGRE = $_typecontratGRE;
	}

	public function get_nbJoursRttMonetisess(){
		return $this->_nbJoursRttMonetises;
	}

	public function set_nbJoursRttMonetises($_nbJoursRttMonetises){
		$this->_nbJoursRttMonetises = $_nbJoursRttMonetises;
	}

	public function get_date_fin_contrat(){
		return $this->_dateFinContrat;
	}

	public function set_date_fin_contrat($_date_fin_contrat){
		$this->_dateFinContrat = $_date_fin_contrat;
	}

	public function get_direction(){
		return $this->_direction;
	}

	public function set_direction($_direction){
		$this->_direction = $_direction;
	}

	public function get_poste(){
		return $this->_poste;
	}

	public function set_poste($_poste){
		$this->_poste = $_poste;
	}
	
	public static function findById($idCandidat) {
		
		try{
			$query = "SELECT
						t01_03_civilite_candidat,
						t01_01_nom_candidat,
						t01_02_prenom_candidat,
						t01adresse,
						t01complement,
						t01codepostal,
						t01ville,
						t01_22_nationalite_candidat,
						t01_20_date_naissance,
						t01departementnaissance,
						t01_21_lieu_naissance,
						t01_23_num_securite_sociale,
						t01clesecuritesociale,
						demande,
						t01_24_type_recrutement,
						t01_30_date_debut_contrat,
						cs00societes,
						t01typecontratGRE,
						t01_19_nb_jours_rtt_monetises,
						t01_31_date_fin_contrat,
						cs00direction,
						t01_32_poste,
						r32detailmouvement
					FROM candidat WHERE idcandidat = '".$idCandidat."'";

			// on va chercher tous les enregistrements de la requ?te
			$result=Script::$db->prepare($query); 
			$result->execute();

			// on dit qu'on veut que le resultat soit recuperable sous forme de tableau
			$data = $result->fetchAll((PDO::FETCH_OBJ));

			// on ferme le curseur des r?sultats
			$result->closeCursor(); 
		}
		catch(PDOException  $e){
			$errMsg = $e->getMessage();
			echo $errMsg;
		}
		
		$candidat = new Candidat;
		
		$candidat->set_civilite($data[0]->t01_03_civilite_candidat);
		$candidat->set_nom($data[0]->t01_01_nom_candidat);
		$candidat->set_prenom($data[0]->t01_02_prenom_candidat);
		$candidat->set_adresse($data[0]->t01adresse);
		$candidat->set_complement($data[0]->t01complement);
		$candidat->set_codepostal($data[0]->t01codepostal);
		$candidat->set_ville($data[0]->t01ville);
		$candidat->set_nationalite($data[0]->t01_22_nationalite_candidat);
		$candidat->set_date_naissance($data[0]->t01_20_date_naissance);
		$candidat->set_departementnaissance($data[0]->t01departementnaissance);
		$candidat->set_lieu_naissance($data[0]->t01_21_lieu_naissance);
		$candidat->set_num_securite_sociale($data[0]->t01_23_num_securite_sociale);
		$candidat->set_clesecuritesociale($data[0]->t01clesecuritesociale);
		$candidat->set_cleDar($data[0]->demande);
		$candidat->set_type_recrutement($data[0]->t01_24_type_recrutement);
		$candidat->set_dateDebutContrat($data[0]->t01_30_date_debut_contrat);
		$candidat->set_societes($data[0]->cs00societes);
		$candidat->set_typecontratGRE($data[0]->t01typecontratGRE);
		$candidat->set_nbJoursRttMonetises($data[0]->t01_19_nb_jours_rtt_monetises);
		$candidat->set_date_fin_contrat($data[0]->t01_31_date_fin_contrat);
		$candidat->set_direction($data[0]->cs00direction);
		$candidat->set_poste($data[0]->t01_32_poste);
		$candidat->set_detailMouvement($data[0]->r32detailmouvement);
		return $candidat;

	}
	
}

?>