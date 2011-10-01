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

if (substr_count($_SERVER['SCRIPT_NAME'],  "random.php")!=1) {
		//header( 'Location: http://www.kremica.com' ) ;
		header( 'Location: index.php' ) ;
		exit;
}

/*
 * The goal of this file is to be a prototype for how quests should be handled
 * 
 * Characters will have SESSION variables set for the name of the current quest, the 
 * include file to use for the current quest, and the step number of
 * the quest the current character is on.
 * 
 * Quests are handled like so:
 * When a character gets directed to random.php, a number is then selected at random. If the number corresponds
 * to the number range for landing on a quest (as opposed to an infuser or what-have-you),
 * the random page then looks at the SESSION variables to see if the character already has a quest.
 * If it does, re-open the given include file according to the appropriate session variable.
 * 
 * If not, give a quest according to the level correspondance... Levels of quests can
 * be determined by looking at what folder they are in.
 * 
 * Levels are:
 * 1: 1-4
 * 5: 5-9
 * 10: 10-14
 * 15: 15-19
 * 
 * Etc...
 * 
 * In this quest, a character finds an amulet which temporarily increases his/her
 * defense. It wears out after the character runs into this quest again
 * 
 */



//Title of Quest
echo "<h2>A strange amulet</h2>";
//If a character is not on a quest, his/her queststep will be equal to 0. Start quest here
if ($_SESSION['queststep']==0) {
	
	//Narrative of Quest
	echo "<p>Your journeys lead you to a deserted beach. Something glimmering in the sand catches your eye. You pick it up, and immediately recognize it
	as an awesome amulet. You put it on and feel like you could take on the world! Well, ok not the world but you definitely feel tougher defense wise.</p>";
	echo "<p>Defense increased!</p>";
	//Update queststep
	$_SESSION['queststep']++;
	//Update questfile
	$_SESSION['questfile']="inc_quest3.php";
	//Update questID
	$_SESSION['questID']=3;
	//Make Defense Higher
	$_SESSION['defense']+=5;
	include 'inc_charactersave.php';
	unset($_SESSION['randomencounter']);
	//Continue button
	echo "<form name='formvictory' action='index.php' method='post'>
	<input type='submit' name='continue' value='Continue'/></form>";
	
}
else if ($_SESSION['queststep']==1) {
	
	//Narrative of Quest
	echo "<p>Aw, shoot! Your amulet literally exploded. Luckily it was a small amulet, and the metal shards won't take long to pick out of your chest.</p>";
	echo "<p>Defense decreased!</p>";
	echo "<p>Health decreased!</p>";
	//Upon completion of quest, must insert new row into questscompleted table
	include 'inc_connection.php';
	$getcharacteridquery="SELECT characterid FROM characters WHERE name='" . $_SESSION['name'] . "'";
	$getcharacteridarray=mysql_fetch_assoc(mysql_query($getcharacteridquery));
	$questcompletequery="INSERT INTO questscompleted VALUES (NULL, '" . $_SESSION['questID'] . "', '" . $getcharacteridarray['characterid'] . "')";
	$questcompleteresult=mysql_query($questcompletequery);
	if (!$questcompleteresult) {
		die ('This shouldn\'t happen. Woops. ' . mysql_error());
	}
	include 'inc_close_connection.php';
	//Reset queststep
	$_SESSION['queststep']=0;
	//Unset questfile
	unset($_SESSION['questfile']);
	//Update questID
	$_SESSION['questID']=0;
	//Return Defense to normal
	$_SESSION['defense']-=5;
	//Lower HP
	$_SESSION['hp']-=2;
	//If a this kills a character:
	if ($_SESSION['hp']<=0) {
		include 'inc_dead.php';
		echo "You died. Sorry.";
		echo "<form name='formvictory' action='index.php' method='post'>
		<input type='submit' name='continue' value='Continue'/></form>";
		die();
	}
	
	
	include 'inc_charactersave.php';
	unset($_SESSION['randomencounter']);

	//Continue button
	echo "<form name='formvictory' action='index.php' method='post'>
	<input type='submit' name='continue' value='Continue'/></form>";
}
?>