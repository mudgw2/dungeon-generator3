<?php
//Allow for functions to be requested by ajax (for example), this will then check to see if this page has the needed function then execute it.
//var_dump($_POST);
//SEED RANDOM Number

if(isset($_GET['seed'])){$GLOBALS['seed'] = $_GET['seed'];}else{$GLOBALS['seed'] = 42;}
srand($GLOBALS['seed']);

$_SESSION['dungeon']['uid'] = uniqid();

if(isset($_GET['function'])){
	$funcParams = [];
	$function = $_GET['function']; // 1


	$callable = is_callable($function, false, $callable_name);

	if($callable){
		if(isset($_SESSION['dungeon'])){
			$funcParams[0] = $_SESSION['dungeon'];
		}else{
			$funcParams[0] = "";
		}
		if(isset($_POST['from_uid'])){
			$funcParams[1] = $_POST['from_uid'];
		}else{
			$funcParams[1] = "";
		}		
		$result = call_user_func_array($function,  $funcParams);
	}else{
		return false;
	}
}

// Generate the Goal for the adventure
function goal($g,$type){
	$str = file_get_contents('json/goals.json');
	$json = json_decode($str, true); // decode the JSON into an associative array
		if ($json === null
		&& json_last_error() !== JSON_ERROR_NONE) {
			error_modal('ERROR: Invalid goals.json','Your goals.json file is invalid, please correct and try again.<br>[See <a href="http://jsonlint.com/" target="_blank">http://jsonlint.com/</a>]');
		}else{
			if (isset($json['goals'][0][$type]) || isset($g)){
			//Seed Random Number Generator used by PHP to control output
			//srand($GLOBALS['seed']);
			$rand_key = array_rand($json['goals'][0][$type], $g);
			
			$_SESSION['dungeon']['goal'] = $json['goals'][0][$type][$rand_key]['goal'];
		
			echo $_SESSION['dungeon']['goal'];
			}else{
				error_modal('ERROR: Invalid dungeon type selected','Your dungeon type selection is invalid, please correct and try again.');
			}
		}
}

// Generate the Introduction of the adventure
function introduction(){
	$str = file_get_contents('json/introductions.json');
	$json = json_decode($str, true); // decode the JSON into an associative array
		if ($json === null
		&& json_last_error() !== JSON_ERROR_NONE) {
			error_modal('ERROR: Invalid introductions.json','Your introductions.json file is invalid, please correct and try again.<br>[See <a href="http://jsonlint.com/" target="_blank">http://jsonlint.com/</a>]');
		}else{
			//Seed Random Number Generator used by PHP to control output
			//srand($GLOBALS['seed']);
			$rand_key = array_rand($json['introductions']);
			$_SESSION['dungeon']['intro'] = $json['introductions'][$rand_key]['intro'];
			echo $_SESSION['dungeon']['intro'];
		}
}

function rand_dungeon_starting_area($chamber_id){
	// Generate the random starting area for the adventure
	$str = file_get_contents('json/dungeon_starting_areas.json');
	$json = json_decode($str, true); // decode the JSON into an associative array
		if ($json === null
		&& json_last_error() !== JSON_ERROR_NONE) {
			error_modal('ERROR: Invalid dungeon_starting_areas.json','Your dungeon_starting_areas.json file is invalid, please correct and try again.<br>[See <a href="http://jsonlint.com/" target="_blank">http://jsonlint.com/</a>]');
		}else{
			//Seed Random Number Generator used by PHP to control output
			//srand($GLOBALS['seed']);
			$rand_key = array_rand($json['starting_areas'],1);
			$dungeon[$chamber_id] =  $json['starting_areas'][$rand_key];
			
			$_SESSION['dungeon'][$chamber_id] = $json['starting_areas'][$rand_key];
			//Generate Unique ID for this instance
			$dungeon['uid'] = uniqid();
			$_SESSION['dungeon'][$chamber_id]['uid'] = uniqid();
		}
}
function rand_dungeon_chamber($chamber_id){
	// Generate the random starting area for the adventure
	$str = file_get_contents('json/dungeon_chambers.json');
	$json = json_decode($str, true); // decode the JSON into an associative array
		if ($json === null
		&& json_last_error() !== JSON_ERROR_NONE) {
			error_modal('ERROR: Invalid dungeon_chambers.json','Your dungeon_chambers.json file is invalid, please correct and try again.<br>[See <a href="http://jsonlint.com/" target="_blank">http://jsonlint.com/</a>]');
		}else{
			//Seed Random Number Generator used by PHP to control output
			//srand($GLOBALS['seed']);
			$rand_key = array_rand($json['chambers'][0]['lair'],1);
			$dungeon[$chamber_id] =  $json['chambers'][0]['lair'][$rand_key];
			
			$_SESSION['dungeon'][$chamber_id] = $json['chambers'][0]['lair'][$rand_key];
			//Generate Unique ID for this instance
			$dungeon['uid'] = uniqid();
			$_SESSION['dungeon'][$chamber_id]['uid'] = uniqid();
			ksort($_SESSION['dungeon'][$chamber_id]);
			$chamber = $_SESSION['dungeon'][$chamber_id];
			
			return $_SESSION['dungeon'];
		}
}

