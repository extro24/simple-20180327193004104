<?php
	include 'includes/system.php';
	
	$ajax=new Ajax();
	//cleanPostVars(); //clean psot variables from SQLInjection includes/functions.php
	//sleep(1);
	$output=array(); // output contains arrays of $data["msg"]	
	$bigsplitter='~^*^~'; // to split between commands			
	$splitter='(._._.)'; // to split between commands			
	$equal='"_TMR_"'; // to split between commands				
	
	// include ajax functions file by action variable
	$action=p("action"); // read post var ... all $_POST vars are cleaned with cleanPostVars(); found in includes/functions.php
	// get action prefix (PREFIX_...)
		if ($prepos=strpos($action,'_')){
			$prefix=substr($action,0,$prepos); 	// get prefix to include the file
			$act=substr($action,$prepos+1);		// set act
		}
		//echo $prefix;
		include 'includes/ajax/'.$prefix.'.php';
	
	$ajax->sendData();
?>