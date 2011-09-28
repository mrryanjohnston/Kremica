<?php
/*
	Kremica was coded entirely by Ryan Johnston, a senior at the University
	of Pittsburgh. Kremica is meant to be an enjoyable (yet frustrating)
	game that will not over-burden a student. Rather, the game functions as
	something to do on a quick study break. Make a character, play until the
	character dies, go back to studying.
	Copyright (C) 2010 Ryan Johnston
	
	For a copy of the GNU GPL, visit:
	http://www.gnu.org/licenses/gpl.html
	
    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

/*
 * This is the functions page. Listed below are all of the functions necessary for the game. Include this on every page.
 */

/*
 * Login Function. 
 * $username is the username attempting to log in
 * $password is the unhashed password to be checked against the database for the given username
 * $sessionID is the sessionID for the current session. This will be entered into the database
 * Returns TRUE on successful Login.
 * Returns FALSE on failure. To be moved to function page
 */
function login($username, $password, $sessionID) {
	include 'inc_connection.php';
	
	/* 
	 * Query grabs all of the usernames (which are unique) from the users table given username and userpassword are
	 * equal to their submitted entries.
	 * 
	 */
	$query = mysql_query("SELECT userID, username FROM users WHERE username='" . mysql_real_escape_string(stripslashes($username)) . "' AND  userpassword='" . md5(mysql_real_escape_string(stripslashes($password))) . "'");
	
	//If the result is equal to 1 (aka, it found one account with the given credentials),
	//Return a success message and
	//Set $_SESSION['username'] so that the system will know they are logged in.
	//User will then be presented with a character creation screen.
	//Also set session information for the user into the database.

	if (is_numeric(mysql_num_rows($query)) && mysql_num_rows($query)==1) {
		$queryarray = mysql_fetch_assoc($query);
		$_SESSION['username']=$username;
		$_SESSION['userID']=$queryarray['userID'];
		//If there is a character in the character table whose userID=$_SESSION['userID'] AND dead='no' (aka a character
		//is saved and still alive), load that character with all the stats from the characters table.
		$characterquery=mysql_query("SELECT * FROM characters WHERE userID='" . $_SESSION['userID'] . "' AND dead='no'");
		if (mysql_num_rows($characterquery)===1) {
			include 'inc_characterload.php';
		}
		$sessionIDquery="UPDATE users SET sessionID='" . $sessionID . "' WHERE username='" . $username . "'";
		$sessionIDresult=mysql_query($sessionIDquery);
		
		if (!$sessionIDresult) {
			die('Something went wrong: ' . mysql_error());
		}

		return TRUE;
	}
	else {

		return FALSE;
	}
}
/*
 * isForcedLoggedOut function
 * If a sessionID is different than the one in the database and the user isn't logging in, log out the user.
 * $username current username for user
 * $sessionID is session for the user on this machine
 * Returns TRUE if user has been forced to log out
 * Returns FALSE if otherwise
 */
function isForcedLoggedOut($username, $sessionID) {
	include 'inc_connection.php';
	/* 
	 * Grabs the sessionID for the database to be checked against
	 */
	$query="SELECT sessionID FROM users WHERE username='" . mysql_real_escape_string(stripslashes($username)) . "'";
	$queryresult=mysql_query($query);
	$queryarray=mysql_fetch_assoc($queryresult);
	if ($queryarray['sessionID']==$sessionID) {
		return FALSE;
	}
	else {
		return TRUE;
	}
}
/*
 * logout function
 * Logs current user out by destroying the session. Saves the character if not in battle, kills otherwise.
 * $monstername if set, kill the character
 */
function logout($username, $logoutforced) {
		if (!$logoutforced) {
			include 'inc_connection.php';
			//Update the sessionID so that it is NULL only if logout is not forced
			$sessionIDquery="UPDATE users SET sessionID=NULL WHERE username='" . $username . "'";
			$sessionIDresult=mysql_query($sessionIDquery);
		}	
		//Finally, destroy session, logging the user out.
		$_SESSION = array();
		session_destroy();
}

/*
 * createcharacter function
 * Creates the given character with the given $charactername, then saves the new character into the database
 * $charactername the name for the new character
 */
