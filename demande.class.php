<?php

/**
 * @author JRN
 * Classe sur la table demande (DAR)
 */
class demande {

	private $_annee;
	private $_transverse;
	private $_candidatCree;
	private $_cle;
	private $_prevuDernierPbActuCommentaire;
	private $_commentaireRefus;
	private $_commentaireRenvoiDemandeur;
	private $_confirmationValideur1;
	private $_confirmationValideur2;
	private $_confirmationValideur3;
	private $_confirmationValideur4;
	private $_confirmationValideur5;
	private $_confirmationValideur6;
	private $_contratRealise;
	private $_coutFormation;
	private $_creationPar;
	private $_cv;
	private $_dateCreation;
	private $_dateValidation1;
	private $_dateValidation2;
	private $_dateValidation3;
	private $_dateValidation4;
	private $_dateValidation5;
	private $_dateValidation6;
	private $_dateDemande;
	private $_dateFin;
	private $_dateModification;
	private $_dateArriveeSouhaitee;
	private $_demandeInitiale;
	private $_demandeur;
	private $_missionPrincipale;
	private $_d00direction;
	private $_direction;
	private $_nbMoisCdd;
	private $_duree;
	private $_prevuDernierPbActu;
	private $_etape;
	private $_pdf;
	private $_heureCreation;
	private $_heureModification;
	private $_motivationDemande;
	private $_intitulePoste;
	private $_modification;
	private $_d00materielinformatique;
	private $_materielinformatique;
	private $_d00metiers;
	private $_metier;
	private $_modificationPar;
	private $_d00motifpap;
	private $_d00motifrecrutement;
	private $_motifRecrutement;
	private $_nomDiplome;
	private $_d00nommanager;
	private $_nomManager;
	private $_nomEcole;
	private $_organigramme;
	private $_permission;
	private $_d00recruteur;
	private $_recruteur;
	private $_d00personneremplacee;
	private $_personneRemplacee;
	private $_d00postes;
	private $_clePap; // correspond au champ a07postesbudgetaires
	private $_prochainValideur;
	private $_salaire;
	private $_salaireFixeBrutMensuel;
	private $_impactBudgetaireAnneeN;
	private $_impactBudgetaireAnneeNPlus1;
	private $_sousService;
	private $_d00societes;
	private $_societe;
	private $_statut;
	private $_token;
	private $_r06typecontrat;
	private $_typeContrat;
	private $_d00valeursalaire;
	private $_valideur1;
	private $_valideur2;
	private $_valideur3;
	private $_valideur4;
	private $_valideur5;
	private $_valideur6;

	
	public static function findById($cleDar) {
	
		try{
			$query = "SELECT
						demande.a07postesbudgetaires as dar_cle_pap,
						demande.d00nommanager as dar_superieur,
						demande.d00personneremplacee as dar_remplace
					FROM demande
					where cle = '".$cleDar."';";
	
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
	
		$dar = new Dar;
	
		$dar->set_clePap($data[0]->dar_cle_pap);
		$dar->set_nomManager($data[0]->dar_superieur);
		$dar->set_personneRemplacee($data[0]->dar_remplace);
	
		return $dar;
	
	}
		
	public function get_clePap(){
		return $this->_clePap;
	}
	
	public function set_clePap($_clePap){
		$this->_clePap = $_clePap;
	}
	public function get_annee(){
		return $this->_annee;
	}

	public function get_transverse(){
		return $this->_transverse;
	}

	public function get_candidatCree(){
		return $this->_candidatCree;
	}

	public function get_cle(){
		return $this->_cle;
	}

	public function get_prevuDernierPbActuCommentaire(){
		return $this->_prevuDernierPbActuCommentaire;
	}

	public function get_commentaireRefus(){
		return $this->_commentaireRefus;
	}

	public function get_commentaireRenvoiDemandeur(){
		return $this->_commentaireRenvoiDemandeur;
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

	public function get_contratRealise(){
		return $this->_contratRealise;
	}

	public function get_coutFormation(){
		return $this->_coutFormation;
	}

	public function get_creationPar(){
		return $this->_creationPar;
	}

	public function get_cv(){
		return $this->_cv;
	}

	public function get_dateCreation(){
		return $this->_dateCreation;
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

	public function get_dateDemande(){
		return $this->_dateDemande;
	}

	public function get_dateFin(){
		return $this->_dateFin;
	}

	public function get_dateModification(){
		return $this->_dateModification;
	}

	public function get_dateArriveeSouhaitee(){
		return $this->_dateArriveeSouhaitee;
	}

	public function get_demandeInitiale(){
		return $this->_demandeInitiale;
	}

	public function get_demandeur(){
		return $this->_demandeur;
	}

	public function get_missionPrincipale(){
		return $this->_missionPrincipale;
	}

	public function get_d00direction(){
		return $this->_d00direction;
	}

	public function get_direction(){
		return $this->_direction;
	}

	public function get_nbMoisCdd(){
		return $this->_nbMoisCdd;
	}

	public function get_duree(){
		return $this->_duree;
	}

	public function get_prevuDernierPbActu(){
		return $this->_prevuDernierPbActu;
	}

	public function get_etape(){
		return $this->_etape;
	}

	public function get_pdf(){
		return $this->_pdf;
	}

	public function get_heureCreation(){
		return $this->_heureCreation;
	}

	public function get_heureModification(){
		return $this->_heureModification;
	}

	public function get_motivationDemande(){
		return $this->_motivationDemande;
	}

	public function get_intitulePoste(){
		return $this->_intitulePoste;
	}

	public function get_modification(){
		return $this->_modification;
	}

	public function get_d00materielinformatique(){
		return $this->_d00materielinformatique;
	}

	public function get_materielinformatique(){
		return $this->_materielinformatique;
	}

	public function get_d00metiers(){
		return $this->_d00metiers;
	}

	public function get_metier(){
		return $this->_metier;
	}

	public function get_modificationPar(){
		return $this->_modificationPar;
	}

	public function get_d00motifpap(){
		return $this->_d00motifpap;
	}

	public function get_d00motifrecrutement(){
		return $this->_d00motifrecrutement;
	}

	public function get_motifRecrutement(){
		return $this->_motifRecrutement;
	}

	public function get_nomDiplome(){
		return $this->_nomDiplome;
	}

	public function get_d00nommanager(){
		return $this->_d00nommanager;
	}

	public function get_nomManager(){
		return $this->_nomManager;
	}

	public function get_nomEcole(){
		return $this->_nomEcole;
	}

	public function get_organigramme(){
		return $this->_organigramme;
	}

	public function get_permission(){
		return $this->_permission;
	}

	public function get_d00recruteur(){
		return $this->_d00recruteur;
	}

	public function get_recruteur(){
		return $this->_recruteur;
	}

	public function get_d00personneremplacee(){
		return $this->_d00personneremplacee;
	}

	public function get_personneRemplacee(){
		return $this->_personneRemplacee;
	}

	public function get_d00postes(){
		return $this->_d00postes;
	}

	public function get_prochainValideur(){
		return $this->_prochainValideur;
	}

	public function get_salaire(){
		return $this->_salaire;
	}

	public function get_salaireFixeBrutMensuel(){
		return $this->_salaireFixeBrutMensuel;
	}

	public function get_impactBudgetaireAnneeN(){
		return $this->_impactBudgetaireAnneeN;
	}

	public function get_impactBudgetaireAnneeNPlus1(){
		return $this->_impactBudgetaireAnneeNPlus1;
	}

	public function get_sousService(){
		return $this->_sousService;
	}

	public function get_d00societes(){
		return $this->_d00societes;
	}

	public function get_societe(){
		return $this->_societe;
	}

	public function get_statut(){
		return $this->_statut;
	}

	public function get_token(){
		return $this->_token;
	}

	public function get_r06typecontrat(){
		return $this->_r06typecontrat;
	}

	public function get_typeContrat(){
		return $this->_typeContrat;
	}

	public function get_d00valeursalaire(){
		return $this->_d00valeursalaire;
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

	public function set_annee($annee){
		$this->_annee = $annee;
	}

	public function set_transverse($transverse){
		$this->_transverse = $transverse;
	}

	public function set_candidatCree($candidatCree){
		$this->_candidatCree = $candidatCree;
	}

	public function set_cle($cle){
		$this->_cle = $cle;
	}

	public function set_prevuDernierPbActuCommentaire($prevuDernierPbActuCommentaire){
		$this->_prevuDernierPbActuCommentaire = $prevuDernierPbActuCommentaire;
	}

	public function set_commentaireRefus($commentaireRefus){
		$this->_commentaireRefus = $commentaireRefus;
	}

	public function set_commentaireRenvoiDemandeur($commentaireRenvoiDemandeur){
		$this->_commentaireRenvoiDemandeur = $commentaireRenvoiDemandeur;
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

	public function set_contratRealise($contratRealise){
		$this->_contratRealise = $contratRealise;
	}

	public function set_coutFormation($coutFormation){
		$this->_coutFormation = $coutFormation;
	}

	public function set_creationPar($creationPar){
		$this->_creationPar = $creationPar;
	}

	public function set_cv($cv){
		$this->_cv = $cv;
	}

	public function set_dateCreation($dateCreation){
		$this->_dateCreation = $dateCreation;
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

	public function set_dateDemande($dateDemande){
		$this->_dateDemande = $dateDemande;
	}

	public function set_dateFin($dateFin){
		$this->_dateFin = $dateFin;
	}

	public function set_dateModification($dateModification){
		$this->_dateModification = $dateModification;
	}

	public function set_dateArriveeSouhaitee($dateArriveeSouhaitee){
		$this->_dateArriveeSouhaitee = $dateArriveeSouhaitee;
	}

	public function set_demandeInitiale($demandeInitiale){
		$this->_demandeInitiale = $demandeInitiale;
	}

	public function set_demandeur($demandeur){
		$this->_demandeur = $demandeur;
	}

	public function set_missionPrincipale($missionPrincipale){
		$this->_missionPrincipale = $missionPrincipale;
	}

	public function set_d00direction($d00direction){
		$this->_d00direction = $d00direction;
	}

	public function set_direction($direction){
		$this->_direction = $direction;
	}

	public function set_nbMoisCdd($nbMoisCdd){
		$this->_nbMoisCdd = $nbMoisCdd;
	}

	public function set_duree($duree){
		$this->_duree = $duree;
	}

	public function set_prevuDernierPbActu($prevuDernierPbActu){
		$this->_prevuDernierPbActu = $prevuDernierPbActu;
	}

	public function set_etape($etape){
		$this->_etape = $etape;
	}

	public function set_pdf($pdf){
		$this->_pdf = $pdf;
	}

	public function set_heureCreation($heureCreation){
		$this->_heureCreation = $heureCreation;
	}

	public function set_heureModification($heureModification){
		$this->_heureModification = $heureModification;
	}

	public function set_motivationDemande($motivationDemande){
		$this->_motivationDemande = $motivationDemande;
	}

	public function set_intitulePoste($intitulePoste){
		$this->_intitulePoste = $intitulePoste;
	}

	public function set_modification($modification){
		$this->_modification = $modification;
	}

	public function set_d00materielinformatique($d00materielinformatique){
		$this->_d00materielinformatique = $d00materielinformatique;
	}

	public function set_materielinformatique($materielinformatique){
		$this->_materielinformatique = $materielinformatique;
	}

	public function set_d00metiers($d00metiers){
		$this->_d00metiers = $d00metiers;
	}

	public function set_metier($metier){
		$this->_metier = $metier;
	}

	public function set_modificationPar($modificationPar){
		$this->_modificationPar = $modificationPar;
	}

	public function set_d00motifpap($d00motifpap){
		$this->_d00motifpap = $d00motifpap;
	}

	public function set_d00motifrecrutement($d00motifrecrutement){
		$this->_d00motifrecrutement = $d00motifrecrutement;
	}

	public function set_motifRecrutement($motifRecrutement){
		$this->_motifRecrutement = $motifRecrutement;
	}

	public function set_nomDiplome($nomDiplome){
		$this->_nomDiplome = $nomDiplome;
	}

	public function set_d00nommanager($d00nommanager){
		$this->_d00nommanager = $d00nommanager;
	}

	public function set_nomManager($nomManager){
		$this->_nomManager = $nomManager;
	}

	public function set_nomEcole($nomEcole){
		$this->_nomEcole = $nomEcole;
	}

	public function set_organigramme($organigramme){
		$this->_organigramme = $organigramme;
	}

	public function set_permission($permission){
		$this->_permission = $permission;
	}

	public function set_d00recruteur($d00recruteur){
		$this->_d00recruteur = $d00recruteur;
	}

	public function set_recruteur($recruteur){
		$this->_recruteur = $recruteur;
	}

	public function set_d00personneremplacee($d00personneremplacee){
		$this->_d00personneremplacee = $d00personneremplacee;
	}

	public function set_personneRemplacee($personneRemplacee){
		$this->_personneRemplacee = $personneRemplacee;
	}

	public function set_d00postes($d00postes){
		$this->_d00postes = $d00postes;
	}

	public function set_prochainValideur($prochainValideur){
		$this->_prochainValideur = $prochainValideur;
	}

	public function set_salaire($salaire){
		$this->_salaire = $salaire;
	}

	public function set_salaireFixeBrutMensuel($salaireFixeBrutMensuel){
		$this->_salaireFixeBrutMensuel = $salaireFixeBrutMensuel;
	}

	public function set_impactBudgetaireAnneeN($impactBudgetaireAnneeN){
		$this->_impactBudgetaireAnneeN = $impactBudgetaireAnneeN;
	}

	public function set_impactBudgetaireAnneeNPlus1($impactBudgetaireAnneeNPlus1){
		$this->_impactBudgetaireAnneeNPlus1 = $impactBudgetaireAnneeNPlus1;
	}

	public function set_sousService($sousService){
		$this->_sousService = $sousService;
	}

	public function set_d00societes($d00societes){
		$this->_d00societes = $d00societes;
	}

	public function set_societe($societe){
		$this->_societe = $societe;
	}

	public function set_statut($statut){
		$this->_statut = $statut;
	}

	public function set_token($token){
		$this->_token = $token;
	}

	public function set_r06typecontrat($r06typecontrat){
		$this->_r06typecontrat = $r06typecontrat;
	}

	public function set_typeContrat($typeContrat){
		$this->_typeContrat = $typeContrat;
	}

	public function set_d00valeursalaire($d00valeursalaire){
		$this->_d00valeursalaire = $d00valeursalaire;
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

class Dar{


}

?>