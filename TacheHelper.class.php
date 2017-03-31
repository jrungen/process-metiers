<?php

/**
 * @author YSA
 * Actions sur le référentiel des tâches.
 */
class TacheHelper {
	
	
	/**
	 * Création des tâches du mouvement selon le rôle de la pp et le type de mouvement.
	 */
	public static function createTaches($mvmt){
		
		// récupérer le référentiel des tâches pour savoir quel sont les tâches à créer.
		$data_r02listetaches = TacheHelper::get_refTaches($mvmt->get_personnePhysique()->get_roleTiers(),$mvmt->get_typeMouvement());
		
		
		/*-----------------------------------*/
		
		/*
		 * Création tâches
		 */
		echo "\n".'-> Création tâches='.sizeof($data_r02listetaches).
		' pour type de mouvement '.$mvmt->get_typeMouvement().
		'et rôle tiers '.$mvmt->get_personnePhysique()->get_roleTiers()."\n";
		try{
		
			$i = 1;
			foreach ($data_r02listetaches as $r02listetache) {
				$getLastKey = Script::$db->prepare("SELECT cle FROM a04taches ORDER BY cle DESC LIMIT 1");
				$getLastKey->execute();
				$lastKey = $getLastKey->fetchColumn();
				$getLastKey->closeCursor();
		
				if($lastKey === FALSE)
				{
					$lastNumber = "T000000001";
				}
		
				$lastNumber = (int) substr($lastKey, 3); // retire "MVT", il reste : [compteur] , qui est une valeur numérique
				$lastNumber = $lastNumber+1;
				$lastNumber = str_pad($lastNumber,9,'0',STR_PAD_LEFT);
				$tache_cle = "T".$lastNumber;
		
				// Creation de la requete
		
				// Associer la tâches suivant l'entrée sortie (DRH, DRI ou DSI)
				$entreesortie = '';
				$allocation = '';
		
				if($r02listetache->r01allocationtache == 'DRH') {
					$entreesortie = 'a05typemouvement';
					$allocation = 'drh';
		
					$query = "	INSERT INTO a04taches
							( cle, r02listetaches, a04statuttache, a00personnephysique, utilisateur, r01allocationtache,
							".$entreesortie.",
							creation_par, date_creation, heure_creation, modification_par, date_modification, heure_modification)
							values ('".$tache_cle."',
							'".$r02listetache->cle."',
							'Non Traité',
							'".$mvmt->get_personnePhysique()->get_cle()."',
							'".$r02listetache->utilisateur."',
							'".$r02listetache->r01allocationtache."',
							'".$mvmt->get_cle()."',
							'candidat', CURDATE(),CURTIME(), 'candidat', CURDATE(),  CURTIME() );
							";
				}
		
				if ( ($r02listetache->r01allocationtache == 'DRI') || ($r02listetache->r01allocationtache == 'DSI') ){
					if($r02listetache->r01allocationtache == 'DRI'){
						$entreesortie = 'a02entreessortiesdri';
						$allocation = 'dri';
					}
		
					if($r02listetache->r01allocationtache == 'DSI'){
						$entreesortie = 'a03entreessortiesdsi';
						$allocation = 'dsi';
					}
		
					$query = "	INSERT INTO a04taches
							( cle, r02listetaches, a04statuttache, a00personnephysique, a05typemouvement, utilisateur, r01allocationtache, ".$entreesortie." , creation_par, date_creation, heure_creation, modification_par, date_modification, heure_modification)
							values ('".$tache_cle."',
							'".$r02listetache->cle."', 'Non Traité',
							'".$mvmt->get_personnePhysique()->get_cle()."',
							'".$mvmt->get_cle()."',
							'".$r02listetache->utilisateur."',
							'".$r02listetache->r01allocationtache."',
							'".$mvmt->get_cle()."_".$allocation."',
							'candidat', CURDATE(),CURTIME(), 'candidat', CURDATE(),  CURTIME() );
							";
				}
				// on va chercher tous les enregistrements de la requête
				$result=Script::$db->prepare($query);
				$result->execute();
		
				$i++;
			}
		
		}
		catch(PDOException  $e){
			$errMsg = $e->getMessage();
			echo $errMsg;
		}
	}
	
	/**
	 * @param unknown $roleTiers (salarié, consultant, interimaire)
	 * @param unknown $typeMvmt entrée ou sortie
	 * @return $data_r02listetaches La liste des tâches à créer par rôle tiers et type de mouvement.
	 */
	
	public static function get_refTaches($roleTiers, $typeMvmt) {
		// récupérer les données r02listetaches
		try{
	
			// Requete INSERT INTO
			$query = "SELECT * FROM r02listetaches where r03typemouvement = '".$typeMvmt."' and r04roletiers = '".$roleTiers."' ";
			
			// on va chercher tous les enregistrements de la requête
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