// Generate the random dungeon passage for the adventure
function rand_dungeon_passage($uid,$direction){
	//pg. 290 5e DMG - Passage width can be determined randomly as well.
	
	$str = file_get_contents('json/dungeon_passages.json');
	$json = json_decode($str, true); // decode the JSON into an associative array
		if ($json === null
		&& json_last_error() !== JSON_ERROR_NONE) {
			error_modal('ERROR: Invalid dungeon_starting_areas.json','Your dungeon_starting_areas.json file is invalid, please correct and try again.<br>[See <a href="http://jsonlint.com/" target="_blank">http://jsonlint.com/</a>]');
		}else{
			//Seed Random Number Generator used by PHP to control output
			$rand_key = array_rand($json['passage'],1);
			$passage = $json['passage'][$rand_key];
			//Secret Door?
			//if there is a chance of a secret door - Perception DC 20 or 30
			if($json['passage'][$rand_key]['secret_chance']){
				$chance = rand($json['passage'][$rand_key]['secret_chance'],100);
				$passage['secret']['chance'] = $chance;
				if(intval($chance) <= intval($json['passage'][$rand_key]['secret_chance'])){
					//Success Door Found!
					$passage['secret']['description'] = "There is a Secret Door to the South.";
				}{
					//Theres no door here!
					$passage['secret']['description'] = "No hidden doors found.";
				}
			}
			//Generate Unique ID for this instance
			$passage['uid'] = uniqid();
			//Pass FROM UID value
			if(isset($uid)){
				$passage['from_uid'] = $uid;
			}
			//This needs to change to the location being generated for and not fixed to starting area
			//Using $uid provided, append to array at that point
			
			//FIND $uid and append to that part of the array
			//$_SESSION['dungeon']['starting_area'][$direction]['passage'] = $passage;
			return $passage;
		}	
}

function door_check($passage,$direction){
	$door = [];
	$doors 	= explode(',',$passage['doors']);
		if (in_array("N", $doors))
		{	$door = rand_dungeon_door($passage,$direction,'N');	}
		if (in_array("S", $doors))
		{	$door = rand_dungeon_door($passage,$direction,'S');	}  
		if (in_array("E", $doors))
		{	$door = rand_dungeon_door($passage,$direction,'E');	}  	  
		if (in_array("W", $doors))
		{	$door = rand_dungeon_door($passage,$direction,'W');	}
		return $door;
}
			
			
// Generate the random dungeon door for the adventure
function rand_dungeon_door($passage,$passage_direction,$door_direction){
	$door = [];
	$str = file_get_contents('json/dungeon_doors.json');
	$json = json_decode($str, true); // decode the JSON into an associative array
		if ($json === null
		&& json_last_error() !== JSON_ERROR_NONE) {
			error_modal('ERROR: Invalid dungeon_starting_areas.json','Your dungeon_starting_areas.json file is invalid, please correct and try again.<br>[See <a href="http://jsonlint.com/" target="_blank">http://jsonlint.com/</a>]');
		}else{
			//Seed Random Number Generator used by PHP to control output
			//srand($GLOBALS['seed']);
			$rand_key = array_rand($json['door'],1);
			$door = $json['door'][$rand_key];
			//Generate Unique ID for this instance
			$door['uid'] = uniqid();
			$door['direction'] = $door_direction;
			return $door;
		}
}

// Generate the random chamber purpose for the adventure
function rand_dungeon_beyond_door(){
	$beyond = ["passage","passage","passage","passage","passage","passage","passage","chamber","chamber","chamber","chamber","chamber","stairs"];
	$rand_key = array_rand($beyond, 1);
	return $beyond[$rand_key];
}

