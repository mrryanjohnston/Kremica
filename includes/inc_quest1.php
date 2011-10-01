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
 * In this quest, a character finds a curious scrap of paper that looks like it belongs to a map.
 * The character must find the other 3 pieces to the map, fighting a drunken pirate in order to get the last piece
 * So, steps will be counted as 0,1,2,3 etc...
 * 
 */



//Title of Quest
echo "<h2>A strange piece of paper...</h2>";
//If a character is not on a quest, his/her queststep will be equal to 0. Start quest here
if ($_SESSION['queststep']==0) {
	
	//Narrative of Quest
	echo "<p>As you adventure, your eye catches a small scrap of paper on the path ahead. Your keen adventurer senses tell you this could lead you on a wonderful journey
	with, perhaps, a nice reward at the end of it. You bend and pick up the piece of paper as soon as you reach it, recognizing it immediately as part of a map. You notice
	the paper's torn edges on the top and right side. Once again, your adventurer senses tell you that you need 3 more pieces in order to follow this map.</p>";
	//Update queststep
	$_SESSION['queststep']++;
	//Update questfile
	$_SESSION['questfile']="inc_quest1.php";
	//Update questID
	$_SESSION['questID']=1;
	include 'inc_charactersave.php';
	unset($_SESSION['randomencounter']);
	//Continue button
	echo "<form name='formvictory' action='index.php' method='post'>
	<input type='submit' name='continue' value='Continue'/></form>";
	
}
else if ($_SESSION['queststep']==1) {
	
	//Narrative of Quest
	echo "<p>You see another piece of paper laying before you! You pick it up and... yes! It fits the other piece of paper! Only 2 more to go...</p>";
	//Update queststep
	$_SESSION['queststep']++;
	include 'inc_charactersave.php';
	unset($_SESSION['randomencounter']);
	//Continue button	
	echo "<form name='formvictory' action='index.php' method='post'>
	<input type='submit' name='continue' value='Continue'/></form>";
	
}
else if ($_SESSION['queststep']==2) {
	
	//Narrative of Quest
	echo "<p>You chase the next scrap of paper on the wind for a good ten minutes. As you finally grab the wandering paper, you notice a splotch of booze-smelling liquid on the corner. Hmm...</p>";
	//Update queststep
	$_SESSION['queststep']++;
	include 'inc_charactersave.php';
	unset($_SESSION['randomencounter']);
	//Continue button
	echo "<form name='formvictory' action='index.php' method='post'>
	<input type='submit' name='continue' value='Continue'/></form>";
	
}
//This is the climax of the quest. This will then direct the character to the fight page.
else if ($_SESSION['queststep']==3) {
	
	//Narrative of Quest
	echo "<p>You follow a strange sound into a dense patch of trees. Your ears ache from the horrible sound, and at this point, you're willing to put whatever is singing the horrible ballad out of its misery.</p>";
	echo "<p>Just as the song is reaching its climax, you happen upon an extremely drunken pirate. Apparently he's looking for a fight!</p>";
	//Update queststep
	$_SESSION['queststep']++;
	//Set a session variable to let the fight page know this is a fight for a quest
	$_SESSION['questfight']=TRUE;
	//Set the monster information
	$_SESSION['monstername']="Really Drunken Pirate";
	$_SESSION['monsterweapontype']="metal";
	$_SESSION['monstershieldtype']="flesh";
	$_SESSION['monsterhp']=20;
	$_SESSION['monstermaxhp']=20;
	$_SESSION['monsterlevel']=3;
	$_SESSION['monsterstrength']=3;
	$_SESSION['monsterdefense']=1;
	$_SESSION['monsterspeed']=1;
	$_SESSION['monsteraccuracy']=10;
	$_SESSION['monsteragility']=0;
	$_SESSION['monsterweaponlow']=3;
	$_SESSION['monsterweaponhigh']=7;
	include 'inc_charactersave.php';
	unset($_SESSION['randomencounter']);
	//Continue button
	echo "<form name='formvictory' action='fight.php' method='post'>
	<input type='submit' name='fight' value='Fight!'/></form>";
	
}
//After the character fights, they'll be sent back here directly.
else if ($_SESSION['queststep']==4) {
	//Unset questfight
	unset($_SESSION['questfight']);	
	echo "<p>The pirate dies with a belch. Pinching your nose, you lean in and reach into his sweet pirate jacket, finding what feels like a scrap of paper.</p>";
	echo "<p>At long last! You've found the map!</p>";
	echo "<p>As you fit the pieces of the map together, you notice it's actually just a piece of a journal with a scribble drawn over top of it.</p>";
	echo "<p>The note reads: \"That drn'd cap'n o' mine. Always hidin' the treasure from me. ME! His first mate! Well I certainly made him change his tune. YAR!\"</p>";
	echo "<p>You shutter at the deed that must have been done. As you toss the journal page to the ground, disappointed, your eye catches something that looks awful like a treasure chest.</p>";
	echo "<p>You open the treasure chest. BOOM! GOLD! And all for you! You go, you adventurer, you.</p>";
	echo "<p>You gained 100 Gold!</p>";
	$_SESSION['gold']+=100;
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
	include 'inc_charactersave.php';
	unset($_SESSION['randomencounter']);

	//Continue button
	echo "<form name='formvictory' action='index.php' method='post'>
	<input type='submit' name='continue' value='Continue'/></form>";
}
?>