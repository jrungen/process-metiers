<?php // a supprimer

Script::init(array('content'=>'text/plain'));

if($_REQUEST['mode']=='getKey'){
	$getLastKey = Script::$db->prepare("SELECT cle FROM a07postesbudgetaires ORDER BY cle DESC LIMIT 1");
	$getLastKey->execute();
	$lastKey = $getLastKey->fetchColumn();
	$getLastKey->closeCursor();

	if($lastKey === FALSE)
	{
	exit( "PAP0000001" );
	}

	$lastNumber = (int) substr($lastKey, 7); // retire "PAP", il reste : [compteur] , qui est une valeur numérique
	$lastNumber = $lastNumber+1;
	$lastNumber = str_pad($lastNumber,7,'0',STR_PAD_LEFT);
	exit("PAP".$lastNumber);
}