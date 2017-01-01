

<?php

/*
// simple execution
$this->taskGulpRun()->run();

// run task 'clean' with --silent option
$this->taskGulpRun('clean')
     ->silent()
     ->run();
     
     */

// apitest.php
// by Karl Kranich - karl.kranich.org
// version 3.1 - edited query section
require_once realpath(dirname(__FILE__) . '/vendor/autoload.php');
include_once "google-api-php-client/examples/templates/base.php";
$client = new Google_Client();
/************************************************
  ATTENTION: Fill in these values, or make sure you
  have set the GOOGLE_APPLICATION_CREDENTIALS
  environment variable. You can get these credentials
  by creating a new Service Account in the
  API console. Be sure to store the key file
  somewhere you can get to it - though in shuffled
  operations you'd want to make sure it wasn't
  accessible from the webserver!
 ************************************************/
putenv("GOOGLE_APPLICATION_CREDENTIALS=service-account-credentials.json");
if ($credentials_file = checkServiceAccountCredentialsFile()) {
  // set the location manually
  $client->setAuthConfig($credentials_file);
} elseif (getenv('GOOGLE_APPLICATION_CREDENTIALS')) {
  // use the application default credentials
  $client->useApplicationDefaultCredentials();
} else {
  echo missingServiceAccountDetailsWarning();
  exit;
}
$client->setApplicationName("Sheets API Testing");
$client->setScopes(['https://www.googleapis.com/auth/drive','https://spreadsheets.google.com/feeds']);
// Some people have reported needing to use the following setAuthConfig command
// which requires the email address of your service account (you can get that from the json file)
// $client->setAuthConfig(["type" => "service_account", "client_email" => "my-service-account@developer.gserviceaccount.com"]);
// The file ID was copied from a URL while editing the sheet in Chrome
$fileId = '15byt2tfdaHmaEpdwd4UYGWs70Eaej8edkQ2dS8x4mIk';
// Access Token is used for Steps 2 and beyond
$tokenArray = $client->fetchAccessTokenWithAssertion();
$accessToken = $tokenArray["access_token"];
// Section 1: Uncomment to get file metadata with the drive service
// This is also the service that would be used to create a new spreadsheet file
$service = new Google_Service_Drive($client);
$results = $service->files->get($fileId);
// var_dump($results);
// Section 4: Uncomment to add a row to the sheet
// $url = "https://spreadsheets.google.com/feeds/list/$fileId/od6/private/full";
// $method = 'POST';
// $headers = ["Authorization" => "Bearer $accessToken", 'Content-Type' => 'application/atom+xml'];
// $postBody = '<entry xmlns="http://www.w3.org/2005/Atom" xmlns:gsx="http://schemas.google.com/spreadsheets/2006/extended"><gsx:gear>more gear</gsx:gear><gsx:quantity>44</gsx:quantity></entry>';
// $httpClient = new GuzzleHttp\Client(['headers' => $headers]);
// $resp = $httpClient->request($method, $url, ['body' => $postBody]);
// $body = $resp->getBody()->getContents();
// $code = $resp->getStatusCode();
// $reason = $resp->getReasonPhrase();
// echo "$code : $reason\n\n";
// echo "$body\n";
// Section 5: Uncomment to edit a row
// You'll need to get the etag and row ID, and send a PUT request to the edit URL
// $rowid = 'cre1l';                 // got this and the etag from the table data output from section 3
// $etag = 'NQ8SVE8fDSt7ImA.';
// $url = "https://spreadsheets.google.com/feeds/list/$fileId/od6/private/full/$rowid";
// $method = 'PUT';
// $headers = ["Authorization" => "Bearer $accessToken", 'Content-Type' => 'application/atom+xml', 'GData-Version' => '3.0'];
// $postBody = "<entry xmlns=\"http://www.w3.org/2005/Atom\" xmlns:gsx=\"http://schemas.google.com/spreadsheets/2006/extended\" xmlns:gd=\"http://schemas.google.com/g/2005\" gd:etag='&quot;$etag&quot;'><id>https://spreadsheets.google.com/feeds/list/$fileid/od6/$rowid</id><gsx:gear>phones</gsx:gear><gsx:quantity>6</gsx:quantity></entry>";
// $httpClient = new GuzzleHttp\Client(['headers' => $headers]);
// $resp = $httpClient->request($method, $url, ['body' => $postBody]);
// $body = $resp->getBody()->getContents();
// $code = $resp->getStatusCode();
// $reason = $resp->getReasonPhrase();
// echo "$code : $reason\n\n";
// echo "$body\n";
// ADD PEOPLE AND INFO FROM SIGNUPS SHEET
$service = new Google_Service_Sheets($client);
$spreadsheetId =   '1LhCT9KRfMrXinRyphcBn1jz3JIUh5LQSli9mQFmOc7w';
$range = 'signups!A1:D';
$response = $service->spreadsheets_values->get($spreadsheetId, $range);
$values = $response->getValues();
$GLOBALS['ppl'] = array(); // all people and info 
$GLOBALS['pool'] = array (); /// people list of names
$GLOBALS['events'] = array (); // all events and info
$schedule = array(); // time slots 
$countins = 0;  // total num event requests
$countevents = 0; // total num events
$GLOBALS['shuffled'] = array (); // TEAM SHUFFLED events and competitors
$GLOBALS['cats'] = array ();  // TEAM CATS events and competitors
$GLOBALS['shuffled']['roster'] = array(); // teams currently empty
$GLOBALS['cats']['roster'] = array();
// find index of columns of info (name, grade, events)
$Cfn = array_search ( 'First Name', $values[0]);
$Cln = array_search ( 'Last Name', $values[0]);
$Cgrade = array_search ( 'Grade', $values[0]);
$Cevents = array_search ( 'Events', $values[0]);
// add people and their info
for ($i = 1 ; $i < count ($values) ; $i++){
	$info = $values[$i];
	$person = array();
	$person ['name']= $info[$Cfn] . ' ' . $info[$Cln];
	$person ['grade'] = $info[$Cgrade];
	$person ['eventrequests'] = explode (", " , $info[$Cevents]); // a person's events is put into an array
	$person ['numrequests'] = count ($person ['eventrequests']);
	$person ['events'] = array();
	$person ['numevents'] = count ($person ['events']);
	
	$GLOBALS['ppl'][$info[$Cfn] . ' ' . $info[$Cln]] = $person; // key to a person's info is their name
	$GLOBALS['pool']['roster'][] = $person['name']; // everyone added to pool
}
// ADD EVENTS AND INFO FROM EVENTS SHEET
$range = 'events!A3:C';
$response = $service->spreadsheets_values->get($spreadsheetId, $range);
$values = $response->getValues();
$rownum = 3; 
foreach ($values as $row){
	if (isset ($row[1])) {
		$countevents++;
		$event = array();
		$event['time'] = $row[0]; // time slot
		$event['name'] = $row[1]; // event name
		if (!in_array ($event['time'], $schedule))   // add times to schedule
			$schedule[$event['time']] = null;
		$event['numpeopleperteam'] = $row[2]; // number of people who compete in such event (2 or 3)
		$event['competitors'] = array(); // event set with no competitors 
		$event['numcompetitors'] = count ($event['competitors']);
		$event['open'] = true;  // need to add competitors
		$event['row'] = $rownum; 
		$event['drop'] = FALSE;
	
		$GLOBALS['shuffled'] ['events'] [$event['name']] = $event;  // add event to global teams
		$GLOBALS['cats'] ['events'] [$event['name']] = $event;	
		
		
		
		// people who signued up for such event added to event's pool of avaliable competitors who requested the event
		$signups = array ();
		foreach ($GLOBALS['ppl'] as $person){
			if (in_array ( $event['name'], $person ['eventrequests'] )){
				//echo $person ['name'];
				$countins++;
				$signups [] = $person['name'];
			}
		}		
		
		$event['signups'] = $signups;
		$event['numsignups'] = count ($event['signups']);
		$event['pool'] = $event['signups'];
		$event['numpool'] = $event['numsignups'];
		
		
		$GLOBALS['events'][$event['name']] = $event; // add event including signups and pool to global events
	}
	$rownum++; 
}
foreach ($GLOBALS['ppl'] as $person){ // person has blank schedule
	$GLOBALS['ppl'][$person['name']]['schedule'] = $schedule; 
}
   
