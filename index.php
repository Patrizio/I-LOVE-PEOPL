<?php

require "set.php";

/**
 * Function to calculate date or time difference.
 * 
 * Function to calculate date or time difference. Returns an array or
 * false on error.
 *
 * @author       J de Silva                             <giddomains@gmail.com>
 * @copyright    Copyright &copy; 2005, J de Silva
 * @link         http://www.gidnetwork.com/b-16.html    Get the date / time difference with PHP
 * @param        string                                 $start
 * @param        string                                 $end
 * @return       array
 */
function get_time_difference( $start) {
    $uts['start']      =    strtotime( $start );
    $uts['end']        =    time();
    if( $uts['start']!==-1 && $uts['end']!==-1 )
    {
        if( $uts['end'] >= $uts['start'] )
        {
            $diff    =    $uts['end'] - $uts['start'];
            if( $days=intval((floor($diff/86400))) )
                $diff = $diff % 86400;
            if( $hours=intval((floor($diff/3600))) )
                $diff = $diff % 3600;
            if( $minutes=intval((floor($diff/60))) )
                $diff = $diff % 60;
            $diff    =    intval( $diff );
			
            $result = array('days'=>$days, 'hours'=>$hours, 'minutes'=>$minutes, 'seconds'=>$diff);
						
			// return a single string
			if ($result['days'] > 0): 
				$daytail = ( $result['days'] == 1) ? " day ago" : " days ago" ;
				$difference = $result['days'].$daytail;
			elseif ($result['hours'] > 0):
				$hourtail = ( $result['hours'] == 1) ? " hour ago" : " hours ago" ;
				$difference = $result['hours'].$hourtail; 			
			else: 
				if ($result['minutes'] > 0) {
					$minutetail = ( $result['minutes'] == 1) ? " minute ago" : " minutes ago" ;
					$difference = $result['minutes'].$minutetail;				
				} else  {
					$sectail = ( $result['seconds'] == 1) ? " second ago" : " seconds ago" ;
					$difference = $result['seconds'].$sectail; 									
				}
			endif;
			
			return( $difference );
        }
        else
        {
            trigger_error( "Ending date/time is earlier than the start date/time", E_USER_WARNING );
        }
    }
    else {
        trigger_error( "Invalid date/time data detected", E_USER_WARNING );
    }
    return( false );
}

/**
 * Function to get the country of origin of an IP-address
 * 
 * Function to get the country of an IP-address. Returns an image name (nl.png, uk.png
 * xx.png on undefined.
 *
 * @author		P Beeker                             <patrick.beeker@gmail.com>
 * @copyleft  	Copyright &copy; 2005, J de Silva
 * @param       string                                 $start
 * @param       string                                 $end
 * @return      array
 */
function getCountryFlag ( $longIpValue ) {	
	$ip_address = long2ip( $longIpValue );
	$url = "http://api.wipmania.com/".$ip_address; // API to consult
	$ch = curl_init() or die(curl_error());
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$data1=curl_exec($ch) or die(curl_error());
	echo curl_error($ch);
	curl_close($ch);

	if (!$data1) $data1="XX"; // if empty set default flag
	
	$country_img = strtolower($data1).".png"; // make lowercase and add .PNG
	
	return ( $country_img );
}


// connect to DB using ADOdb 
include('./adodb/adodb.inc.php');
$DB = NewADOConnection('mysql');
$DB->Connect($server, $user, $pwd, $db);

// get user's IP
$user_ip = $_SERVER['REMOTE_ADDR']; 

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
      "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title>I &#9829; PEOPLE WHO...</title>
	<meta name="author" content="Patrizio" />
	<meta name="description" content="I LOVE PEOPLE WHO - what do you appreciate in humanity?" />
	<meta name="keywords" content="i love people, i love people who, crowdsourcing, user generated" />
	<meta name="robots" content="index, follow" />
	<link rel="shortcut icon" href="/favicon.ico" />
	<link rel="icon" href="/favicon.ico" type="image/x-icon">
	
	<link rel="stylesheet" href="style.css" type="text/css"  />
	<script type="text/javascript" src="./js/jquery-1.3.2.min.js"></script>
	<script type="text/javascript" src="./js/jquery.editinplace.js"></script>
	<script type="text/javascript" src="./js/editinplace.js"></script>	
	<script type="text/javascript">
	$(document).ready(function(){
						
		function lastAddedLiveFunc() 
		{ 
			$('div#lastPostsLoader').html('<img src="images/bigLoader.gif">');
			$.post("get_phrase.php?action=getLastPosts&id="+$(".rule:last").attr("id"),

			function(data){
				if (data != "") {
				$(".rule:last").after(data);			
				}
				$('div#lastPostsLoader').empty();
			});

		};  
		
		$(window).scroll(function(){
			if  ($(window).scrollTop() == $(document).height() - $(window).height()){
			   lastAddedLiveFunc();
			}
		}); 
	});	
	</script>	
	
	<script>
		var user_ip = "<?= $user_ip ?>";
	</script>
</head>

<body>
 
	
	<div id="header">				
		<div id="suggest_box">								
				Have fun!&nbsp;|&nbsp;<a href="contact.php">contact</a>&nbsp;|&nbsp;<a href="about.php">about</a></span>			
		</div>		
		<h1>i <span class="heartBig">&hearts;</span> people who... </h1>
		<div id="invite">		
			<div class="phrase">{&nbsp;add your phrase here&nbsp;}</div>
		</div>
	</div>	
	<div id="body">
	<?
			// data retrieval
			$rs = $DB->Execute("select * from loves ORDER BY id DESC LIMIT 25");
			while ($array = $rs->FetchRow()) {

				//assign values from current row	
				$id			= $array['id'];
				$phrase 	= $array['phrase'];
				$timestamp 	= $array['stamp'];
				$ip_value	= $array['ip'];

				$countryFlag = getCountryFlag( $ip_value ); 

				// get the stamp when it was added to DB
				$diff = get_time_difference($timestamp);
				
				print '<div class="rule" id="'.$id.'">';
				print '<span class="heart">&hearts;&nbsp;&nbsp;</span>';
				print '<span class="writing">'.htmlentities(strip_tags($phrase)).'&nbsp;</span>';
				print '<span class="stamp"><img src="/flags/'.$countryFlag.'" />&nbsp;'.$diff.'</span>';
				//print '<span class="stamp">'.$diff.'</span>';
				print '</div>';
			}	 
		?>	
	</div>	
	
	<div id="lastPostsLoader">

<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
try {
var pageTracker = _gat._getTracker("UA-2214850-4");
pageTracker._trackPageview();
} catch(err) {}</script>
</body>
</html>


