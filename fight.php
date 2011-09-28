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

require_once('inc_functions.php');

/******
 * Must have a character
 ******
 * This page cannot be accessed unless the user is on a character
 */
session_start();
	if (!isset($_SESSION['name'])) {
		//header( 'Location: http://www.kremica.com' ) ;
		header( 'Location: index.php' ) ;
		exit;
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
<title>Kremica: Battle</title>
<link rel="stylesheet" type="text/css" href="stylesheet.css" />
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<meta name="generator" content="Geany 0.19.1" />

</head>

<body>

<div id="wrapper">
	<div id="header">
<?php
include 'inc_userheader.php';
?>
	</div>
	<div id="content">

<h2>Battle</h2>

<?php
/******
 * Use Potion
 ******
 * If character uses a potion, uses one of the potions from potion holster,
 * Heals character's health, and then removes from the potion holster array.
 * If a character uses a potion, he or she will not be able to attack this turn.
 */
if (isset($_POST['usepotion'])) {
	//Since $_POST likes to make my life annoying, it adds a _ instead of a space. str_replace time
	$potiontouse=str_replace("_", " ", $_POST['itemtouse']);
	//If, for some reason, the potion isn't in the potion holster
	if (array_search_holsters($potiontouse, $_SESSION['potionholster'])===FALSE) {
		//If something went wrong, just send back to the fight page
		//header( 'Location: http://www.kremica.com' ) ;
		header( 'Location: fight.php' ) ;
		exit;

	}
	else {
		//Return the key where the first potion in the array with the name past via post is located
		$key=array_search_holsters($potiontouse, $_SESSION['potionholster']);
		//If a character's maximum HP will be reached by using the potion, just set
		//hp to maxhp
		if (($_SESSION['maxhp']-$_SESSION['hp'])<=$_SESSION['potionholster'][$key][1]) {
			$_SESSION['hp']=$_SESSION['maxhp'];
		}
		else {
			$_SESSION['hp']+=$_SESSION['potionholster'][$key][1];
		}
		echo "<center>You have been healed!</center>";
		//attack is set to true so that we may enter the battle conditional.
		//A character will not attack, though, since they used a potion this turn
		$_POST['attack']="true";
		//Remove the potion from the potion holster
		unset($_SESSION['potionholster'][$key]);
		//Re-organize the potion holster, removing empty slot
		$_SESSION['potionholster']=array_values($_SESSION['potionholster']);
	}
}

/******
 * Change Weapon
 ******
 * If character choses to change their currently equipped weapon,
 * They use their turn to switch out to a different type of weapon (something perhaps
 * giving them the advantage in battle.
 * If a character choses to change their current equipped weapon,
 * he or she will not be able to attack this turn.
 */
else if (isset($_POST['changeweapon'])) {
	//Since $_POST likes to make my life annoying, it adds a _ instead of a space. str_replace time
	$weapontoswitch=str_replace("_", " ", $_POST['weapontoswitchto']);
	//If, for some reason, weapon to switch to isn't in the weapon holster
	if (array_search_holsters($weapontoswitch, $_SESSION['weaponholster'])===FALSE) {
		//If something went wrong, just send back to the fight page
		//header( 'Location: http://www.kremica.com' ) ;
		header( 'Location: fight.php' ) ;
		exit;
	}
	else {
		//Searches the holsters with my custom function and returns the key of the weapon to switch to
		$key=array_search_holsters($weapontoswitch, $_SESSION['weaponholster']);
		//Then equips the weapon
		$_SESSION['weaponslotequipped']=$key;
		$_SESSION['weaponequipped']=$_SESSION['weaponholster'][$key][0];
		$_SESSION['weapontype']=$_SESSION['weaponholster'][$key][3];
		$_SESSION['weaponlow']=$_SESSION['weaponholster'][$key][1];
		$_SESSION['weaponhigh']=$_SESSION['weaponholster'][$key][2];
		//Gives a victory message
		echo "You equipped " . $_SESSION['weaponequipped'];
		//attack is set to true so that we may enter the battle conditional
		//A character will not attack, though, since they switched weapons this turn
		$_POST['attack']="true";
	}
}	

/******
 * Change Shield
 ******
 * If character choses to change their currently equipped shield,
 * They use their turn to switch out to a different type of shield (something perhaps
 * giving them the advantage in battle.
 * If a character choses to change their current equipped shield,
 * he or she will not be able to attack this turn.
 */
else if (isset($_POST['changeshield'])) {
	//Since $_POST likes to make my life annoying, it adds a _ instead of a space. str_replace time
	$shieldtoswitch=str_replace("_", " ", $_POST['shieldtoswitchto']);
	//If, for some reason, shield to switch to isn't in the shield holster
	
	if (array_search_holsters($shieldtoswitch, $_SESSION['shieldholster'])===FALSE) {
		//If something went wrong, just send back to the fight page
		//header( 'Location: http://www.kremica.com' ) ;
		echo array_search_holsters($shieldtoswitch, $_SESSION['shieldholster']);
		echo $shieldtoswitch;
		//header( 'Location: fight.php' ) ;
		//exit;
	}
	else {
		//Searches the holsters with my custom function and returns the key of the shield to switch to
		$key=array_search_holsters($shieldtoswitch, $_SESSION['shieldholster']);
		//Then equips the shield
		$_SESSION['shieldslotequipped']=$key;
		$_SESSION['shieldequipped']=$_SESSION['shieldholster'][$key][0];
		$_SESSION['shieldtype']=$_SESSION['shieldholster'][$key][2];
		$_SESSION['shieldpower']=$_SESSION['shieldholster'][$key][1];
		//Gives a victory message
		echo "You equipped " . $_SESSION['shieldequipped'];
		//attack is set to true so that we may enter the battle conditional
		//A character will not attack, though, since they switched shields this turn
		$_POST['attack']="true";
	}
}
/******
 * Battle Conditional
 ******
 * This is where damage calculations take place. Damage is calculated based on strength,
 * defense, shield/weapon power, and type bonuses.
 */
if (isset($_POST['attack']) && isset($_SESSION['monstername']))  {
	echo "<center>";

	//If players speed is greater than monster's, the player will attack first
	//If not, the monster will attack first
	//Saves the character after every turn
	if ($_SESSION['speed']>=$_SESSION['monsterspeed'])  {
		calculate_players_damage();
		echo "<br/>";
		calculate_monsters_damage();
		include 'inc_charactersave.php';
	}
	else {
		calculate_monsters_damage();
		echo "<br/>";
		calculate_players_damage();
		include 'inc_charactersave.php';
	}

	//If the character's health drops to 0 or below, they died.
	if ($_SESSION['hp']<=0) {
		include 'inc_dead.php';
		echo "You died. Sorry.";
		echo "<form name='formvictory' action='index.php' method='post'>
		<input type='submit' name='continue' value='Continue'/></form>";
	}
	//If their health is not 0, they can still attack
	else {
		if (isset($_SESSION["monstername"])) {
		
			echo "You are fighting a $_SESSION[monstername] !<br/>
			$_SESSION[monstername]'s HP: $_SESSION[monsterhp] / $_SESSION[monstermaxhp]<br/>
			HP: $_SESSION[hp] / $_SESSION[maxhp]";		
			
			echo "<form name='form1' action='fight.php' method='post'>
			<input type='submit' name='attack' value='Attack $_SESSION[monstername]!'/><br/>
			Potion Holster: <select name='itemtouse'><option value='none'>---</option>";

			foreach ($_SESSION['potionholster'] as $key => $value) {

					echo "<option value='" . $value[0] . "'>" . $value[0] . " - " . $value[1] . "</option>";

			}

			echo "</select><input type='submit' name='usepotion' value='Use!'/><br/>
			Change Weapon: <select name='weapontoswitchto'><option value='none'>---</option>";
		
			foreach ($_SESSION['weaponholster'] as $key => $value) {

				//if ($_SESSION['weaponequipped']!==$value[0]) {
					echo "<option value='" . $value[0] . "'>" . $value[0] . " - " . $value[3]  . "</option>";
				//}

			}
		
			echo "</select><input type='submit' name='changeweapon' value='Change Weapon!'/><br />
			Change shield: <select name='shieldtoswitchto'><option value='none'>---</option>";
				
			foreach ($_SESSION['shieldholster'] as $key => $value) {

				//if ($_SESSION['shieldequipped']!==$value[0]) {
					echo "<option value='" . $value[0] . "'>" . $value[0] . " - " . $value[2] . "</option>";
				//}

			}
		
		
			echo "</select><input type='submit' name='changeshield' value='Change Shield!'/></form></center>";
		}
		//If a player's experience points are now equal to 0,
		//They have gained a level and may now choose which attributes to increase
		else if ($_SESSION["exp"]==0) {
			echo "Please choose how you wish to spend your 3 bonus attribute points:<br/>";
			echo "<form name='levelupform' action='fight.php' method='post'><table><tr><td>Strength</td><td>Defense</td><td>Speed</td></tr>
				<tr><td><select name='strength'><option value='0'>--</option><option value='1'>1</option><option value='2'>2</option><option value='3'>3</option></select></td>
				<td><select name='defense'><option value='0'>--</option><option value='1'>1</option><option value='2'>2</option><option value='3'>3</option></select></td>
				<td><select name='speed'><option value='0'>--</option><option value='1'>1</option><option value='2'>2</option><option value='3'>3</option></select></td>
				</tr></table>
				<input type='submit' name='attributeup' value='Add Attributes!'/></form></center>";
		}
	}
}
//If attack was not set, then the character has just run into the monster
else if (isset($_SESSION['monstername']))  {
	echo "<center>You ran into a $_SESSION[monstername] !<br/>
		$_SESSION[monstername]'s HP: $_SESSION[monsterhp] / $_SESSION[monstermaxhp]<br/>
		HP: $_SESSION[hp] / $_SESSION[maxhp]";
		echo "<form name='form1' action='fight.php' method='post'>
		<input type='submit' name='attack' value='Attack $_SESSION[monstername]!'/><br/>
		Potion Holster: <select name='itemtouse'><option value='none'>---</option>";

			foreach ($_SESSION['potionholster'] as $key => $value) {

					echo "<option value='" . $value[0] . "'>" . $value[0] . " - " . $value[1] . "</option>";

			}

		echo "</select><input type='submit' name='usepotion' value='Use!'/><br/>
		Change Weapon: <select name='weapontoswitchto'><option value='none'>---</option>";
		
			foreach ($_SESSION['weaponholster'] as $key => $value) {

				//if ($_SESSION['weaponequipped']!==$value[0]) {
					echo "<option value='" . $value[0] . "'>" . $value[0] . " - " . $value[3] . "</option>";
				//}

			}
		
		echo "</select><input type='submit' name='changeweapon' value='Change Weapon!'/><br />
		Change shield: <select name='shieldtoswitchto'><option value='none'>---</option>";
			
			foreach ($_SESSION['shieldholster'] as $key => $value) {

				//if ($_SESSION['shieldequipped']!==$value[0]) {
					echo "<option value='" . $value[0] . "'>" . $value[0] . " - " . $value[2] . "</option>";
				//}

			}
		
		
		echo "</select><input type='submit' name='changeshield' value='Change Shield!'/></form></center>";
}
/******
 * Attribute Up
 ******
 * If a character has chosen their attributes correctly, they are increased accordingly. A character
 * may not increase their stats by more than 3 BONUSE points per level. By default, all attributes are increased by 1 every
 * level.
 * 
 * If something happened which caused the character to navigate away from this page without setting
 * their attributes properly (only adding 3, no more, no less), then they are redirected away
 */
else if (isset($_POST["attributeup"]) || ($_SESSION['strength']+$_SESSION['speed']+$_SESSION['defense']<(($_SESSION['level']*5)-3))) {
	$attributetest=0;
	if (isset($_POST["strength"])) 
		$attributetest+=$_POST["strength"];
	if (isset($_POST["defense"])) 
		$attributetest+=$_POST["defense"];
	if (isset($_POST["speed"]))
		$attributetest+=$_POST["speed"];
	
	if ($attributetest==3) {
		$_SESSION['strength']+=$_POST["strength"];
		$_SESSION['defense']+=$_POST["defense"];
		$_SESSION['speed']+=$_POST["speed"];
		echo "<center>Attributes increased!";
		include 'inc_charactersave.php';
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
	else {
		echo "<center>You tried to add more or less than 3 attribute points. Try again, smarty pants.<br/><br/>";
		echo "Please choose how you wish to spend your 3 attribute points:<br/>";
		echo "<form name='levelupform' action='fight.php' method='post'><table><tr><td>Strength</td><td>Defense</td><td>Speed</td></tr>
		<tr><td><select name='strength'><option value='0'>--</option><option value='1'>1</option><option value='2'>2</option><option value='3'>3</option></select></td>
		<td><select name='defense'><option value='0'>--</option><option value='1'>1</option><option value='2'>2</option><option value='3'>3</option></select></td>
		<td><select name='speed'><option value='0'>--</option><option value='1'>1</option><option value='2'>2</option><option value='3'>3</option></select></td>
		</tr></table>
		<input type='submit' name='attributeup' value='Add Attributes!'/></form></center>";
	}
}
//If something weird happens, just re-direct to index.php
else {
	//header( 'Location: http://www.kremica.com' ) ;
	header( 'Location: index.php' ) ;
	exit;
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