// ADD COMPETITORS TO EVENTS
// ROUND ONE adds competitors to the events that had less people sign up for them (1-4 people interested)
// looped because events and peoples schedules fill up, so more opportunities for others
do{
	$keepgoing = FALSE;		
	foreach ($GLOBALS['events'] as $event){ 
		if (isEventOpen($event, $GLOBALS['shuffled']) == true && isEventOpen($event, $GLOBALS['cats']) == true && $event['numpool'] < ($event['numpeopleperteam']*2+1) && $event['numpool']>0 ){
			$keepgoing = TRUE;
			shuffle ($event['pool']); // I love php
		
			foreach ($event['pool'] as $name){
				$person = $ppl [$name];
				if ( isScheduleOpen($person, $event) ){
					if (isOnTeam ($person, $GLOBALS['shuffled']) || ( isOnTeam ($person, $GLOBALS['pool']) && numCompetitors ($GLOBALS['shuffled'], $event) < numCompetitors ($GLOBALS['cats'], $event) ) )  // balances competitors between shuffled and cats
						addToEvent ($person, $event, $GLOBALS['shuffled']);						
					else if (isOnTeam ($person, $GLOBALS['cats']) || ( isOnTeam ($person, $GLOBALS['pool'])  && numCompetitors ($GLOBALS['cats'], $event) < numCompetitors ($GLOBALS['shuffled'], $event) ) )
						addToEvent ($person, $event, $GLOBALS['cats']);		
					else{ // pool peeps are non priority// if persion is in pool and equalnum compeittiors  in shuffled and cats
						$rng = rand (0,1);
						if ($rng == 0)
							addToEvent ($person, $event, $GLOBALS['shuffled']);	
						else 
							addToEvent ($person, $event, $GLOBALS['cats']);							
					}
				}
			// else echo 'schedulecoles';
			}
		}
	}		
}
while (	$keepgoing == TRUE);
	
