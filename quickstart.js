var fs = require('fs');
var readline = require('readline');
var google = require('googleapis');
var googleAuth = require('google-auth-library');

// If modifying these scopes, delete your previously saved credentials
// at ~/.credentials/sheets.googleapis.com-nodejs-quickstart.json
var SCOPES = ['https://www.googleapis.com/auth/spreadsheets.readonly'];
var TOKEN_DIR = (process.env.HOME || process.env.HOMEPATH ||
    process.env.USERPROFILE) + '/.credentials/';
var TOKEN_PATH = TOKEN_DIR + 'sheets.googleapis.com-nodejs-quickstart.json';

// Load client secrets from a local file.
fs.readFile('client_secret.json', function processClientSecrets(err, content) {
  if (err) {
    console.log('Error loading client secret file: ' + err);
    return;
  }
  // Authorize a client with the loaded credentials, then call the
  // Google Sheets API.
  authorize(JSON.parse(content), main);
});

/**
 * Create an OAuth2 client with the given credentials, and then execute the
 * given callback function.
 *
 * @param {Object} credentials The authorization client credentials.
 * @param {function} callback The callback to call with the authorized client.
 */
function authorize(credentials, callback) {
  var clientSecret = credentials.installed.client_secret;
  var clientId = credentials.installed.client_id;
  var redirectUrl = credentials.installed.redirect_uris[0];
  var auth = new googleAuth();
  var oauth2Client = new auth.OAuth2(clientId, clientSecret, redirectUrl);

  // Check if we have previously stored a token.
  fs.readFile(TOKEN_PATH, function(err, token) {
    if (err) {
      getNewToken(oauth2Client, callback);
    } else {
      oauth2Client.credentials = JSON.parse(token);
      callback(oauth2Client);
    }
  });
}

/**
 * Get and store new token after prompting for user authorization, and then
 * execute the given callback with the authorized OAuth2 client.
 *
 * @param {google.auth.OAuth2} oauth2Client The OAuth2 client to get token for.
 * @param {getEventsCallback} callback The callback to call with the authorized
 *     client.
 */
function getNewToken(oauth2Client, callback) {
  var authUrl = oauth2Client.generateAuthUrl({
    access_type: 'offline',
    scope: SCOPES
  });
  console.log('Authorize this app by visiting this url: ', authUrl);
  var rl = readline.createInterface({
    input: process.stdin,
    output: process.stdout
  });
  rl.question('Enter the code from that page here: ', function(code) {
    rl.close();
    oauth2Client.getToken(code, function(err, token) {
      if (err) {
        console.log('Error while trying to retrieve access token', err);
        return;
      }
      oauth2Client.credentials = token;
      storeToken(token);
      callback(oauth2Client);
    });
  });
}

/**
 * Store token to disk be used in later program executions.
 *
 * @param {Object} token The token to store to disk.
 */
function storeToken(token) {
  try {
    fs.mkdirSync(TOKEN_DIR);
  } catch (err) {
    if (err.code != 'EEXIST') {
      throw err;
    }
  }
  fs.writeFile(TOKEN_PATH, JSON.stringify(token));
  console.log('Token stored to ' + TOKEN_PATH);
}

/**
 * Print the names and majors of students in a sample spreadsheet:
 * https://docs.google.com/spreadsheets/d/1BxiMVs0XRA5nFMdKvBdBZjgmUUqptlbs74OgvE2upms/edit
 */
 
 function main (auth){
	// listSignups (auth); 
	 events (auth);
 }
 
function listSignups(auth) {
  var sheets = google.sheets('v4');
  
  sheets.spreadsheets.values.get({
    auth: auth,
    spreadsheetId: '1LhCT9KRfMrXinRyphcBn1jz3JIUh5LQSli9mQFmOc7w',
    range: 'signups!A1:D',
  }, function(err, response) {
    if (err) {
      console.log('The API returned an error: ' + err);
      return;
    }
    var rows = response.values;
    if (rows.length == 0) {
      console.log('No data found.');
    } else {
	  
	  var Cfn = rows [0].indexOf ("First Name");
	  var Cln = rows [0].indexOf ("Last Name");
	  var Cgrade = rows [0].indexOf ("Grade");
	  var Cevents = rows [0].indexOf ("Events");
	  
	  var ppl = [];
	  
      for (var i = 1; i < rows.length; i++) {
        var data = rows[i];
        // console.log('%s, %s', data[Cfn] + ' ' + data[Cln], data[Cevents]);
		var person = {};
		person ['name'] = data[Cfn] + ' ' + data[Cln];
		person ['grade'] = data[Cgrade];
		person ['events'] = data[Cevents].split (", ");
		ppl.push (person);
      }
	  
	  console.log(ppl);
	  
    }
  });
 
}


 function events(auth) {
  var sheets = google.sheets('v4');
  
  sheets.spreadsheets.values.get({
    auth: auth,
    spreadsheetId: '1LhCT9KRfMrXinRyphcBn1jz3JIUh5LQSli9mQFmOc7w',
    range: 'events!A1:B',
  }, function(err, response) {
    if (err) {
      console.log('The API returned an error: ' + err);
      return;
    }
    var rows = response.values;
    if (rows.length == 0) {
      console.log('No data found.');
    } else {
	  
	  
	var blocks = {};
	for (var i = 2; i < rows.length; i++) {
		var time = rows[i][0];
		//console.log (rows[i][0]);
		//console.log (rows [i][1]);
		
		if (time != null && time != undefined && time != ''){
			var k = i-1;
			do {
				k++;	
			} while (rows [k] != undefined && rows[k][0] != null);
		
			var eventsAtTime = [];
			for (var p = i ; p < k ; p++){
				eventsAtTime.push (rows[p][1]);
			}
		
			console.log (time);
			blocks [time] = eventsAtTime; 			
		}

    }
	
	console.log (blocks); 
	
	}
  });
 
}
 