// Generate the random chamber purpose for the adventure
function rand_dungeon_chamber_purpose($type,$chamber_id){
	$purpose = [];
	$str = file_get_contents('json/dungeon_chamber_purpose.json');
	$json = json_decode($str, true); // decode the JSON into an associative array
		if ($json === null
		&& json_last_error() !== JSON_ERROR_NONE) {
			error_modal('ERROR: Invalid dungeon_chamber_purpose.json','Your dungeon_chamber_purpose.json file is invalid, please correct and try again.<br>[See <a href="http://jsonlint.com/" target="_blank">http://jsonlint.com/</a>]');
		}else{
			$rand_key = array_rand($json['purpose'][0][$type],1);
			$purpose = $json['purpose'][0][$type][$rand_key]['purpose'];
			$_SESSION['dungeon'][$chamber_id]['purpose'] = $purpose;
			return $purpose;
		}
}

// Generate the chamber state
function rand_dungeon_chamber_state($type,$chamber_id){
	$state = [];
	$str = file_get_contents('json/dungeon_chamber_state.json');
	$json = json_decode($str, true); // decode the JSON into an associative array
		if ($json === null
		&& json_last_error() !== JSON_ERROR_NONE) {
			error_modal('ERROR: Invalid dungeon_chamber_state.json','Your dungeon_chamber_state.json file is invalid, please correct and try again.<br>[See <a href="http://jsonlint.com/" target="_blank">http://jsonlint.com/</a>]');
		}else{
			$rand_key = array_rand($json['states'][0][$type],1);
			$state = $json['states'][0][$type][$rand_key]['state'];
			
			$_SESSION['dungeon'][$chamber_id]['state'] = $state;
			ksort($_SESSION['dungeon'][$chamber_id]);
			return $state;
		}	
}
// Generate the dungeon chamber contents
function rand_dungeon_chamber_contents($type,$chamber_id){
	$contents = [];
	$str = file_get_contents('json/dungeon_chamber_contents.json');
	$json = json_decode($str, true); // decode the JSON into an associative array
		if ($json === null
		&& json_last_error() !== JSON_ERROR_NONE) {
			error_modal('ERROR: Invalid dungeon_chamber_contents.json','Your dungeon_chamber_contents.json file is invalid, please correct and try again.<br>[See <a href="http://jsonlint.com/" target="_blank">http://jsonlint.com/</a>]');
		}else{
			$rand_key = array_rand($json['contents'][0][$type],1);
			$content = $json['contents'][0][$type][$rand_key];
			
			$_SESSION['dungeon'][$chamber_id]['content'] = $content;
			ksort($_SESSION['dungeon'][$chamber_id]);
			return $content;
		}		
}
// Generate the dungeon chamber monsters
function add_monster($type,$chamber_id){
	$monsters = [];
	$str = file_get_contents('json/dungeon_monsters.json');
	$json = json_decode($str, true); // decode the JSON into an associative array
		if ($json === null
		&& json_last_error() !== JSON_ERROR_NONE) {
			error_modal('ERROR: Invalid dungeon_monsters.json','Your dungeon_monsters.json file is invalid, please correct and try again.<br>[See <a href="http://jsonlint.com/" target="_blank">http://jsonlint.com/</a>]');
		}else{
			$rand_key = array_rand($json['monsters'][0][$type],1);
			$monster = $json['monsters'][0][$type][$rand_key];
			
			$_SESSION['dungeon'][$chamber_id]['monster'] = $monster;
			ksort($_SESSION['dungeon'][$chamber_id]);
			return $monster;
		}		
}
//Handle Errors
function error_modal($title,$message){
	echo '<div class="modal fade" tabindex="-1" role="dialog" id="myModal">
	  <div class="modal-dialog">
		<div class="modal-content">
		  <div class="modal-header alert-danger">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h4 class="modal-title">';
			echo $title;
			echo '</h4></div><div class="modal-body">';
			echo $message;
		  echo '</div><div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Close</button></div></div></div></div>
		  <script>$("#myModal").modal();</script>';
}


function search($arr, $key)
{
	$ritit = new RecursiveIteratorIterator(new RecursiveArrayIterator($arr));
	$results = array();
	foreach ($ritit as $leafValue) {
	$path = array();
	foreach (range(0, $ritit->getDepth()) as $depth) {
		$path[] = $ritit->getSubIterator($depth)->key();
	}
		$results[] = join('_', $path);
	}
	array_pop($path);
	return $path;
}


function insert_into_array($path,$a2,$label)
{
	$dest = &$_SESSION['dungeon'];
	foreach($path as $pathSegment) {
		$dest = &$dest[$pathSegment];
	}
	$dest[$label] = $a2;
	return true;
}
?>