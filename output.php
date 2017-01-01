<?php
define('__ROOT__', dirname(dirname(__FILE__))); 
require_once(__ROOT__.'teams/input.php'); 

require_once(__ROOT__.'teams/addcats.php'); 


$output = array();
foreach ($values as $row){
	if (isset ($row[1])) {
		$output[] = $GLOBALS['shuffled']['events'][$row[1]]['competitors'];
	}
	else
		$output[] = array();
}

// INPUT RESULTS BACK INTO SHEETS
		
$range = 'events!D3:F';
$valueInputOption = "raw"; 
$params = array('valueInputOption' => $valueInputOption);		
$body = new Google_Service_Sheets_ValueRange(array('values' => $output));
$result = $service->spreadsheets_values->update($spreadsheetId, $range,$body, $params);	
		


$output = array();
foreach ($values as $row){
	if (isset ($row[1])) {
		$output[] = $GLOBALS['cats']['events'][$row[1]]['competitors'];
	}
	else
		$output[] = array();
}

// INPUT RESULTS BACK INTO SHEETS
		
$range = 'events!H3:J';
$valueInputOption = "raw"; 
$params = array('valueInputOption' => $valueInputOption);		
$body = new Google_Service_Sheets_ValueRange(array('values' => $output));
$result = $service->spreadsheets_values->update($spreadsheetId, $range,$body, $params);	



// STATS
$GLOBALS['shuffled']['memedevents'] = array(); // events that are missing competitiors :(
$GLOBALS['cats']['memedevents'] = array();
$GLOBALS['ppl']['thememed']= array(); // people who got only 1 of their requested events woops
foreach ($GLOBALS['events'] as $event){  /// add memed events to memedevents
	if (isEventOpen ($event, $GLOBALS['shuffled']))
		$GLOBALS['shuffled']['memedevents'][] = $event['name'];
	if (isEventOpen ($event, $GLOBALS['cats']))
		$GLOBALS['cats']['memedevents'][] = $event['name'];
}


foreach ($GLOBALS['ppl'] as $person){ 
	if ($person['numevents'] == 1){
		echo 'someone memed ' . $person['name']; 
		$GLOBALS['ppl']['thememed'][] = $person['name'];
	}
}
?>
