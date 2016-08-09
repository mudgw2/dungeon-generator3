<!doctype html>
<html class="no-js" lang="en">
<?php
	include "header.php";
	include "functions.php";
	include "generator.php";
?>
<body id="page-top">
<div class="container">
<?php 
//Generate Intro
echo "<h2>Introduction</h2>";
echo $_SESSION['dungeon']['intro'];
echo "<br>";
//Generate Goal
echo "<h2>Goal</h2>";
echo $_SESSION['dungeon']['goal'];
?>

<div class="row" id="view_area">
<div class="panel panel-primary">
  <div class="panel-heading">
    <h3 class="panel-title"> 
	<?php 
		$keys = array_keys($_SESSION["dungeon"]);
		echo $keys[2];
	?></h3>
  </div>
  <div class="panel-body">
	  <div class="col-md-6">
	  <?php echo $_SESSION['dungeon']['starting_area']['desc'];?>
	  </div>
	  <div class="col-md-6">

	  </div>
	  <div class="col-md-12">
	  <strong>Passages</strong><br>
		<?php    
		// var_dump($directions);
		foreach ($directions as $key => $value) {
			if(isset($_SESSION['dungeon']['starting_area'][$key]['desc'])){
			echo "<br><strong>{$value}:</strong>&nbsp;";
			echo $_SESSION['dungeon']['starting_area'][$key]['desc'];
				//Check for doors
				foreach ($directions as $door_key => $door_value) {
					if(isset($_SESSION['dungeon']['starting_area'][$key][$door_key])){
						//find matching from_uid in session
						$path		= [];
						$path 		= search($_SESSION['dungeon'],$_SESSION['dungeon']['starting_area'][$key][$door_key]['uid']);
						//var_dump($path);
						echo "<form id='move_form_{$_SESSION['dungeon']['starting_area'][$key][$door_key]['uid']}' >
						<input type='hidden' name='target_uid' value='{$path[0]}'>
						<input type='submit' class='btn btn-default' value='Door {$door_value}'>{$path[0]}</form>";
					}
				}
			}
		}
		?>
	  </div>
  </div>
</div>
</div>




<div class="row">
<button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
  View SESSION
</button>
<div class="collapse" id="collapseExample">
  <div class="well">
    <?php var_dump($_SESSION['dungeon']); ?>
  </div>
</div>
</div>



</div>
<script src="js/main.js"></script>
</body>
</html>
