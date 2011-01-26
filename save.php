<?php

	require "set.php";
	
	
	// Original PHP code by Chirp Internet: www.chirp.com.au // Please acknowledge use of this code by including this header. 
	function myTruncate($string, $limit, $break=" ", $pad="...") {
		// return with no change if string is shorter than $limit  
		if(strlen($string) <= $limit) return $string; 
			$string = substr($string, 0, $limit); 
			if(false !== ($breakpoint = strrpos($string, $break))) { 
				$string = substr($string, 0, $breakpoint); 
			} 
			return $string . $pad; 
	}
		
	$table = "loves";
	
	// connect to DB using ADOdb 
	include('./adodb/adodb.inc.php');
	$DB = NewADOConnection('mysql');
	$DB->Connect($server, $user, $pwd, $db);
		
	// serialize variables
	$record["phrase"] = myTruncate($_POST["update_value"], 140);
	$record["created"] = date( 'Y-m-d H:i:s', time());
	$record["ip"] = $_POST["ip"];			
	$ip_calculated = ip2long($record["ip"]); //hash the IP address
		
	$DB->query("SET NAMES 'utf8'"); // ensure correct characterset when inserting in db
	
	if ($_POST["update_value"]) {
		// DB insertion
		$sql = "insert into ".$table." (id, phrase, stamp, ip) ";
		$sql .= "values ('NULL','".$record["phrase"]."','".$record["created"]."','".$ip_calculated."')";
			
		if ($DB->Execute($sql) === false) {
			print 'error inserting: '.$DB->ErrorMsg().'<BR>';
		} else {
			// return values to webpage
			print ($record["phrase"]);
		}		
	} else {
		print ("Error: Sorry I don't like this request.");
	}
	
?>