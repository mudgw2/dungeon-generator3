<?php
	include "functions.php";

	//Get passed UID and find matching area in array
	//find matching from_uid in session
	$target_uid = (isset($_REQUEST['target_uid']) ? $_REQUEST['target_uid'] : null);
	$dungeon 	= (isset($_SESSION['dungeon']) ? $_SESSION['dungeon'] : null);

	$path		= [];
	$slice		= [];
	$path 		= search($dungeon,$target_uid);
	$slice 		= array_slice($dungeon[$target_uid],0);
	//var_dump($slice);
	echo json_encode($slice);
?>