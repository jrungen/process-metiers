<?php // A supprimer

Script::init(array('content'=>'text/plain'));

if($_REQUEST['mode']=='getKey'){
	
	$societe = $_REQUEST['societe'];
	
	try{
		// Creation de la requete
		$query = "SELECT * FROM r00societes WHERE cle = '".$societe."'";

		// on va chercher tous les enregistrements de la requete
		$result=Script::$db->prepare($query); 
		$result->execute();
		
		// on dit qu'on veut que le resultat soit recuperable sous forme de tableau
		$data_societe = $result->fetchAll((PDO::FETCH_OBJ));
		
		// on ferme le curseur des r?sultats			
		$result->closeCursor(); 

	}
	catch(PDOException  $e){
		$errMsg = $e->getMessage();
		echo $errMsg;
	}

	$code_societe = $data_societe[0]->r00codesociete;

	$getLastKey = Script::$db->prepare("SELECT cle FROM r15direction where cle like 'DIR_".$code_societe."%' ORDER BY cle DESC LIMIT 1");
	$getLastKey->execute();
	$lastKey = $getLastKey->fetchColumn();
	$getLastKey->closeCursor();

	if($lastKey === FALSE)
	{
	exit( "DIR_".$code_societe."001" );
	}

	$lastNumber = (int) substr($lastKey, 7); // retire "DIR_XXX", il reste : [compteur] , qui est une valeur numérique
	$lastNumber = $lastNumber+1;
	$lastNumber = str_pad($lastNumber,3,'0',STR_PAD_LEFT);
	exit("DIR_".$code_societe.$lastNumber);
}
