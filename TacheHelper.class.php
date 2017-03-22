<?php

/**
 * @author YSA
 * Actions sur le référentiel des tâches.
 */
class TacheHelper {
	
	/**
	 * @param unknown $role_tiers (salarié, consultant, interimaire)
	 * @param unknown $mouvement entrée ou sortie
	 * @return $data_r02listetaches La liste des tâches à créer par rôle tiers et type de mouvement.
	 */
	
	public static function get_refTaches($role_tiers, $mouvement) {
		// récupérer les données r02listetaches
		try{
	
			// Requete INSERT INTO
			$query = "SELECT * FROM r02listetaches where r03typemouvement = '".$mouvement."' and r04roletiers = '".$role_tiers."' ";
			
			// on va chercher tous les enregistrements de la requ?te
			$result=Script::$db->prepare($query);
			$result->execute();
	
			// on dit qu'on veut que le résultat soit récupérable sous forme de tableau
			$data_r02listetaches = $result->fetchAll((PDO::FETCH_OBJ));
	
			// on ferme le curseur des résultats
			$result->closeCursor();
	
		}
		catch(PDOException  $e){
			$errMsg = $e->getMessage();
			echo $errMsg;
		}
		return $data_r02listetaches;
	}
}
?>