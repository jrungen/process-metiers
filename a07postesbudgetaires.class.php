<?php

/**
 * @author JRN
* Classe sur la table a07postesbudgetaires (PAP)
*/
class Pap{

	private $_statutPap;
	private $_salariePap;
	private $_origineRecrutement;

	public function get_statutPap(){
		return $this->_statutPap;
	}
	
	public function set_statutPap($_statutPap){
		$this->_statutPap = $_statutPap;
	}
	
	public function get_salariePap(){
		return $this->_salariePap;
	}
	
	public function set_salariePap($_salariePap){
		$this->_salariePap = $_salariePap;
	}
	
	public function get_origineRecrutement(){
		return $this->_origineRecrutement;
	}
	
	public function set_origineRecrutement($_origineRecrutement){
		$this->_origineRecrutement = $_origineRecrutement;
	}

	public static function valideRecrutement($personnePhysique) {
		
		$dar = Dar::findById($personnePhysique->get_candidat()->get_cleDar());
	
		try{
			$query = "UPDATE a07postesbudgetaires
				SET a07statutpap = '".StatutPAP::RECRUTEMENT_VALIDE."',
				a07salariePAP = '".$personnePhysique->get_cle()."',
				a07originerecrutement = '".$personnePhysique->get_candidat()->get_type_recrutement()."'
				where cle = '".$dar->get_clePap()."';";

			// on va chercher tous les enregistrements de la requete
			$result=Script::$db->prepare($query);
			$result->execute();

			// on dit qu'on veut que le resultat soit recuperable sous forme de tableau
			$data = $result->fetchAll((PDO::FETCH_OBJ));

			// on ferme le curseur des resultats
			$result->closeCursor();
		}
		catch(PDOException  $e){
			$errMsg = $e->getMessage();
			echo $errMsg;
		}

	}


}

?>