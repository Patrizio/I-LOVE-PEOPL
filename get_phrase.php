<?

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

$latest_id = $_GET['id'];
$action = $_GET['action'];

// connect to DB using ADOdb 
include('./adodb/adodb.inc.php');
$DB = NewADOConnection('mysql');
$DB->Connect($server, $user, $pwd, $db);

//determine highest ID from db
if ( $action == 'getLastPosts') {
	$value = $DB->GetOne("SELECT max( id ) FROM loves");
}

// data retrieval
$rs = $DB->Execute("select * from loves where id <".$latest_id." ORDER BY id DESC limit 10");

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
	print '</div>';
}	 	
?>
