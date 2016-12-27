<?php
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
  somewhere you can get to it - though in real
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
// Section 2: Uncomment to get list of worksheets
// $url = "https://spreadsheets.google.com/feeds/worksheets/$fileId/private/full";
// $method = 'GET';
// $headers = ["Authorization" => "Bearer $accessToken"];
// $httpClient = new GuzzleHttp\Client(['headers' => $headers]);
// $resp = $httpClient->request($method, $url);
// $body = $resp->getBody()->getContents();
// $code = $resp->getStatusCode();
// $reason = $resp->getReasonPhrase();
// echo "$code : $reason\n\n";
// echo "$body\n";
// Section 3: Uncomment to get the table data
/* $url = "https://spreadsheets.google.com/feeds/list/$fileId/od6/private/full";
 $method = 'GET';
 $headers = ["Authorization" => "Bearer $accessToken", "GData-Version" => "3.0"];
 $httpClient = new GuzzleHttp\Client(['headers' => $headers]);
 $resp = $httpClient->request($method, $url);
 $body = $resp->getBody()->getContents();
 $code = $resp->getStatusCode();
 $reason = $resp->getReasonPhrase();
 echo "$code : $reason\n\n";
 echo "$body\n";  */
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
// Section 6: Uncomment to parse table data with SimpleXML  
 /*$url = "https://spreadsheets.google.com/feeds/list/$fileId/od6/private/full";
 $method = 'GET';
 $headers = ["Authorization" => "Bearer $accessToken", "GData-Version" => "3.0"];
 $httpClient = new GuzzleHttp\Client(['headers' => $headers]);
 $resp = $httpClient->request($method, $url);
 $body = $resp->getBody()->getContents();
 $tableXML = simplexml_load_string($body);
 
 echo xmlObjToArr ($tableXML)
 
function xmlObjToArr($obj) { 
        $namespace = $obj->getDocNamespaces(true); 
        $namespace[NULL] = NULL; 
        
        $children = array(); 
        $attributes = array(); 
        $name = strtolower((string)$obj->getName()); 
        
        $text = trim((string)$obj); 
        if( strlen($text) <= 0 ) { 
            $text = NULL; 
        } 
        
        // get info for all namespaces 
        if(is_object($obj)) { 
            foreach( $namespace as $ns=>$nsUrl ) { 
                // atributes 
                $objAttributes = $obj->attributes($ns, true); 
                foreach( $objAttributes as $attributeName => $attributeValue ) { 
                    $attribName = strtolower(trim((string)$attributeName)); 
                    $attribVal = trim((string)$attributeValue); 
                    if (!empty($ns)) { 
                        $attribName = $ns . ':' . $attribName; 
                    } 
                    $attributes[$attribName] = $attribVal; 
                } 
                
                // children 
                $objChildren = $obj->children($ns, true); 
                foreach( $objChildren as $childName=>$child ) { 
                    $childName = strtolower((string)$childName); 
                    if( !empty($ns) ) { 
                        $childName = $ns.':'.$childName; 
                    } 
                    $children[$childName][] = xmlObjToArr($child); 
                } 
            } 
        } 
        
        return array( 
            'name'=>$name, 
            'text'=>$text, 
            'attributes'=>$attributes, 
            'children'=>$children 
        ); 
    } 
	
	*/
	
 /*echo "Rows:\n";
 foreach ($tableXML->entry as $entry) {
   $etag = $entry->attributes('gd', TRUE);
   $id = $entry->id;
   echo "etag: $etag\n";
   echo "id: $id\n";
   foreach ($entry->children('gsx', TRUE) as $column) {
     $colName = $column->getName();
     $colValue = $column;
     echo "$colName : $colValue\n";
   }
 } */
// Section 7: Uncomment to query for a subset of rows and parse data with SimpleXML
// $url = "https://spreadsheets.google.com/feeds/list/$fileId/od6/private/full?sq=quantity>9";
// $myQuery = 'quantity>9';  // and here is an example with a space in it: $myQuery = 'gear="mifi device"';
// $method = 'GET';
// $headers = ["Authorization" => "Bearer $accessToken", "GData-Version" => "3.0"];
// $httpClient = new GuzzleHttp\Client(['headers' => $headers]);
// $resp = $httpClient->request($method, $url, ['query' => ['sq' => $myQuery]]);
// $body = $resp->getBody()->getContents();
// $tableXML = simplexml_load_string($body);
// echo "Rows:\n";
// foreach ($tableXML->entry as $entry) {
//   $etag = $entry->attributes('gd', TRUE);
//   $id = $entry->id;
//   echo "etag: $etag\n";
//   echo "id: $id\n";
//   foreach ($entry->children('gsx', TRUE) as $column) {
//     $colName = $column->getName();
//     $colValue = $column;
//     echo "$colName : $colValue\n";
//   }
// }