function createcharacter($charactername, $gender) {
	$_SESSION['name'] = $charactername;
	$_SESSION['hp']=10;
	$_SESSION['maxhp']=10;
	$_SESSION['level']=1;
	$_SESSION['exp']=0;
	$_SESSION['maxexp']=420;
	$_SESSION['strength']=1;
	$_SESSION['accuracy']=100;
	$_SESSION['agility']=0;
	$_SESSION['defense']=1;
	$_SESSION['speed']=1;
	$_SESSION['numberofpotionholsterslots']=5;
	$_SESSION['numberofitemsinpotionholster']=3;
	//$_SESSION['bagslot1']="p1";
	//$_SESSION['bagslot2']="p1";
	//$_SESSION['bagslot3']="p1";
	//$_SESSION['bagslot4']="empty";
	//$_SESSION['bagslot5']="empty";
	$_SESSION['potionholster']=array(array("Weak Potion", 10, 1),array("Weak Potion", 10, 1), array("Weak Potion", 10, 1));
	$_SESSION['weaponholster']=array(array("Fists", 1, 3, "flesh", 1));
	$_SESSION['shieldholster']=array(array("Bare Arms", 0, "flesh", 1));
	$_SESSION['itemholster']=array();
	$_SESSION['weaponslotequipped']=0;
	$_SESSION['weaponequipped']=$_SESSION['weaponholster'][$_SESSION['weaponslotequipped']][0];
	$_SESSION['weaponequippedID']=$_SESSION['weaponholster'][$_SESSION['weaponslotequipped']][4];
	$_SESSION['weapontype']=$_SESSION['weaponholster'][$_SESSION['weaponslotequipped']][3];
	$_SESSION['weaponlow']=$_SESSION['weaponholster'][$_SESSION['weaponslotequipped']][1];
	$_SESSION['weaponhigh']=$_SESSION['weaponholster'][$_SESSION['weaponslotequipped']][2];
	$_SESSION['shieldslotequipped']=0;
	$_SESSION['shieldtype']=$_SESSION['shieldholster'][$_SESSION['shieldslotequipped']][2];
	$_SESSION['shieldequipped']=$_SESSION['shieldholster'][$_SESSION['shieldslotequipped']][0];
	$_SESSION['shieldequippedID']=$_SESSION['shieldholster'][$_SESSION['shieldslotequipped']][3];
	$_SESSION['shieldpower']=$_SESSION['shieldholster'][$_SESSION['shieldslotequipped']][1];
	$_SESSION['gold']=15;
	//$_SESSION['questfile']=NULL;
	$_SESSION['questID']=0;
	$_SESSION['queststep']=0;
	$_SESSION['gender']=$gender;
	include 'inc_charactersave.php';
}

/*
 * createrandommonster function
 * Searches database for a random monster based on character's level and a really random number
 */
