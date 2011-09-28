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
 * This page contains detailed information about the current character. Later,
 * this can be expanded to chow details about any character. For now, though,
 * it only does the current character for the user account.
 */
 
 //Session starts
session_start();

/******
 * Must have account
 ******
 * Must have an account to view
 */

if (!isset($_SESSION['username'])) {
		//header( 'Location: http://www.kremica.com' ) ;
		header( 'Location: index.php' ) ;
		exit;
}

/******
 * In-Battle Check 
 ******
 * If the character is in the middle of a fight (ie if the monstername is set), OR if they have leveled and not yet set their attribute points
 * (ie if their strength, defense, and speed are all combined less than 6 times that of their level (minus 3)), transfer back to the fight page.
 * Also, unset the charsheet variable, because, apparently, it was accidentally set.
 */
if (isset($_SESSION['name']) && (isset($_SESSION['monstername']) || ($_SESSION['strength']+$_SESSION['defense']+$_SESSION['speed']<(6*$_SESSION['level'])-3))) {
	//header( 'Location: http://www.kremica.com' ) ;
	unset($_SESSION['charsheet']);
	header( 'Location: fight.php' );
	exit;
}

/******
 * Whose charsheet?
 ******
 * If the user wishes to look at someone else's character sheet, then charname will be
 * set in the address bar. If not, or if the charname is the current character's name, no look-up is necessary.
 * 
 * If the character to look at doesn't exist, just show the N/A character sheet
 */
$charactername="N/A";
$hp="?";
$maxhp="?";
$exp="?";
$maxexp="?";
$level="?";
$strength="?";
$defense="?";
$speed="?";
$gender="";
//If the charname is specified in the addressbar and it isn't the current character,
//look up the character stats in the database
if (isset($_GET['charname']) && $_GET['charname']!=$charactername) {
	include 'inc_connection.php';

	$query="SELECT * FROM characters WHERE name='" . mysql_real_escape_string(trim($_GET['charname'])) . "'";
	$queryresult=mysql_query($query);
	if (!$queryresult) {
		die('mySql error: ' . mysql_error());
	}
	$queryarray=mysql_fetch_assoc($queryresult);
	//if character name found:
	if (isset($queryarray['name'])) {
		$charactername=$queryarray['name'];
		$hp=$queryarray['hp'];
		$maxhp=$queryarray['maxhp'];
		$exp=$queryarray['exp'];
		$maxexp=$queryarray['maxexp'];
		$level=$queryarray['level'];
		$strength=$queryarray['strength'];
		$defense=$queryarray['defense'];
		$speed=$queryarray['speed'];
		$gender=$queryarray['gender'];
	}
	include 'inc_close_connection.php';
}
//Else, if there is a current character alive, show that
else if (isset($_SESSION['name'])) {
	$charactername=$_SESSION['name'];
	$hp=$_SESSION['hp'];
	$maxhp=$_SESSION['maxhp'];
	$exp=$_SESSION['exp'];
	$maxexp=$_SESSION['maxexp'];
	$level=$_SESSION['level'];
	$strength=$_SESSION['strength'];
	$defense=$_SESSION['defense'];
	$speed=$_SESSION['speed'];
	$gender=$_SESSION['gender'];
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>Kremica: Character Stats</title>
<link rel="stylesheet" type="text/css" href="stylesheet.css" />
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<meta name="generator" content="Geany 0.19.1" />
<script type="text/javascript" src="jquery-1.4.4.js"></script>
<script type="text/javascript">
$(function() {
	$('.characterstats tr:odd').addClass('alt');
});
</script>
</head>

<body>
<div id="wrapper">
	<div id="header">
<?php
include 'inc_userheader.php';
?>
	</div>
	<div id="content">
		<div id="onecolumn">
			<h2>Character Stats</h2>
			<br/>
<?php
echo "
			<img class='floatleft' width='150px' src='images/" . $gender . "head.jpg' alt='That\s your head!'/>
";

?>
			<table class="characterstats floatright">
<?php	
	/******
	 * Stat Table
	 ******
	 * This is a basic layout of the character's stats.
	 * 
	 * Character can either by found, not found, or found and dead.
	 */		
	echo "
				<tr>
					<th>";
	echo "<h3 class='floatleft'>" . $charactername . "</h3>";
	//If no character was found, report back accordingly
	if ($hp=="?") {
		echo "<p class='floatleft'>Couldn't find character</p>";
	}
	//If a character is dead, report back accordingly
	else if ($hp<=0) {
		echo "<p class='floatleft'>&nbsp;(Dead)</p>";
	}
	echo "</th>
				</tr>
";
	echo "
				<tr>
					<td><p>Level: " . $level . "</p></td>
					<td><p>Strength: " . $strength . "</p></td>
				</tr>
				<tr>
					<td><p>HP: " . $hp . " / " . $maxhp . "</p></td>
					<td><p>Defense: " . $defense . "</p></td>
				</tr>
				<tr>
					<td><p>EXP: " . $exp . " / " . $maxexp . "</p></td>
					<td><p>Speed: " . $speed . "</p></td>
				</tr>
			</table>
";
	
?>
		</div>
	</div>

<div id="footer">
	<div id="footer_inner">
<?php	include('inc_footer.html'); ?>
	</div>
</div>
	
</div>

</body>

</html>