// ROUND TWO add people from pool to a team
foreach ($GLOBALS['events'] as $event){ 
	if ( isEventOpen($event, $GLOBALS['shuffled']) == TRUE && $event['numpool']>0 ){
		shuffle ($event['pool']);
		foreach ($event['pool'] as $name){
			$person = $ppl [$name];
			if ( isScheduleOpen($person, $event) && isOnTeam ($person, $GLOBALS['pool']) ){ 
				addToEvent ($person, $event, $GLOBALS['shuffled']);		
			}
		// else echo 'schedulecoles';
		}
	}
}
foreach ($GLOBALS['events'] as $event){ 
	if ( isEventOpen($event, $GLOBALS['cats']) == TRUE && $event['numpool']>0 ){
		shuffle ($event['pool']);
		foreach ($event['pool'] as $name){
			$person = $ppl [$name];
			if ( isScheduleOpen($person, $event) && isOnTeam ($person, $GLOBALS['pool'])  ){ 
				addToEvent ($person, $event, $GLOBALS['cats']);		
			}
		// else echo 'schedulecoles';
		}
	}
}
// ROUND THREE fill up events with people set on a team
foreach ($GLOBALS['events'] as $event){ 
	if ( isEventOpen($event, $GLOBALS['shuffled']) == true && $event['numpool']>0 ){
		shuffle ($event['pool']);
		foreach ($event['pool'] as $name){
			$person = $ppl [$name];
			if (isScheduleOpen($person, $event) && isOnTeam ($person, $GLOBALS['shuffled'])){
					addToEvent ($person, $event, $GLOBALS['shuffled']);											
			}
		// else echo 'schedulecoles';
		}
	}
}
foreach ($GLOBALS['events'] as $event){ 
	if ( isEventOpen($event, $GLOBALS['cats']) == true && $event['numpool']>0 ){
		shuffle ($event['pool']);
		foreach ($event['pool'] as $name){
			$person = $ppl [$name];
			if (isScheduleOpen($person, $event) && isOnTeam ($person, $GLOBALS['cats'])){
					addToEvent ($person, $event, $GLOBALS['cats']);											
			}
		// else echo 'schedulecoles';
		}
	}
}


// GIVE PEOPLE WITH 1 EVENT MORE EVENTS

