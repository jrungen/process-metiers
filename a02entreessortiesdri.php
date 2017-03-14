<?php // A supprimer

Script::init(array('content'=>'application/json'));
// if($_REQUEST['mode']=='getCartouche'){

	// // récupérer les données du candidat sélectionné
	// $ida02entreessortiesdri = $_REQUEST['ida02entreessortiesdri'];
	
	// try{	
		// // Creation de la requete
		// $query = "SELECT r03typemouvement.r03libelletypemouvement as Mouvement, a05typemouvement.a05dateeffet as Date_effet,
		// a00personnephysique.a00nom as Nom, a00personnephysique.a00prenom as Prenom,	a02entreessortiesdri.cle as cle, r04roletiers.r04libellerole as roletiers
		// FROM a02entreessortiesdri
		// left outer join a00personnephysique on a00personnephysique.cle = a02entreessortiesdri.a00personnephysique 
		// left outer join r04roletiers on r04roletiers.cle = a00personnephysique.r04roletiers 
		// left outer join a05typemouvement on a05typemouvement.cle = a02entreessortiesdri.a05typemouvement 
		// left outer join r03typemouvement on r03typemouvement.cle = a05typemouvement.r03typemouvement
		// WHERE a02entreessortiesdri.ida02entreessortiesdri = '".$ida02entreessortiesdri ."'";
		// // echo $query;
		// // on va chercher tous les enregistrements de la requete
		// $result=Script::$db->prepare($query); 
		// $result->execute();
		
		// // on dit qu'on veut que le r?sultat soit r?cup?rable sous forme de tableau
		// $data_cartouche = $result->fetchAll((PDO::FETCH_OBJ));
		
		// // on ferme le curseur des r?sultats			
		// $result->closeCursor(); 

	// }
	// catch(PDOException  $e){
		// $errMsg = $e->getMessage();
		// echo $errMsg;
	// }
	// echo json_encode($data_cartouche);
// }