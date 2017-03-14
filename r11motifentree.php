<?php // A supprimer

Script::init(array('content'=>'text/plain'));

if($_REQUEST['mode']=='getKey'){
	$getLastKey = Script::$db->prepare("SELECT cle FROM r11motifentree ORDER BY cle DESC LIMIT 1");
	$getLastKey->execute();
	$lastKey = $getLastKey->fetchColumn();
	$getLastKey->closeCursor();

	if($lastKey === FALSE)
	{
	exit( "MOTENT001" );
	}

	$lastNumber = (int) substr($lastKey, 6); // retire "MOTENT", il reste : [compteur] , qui est une valeur numérique
	$lastNumber = $lastNumber+1;
	$lastNumber = str_pad($lastNumber,3,'0',STR_PAD_LEFT);
	exit("MOTENT".$lastNumber);
}