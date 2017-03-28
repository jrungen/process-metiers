<?php

class Candidat {
	
	private $_t01adresse;
	private $_annee;
	private $_cs0autrecabinet;
	private $_transverse;
	private $_c1n6rteVitaleC0t0dittdat;
	private $_t0103CiviliteCandidat;
	private $_cle;
	private $_t01clesecuritesociale;
	private $_t0105CniCandidat;
	private $_t01codepostal;
	private $_commentaireRefus;
	private $_t0127CommentaireRh;
	private $_t01complement;
	private $_confirmationValideur1;
	private $_confirmationValideur2;
	private $_confirmationValideur3;
	private $_confirmationValideur4;
	private $_confirmationValideur5;
	private $_confirmationValideur6;
	private $_creationPar;
	private $_t0104CvCandidat;
	private $_dateCreation;
	private $_dateArriveeSouhaitee;
	private $_t0131DateFinContrat;
	private $_dateFin;
	private $_t0120DateNaissance;
	private $_t0130DateDebutContrat;
	private $_dateDemande;
	private $_dateModification;
	private $_dateValidation1;
	private $_dateValidation2;
	private $_dateValidation3;
	private $_dateValidation4;
	private $_dateValidation5;
	private $_dateValidation6;
	private $_demande;
	private $_pdf;
	private $_demandeInitiale;
	private $_demandeur;
	private $_t01departementnaissance;
	private $_r32detailmouvement;
	private $_cs00direction;
	private $_nbMoisCdd;
	private $_etape;
	private $_cs0fraisrecrutementprevisionnel;
	private $_heureCreation;
	private $_heureModification;
	private $_t0121LieuNaissance;
	private $_cs00metiers;
	private $_modificationPar;
	private $_cs00motifentree;
	private $_cs00nommanager;
	private $_t0122NationaliteCandidat;
	private $_t01typecontratGRE;
	private $_t0101NomCandidat;
	private $_t0125NomCabinet;
	private $_t0119NbJoursRttMonetises;
	private $_cs0numerocontrat;
	private $_t0123NumSecuriteSociale;
	private $_organigramme;
	private $_t0124TypeRecrutement;
	private $_pdfCandidat;
	private $_permission;
	private $_recruteur;
	private $_t0132Poste;
	private $_cs00postes;
	private $_t0102PrenomCandidat;
	private $_t01primecontractuelle;
	private $_t01primediscretionnaire;
	private $_prochainValideur;
	private $_t0133RemunerationFixeAnnuelleBrute;
	private $_salaire;
	private $_t0118RemunerationFixeMensuelleBrute;
	private $_t0107RibCandidat;
	private $_cs00societes;
	private $_t0116Statut;
	private $_cs00superieurhierarchique;
	private $_cs0tauxfraisrecrutementprev;
	private $_token;
	private $_cs00typecontrat;
	private $_valideur1;
	private $_valideur2;
	private $_valideur3;
	private $_valideur4;
	private $_valideur5;
	private $_valideur6;
	private $_t01ville;
	private $_t0126Voiture;
	
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
						r32detailmouvement,
						cle,
						cs00superieurhierarchique
						FROM candidat WHERE idcandidat = '".$idCandidat."'";
	