// set people from signups

$service = new Google_Service_Sheets($client);
$spreadsheetId = '1LhCT9KRfMrXinRyphcBn1jz3JIUh5LQSli9mQFmOc7w';
$range = 'signups!A1:D';
$response = $service->spreadsheets_values->get($spreadsheetId, $range);
$values = $response->getValues();

// find index of columns of info (name, grade, events)
$Cfn = array_search ( 'First Name', $values[0]);
$Cln = array_search ( 'Last Name', $values[0]);
$Cgrade = array_search ( 'Grade', $values[0]);
$Cevents = array_search ( 'Events', $values[0]);

$GLOBALS['ppl'] = array(); // info
$GLOBALS['pool'] = array (); /// people
$GLOBALS['okey'] = array (); // events and competitors
$GLOBALS['dokey'] = array (); 
$countins = 0; 
$countevents = 0; 
$GLOBALS['events'] = array ();
$schedule = array();
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
	
	$GLOBALS['ppl'][$info[$Cfn] . ' ' . $info[$Cln]] = $person; // key to a person is their name
	$GLOBALS['pool']['roster'][] = $person['name'];
}
	$GLOBALS['okey']['roster'] = array();
	$GLOBALS['dokey']['roster'] = array();
$range = 'events!A3:C';
$response = $service->spreadsheets_values->get($spreadsheetId, $range);
$values = $response->getValues();
foreach ($values as $row){
	if (isset ($row[1])) {
		$countevents++;
		$event = array();
		$event['time'] = $row[0];
		$event['name'] = $row[1];
		
		if (!in_array ($event['time'], $schedule))   // set schedule
			$schedule[$event['time']] = null;
	
		$event['numpeopleperteam'] = $row[2];
		$event['competitors'] = array();
		$event['numcompetitors'] = count ($event['competitors']);
	
		$GLOBALS['okey'] ['events'] [$event['name']] = $event;
		$GLOBALS['dokey'] ['events'] [$event['name']] = $event;	
		
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
		$event['pool'] = $event['signups']; // TODO remove people from eventpool if  1) schedule filled o  over evented
		$event['numpool'] = $event['numsignups'];
		
		$GLOBALS['events'][$event['name']] = $event; // includes signups and pool
	
	}
}
foreach ($GLOBALS['ppl'] as $person){
	$GLOBALS['ppl'][$person['name']]['schedule'] = $schedule; 
}
   
/*
	if ($event['numsignups'] < ($event['numpeopleperteam']*2) && $event['numsignups']>0){
		echo json_encode ($event, JSON_PRETTY_PRINT); 
		shuffle ($event['signups']);
		foreach ($event['signups'] as $person){
			//if (in_array ($person['name'], $GLOBALS['okey']roster) && $person['numevents'] < 3)
					
		//	if ($person['team'] == 'dokey' && $person['numevents'] <3)
		}
	}
*/
	do{
	$keepgoing = FALSE;		
foreach ($GLOBALS['events'] as $event){ // TODO loop this until events filled with competitiros

	if ($event['numpool'] < ($event['numpeopleperteam']*2) && $event['numpool']>0){
		$keepgoing = TRUE;
		shuffle ($event['pool']);
	
		//nonpriority for the pool peeps
		foreach ($event['pool'] as $name){
			$person = $ppl [$name];
			if ( isScheduleOpen($person, $event) ){
				if (isOnTeam ($person, $GLOBALS['okey']) || ( isOnTeam ($person, $GLOBALS['pool']) && numCompetitors ($GLOBALS['okey'], $event) < numCompetitors ($GLOBALS['dokey'], $event) ) )
					addToEvent ($person, $event, $GLOBALS['okey']);						
				else if (isOnTeam ($person, $GLOBALS['dokey']) || ( isOnTeam ($person, $GLOBALS['pool'])  && numCompetitors ($GLOBALS['dokey'], $event) < numCompetitors ($GLOBALS['okey'], $event) ) )
					addToEvent ($person, $event, $GLOBALS['dokey']);		
				else{ // if persion is in pool and equalnum compeittiors  in okdy and dokey
					$rng = rand (0,1);
					if ($rng == 0)
						addToEvent ($person, $event, $GLOBALS['okey']);	
					else 
						addToEvent ($person, $event, $GLOBALS['dokey']);							
				}
					
			}
		else echo 'schedulecoles';
		}
		
	}
}		
	}
	while (	$keepgoing == TRUE);