function createrandommonster() {
	include 'inc_connection.php';
		
	$charlevel=$_SESSION['level'];
	$possiblemonsters=null;
	$monsternumber=0;
	$monsteritemtype=null;

	//Make a really random number
	$randomarray=array(1, 2, 2, 3, 3, 3, 4, 4, 4, 4, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 6, 6, 6, 7, 7, 8, 9);
	$randomarray=array(-3, -3, -2, -2, -2, -1, -1, -1, -1, -1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 2, 2, 3);
	shuffle($randomarray);
	shuffle($randomarray);
	$random=array_rand($randomarray);

	//then, based on this number, choose level of monster to fight
	$possiblemonsters="";
	if ($charlevel>3) {
		$possiblemonsters=mysql_query("SELECT * FROM monsters WHERE level='" . $charlevel+$random . "'");
	}
	//If character is level 3 or less, it is impossible for them to run into monsters of 3 less levels than them
	else {
		$possiblemonsters=mysql_query("SELECT * FROM monsters WHERE level='" . $charlevel . "'");
	}
	$highprobability=mysql_num_rows($possiblemonsters);
	$monsternumber=mt_rand(1, $highprobability)-1;
	$monsteritemtype=mysql_query("SELECT * FROM items WHERE itemID='" . mysql_result($possiblemonsters,$monsternumber,12) . "'");
	$monsteritemarray=mysql_fetch_array($monsteritemtype);
	mysql_close($con);
	
	$_SESSION['monstername']=mysql_result($possiblemonsters,$monsternumber,1);
	$_SESSION['monsterweapontype']=mysql_result($possiblemonsters,$monsternumber,2);
	$_SESSION['monstershieldtype']=mysql_result($possiblemonsters,$monsternumber,3);
	$_SESSION['monsterhp']=mysql_result($possiblemonsters,$monsternumber,4);
	$_SESSION['monstermaxhp']=mysql_result($possiblemonsters,$monsternumber,5);
	$_SESSION['monsterlevel']=mysql_result($possiblemonsters,$monsternumber,6);
	$_SESSION['monsterstrength']=mysql_result($possiblemonsters,$monsternumber,7);
	$_SESSION['monsterdefense']=mysql_result($possiblemonsters,$monsternumber,8);
	$_SESSION['monsterspeed']=mysql_result($possiblemonsters,$monsternumber,9);
	$_SESSION['monsteraccuracy']=100;
	$_SESSION['monsteragility']=0;
	$_SESSION['monsterweaponlow']=mysql_result($possiblemonsters,$monsternumber,10);
	$_SESSION['monsterweaponhigh']=mysql_result($possiblemonsters,$monsternumber,11);
	$_SESSION['monsteritem']=array($monsteritemarray['name'], $monsteritemarray['type'], $monsteritemarray['itemID']);
	$_SESSION['monsteroriginalspeed']=$_SESSION['monsterspeed'];
	$_SESSION['monsterpoisoned']=NULL;
	//$_SESSION['monsterweaponproficiency']=mysql_result($possiblemonsters,$monsternumber,11);
}
 
 
 function determinetypebonus($attackertype, $defendertype) {
	$multiplier=1;
	switch ($attackertype) {
		case "metal":
			switch ($defendertype) {
				case "metal":
					break;
				case "technology":
					break;
				case "nature":
					break;
				case "life":
					break;
				case "death":
					break;
				case "time":
					break;
				case "valor":
					break;
				case "poison":
					break;
				case "flesh":
					$multiplier=2;
					break;
			}
			break;
		case "technology":
			switch ($defendertype) {
				case "metal":
					$multiplier=.5;
					break;
				case "technology":
					$multiplier=.5;
					break;
				case "nature":
					$multiplier=2;
					break;
				case "life":
					break;
				case "death":
					$multiplier=2;
					break;
				case "time":
					break;
				case "valor":
					break;
				case "poison":
					$multiplier=2;
					break;
				case "flesh":
					break;
			}
			break;
		case "nature":
			switch ($defendertype) {
				case "metal":
					$multiplier=2;
					break;
				case "technology":
					break;
				case "nature":
					break;
				case "life":
					$multiplier=.5;
					break;
				case "death":
					break;
				case "time":
					break;
				case "valor":
					break;
				case "poison":
					$multiplier=2;
					break;
				case "flesh":
					$multiplier=.5;
					break;
			}
			break;
		case "life":
			switch ($defendertype) {
				case "metal":
					break;
				case "technology":
					break;
				case "nature":
					$multiplier=.5;
					break;
				case "life":
					break;
				case "death":
					$multiplier=.5;
					break;
				case "time":
					break;
				case "valor":
					break;
				case "poison":
					break;
				case "flesh":
					$multiplier=.5;
					break;
			}
			break;
		case "death":
			switch ($defendertype) {
				case "metal":
					$multiplier=.5;
					break;
				case "technology":
					$multiplier=.5;
					break;
				case "nature":
					$multiplier=2;
					break;
				case "life":
					$multiplier=2;
					break;
				case "death":
					break;
				case "time":
					break;
				case "valor":
					$multiplier=2;
					break;
				case "poison":
					break;
				case "flesh":
					$multiplier=2;
					break;
			}
			break;
		case "time":
			switch ($defendertype) {
				case "metal":
					$multiplier=2;
					break;
				case "technology":
					$multiplier=2;
					break;
				case "nature":
					break;
				case "life":
					break;
				case "death":
					break;
				case "time":
					break;
				case "valor":
					break;
				case "poison":
					$multiplier=.5;
					break;
				case "flesh":
					$multiplier=2;
					break;
			}
			break;
		case "valor":
			switch ($defendertype) {
				case "metal":
					break;
				case "technology":
					break;
				case "nature":
					break;
				case "life":
					break;
				case "death":
					$multiplier=2;
					break;
				case "time":
					break;
				case "valor":
					break;
				case "poison":
					break;
				case "flesh":
					$multiplier=.5;
					break;
			}
			break;
		case "poison":
			switch ($defendertype) {
				case "metal":
					$multiplier=.5;
					break;
				case "technology":
					$multiplier=.5;
					break;
				case "nature":
					$multiplier=2;
					break;
				case "life":
					$multiplier=2;
					break;
				case "death":
					$multiplier=.5;
					break;
				case "time":
					$multiplier=2;
					break;
				case "valor":
					break;
				case "poison":
					break;
				case "flesh":
					$multiplier=2;
					break;
			}
			break;
		case "flesh":
			switch ($defendertype) {
				case "metal":
					$multiplier=.5;
					break;
				case "technology":
					$multiplier=.5;
					break;
				case "nature":
					break;
				case "life":
					break;
				case "death":
					$multiplier=.5;
					break;
				case "time":
					$multiplier=.5;
					break;
				case "valor":
					break;
				case "poison":
					break;
				case "flesh":
					break;
			}
			break;
		
	}
	return $multiplier;
}


