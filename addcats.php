<?php 


   
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

/*
// GIVE PEOPLE WITH 1 EVENT MORE EVENTS

foreach ($GLOBALS['ppl'] as $person){ 
	if ($person['numevents'] == 1){
		echo $person['name'].'needmoe evets ';
		
		if (isOnTeam($person, $GLOBALS['shuffled'])){
			foreach ($person['eventrequests'] as $eventname){
				if (!in_array ($eventname, $person['events']) && isScheduleOpen($person, $GLOBALS['events'][$eventname])){
				$event = $GLOBALS['events'][$eventname];
				
				foreach ($GLOBALS['shuffled']['events'][$event['name']]['competitors'] as $p){
					if ($person['numevents'] == 1){
					$otherperson =  $GLOBALS['ppl'][$p]; 
						if ($otherperson['numevents'] > 2)
							$persontoswitch = $otherperson; 
					}
				}
				/*
				if (!isset($persontoswitch))
				foreach ($GLOBALS['shuffled']['events'][$event['name']]['competitors'] as $p){
					if ($person['numevents'] == 1){
					$otherperson =  $GLOBALS['ppl'][$p]; 
						if ($otherperson['numevents'] > 2)
							$persontoswitch = $otherperson; 
					}
				}			
					
				}

			}
			removeFromEvent ($persontoswitch, $event, $GLOBALS['shuffled']);
			addToEvent ($person, $event, $GLOBALS['shuffled']);				
		}
		
		if (isOnTeam($person, $GLOBALS['cats'])){
			foreach ($person['eventrequests'] as $eventname){
				if (!in_array ($eventname, $person['events']) && isScheduleOpen($person, $GLOBALS['events'][$eventname])){
				$event = $GLOBALS['events'][$eventname];
				
				foreach ($GLOBALS['cats']['events'][$event['name']]['competitors'] as $p){
					if ($person['numevents'] == 1){
					$otherperson =  $GLOBALS['ppl'][$p]; 
						if ($otherperson['numevents'] > 2)
							$persontoswitch = $otherperson; 
					}
				}
				/*
				if (!isset($persontoswitch))
				foreach ($GLOBALS['cats']['events'][$event['name']]['competitors'] as $p){
					if ($person['numevents'] == 1){
					$otherperson =  $GLOBALS['ppl'][$p]; 
						if ($otherperson['numevents'] > 2)
							$persontoswitch = $otherperson; 
					}
				}		
				
				}
			}
			removeFromEvent ($persontoswitch, $event, $GLOBALS['cats']);
			addToEvent ($person, $event, $GLOBALS['cats']);					
		}
	

	}
}
*/
/*

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
*/


// FILL IN EMPTY SPOTS WITH UNVOLUNTARY ADDITIONS

foreach ($GLOBALS['events'] as $event){ 
	if ( isEventOpen($event, $GLOBALS['shuffled']) == true){
		shuffle ($GLOBALS['shuffled']['roster']);
		foreach ($GLOBALS['shuffled']['roster'] as $name){
			$person = $ppl [$name];
			if (isScheduleOpen($person, $event) && $person['numevents']==2 ){
					addToEvent ($person, $event, $GLOBALS['shuffled'], 1);											
			}

		}
	}
}
foreach ($GLOBALS['events'] as $event){ 
	if ( isEventOpen($event, $GLOBALS['cats']) == true){
		shuffle ($GLOBALS['cats']['roster']);
		foreach ($GLOBALS['cats']['roster'] as $name){
			$person = $ppl [$name];
			if (isScheduleOpen($person, $event) && $person['numevents'] == 2 ){
					addToEvent ($person, $event, $GLOBALS['cats'], 1);											
			}

		}
	}
}

foreach ($GLOBALS['events'] as $event){ 
	if ( isEventOpen($event, $GLOBALS['shuffled']) == true){
		shuffle ($GLOBALS['shuffled']['roster']);
		foreach ($GLOBALS['shuffled']['roster'] as $name){
			$person = $ppl [$name];
			if (isScheduleOpen($person, $event) && $person['numevents']==3 ){
					addToEvent ($person, $event, $GLOBALS['shuffled'], 2);											
			}

		}
	}
}
foreach ($GLOBALS['events'] as $event){ 
	if ( isEventOpen($event, $GLOBALS['cats']) == true){
		shuffle ($GLOBALS['cats']['roster']);
		foreach ($GLOBALS['cats']['roster'] as $name){
			$person = $ppl [$name];
			if (isScheduleOpen($person, $event) && $person['numevents'] == 3 ){
					addToEvent ($person, $event, $GLOBALS['cats'], 2);											
			}

		}
	}
}




function addToEvent ($person, $event, &$team, $forced = null){
	if (isEventOpen($event, $team)  ) // if event needs more competitors
		if ( !isTeamMaxed($team) || isOnTeam($person, $team))   { // if team is not over 15 people limit or if person is already on the team (already included in the 15)
			 // access person's info
			$person = $GLOBALS['ppl'][$person['name']];
			
			 // add person to team event
			if ($forced == null)
				$team['events'][$event['name']]['competitors'][] = $person['name'];
			else if ($forced == 1)
				$team['events'][$event['name']]['competitors'][] = '('.$person['name'].')';
			else if ($forced == 2)
				$team['events'][$event['name']]['competitors'][] = '(('.$person['name'].'))';			
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
	
	echo json_encode($team['events'][$event['name']]['competitors'], JSON_PRETTY_PRINT) . ' minus ' . $person['name']. ' for ' .$event['name'];
	$team['events'][$event['name']]['competitors'] = array_diff($team['events'][$event['name']]['competitors'], array($person['name']));
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