foreach ($GLOBALS['events'] as $event){ // TODO loop this until events filled with competitiros
	if ($event['numpool']>0){
		shuffle ($event['pool']);

		//nonpriority for the pool peeps
		foreach ($event['pool'] as $name){
			$person = $ppl [$name];
			if ( isScheduleOpen($person, $event) ){
				if (isOnTeam ($person, $GLOBALS['okey']) || ( isOnTeam ($person, $GLOBALS['pool']) && numCompetitors ($GLOBALS['okey'], $event) < numCompetitors ($GLOBALS['dokey'], $event) ) )
					addToEvent ($person, $event, $GLOBALS['okey']);						
				else if (isOnTeam ($person, $GLOBALS['dokey']) || ( isOnTeam ($person, $GLOBALS['pool'])  && numCompetitors ($GLOBALS['dokey'], $event) < numCompetitors ($GLOBALS['okey'], $event) ) )
					addToEvent ($person, $event, $GLOBALS['dokey']);		
				else{ // if persion is in pool and equalnum compeittiors  in okdy and dokey
					$rng = rand (0,1);
					if ($rng == 0)
						addToEvent ($person, $event, $GLOBALS['okey']);	
					else 
						addToEvent ($person, $event, $GLOBALS['dokey']);							
				}
					
			}
		else echo 'schedulecoles';
		}
		
	}
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
		echo 'OVER EVENTED . ' . $person['numevents'] . $person['name'];
		return FALSE;	
	}
}
function isEventOpen ($event, $team){
	if ($team['events'][$event['name']]['numcompetitors'] < $team['events'][$event['name']]['numpeopleperteam'] )
		return TRUE;
	return FALSE;
}
function isTeamMaxed ($team){
	if (count ($team['roster']) > 14)
		return TRUE;
	return FALSE;
}
function addToEvent ($person, $event, &$team){
	
	// if (  !($team == $GLOBALS['okey'] && isOnTeam ($person,$dokey)) && !($team == $GLOBALS['dokey'] && isOnTeam ($person,$okey))  )
// catches traitors e.g. milad
	if (isEventOpen($event, $team)  ) { 
		$person = $GLOBALS['ppl'][$person['name']];
		
		$team['events'][$event['name']]['competitors'][] = $person['name'];
		$team['events'][$event['name']]['numcompetitors'] = count ($team['events'][$event['name']]['competitors']);
		$person['events'][] = $event['name'];
		$person['schedule'][$event['time']] = $event['name'];
		$person['numevents'] = count ($person['events']);
		// echo $person['numevents']. $person['name']. $event['name']. "\n";
		
		$GLOBALS['ppl'][$person['name']]= $person;
		
		$time = $event['time'];
		foreach ($GLOBALS['events'] as $otherevents)
			if ($otherevents['time']==$time || isUnderEvented($person) == FALSE ){
				echo "do". $otherevents['name'] . $person['name']. $time. "      "; 
				$GLOBALS['events'][$otherevents['name']]['pool'] = array_diff($GLOBALS['events'][$otherevents['name']]['pool'], array($person['name']));
				$GLOBALS['events'][$otherevents['name']]['numpool'] = count ($GLOBALS['events'][$otherevents['name']]['pool']);				
			}
		
		if (!isOnTeam ($person, $team))
			enlist ($person, $team);
	
	}
	else echo "event maxed";  
	//else echo "wrong team";
}
function enlist ($person, &$team){
	$team['roster'][] = $person['name'];
	$GLOBALS['pool']['roster'] = array_diff($GLOBALS['pool']['roster'], array($person['name']));
}


 echo '<pre>'.json_encode ($GLOBALS['ppl'], JSON_PRETTY_PRINT); 
 echo json_encode ($GLOBALS['events'], JSON_PRETTY_PRINT);
 echo 'TEAM OKEY' . json_encode ($GLOBALS['okey'], JSON_PRETTY_PRINT);
 echo 'TEAM DOKEY' . json_encode ($GLOBALS['dokey'], JSON_PRETTY_PRINT);
 echo 'TEAM POOL' . json_encode ($GLOBALS['pool'], JSON_PRETTY_PRINT);
 echo "\n";
 echo 'TOTAL EVENT REQUESTS: ' . $countins. "\n";
 echo 'TOTAL PEOPLE: ' . count ($GLOBALS['ppl']). "\n";
 echo 'TOTAL EVENTS: ' . $countevents. "\n" ;  
 echo 'CANCER: MATT MILAD' . '</pre>';  //  ends up in both teams... 

?>
