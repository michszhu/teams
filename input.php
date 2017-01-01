

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






?>
