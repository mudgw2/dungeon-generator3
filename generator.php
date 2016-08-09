<?php 

	$max_iterations = 10;
	$i 		= 0;
	$type 	= 'lair';
	$level 	= 1;
	$age 	= 500;
	$tmp_from_uid = 0;
	$BuildQueue = [];
	//difficulty
	// 1 = easy (CR is .5x of party)
	// 2 = normal (CR matches party)
	// 3 = hard (CR is 1.5x the party)
	$difficulty = 1;
	
	
	introduction();
	
	goal(1,'dungeon');
	
	// LOOP through generator until complete or max iterations is met.
while($i < $max_iterations){
	//var_dump($BuildQueue);
	if($i == 0){
		// First Iteration generates starting area, further iteration should look for doors and passages which have no chambers and complete them
		$chamber_id = 'starting_area';
		$dungeon 	= rand_dungeon_starting_area($chamber_id); 
	}else{
		$chamber_id = uniqid();
		$dungeon 	= rand_dungeon_chamber($chamber_id,$tmp_from_uid);
		rand_dungeon_chamber_purpose($type,$chamber_id);
		rand_dungeon_chamber_state($type,$chamber_id);
		$content = rand_dungeon_chamber_contents($type,$chamber_id);
		if($content['monster']){
			$monsters = add_monster($type,$chamber_id,$level,$difficulty);
			//var_dump($monsters);
		}
		//Is this chamber connected to a door or passage?
		if(isset($tmp_from_uid) && $tmp_from_uid > 0){
			
			$tmp_from_uid = 0;
		}

		
		//Loop through Queue, generating doors/stairs/passages/chambers setting uid provided to from_uid
		
		//set path to location of uid

		//echo "<br>working on: ";

		 if($BuildQueue[0]['type'] == 'chamber'){
			$chamber_id 	= uniqid();
			$_SESSION['dungeon'][$chamber_id]['from_uid'] = $BuildQueue[0]['uid'];
			$dungeon 		= rand_dungeon_chamber($chamber_id,$BuildQueue[0]['uid']);
			rand_dungeon_chamber_purpose($type,$chamber_id);
			rand_dungeon_chamber_state($type,$chamber_id);
			$content 		= rand_dungeon_chamber_contents($type,$chamber_id);
			if($content['monster']){
				$monsters = add_monster($type,$chamber_id,$level,$difficulty);
			}
			//generate passages and doors
			

		 }
		 //var_dump($_SESSION['dungeon'][$chamber_id]['uid']);
		 
		 
		if($BuildQueue[0]['type'] == 'door'){
				//Search for array path to current location for passage
				$path			= [];
				$path 			= search($_SESSION['dungeon'], $BuildQueue[0]['uid']);
				$tmp_passage 	= rand_dungeon_passage($BuildQueue[0]['uid'],$BuildQueue[0]['type']);
				ksort($tmp_passage);
				insert_into_array($path,$tmp_passage,$label);
		}
		if($BuildQueue[0]['type'] == 'passage'){
				//Search for array path to current location for passage
				$path			= [];
				$path 			= search($_SESSION['dungeon'], $BuildQueue[0]['uid']);
				$tmp_passage 	= rand_dungeon_passage($BuildQueue[0]['uid'],$BuildQueue[0]['type']);
				ksort($tmp_passage);
				
				//insert_into_array($path,$tmp_passage,$BuildQueue[0]['type']);
		}
		//generate new random content
		
		//set from_uid
		
		//add to array
	
		//remove UID from top of array
		array_shift($BuildQueue);
	
	}
		$passages 	= [];
		$passages 	= explode(',',$_SESSION['dungeon'][$chamber_id]['passages']);
		$path		= [];
		$path 		= search($_SESSION['dungeon'],$_SESSION['dungeon'][$chamber_id]['uid']);
					
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
								//Set from_uid for next chamber connected to this door
								$tmp_from_uid = $door['uid'];
							}
							if($check == 'chamber'){
								//Set from_uid for next chamber connected to this door
								//echo "ADD CHAMBER TO QUEUE<br>";
								$BuildQueue2 = [];
								$BuildQueue2['type'] = $check;
								$BuildQueue2['uid'] = $door['uid'];
								
								array_push($BuildQueue,$BuildQueue2);
							}
						}else{
							//passage UID
							//echo "ADD PASSAGE TO QUEUE<br>";
								$BuildQueue2 = [];
								$BuildQueue2['type'] = 'passage';
								$BuildQueue2['uid'] = $passage['uid'];
								
								array_push($BuildQueue,$BuildQueue2);
						}

					}
				}
			// DOOR GENERATION
			$BuildQueue = 	dungeon_door($chamber_id,$direction,$BuildQueue);
$i++;
}


//Modify resident monsters for jobs/roles/leadership 
$fp = fopen('dungeon.json', 'w');
fwrite($fp, json_encode($_SESSION['dungeon']));
fclose($fp);
	
	
	
	
	
	
?>