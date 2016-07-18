<!doctype html>
<html class="no-js" lang="en">
<?php 
//Clear SESSION scope
	session_start();
	session_unset();
	session_destroy();
	session_write_close();
	setcookie(session_name(),'',0,'/');
	session_regenerate_id(true);
	
	include "header.php";
	include "functions.php";
	$max_iterations = 10;
	$i = 0;
	$type = 'lair';
?>
<body id="page-top">
<?php 
//Generate Intro
echo "<h2>Introduction</h2>";
echo introduction();
echo "<br>";
//Generate Goal
echo "<h2>Goal</h2>";
echo goal(1,'dungeon');

// LOOP through generator until complete or max iterations is met.
while($i < $max_iterations){
	if($i == 0){
		// First Iteration generates starting area, further iteration should look for doors and passages which have no chambers and complete them
		$chamber_id = 'starting_area';
		$dungeon 	= rand_dungeon_starting_area($chamber_id); 
	}else{
		$chamber_id = uniqid();
		$dungeon 	= rand_dungeon_chamber($chamber_id);
		rand_dungeon_chamber_purpose($type,$chamber_id);
		rand_dungeon_chamber_state($type,$chamber_id);
		rand_dungeon_chamber_contents($type,$chamber_id);
	}
//var_dump($dungeon);

			// PASSAGE AND DOOR GENERATION
				//Check for each passage and generate those
				$passages 	= explode(',',$_SESSION['dungeon'][$chamber_id]['passages']);

				if (in_array("N", $passages))
				  {
					$direction = "N";
					$label = 'N';
					$passage = rand_dungeon_passage($_SESSION['dungeon'][$chamber_id ]['uid'],$direction);

					$path = search($_SESSION['dungeon'], $passage['uid'] );
					insert_into_array($path,$passage,$label);
					
					// DOOR CHECK
					// CHECK: DOES THIS HANDLE MULTIPLE DOORS? SHOULD RETURN ARRAY OF door_uid then should loop through the array
					$door = door_check($passage,$direction);
					
					//var_dump($door);
					if(isset($door['uid'])){
						$path = '';
						
						$path = search($_SESSION['dungeon'],$door['uid']);
						insert_into_array($path,$door,$door['direction']);
						
						$check = rand_dungeon_beyond_door($door['uid']);
						if($check = 'passage'){						
							//Search for array path to current location for passage
							//Generate new passage
							$tmp_passage = rand_dungeon_passage($door['uid'],$check);
							//Insert passage into array
							$label = 'passage';
							array_push($path,$door['direction']);
							ksort($tmp_passage);
							insert_into_array($path,$tmp_passage,$label);
						}
					}
				
					// PASSAGE CHECK
					$tmp_passages = [];
					$tmp_passages 	= explode(',',$tmp_passage['passages']);
					if (in_array("N", $tmp_passages)){
						$direction = "N";
						$label = 'N';
						rand_dungeon_passage($_SESSION['dungeon'][$chamber_id ]['uid'],$direction);						
					}
					if (in_array("S", $tmp_passages)){
						$direction = "S";
						$label = 'S';
						rand_dungeon_passage($_SESSION['dungeon'][$chamber_id ]['uid'],$direction);						
					}					
					if (in_array("E", $tmp_passages)){
						$direction = "E";
						$label = 'E';
						rand_dungeon_passage($_SESSION['dungeon'][$chamber_id ]['uid'],$direction);						
					}					
					if (in_array("W", $tmp_passages)){
						$direction = "W";
						$label = 'W';
						rand_dungeon_passage($_SESSION['dungeon'][$chamber_id ]['uid'],$direction);						
					}	
				  }
				if (in_array("S", $passages))
				  {
					$direction = "S";
					$label = 'S';
					$passage = rand_dungeon_passage($_SESSION['dungeon'][$chamber_id ]['uid'],$direction);
			
					//$path = search($_SESSION['dungeon'], $passage['uid'] );
					$_SESSION['dungeon'][$chamber_id ][$direction] = $passage;
					//insert_into_array($path,$passage,$label);
					
					// DOOR CHECK
					// CHECK: DOES THIS HANDLE MULTIPLE DOORS? SHOULD RETURN ARRAY OF door_uid then should loop through the array
					$door = door_check($passage,$direction);
					
					//var_dump($door);
					if(isset($door['uid'])){
						$path = '';
						
						$path = search($_SESSION['dungeon'],$door['uid']);
						insert_into_array($path,$door,$direction);
						
						$check = rand_dungeon_beyond_door($door['uid']);
						if($check = 'passage'){						
							//Search for array path to current location for passage
							//Generate new passage
							$tmp_passage = rand_dungeon_passage($door['uid'],$check);
							//Insert passage into array
							$label = 'passage';
							array_push($path,$direction);
							ksort($tmp_passage);
							insert_into_array($path,$tmp_passage,$label);
						}
					}
					
					// PASSAGE CHECK
					$tmp_passages 	= explode(',',$tmp_passage['passages']);
					if (in_array("N", $tmp_passages)){
						$direction = "N";
						$label = 'N';
						//rand_dungeon_passage($_SESSION['dungeon'][$chamber_id ]['uid'],$direction);						
					}
					if (in_array("S", $tmp_passages)){
						$direction = "S";
						$label = 'S';
						//rand_dungeon_passage($_SESSION['dungeon'][$chamber_id ]['uid'],$direction);						
					}					
					if (in_array("E", $tmp_passages)){
						$direction = "E";
						$label = 'E';
						//rand_dungeon_passage($_SESSION['dungeon'][$chamber_id ]['uid'],$direction);						
					}					
					if (in_array("W", $tmp_passages)){
						$direction = "W";
						$label = 'W';
						//rand_dungeon_passage($_SESSION['dungeon'][$chamber_id ]['uid'],$direction);						
					}					
					
				  }
				if (in_array("E", $passages))
				  {
					$direction = "E";
					$label = 'E';
					$passage = rand_dungeon_passage($_SESSION['dungeon'][$chamber_id ]['uid'],$direction);
					
					//$path = search($_SESSION['dungeon'], $passage['uid'] );
					$_SESSION['dungeon'][$chamber_id ][$direction] = $passage;
					//insert_into_array($path,$passage,$label);
	
					// DOOR CHECK
					// CHECK: DOES THIS HANDLE MULTIPLE DOORS? SHOULD RETURN ARRAY OF door_uid then should loop through the array	
					$door_uid = door_check($passage,$direction);
					if($door_uid){
						$check = rand_dungeon_beyond_door($door_uid);
						if($check = 'Passage'){						
						//Search for array path to current location for passage
							$path = search($_SESSION['dungeon'], $door_uid );
						//Generate new passage
						$tmp_passage = rand_dungeon_passage($door_uid,$check);
						//Insert passage into array
						$label = 'Passage';
						ksort($tmp_passage);
						insert_into_array($path,$tmp_passage,$label);
					}}
				  }
				if (in_array("W", $passages))
				  {
					$direction = "W";
					$label = 'W';
					$passage = rand_dungeon_passage($_SESSION['dungeon'][$chamber_id ]['uid'],$direction);
					
					//$path = search($_SESSION['dungeon'], $passage['uid'] );
					$_SESSION['dungeon'][$chamber_id ][$direction] = $passage;
					//insert_into_array($path,$passage,$label);

					// DOOR CHECK
					// CHECK: DOES THIS HANDLE MULTIPLE DOORS? SHOULD RETURN ARRAY OF door_uid then should loop through the array
					$door_uid = door_check($passage,$direction);
					if($door_uid){
						$check = rand_dungeon_beyond_door($door_uid);
						if($check = 'Passage'){						
						//Search for array path to current location for passage
							$path = search($_SESSION['dungeon'], $door_uid );
						//Generate new passage
						$tmp_passage = rand_dungeon_passage($door_uid,$check);
						//Insert passage into array
						$label = 'Passage';
						ksort($tmp_passage);
						insert_into_array($path,$tmp_passage,$label);
					}}
				  }
			
			// DOOR GENERATION
				//Check for each door and generate those
				$doors	= explode(',',$_SESSION['dungeon'][$chamber_id]['doors']);
				if (in_array("N", $doors))
				  {
					$direction = "N";
					$label = 'N';
					//$passage = rand_dungeon_passage($_SESSION['dungeon'][$chamber_id ]['uid'],$direction);
					
					$door_uid = door_check($passage,$direction);
					if($door_uid){
						$check = rand_dungeon_beyond_door($door_uid);
						echo $check;
						if($check = 'Passage'){						
						//Search for array path to current location for passage
							$path = search($_SESSION['dungeon'], $door_uid );
						//Generate new passage
						$tmp_passage = rand_dungeon_passage($door_uid,$check);
						//Insert passage into array
						$label = 'Passage';
						ksort($tmp_passage);
						insert_into_array($path,$tmp_passage,$label);
					}}				
				  }

$i++;
}




echo "<HR/>";

$fp = fopen('dungeon.json', 'w');
fwrite($fp, json_encode($_SESSION['dungeon']));
fclose($fp);
?>
</body>
</html>