//array_search for 2-d arrays of holsters
function array_search_holsters ($needle, $haystack) {
	foreach ($haystack as $key => $value) {
		//Search the first field in the array because this is where the name of the item is
		if (array_search($needle, $haystack[$key])!==FALSE) {
			return $key;
		}
	}
	return FALSE;	
}

function calculate_players_damage () {
		if (!isset($_POST['usepotion']) && !isset($_POST['changeweapon']) && !isset($_POST['changeshield']) && ($_SESSION['hp']>0)) {
		
		$random=mt_rand(1,100);
		if ($random<$_SESSION['accuracy']) {
			
			$random=mt_rand(1,100);
			if ($random>$_SESSION['monsteragility']) {
			
				//$random = ((((4*$_SESSION['strength']*mt_rand($_SESSION['weaponlow'], $_SESSION['weaponhigh']/50)/$_SESSION['monsterdefense']))+1)*mt_rand(217, 255))/255;
				
				$firstportionofequation = ($_SESSION['strength']-$_SESSION['monsterdefense']);
				if ($firstportionofequation<0) {
						$firstportionofequation=0;
				}
				$bonusmultiplier=determinetypebonus($_SESSION['weapontype'], $_SESSION['monstershieldtype']);
				$random = $firstportionofequation + (((mt_rand($_SESSION['weaponlow'], $_SESSION['weaponhigh'])+1)-$_SESSION['monsterdefense'])*$bonusmultiplier);
				$random = round($random);
				
				//Since as of right now you can hit for less than 1:
				if ($random<=0) {
					$random=1;
				}
				echo "You hit $_SESSION[monstername] for " . $random . " " . $_SESSION['weapontype'] . " damage!<br/>";
				$_SESSION['monsterhp']-=$random;
				if ($bonusmultiplier>1) {
					echo "You strike for x2 damage!<br/>";
				}
				else if ($bonusmultiplier<1) {
					echo "You strike for x1/2 damage!<br/>";
				}
				else {
				}
				//Some weapons have bonus effects
				determinetypeeffect($_SESSION['weapontype'], $_SESSION['monstershieldtype'], "monster");
				//If monster is poisoned
				if (isset($_SESSION['monsterispoisoned'])) {
					$poisondamage=round((1/10)*$_SESSION['monstermaxhp']);
					//Do at least 1 damage
					if ($poisondamage<1) {
							$poisondamage=1;
					}
					echo $_SESSION['monstername'] . " took " . $poisondamage . " damage from the poisoning!<br/>";
					$_SESSION['monsterhp']-=$poisondamage;
				}

	
				if ($_SESSION['monsterhp'] <= 0) {

					echo "$_SESSION[monstername] died!<br/>";
					
					unset($_SESSION['monstername']);
					$_SESSION['accuracy']=100;
					$_SESSION['agility']=0;
					if (isset($_SESSION['originalspeed'])) {
						$_SESSION['speed']=$_SESSION['originalspeed'];
						unset($_SESSION['originalspeed']);
					}
					if (isset($_SESSION['monsterispoisoned'])) {
						unset($_SESSION['monsterispoisoned']);
					}
					
				
					//$expgain is more or less depending on monster's level
				
					$expgain = (50 + (5*$_SESSION['monsterlevel']));
					
					$_SESSION['exp']+=$expgain;
					echo "You gained $expgain EXP!<br/>";
					if (mt_rand(1,100)<=85) {
						$goldfound=round(((mt_rand(10,75)*$_SESSION['monsterlevel'])/100)+$_SESSION['monsterlevel']);
						$_SESSION['gold']+=$goldfound;
						echo "You found " . $goldfound . " gold!<br/>";
					}
					//Don't find an item if you're fighting a quest monster
					if (mt_rand(1,100)<=50 && !isset($_SESSION['questfight'])) {
						echo "You found a " . $_SESSION['monsteritem'][0] . ". ";
						if (count($_SESSION['itemholster'])<5) {
							echo "You put it in your item bag.";
							$_SESSION['itemholster'][]=array($_SESSION['monsteritem'][0],$_SESSION['monsteritem'][1], $_SESSION['monsteritem'][2]);
						}
						else {
							echo "But you're out of room in your bag, so you throw it away.";
						}
					}
					
					
					if ($_SESSION['exp'] >= $_SESSION['maxexp']) {
					
						$_SESSION['level']++;
						$_SESSION['strength']++;
						$_SESSION['defense']++;
						$_SESSION['speed']++;
						//$_SESSION['agility']++;
						//$_SESSION['accuracy']++;
						$_SESSION['maxhp']+=5;
						$_SESSION['hp']=$_SESSION['maxhp'];
						$_SESSION['exp']=0;
						$_SESSION['maxexp']=(400*($_SESSION['level'])+(($_SESSION['level']*$_SESSION['level'])*20));
						//$_SESSION['maxexp']=1;
						echo "<br/>You are now level $_SESSION[level]!<br/>Each attribute increases by 1!<br/><br/>";
					}
					else {
						//If this is a quest battle, show something that redirects to the random page.
						if (isset($_SESSION['questfight'])) {
							$_SESSION['randomencounter']=true;
							echo "<form name='formvictory' action='random.php' method='post'>
							<input type='submit' name='continue' value='Continue'/></form>";
						}
						else {
							echo "<form name='formvictory' action='index.php' method='post'>
							<input type='submit' name='continue' value='Continue'/></form>";
						}
					}
					include 'inc_charactersave.php';
				}
			}
			else {
				echo "$_SESSION[monstername] dodged!<br/>";
			}
		}
		else {
			echo "You missed!<br/>";
		}
	}
}

