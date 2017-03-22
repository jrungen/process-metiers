<?php

/**
 * @author JRN
 * Classe sur la table demande (DAR)
 */
class Dar{

	private $_clePap;
	private $_nomManager;
	private $_personneRemplacee;

	public function get_clePap(){
		return $this->_clePap;
	}
	
	public function set_clePap($_clePap){
		$this->_clePap = $_clePap;
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

	public static function findById($idDar) {

		try{
			$query = "SELECT
						demande.a07postesbudgetaires as dar_cle_pap,
						demande.d00nommanager as dar_superieur,
						demande.d00personneremplacee as dar_remplace
					FROM demande
					where cle = '".$idDar."';";

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

}

?>