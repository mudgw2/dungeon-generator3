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
	$level = 1;
	$age = 500;
	//difficulty
	// 1 = easy (CR is .5x of party)
	// 2 = normal (CR matches party)
	// 3 = hard (CR is 1.5x the party)
	$difficulty = 1;
?>
<body id="page-top">
<?php 
//Generate Intro 222
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
		$content = rand_dungeon_chamber_contents($type,$chamber_id);
		if($content['monster']){
			$monsters = add_monster($type,$chamber_id,$level,$difficulty);
			var_dump($monsters);
		}
		
	}
//var_dump($dungeon);
		$passages = [];
		$passages 	= explode(',',$_SESSION['dungeon'][$chamber_id]['passages']);
		$path = [];
		$path = search($_SESSION['dungeon'],$_SESSION['dungeon'][$chamber_id]['uid']);
					
				//IF the chamber has passages loop through each one and generate
				if (count(array_filter($passages)) != 0){
				foreach($passages as $direction)
				{
					$passage = rand_dungeon_passage($_SESSION['dungeon'][$chamber_id]['uid'],$direction);
					//Add passage into array
					insert_into_array($path,$passage,$direction);
					
					$passage_path = [];
					$passage_path = search($_SESSION['dungeon'],$passage['uid']);
					
					//Check passage for doors
					$door = door_check($passage,$direction);
					
					if(isset($door['uid'])){
						//Add door into array
						insert_into_array($passage_path,$door,$door['direction']);
						
						//Check for whats beyond the door
						$check = rand_dungeon_beyond_door($door['uid']);
						if($check == 'passage'){						
							//Search for array path to current location for passage
							//Generate new passage
							$tmp_passage = rand_dungeon_passage($door['uid'],$check);
							//Insert passage into array
							array_push($path,$door['direction']);
							ksort($tmp_passage);
							insert_into_array($path,$tmp_passage,$check);
						}
					}
					// PASSAGE CHECK
					//$tmp_passages = [];
					//$tmp_passages 	= explode(',',$tmp_passage['passages']);
					//if ($tmp_passages){foreach($tmp_passages as $direction){rand_dungeon_passage($_SESSION['dungeon'][$chamber_id ]['uid'],$direction);}}
				}}

			// DOOR GENERATION
				//Check for each door and generate those
				/*
				$doors = [];
				$doors	= explode(',',$_SESSION['dungeon'][$chamber_id]['doors']);
		
				if ($doors)
				{
					foreach($doors as $direction){
					
						$label = $door;
						$door = door_check($_SESSION['dungeon'][$chamber_id],$direction);
						if($door){
							$check = rand_dungeon_beyond_door($door['uid']);
							if($check == 'passage'){						
								//Search for array path to current location for passage
								$path = search($_SESSION['dungeon'], $door['uid']);
								//Generate new passage
								$tmp_passage = rand_dungeon_passage($door['uid'],$check);
								//Insert passage into array
								ksort($tmp_passage);
								insert_into_array($path,$door,$direction);
								//insert_into_array($path,$tmp_passage,$label);
							}
							if($check == 'chamber'){						
								$chamber_id = uniqid();
								$dungeon 	= rand_dungeon_chamber($chamber_id);
								rand_dungeon_chamber_purpose($type,$chamber_id);
								rand_dungeon_chamber_state($type,$chamber_id);
								rand_dungeon_chamber_contents($type,$chamber_id);
							}					
							if($check == 'stairs'){						
								echo "stairs<br>";
							}					
						}				
					}
				}
				*/
 
$i++;
}


//Modify resident monsters for jobs/roles/leadership 







echo "<HR/>";

$fp = fopen('dungeon.json', 'w');
fwrite($fp, json_encode($_SESSION['dungeon']));
fclose($fp);
?>
</body>
</html>