function calculate_monsters_damage () {
		if (isset($_SESSION['monstername'])) {
		
		$random=mt_rand(1,100);
		if ($random<$_SESSION['monsteraccuracy']) {
			
			$random=mt_rand(1,100);
			if ($random>$_SESSION['agility']) {

				//$random = ((((4*$_SESSION['monsterstrength']*mt_rand($_SESSION['monsterweaponlow'], $_SESSION['monsterweaponhigh'])/$_SESSION['defense'])/50)+1)*mt_rand(217,255))/255;
				$firstportionofequation = ($_SESSION['monsterstrength']-($_SESSION['defense']));
				if ($firstportionofequation<0) {
						$firstportionofequation=0;
				}
				$bonusmultiplier=determinetypebonus($_SESSION['monsterweapontype'], $_SESSION['shieldtype']);
				$random = $firstportionofequation + (((mt_rand($_SESSION['monsterweaponlow'], $_SESSION['monsterweaponhigh'])+1)-$_SESSION['shieldpower'])*$bonusmultiplier);
				$random = round($random);
				
				//Since as of right now monsters can hit for less than 1:
				if ($random<=0) {
					$random=1;
				}
				echo "$_SESSION[monstername] hit you for $random " . $_SESSION['monsterweapontype'] . " damage!<br/>";
				if ($bonusmultiplier>1) {
					echo "It strikes for x2 damage!<br/>";
				}
				else if ($bonusmultiplier<1) {
					echo "It strikes for x1/2 damage!<br/>";
				}
				else {
				}
				$_SESSION['hp']-=$random;
				//Some weapons have bonus effects
				determinetypeeffect($_SESSION['monsterweapontype'], $_SESSION['shieldtype']);
				//If monster is poisoned
				if (isset($_SESSION['poisoned'])) {
					$poisondamage=round((1/10)*$_SESSION['maxhp']);
					echo "You took " . $poisondamage . " damage from the poisoning!<br/>";
					$_SESSION['hp']-=$poisondamage;
				}
			}
			else {
				echo "You dodged!<br/>";
			}
		}
		else {
			echo "$_SESSION[monstername] missed!<br/>";
		}
	}
}