foreach ($GLOBALS['ppl'] as $person){ 
	if ($person['numevents'] == 1){
		echo $person['name'].'needmoe evets ';
		if (isOnTeam($person, $GLOBALS['shuffled'])){
			foreach ($person['eventrequests'] as $eventname){
				if (!in_array ($eventname, $person['events'])){
				$event = $GLOBALS['events'][$eventname];
				foreach ($GLOBALS['shuffled']['events'][$event['name']]['competitors'] as $p){
					if ($person['numevents'] == 1){
					$otherperson =  $GLOBALS['ppl'][$p]; 
					if ($otherperson['numevents'] > 2){
						echo $otherperson['name'].'hjkl ';
						removeFromEvent ($otherperson, $event, $GLOBALS['shuffled']);
						addToEvent ($person, $event, $GLOBALS['shuffled']);	
					}
					}
				}					
				}

			}			
		}
		
		if (isOnTeam($person, $GLOBALS['cats'])){
			foreach ($person['eventrequests'] as $eventname){
				if (!in_array ($eventname, $person['events'])){
				$event = $GLOBALS['events'][$eventname];
				foreach ($GLOBALS['cats']['events'][$event['name']]['competitors'] as $p){
					if ($person['numevents'] == 1){
					$otherperson =  $GLOBALS['ppl'][$p]; 
					if ($otherperson['numevents'] > 3){
						echo $otherperson['name'].'hjkl ';
						removeFromEvent ($otherperson, $event, $GLOBALS['cats']);
						addToEvent ($person, $event, $GLOBALS['cats']);	
					}
					}
				}
				}
			}			
		}
	

	}
}

echo 'nuext';

// ADD EMPTYS or DROP
foreach ($GLOBALS['events'] as $event){ 
	if ($GLOBALS['shuffled']['events'][$event['name']]['numcompetitors'] == 0){
		$GLOBALS['shuffled']['events'][$event['name']]['competitors'][] = "DROP";
		$GLOBALS['shuffled']['events'][$event['name']]['competitors'][] = "DROP";
		$GLOBALS['shuffled']['events'][$event['name']]['drop'] = TRUE;	
	}
		
	else while ( $GLOBALS['shuffled']['events'][$event['name']]['numcompetitors'] < $GLOBALS['shuffled']['events'][$event['name']]['numpeopleperteam']){
		$GLOBALS['shuffled']['events'][$event['name']]['competitors'][] = "EMPTY";
		$GLOBALS['shuffled']['events'][$event['name']]['numcompetitors']++; 
	}
}

foreach ($GLOBALS['events'] as $event){ 
	if ($GLOBALS['cats']['events'][$event['name']]['numcompetitors'] == 0){
		$GLOBALS['cats']['events'][$event['name']]['competitors'][] = "DROP";
		$GLOBALS['cats']['events'][$event['name']]['competitors'][] = "DROP";
		$GLOBALS['cats']['events'][$event['name']]['drop'] = TRUE;	
	}
	else while ($GLOBALS['cats']['events'][$event['name']]['numcompetitors'] < $GLOBALS['cats']['events'][$event['name']]['numpeopleperteam']){
		$GLOBALS['cats']['events'][$event['name']]['competitors'][] = "EMPTY";
		$GLOBALS['cats']['events'][$event['name']]['numcompetitors']++; 
	}
}


// FILL IN EMPTY SPOTS WITH UNVOLUNTARY ADDITIONS



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



function addToEvent ($person, $event, &$team){
	if (isEventOpen($event, $team)  ) // if event needs more competitors
		if ( !isTeamMaxed($team) || isOnTeam($person, $team))   { // if team is not over 15 people limit or if person is already on the team (already included in the 15)
			 // access person's info
			$person = $GLOBALS['ppl'][$person['name']];
			
			 // add person to team event
			$team['events'][$event['name']]['competitors'][] = $person['name'];
			$team['events'][$event['name']]['numcompetitors'] = count ($team['events'][$event['name']]['competitors']);
			
			// add event to person
			$person['events'][] = $event['name']; 
			$person['schedule'][$event['time']] = $event['name'];
			$person['numevents'] = count ($person['events']);
			// echo $person['numevents']. $person['name']. $event['name']. "\n";
			$GLOBALS['ppl'][$person['name']]= $person; // set new persons info global
		
			
			// other stats changes	
			
			// remove person from the pool for otherevents that now have conflicts (happen in the same time slot)
			$time = $event['time'];
			foreach ($GLOBALS['events'] as $otherevents)
				if ($otherevents['time']==$time || isUnderEvented($person) == FALSE ){
					// echo "do". $otherevents['name'] . $person['name']. $time. "      "; 
					$GLOBALS['events'][$otherevents['name']]['pool'] = array_diff($GLOBALS['events'][$otherevents['name']]['pool'], array($person['name']));
					$GLOBALS['events'][$otherevents['name']]['numpool'] = count ($GLOBALS['events'][$otherevents['name']]['pool']);				
				}
			
			// add name to team roster
			if (!isOnTeam ($person, $team))  
				enlist ($person, $team);
			
			// when someone takes the last spot in an event for a team
			if (isEventOpen($event, $team) == FALSE)  
				closeEvent($event, $team);
		}
}

