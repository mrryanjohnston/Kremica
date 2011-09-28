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

session_start();
	if (!isset($_SESSION['randomencounter'])) {
		//header( 'Location: http://www.kremica.com' ) ;
		header( 'Location: index.php' ) ;
		exit;
}
	if (isset($_SESSION['name']) && (isset($_SESSION['monstername']) || ($_SESSION['strength']+$_SESSION['defense']<(3*$_SESSION['level'])-1))) {
		//header( 'Location: http://www.kremica.com' ) ;
		header( 'Location: fight.php' ) ;
		exit;
}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>Kremica: Random Encounter</title>
<link rel="stylesheet" type="text/css" href="stylesheet.css" />
</head>

<body>
<div id="wrapper">
	<div id="header">
		<?php
		include 'inc_userheader.php';
		?>
	</div>
	
	<div id="conent">
	
<?php

//If character is coming back from a quest battle, re-direct them to their current quest page
if (isset($_SESSION['questfight'])) {
	include($_SESSION['questfile']);
}
else {
	//If character is not currently on a quest
	if (!isset($_SESSION['questfile'])) {
		
		include 'inc_connection.php';
		
		//First, see which quests the character has done (questscompleted table)
		$questcompletequery="SELECT questscompleted.questID FROM questscompleted, characters WHERE characters.characterid=questscompleted.characterID AND characters.name='" . $_SESSION['name'] . "'";
		$questcompleteresult=mysql_query($questcompletequery);
		include 'inc_close_connection.php';
		
		//Next put the IDs of all quests completed by this character into an array
		$questcompletearray=array();
		while ($row=mysql_fetch_assoc($questcompleteresult)) {
			$questcompletearray[]=$row['questID'];
		}
		
		//Now what will happen: we'll try picking quests from the folder until we hit one the character hasn't completed yet,
		//aka one that is not in the $questscompletearray
		
		$directory = "questincludes/1/";
		
		//Count number of quests in the directory
		$filecount = count(glob("inc_quest*.php"));
		
		//Put these numbers into an array
		$filecountarray=array();
		while ($filecount>0) {
			$filecountarray[]=$filecount;
			$filecount--;
		}
		
		//Now, randomly pick a number from the array until you hit one the character hasn't completed yet.
		//If you find one the character has completed, remove it from the array
		$questfound=FALSE;
		$nomorequests=FALSE;
		$random=0;
		while (!$questfound) {
			//If we got through the entire $filecountarray and found nothing (aka the count is now 0, exit the while loop
			if (count($filecountarray)==0) {
				$questfound=TRUE;
				$nomorequests=TRUE;
				break;
			}
			
			//Pick a random number from the quests left
			$random = array_rand($filecountarray);
			//If the quest is not in the $questcompletearray[], set $questfound to TRUE;
			if (!in_array($filecountarray[$random], $questcompletearray)) {
				$questfound=TRUE;
			}
			
			//If the quest is in the $questcompletearray[], remove the quest number from $filecountarray[]
			else {
				foreach($filecountarray as $key => $value) {
					if ($filecountarray[$random]==$value) {
						unset($filecountarray[$key]);
						$filecountarray=array_values($filecountarray);
						break;
					}
				}
			}
		}
		//If a quest was found, include it.
		if (!$nomorequests) {
			//Pick the folder appropriate to the character's level
			//Normally, these would be put into folders. However, for the purpose of the assignment,
			//the quests are just in the current folder at all times.
			/*$levelfolder=1;
			if ($_SESSION['level']>=5) {
				$levelfolder=5;
			}
			else if ($_SESSION['level']>=10) {
				$levelfolder=10;
			}
			else if ($_SESSION['level']>=15) {
				$levelfolder=15;
			}*/
			include(/*'questincludes/' . $levelfolder . '/*/'inc_quest' . $filecountarray[$random] . '.php');
		}
		//If not, tell the character they've done all the quests for now.
		else {
			echo "Sorry, you've done all the quests for now!";
			unset($_SESSION['randomencounter']);
			//Continue button
			echo "<form name='formvictory' action='index.php' method='post'>
			<input type='submit' name='continue' value='Continue'/></form>";
		}
	}
	//If a character is on a quest
	else {
		include(/*'questincludes/' . */$_SESSION['questfile']);
	}
}
?>

</div>
<div id="footer">
	<div id="footer_inner">
<?php	include('inc_footer.html'); ?>
	</div>
</div>
</div>
</body>

</html>