			// on va chercher tous les enregistrements de la requête
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
		$candidat->set_dateNaissance($data[0]->t01_20_date_naissance);
		$candidat->set_departementnaissance($data[0]->t01departementnaissance);
		$candidat->set_lieuNaissance($data[0]->t01_21_lieu_naissance);
		$candidat->set_numSecuriteSociale($data[0]->t01_23_num_securite_sociale);
		$candidat->set_cleSecuriteSociale($data[0]->t01clesecuritesociale);
		$candidat->set_cleDar($data[0]->demande);
		$candidat->set_typeRecrutement($data[0]->t01_24_type_recrutement);
		$candidat->set_dateDebutContrat($data[0]->t01_30_date_debut_contrat);
		$candidat->set_societes($data[0]->cs00societes);
		$candidat->set_typecontratGRE($data[0]->t01typecontratGRE);
		$candidat->set_nbJoursRttMonetises($data[0]->t01_19_nb_jours_rtt_monetises);
		$candidat->set_dateFinContrat($data[0]->t01_31_date_fin_contrat);
		$candidat->set_direction($data[0]->cs00direction);
		$candidat->set_poste($data[0]->t01_32_poste);
		$candidat->set_detailMouvement($data[0]->r32detailmouvement);
		$candidat->set_cle($data[0]->cle);
		$candidat->set_nomManager($data[0]->cs00superieurhierarchique);
		return $candidat;
	
	}

	public function get_adresse(){
		return $this->_t01adresse;
	}

	public function get_annee(){
		return $this->_annee;
	}

	public function get_autreCabinet(){
		return $this->_cs0autrecabinet;
	}

	public function get_transverse(){
		return $this->_transverse;
	}

	public function get_carteVitaleCandidat(){
		return $this->_06CarteVitaleC100t0dittdat;
	}

	public function get_civiliteCandidat(){
		return $this->_t0103CiviliteCandidat;
	}

	public function get_cle(){
		return $this->_cle;
	}

	public function get_cleSecuriteSociale(){
		return $this->_t01clesecuritesociale;
	}

	public function get_cniCandidat(){
		return $this->_t0105CniCandidat;
	}

	public function get_codePostal(){
		return $this->_t01codepostal;
	}

	public function get_commentaireRefus(){
		return $this->_commentaireRefus;
	}

	public function get_commentaireRh(){
		return $this->_t0127CommentaireRh;
	}

	public function get_complement(){
		return $this->_t01complement;
	}

	public function get_confirmationValideur1(){
		return $this->_confirmationValideur1;
	}

	public function get_confirmationValideur2(){
		return $this->_confirmationValideur2;
	}

	public function get_confirmationValideur3(){
		return $this->_confirmationValideur3;
	}

	public function get_confirmationValideur4(){
		return $this->_confirmationValideur4;
	}

	public function get_confirmationValideur5(){
		return $this->_confirmationValideur5;
	}

	public function get_confirmationValideur6(){
		return $this->_confirmationValideur6;
	}

	public function get_creationPar(){
		return $this->_creationPar;
	}

	public function get_cvCandidat(){
		return $this->_t0104CvCandidat;
	}

	public function get_dateCreation(){
		return $this->_dateCreation;
	}

	public function get_dateArriveeSouhaitee(){
		return $this->_dateArriveeSouhaitee;
	}

	public function get_dateFinContrat(){
		return $this->_t0131DateFinContrat;
	}

	public function get_dateFin(){
		return $this->_dateFin;
	}

	public function get_dateNaissance(){
		return $this->_t0120DateNaissance;
	}

	public function get_dateDebutContrat(){
		return $this->_t0130DateDebutContrat;
	}

	public function get_dateDemande(){
		return $this->_dateDemande;
	}

	public function get_dateModification(){
		return $this->_dateModification;
	}

	public function get_dateValidation1(){
		return $this->_dateValidation1;
	}

	public function get_dateValidation2(){
		return $this->_dateValidation2;
	}

	public function get_dateValidation3(){
		return $this->_dateValidation3;
	}

	public function get_dateValidation4(){
		return $this->_dateValidation4;
	}

	public function get_dateValidation5(){
		return $this->_dateValidation5;
	}

	public function get_dateValidation6(){
		return $this->_dateValidation6;
	}

	public function get_cleDar(){
		return $this->_demande;
	}

	public function get_pdf(){
		return $this->_pdf;
	}

	public function get_demandeInitiale(){
		return $this->_demandeInitiale;
	}

	public function get_demandeur(){
		return $this->_demandeur;
	}

	public function get_departementNaissance(){
		return $this->_t01departementnaissance;
	}

	public function get_detailMouvement(){
		return $this->_r32detailmouvement;
	}

	public function get_direction(){
		return $this->_cs00direction;
	}

	public function get_nbMoisCdd(){
		return $this->_nbMoisCdd;
	}

	public function get_etape(){
		return $this->_etape;
	}

	public function get_fraisrecrutementprevisionnel(){
		return $this->_cs0fraisrecrutementprevisionnel;
	}

	public function get_heureCreation(){
		return $this->_heureCreation;
	}

	public function get_heureModification(){
		return $this->_heureModification;
	}

	public function get_lieuNaissance(){
		return $this->_t0121LieuNaissance;
	}

	public function get_metiers(){
		return $this->_cs00metiers;
	}

	public function get_metier(){
		return $this->_metier;
	}

	public function get_modificationPar(){
		return $this->_modificationPar;
	}

	public function get_motifEntree(){
		return $this->_cs00motifentree;
	}

	public function get_motifRecrutement(){
		return $this->_motifRecrutement;
	}

	public function get_nomManager(){
		return $this->_cs00nommanager;
	}

	public function get_nomManager(){
		return $this->_nomManager;
	}

	public function get_nationaliteCandidat(){
		return $this->_t0122NationaliteCandidat;
	}

	public function get_typeContratGRE(){
		return $this->_t01typecontratGRE;
	}

	public function get_typeContrat(){
		return $this->_typeContrat;
	}

	public function get_nom(){
		return $this->_t0101NomCandidat;
	}

	public function get_nomCabinet(){
		return $this->_t0125NomCabinet;
	}

	public function get_nbJoursRttMonetises(){
		return $this->_t0119NbJoursRttMonetises;
	}

	public function get_numeroContrat(){
		return $this->_cs0numerocontrat;
	}

	public function get_numSecuriteSociale(){
		return $this->_t0123NumSecuriteSociale;
	}

	public function get_organigramme(){
		return $this->_organigramme;
	}

	public function get_typeRecrutement(){
		return $this->_t0124TypeRecrutement;
	}

	public function get_pdfCandidat(){
		return $this->_pdfCandidat;
	}

	public function get_permission(){
		return $this->_permission;
	}

	public function get_recruteur(){
		return $this->_recruteur;
	}

	public function get_poste(){
		return $this->_t0132Poste;
	}

	public function get_cs00Postes(){
		return $this->_cs00postes;
	}

	public function get_intitulePoste(){
		return $this->_intitulePoste;
	}

	public function get_prenomCandidat(){
		return $this->_t0102PrenomCandidat;
	}

	public function get_primeContractuelle(){
		return $this->_t01primecontractuelle;
	}

	public function get_PrimeDiscretionnaire(){
		return $this->_t01primediscretionnaire;
	}

	public function get_prochainValideur(){
		return $this->_prochainValideur;
	}

	public function get_remunerationFixeAnnuelleBrute(){
		return $this->_t0133RemunerationFixeAnnuelleBrute;
	}

	public function get_salaire(){
		return $this->_salaire;
	}

	public function get_remunerationFixeMensuelleBrute(){
		return $this->_t0118RemunerationFixeMensuelleBrute;
	}

	public function get_ribCandidat(){
		return $this->_t0107RibCandidat;
	}

	public function get_societes(){
		return $this->_cs00societes;
	}

	public function get_statut(){
		return $this->_t0116Statut;
	}

	public function get_superieurHierarchique(){
		return $this->_cs00superieurhierarchique;
	}

	public function get_tauxFraisRecrutementprev(){
		return $this->_cs0tauxfraisrecrutementprev;
	}

	public function get_token(){
		return $this->_token;
	}

	public function get_typeContrat(){
		return $this->_cs00typecontrat;
	}

	public function get_valideur1(){
		return $this->_valideur1;
	}

	public function get_valideur2(){
		return $this->_valideur2;
	}

	public function get_valideur3(){
		return $this->_valideur3;
	}

	public function get_valideur4(){
		return $this->_valideur4;
	}

	public function get_valideur5(){
		return $this->_valideur5;
	}

	public function get_valideur6(){
		return $this->_valideur6;
	}

	public function get_ville(){
		return $this->_t01ville;
	}

	public function get_voiture(){
		return $this->_t0126Voiture;
	}

	public function set_adresse($t01adresse){
		$this->_t01adresse = $t01adresse;
	}

	public function set_annee($annee){
		$this->_annee = $annee;
	}

	public function set_autreCabinet($cs0autrecabinet){
		$this->_cs0autrecabinet = $cs0autrecabinet;
	}

	public function set_transverse($transverse){
		$this->_transverse = $transverse;
	}

	public function set_carteVitaleCandidat($t010ac6CrtaleCandidat){
		$this106CarteVitaleC->_100t0dittdat = $t0106CarteVitaleCandidat;
	}

	public function set_civilite($t0103CiviliteCandidat){
		$this->_t0103CiviliteCandidat = $t0103CiviliteCandidat;
	}

	public function set_cle($cle){
		$this->_cle = $cle;
	}

	public function set_cleSecuriteSociale($t01clesecuritesociale){
		$this->_t01clesecuritesociale = $t01clesecuritesociale;
	}

	public function set_cniCandidat($t0105CniCandidat){
		$this->_t0105CniCandidat = $t0105CniCandidat;
	}

	public function set_codePostal($t01codepostal){
		$this->_t01codepostal = $t01codepostal;
	}

	public function set_commentaireRefus($commentaireRefus){
		$this->_commentaireRefus = $commentaireRefus;
	}

	public function set_commentaireRh($t0127CommentaireRh){
		$this->_t0127CommentaireRh = $t0127CommentaireRh;
	}

	public function set_complement($t01complement){
		$this->_t01complement = $t01complement;
	}

	public function set_confirmationValideur1($confirmationValideur1){
		$this->_confirmationValideur1 = $confirmationValideur1;
	}

	public function set_confirmationValideur2($confirmationValideur2){
		$this->_confirmationValideur2 = $confirmationValideur2;
	}

	public function set_confirmationValideur3($confirmationValideur3){
		$this->_confirmationValideur3 = $confirmationValideur3;
	}

	public function set_confirmationValideur4($confirmationValideur4){
		$this->_confirmationValideur4 = $confirmationValideur4;
	}

	public function set_confirmationValideur5($confirmationValideur5){
		$this->_confirmationValideur5 = $confirmationValideur5;
	}

	public function set_confirmationValideur6($confirmationValideur6){
		$this->_confirmationValideur6 = $confirmationValideur6;
	}

	public function set_creationPar($creationPar){
		$this->_creationPar = $creationPar;
	}

	public function set_cvCandidat($t0104CvCandidat){
		$this->_t0104CvCandidat = $t0104CvCandidat;
	}

	public function set_dateCreation($dateCreation){
		$this->_dateCreation = $dateCreation;
	}

	public function set_dateArriveeSouhaitee($dateArriveeSouhaitee){
		$this->_dateArriveeSouhaitee = $dateArriveeSouhaitee;
	}

	public function set_dateFinContrat($t0131DateFinContrat){
		$this->_t0131DateFinContrat = $t0131DateFinContrat;
	}

	public function set_dateFin($dateFin){
		$this->_dateFin = $dateFin;
	}

	public function set_dateNaissance($t0120DateNaissance){
		$this->_t0120DateNaissance = $t0120DateNaissance;
	}

	public function set_dateDebutContrat($t0130DateDebutContrat){
		$this->_t0130DateDebutContrat = $t0130DateDebutContrat;
	}

	public function set_dateDemande($dateDemande){
		$this->_dateDemande = $dateDemande;
	}

	public function set_dateModification($dateModification){
		$this->_dateModification = $dateModification;
	}

	public function set_dateValidation1($dateValidation1){
		$this->_dateValidation1 = $dateValidation1;
	}

	public function set_dateValidation2($dateValidation2){
		$this->_dateValidation2 = $dateValidation2;
	}

	public function set_dateValidation3($dateValidation3){
		$this->_dateValidation3 = $dateValidation3;
	}

	public function set_dateValidation4($dateValidation4){
		$this->_dateValidation4 = $dateValidation4;
	}

	public function set_dateValidation5($dateValidation5){
		$this->_dateValidation5 = $dateValidation5;
	}

	public function set_dateValidation6($dateValidation6){
		$this->_dateValidation6 = $dateValidation6;
	}

	public function set_cleDar($demande){
		$this->_demande = $demande;
	}

	public function set_pdf($pdf){
		$this->_pdf = $pdf;
	}

	public function set_demandeInitiale($demandeInitiale){
		$this->_demandeInitiale = $demandeInitiale;
	}

	public function set_demandeur($demandeur){
		$this->_demandeur = $demandeur;
	}

	public function set_departementNaissance($t01departementnaissance){
		$this->_t01departementnaissance = $t01departementnaissance;
	}

	public function set_detailmouvement($r32detailmouvement){
		$this->_r32detailmouvement = $r32detailmouvement;
	}

	public function set_direction($cs00direction){
		$this->_cs00direction = $cs00direction;
	}

	public function set_nbMoisCdd($nbMoisCdd){
		$this->_nbMoisCdd = $nbMoisCdd;
	}

	public function set_etape($etape){
		$this->_etape = $etape;
	}

	public function set_fraisRecrutementPrevisionnel($cs0fraisrecrutementprevisionnel){
		$this->_cs0fraisrecrutementprevisionnel = $cs0fraisrecrutementprevisionnel;
	}

	public function set_heureCreation($heureCreation){
		$this->_heureCreation = $heureCreation;
	}

	public function set_heureModification($heureModification){
		$this->_heureModification = $heureModification;
	}

	public function set_lieuNaissance($t0121LieuNaissance){
		$this->_t0121LieuNaissance = $t0121LieuNaissance;
	}

	public function set_metiers($cs00metiers){
		$this->_cs00metiers = $cs00metiers;
	}

	public function set_modificationPar($modificationPar){
		$this->_modificationPar = $modificationPar;
	}

	public function set_motifEntree($cs00motifentree){
		$this->_cs00motifentree = $cs00motifentree;
	}

	public function set_motifRecrutement($motifRecrutement){
		$this->_motifRecrutement = $motifRecrutement;
	}

	public function set_nomManager($cs00nommanager){
		$this->_cs00nommanager = $cs00nommanager;
	}

	public function set_nationalite($t0122NationaliteCandidat){
		$this->_t0122NationaliteCandidat = $t0122NationaliteCandidat;
	}

	public function set_typeContratGRE($t01typecontratGRE){
		$this->_t01typecontratGRE = $t01typecontratGRE;
	}

	public function set_typeContrat($typeContrat){
		$this->_typeContrat = $typeContrat;
	}

	public function set_nom($t0101NomCandidat){
		$this->_t0101NomCandidat = $t0101NomCandidat;
	}

	public function set_nomCabinet($t0125NomCabinet){
		$this->_t0125NomCabinet = $t0125NomCabinet;
	}

	public function set_nbJoursRttMonetises($t0119NbJoursRttMonetises){
		$this->_t0119NbJoursRttMonetises = $t0119NbJoursRttMonetises;
	}

	public function set_numeroContrat($cs0numerocontrat){
		$this->_cs0numerocontrat = $cs0numerocontrat;
	}

	public function set_numSecuriteSociale($t0123NumSecuriteSociale){
		$this->_t0123NumSecuriteSociale = $t0123NumSecuriteSociale;
	}

	public function set_organigramme($organigramme){
		$this->_organigramme = $organigramme;
	}

	public function set_typeRecrutement($t0124TypeRecrutement){
		$this->_t0124TypeRecrutement = $t0124TypeRecrutement;
	}

	public function set_pdfCandidat($pdfCandidat){
		$this->_pdfCandidat = $pdfCandidat;
	}

	public function set_permission($permission){
		$this->_permission = $permission;
	}

	public function set_recruteur($recruteur){
		$this->_recruteur = $recruteur;
	}

	public function set_Poste($t0132Poste){
		$this->_t0132Poste = $t0132Poste;
	}

	public function set_cs00Postes($cs00postes){
		$this->_cs00postes = $cs00postes;
	}

	public function set_intitulePoste($intitulePoste){
		$this->_intitulePoste = $intitulePoste;
	}

	public function set_prenom($t0102PrenomCandidat){
		$this->_t0102PrenomCandidat = $t0102PrenomCandidat;
	}

	public function set_primeContractuelle($t01primecontractuelle){
		$this->_t01primecontractuelle = $t01primecontractuelle;
	}

	public function set_primeDiscretionnaire($t01primediscretionnaire){
		$this->_t01primediscretionnaire = $t01primediscretionnaire;
	}

	public function set_prochainValideur($prochainValideur){
		$this->_prochainValideur = $prochainValideur;
	}

	public function set_remunerationFixeAnnuelleBrute($t0133RemunerationFixeAnnuelleBrute){
		$this->_t0133RemunerationFixeAnnuelleBrute = $t0133RemunerationFixeAnnuelleBrute;
	}

	public function set_salaire($salaire){
		$this->_salaire = $salaire;
	}

	public function set_remunerationFixeMensuelleBrute($t0118RemunerationFixeMensuelleBrute){
		$this->_t0118RemunerationFixeMensuelleBrute = $t0118RemunerationFixeMensuelleBrute;
	}

	public function set_ribCandidat($t0107RibCandidat){
		$this->_t0107RibCandidat = $t0107RibCandidat;
	}

	public function set_societes($cs00societes){
		$this->_cs00societes = $cs00societes;
	}


	public function set_statut($t0116Statut){
		$this->_t0116Statut = $t0116Statut;
	}

	public function set_superieurHierarchique($cs00superieurhierarchique){
		$this->_cs00superieurhierarchique = $cs00superieurhierarchique;
	}

	public function set_tauxFraisRecrutementprev($cs0tauxfraisrecrutementprev){
		$this->_cs0tauxfraisrecrutementprev = $cs0tauxfraisrecrutementprev;
	}

	public function set_token($token){
		$this->_token = $token;
	}

	public function set_typeContrat($cs00typecontrat){
		$this->_cs00typecontrat = $cs00typecontrat;
	}

	public function set_valideur1($valideur1){
		$this->_valideur1 = $valideur1;
	}

	public function set_valideur2($valideur2){
		$this->_valideur2 = $valideur2;
	}

	public function set_valideur3($valideur3){
		$this->_valideur3 = $valideur3;
	}

	public function set_valideur4($valideur4){
		$this->_valideur4 = $valideur4;
	}

	public function set_valideur5($valideur5){
		$this->_valideur5 = $valideur5;
	}

	public function set_valideur6($valideur6){
		$this->_valideur6 = $valideur6;
	}

	public function set_ville($t01ville){
		$this->_t01ville = $t01ville;
	}

	public function set_voiture($t0126Voiture){
		$this->_t0126Voiture = $t0126Voiture;
	}

	public function create(){
		preCreate();
		postCreate();
	}

	public function update(){
		preUpdate();
		postUpdate();
	}

	public function delete(){
		preDelete();
		postDelete();
	}

	private function preCreate(){
	}

	private function postCreate(){
	}

	private function preUpdate(){
	}

	private function postUpdate(){
	}

	private function preDelete(){
	}

	private function postDelete(){
	}

}

?>