function removeFromEvent($person, $event, &$team){
	$person = $GLOBALS['ppl'][$person['name']];
	
	echo json_encode($team['events'][$event['name']]['competitors'], JSON_PRETTY_PRINT) . ' minus ' . $person['name'];
	// $team['events'][$event['name']]['competitors'] = array_diff($team['events'][$event['name']]['competitors'], array($person['name']));
	$team['events'][$event['name']]['numcompetitors'] = count ($team['events'][$event['name']]['competitors']);
	
	$person['events'] = array_diff($person['events'], array($event['name'])); 
	$person['schedule'][$event['time']] = null;
	$person['numevents'] = count ($person['events']); 

	$GLOBALS['ppl'][$person['name']] = $person;
	
	$time = $event['time'];
	foreach ($GLOBALS['events'] as $otherevents)
		if ($otherevents['time']==$time ){
					// echo "do". $otherevents['name'] . $person['name']. $time. "      "; 
			$GLOBALS['events'][$otherevents['name']]['pool'][] = $person['name'];
			$GLOBALS['events'][$otherevents['name']]['numpool'] = count ($GLOBALS['events'][$otherevents['name']]['pool']);				
		}
	
	openEvent($event, $team);
}
function enlist ($person, &$team){
	$team['roster'][] = $person['name'];
	$GLOBALS['pool']['roster'] = array_diff($GLOBALS['pool']['roster'], array($person['name']));
}
function numCompetitors ($team, $event){
	return $team['events'][$event['name']]['numcompetitors'];
}
function isScheduleOpen($person, $event){
	if ( $person['schedule'][$event['time']] == null  && isUnderEvented($person) )
		return TRUE;
	return FALSE;
}
function isOnTeam ($person, $team){
	if ( in_array ($person['name'], $team['roster']) )
		return TRUE;
	return FALSE;
}
function isUnderEvented ($person){
	if ($person['numevents'] < 4)
		return TRUE;
	else{
		// echo 'OVER EVENTED . ' . $person['numevents'] . $person['name'];
		return FALSE;	
	}
}
function isEventOpen ($event, $team){
	if ($team['events'][$event['name']]['numcompetitors'] < $team['events'][$event['name']]['numpeopleperteam'] )
		return TRUE;
	return FALSE;
}
function closeEvent ($event, &$team){
	$team ['events'][$event['name']]['open'] = FALSE;
}
function openEvent ($event, &$team){
	$team ['events'][$event['name']]['open'] = TRUE;
}
function isTeamMaxed ($team){
	if (count ($team['roster']) > 14){
		// echo 'team maxed'; 
		return TRUE;
	}
	return FALSE;
}

/*
 echo '<pre>'.json_encode ($GLOBALS['ppl'], JSON_PRETTY_PRINT); 
 echo json_encode ($GLOBALS['events'], JSON_PRETTY_PRINT);
 echo 'TEAM shuffled' . json_encode ($GLOBALS['shuffled'], JSON_PRETTY_PRINT);
 echo 'TEAM cats' . json_encode ($GLOBALS['cats'], JSON_PRETTY_PRINT);
 echo 'TEAM POOL' . json_encode ($GLOBALS['pool'], JSON_PRETTY_PRINT);
 echo "\n";
 echo 'TOTAL EVENT REQUESTS: ' . $countins. "\n";
 echo 'TOTAL PEOPLE: ' . count ($GLOBALS['ppl']). "\n";
 echo 'TOTAL EVENTS: ' . $countevents. "\n" ;  
  //echo json_encode ($GLOBALS['shuffled']['memedevents'], JSON_PRETTY_PRINT);
  // echo json_encode ($GLOBALS['cats']['memedevents'], JSON_PRETTY_PRINT);
   // echo json_encode ($GLOBALS['ppl']['thememed'], JSON_PRETTY_PRINT);
 echo 'CANCER: MATT MILAD' . '</pre>';  //  ends up in both teams...   */
?>
