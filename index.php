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
$ppl = array();
// add people and their info
for ($i = 1 ; $i < count ($values) ; $i++){
	$info = $values[$i];
	$person = [];
	$person ['name']= $info[$Cfn] . ' ' . $info[$Cln];
	$person ['grade'] = $info[$Cgrade];
	$person ['events'] = explode (", " , $info[$Cevents]); // a person's events is put into an array
	$person ['numrequests'] = count ($person ['events']);
	$person ['eventsfinal'] = [];
	$person ['numevents'] = count ($person ['eventsfinal']);
	$ppl[$info[$Cfn] . ' ' . $info[$Cln]] = $person; // key to a person is their name
}
//	 echo json_encode ($ppl);    // $ppl[$name][$info]
	
$range = 'events!A1:C';
$response = $service->spreadsheets_values->get($spreadsheetId, $range);
$values = $response->getValues();
$countins = 0; 
$countevents = 0; 
$blocks = array ();
$okeyblocks = array ();
$dokeyblocks = array();
for ($i = 2 ; $i < count ($values); $i++){
	
	// time slots
	if (isset ($values [$i][0]) && $values [$i][0]!=''){
		$time = $values[$i][0];
	
		$k = $i - 1;
		do{
			$k++;
		}
		while ( isset ($values[$k]) && isset ($values[$k][0]) );
		
		$events = array ();
		$okeyevents = array ();
		$dokeyevents = array ();
		for ($p = $i ; $p<$k; $p++){ // per event
			$event = $values [$p][1];
			 echo "\n". $event. ' : ';
			$countevents++;
			
			$signups = array ();
		 
			foreach ($ppl as $person){
				if (in_array ( $event, $person ['events'] )){
					echo $person ['name'];
					$countins++;
					$signups [] = $person;
				}
			}
			$events[$event]['signups'] = $signups; 
			$events[$event]['numsignups'] = count ($signups);
			$okeyevents[$event]['competitors'] = [];
			$okeyevents[$event]['numemptyspots'] = $values[$p][2];
			$dokeyevents[$event]['competitors'] = [];
			$dokeyevents[$event]['numemptyspots'] = $values[$p][2];
		}
		
		$blocks [$time] = $events;
		$okeyblocks [$time] = $okeyevents;
		$dokeyblocks [$time] = $dokeyevents; 
		echo "\n";
	}
}
   // $blocks [$time][$event][$person (key is name)][$info e.g events, event count, name, grade]
   
   
echo json_encode ($blocks, JSON_PRETTY_PRINT);
echo json_encode ($okeyblocks, JSON_PRETTY_PRINT);
echo json_encode ($dokeyblocks, JSON_PRETTY_PRINT);
echo "\n";
echo 'TOTAL EVENT REQUESTS: ' . $countins. "\n";
echo 'TOTAL PEOPLE: ' . count ($ppl). "\n";
echo 'TOTAL EVENTS: ' . $countevents;