/*
 * determinetypeeffect function
 * Certain weapon types can have added attack effects. While these weapon types aren't at type advantages to many types,
 * their effects may be worth it to make the first weapon to attack with
 * $attackertype is the type that the weapon is attacking with
 * $defendertype is the type of the shield the defender is using. This can cause certain effects to be nullified
 * $whicheffected is the creature to be effected. Can either be monster or null
 */
function determinetypeeffect($attackertype, $defendertype, $whicheffected=NULL) {
		
		switch ($attackertype) {
		case "time":
			switch ($defendertype) {
				//Does nothing special if defending type is Time
				case "time":
					break;
				//Otherwise, does this
				default:
					//If this effects the monster, do this.
					if ($whicheffected=="monster") {
						//Can only be decreased 4 times
						if ($_SESSION['monsterspeed']>((6/10)*$_SESSION['monsteroriginalspeed'])) {
							echo "You decrease " . $_SESSION['monstername'] . "'s speed!";
							echo "<br/>";
							$_SESSION['monsterspeed']=$_SESSION['monsteroriginalspeed']-ceil((1/10)*$_SESSION['monsteroriginalspeed']);
						}
					}
					else {
						//Can only be decreased 4 times
						if ($_SESSION['speed']>((6/10)*$_SESSION['originalspeed'])) {
							echo "Your speed decreased!";
							echo "<br/>";
							$_SESSION['speed']=$_SESSION['originalspeed']-ceil((1/10)*$_SESSION['originalspeed']);
						}
					}
					break;
			}
			break;
		case "poison":
			switch ($defendertype) {
				//Does nothing if defending type poison
				case "poison":
					break;
				default:
					//If this effects the monster, do this
					if ($whicheffected=="monster") {
						//If random is 7 or below (A little higher than 70% chance), defender is poisoned (if not already poisoned)
						$random=mt_rand(0, 10);
						if ($random<=7 && !isset($_SESSION['monsterispoisoned'])) {
							echo "You poisoned " . $_SESSION['monstername'] . "!";
							echo "<br/>";
							$_SESSION['monsterispoisoned']=TRUE;
						}
					}
					else {
						//If random is 7 or below (A little higher than 70% chance), defender is poisoned (if not already poisoned)
						$random=mt_rand(0, 10);
						if ($random<=7 && !isset($_SESSION['poisoned'])) {
							echo $_SESSION['monstername'] . " poisoned you!";
							echo "<br/>";
							$_SESSION['poisoned']=TRUE;
						}
					}
					break;
			}
			break;
		case "flesh":
			switch ($defendertype) {
				case "metal":
					break;
				case "technology":
					break;
				default:
					//If this effects the monster, do this
					if ($whicheffected=="monster") {
						if ($_SESSION['monsteraccuracy']>(60)) {
							echo "You decrease " . $_SESSION['monstername'] . "'s accuracy!";
							echo "<br/>";
							$_SESSION['monsteraccuracy']=$_SESSION['monsteraccuracy']-10;
						}
					}
					else {
						if ($_SESSION['accuracy']>(60)) {
							echo $_SESSION['monstername'] . " decreased your accuracy!";
							echo "<br/>";
							$_SESSION['accuracy']=$_SESSION['accuracy']-10;
						}
					}
			break;
		
			}
	}
}
 
?>