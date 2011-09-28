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
 * Characters can carry multiple weapons, shields, potions, and items.
 * This is where they can manipulate them (use, equip, toss, etc)
 */

session_start();
	if (!isset($_SESSION['inventory'])) {
		//header( 'Location: http://www.kremica.com' ) ;
		header( 'Location: index.php' ) ;
		exit;
}
if (isset($_POST['continue'])) {
	unset($_SESSION['inventory']);
	//header( 'Location: http://www.kremica.com' ) ;
	header( 'Location: index.php' ) ;
	exit;
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>Kremica: Inventory</title>
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
		<h2>Inventory</h2>
<?php
//If character uses a potion, character is healed for the power of the potion
//and the potion is discarded from the potion holster.
if (isset($_POST['usepotion'])) {
	//Goes through each item in potion holster and looks for one whose name
	//matches the one about to be used. If potion is not found,
	//return an error and don't heal.
	$potionfound=FALSE;
	$oldhealth=$_SESSION['hp'];
	foreach ($_SESSION['potionholster'] as $key => $value) {
		if ($value[0]===$_POST['potion']) {
			$potionfound=TRUE;
			if (($_SESSION['maxhp']-$_SESSION['hp'])<=$_SESSION['potionholster'][$key][1]) {
				$_SESSION['hp']=$_SESSION['maxhp'];
			}
			else {
				$_SESSION['hp']+=$_SESSION['potionholster'][$key][1];
			}
			unset($_SESSION['potionholster'][$key]);
			$_SESSION['potionholster']=array_values($_SESSION['potionholster']);
			unset($_SESSION['inventory']);
			echo "<p>Used " . $_POST['potion'] . " and healed for " . ($_SESSION['hp']-$oldhealth) . "!</p>";
			echo "<p>Health is now " . $_SESSION['hp'] . "/" . $_SESSION['maxhp'] . ".</p>";
			break;
		}
	}
	if (!$potionfound) {
		echo "<p>Sorry, you don't seem to have that potion anymore.</p>";
	}
	echo "<form name='formvictory' action='index.php' method='post'>
	<input type='submit' name='continue' value='Continue'/></form>";
	include 'inc_charactersave.php';
}
/******
 * Equip Weapon
 ******
 * If a character choses to equip a weapon, find the weapon they've chosen to
 * equip and set all current equipped info to that
 */
else if (isset($_POST['equipweapon'])) {
	$equipweapon=str_replace("_", " ", $_POST['weapontoequip']);
	foreach ($_SESSION['weaponholster'] as $key => $value) {
		if ($equipweapon===$value[0]) {
			$_SESSION['weaponslotequipped']=$key;
			$_SESSION['weaponequipped']=$_SESSION['weaponholster'][$key][0];
			$_SESSION['weapontype']=$_SESSION['weaponholster'][$key][3];
			$_SESSION['weaponlow']=$_SESSION['weaponholster'][$key][1];
			$_SESSION['weaponhigh']=$_SESSION['weaponholster'][$key][2];
			$_SESSION['weaponequippedID']=$_SESSION['weaponholster'][$key][4];
		}
	}
	unset($_SESSION['inventory']);
	echo "<p>You equipped " . $_SESSION['weaponequipped'] . "</p>";
	echo "<form name='formvictory' action='index.php' method='post'>
	<input type='submit' name='continue' value='Continue'/></form></center>";
	include 'inc_charactersave.php';
}
/******
 * Equip Shield
 ******
 * If a character choses to equip a shield, find the weapon they've chosen to
 * equip and set all current equipped info to that
 */
else if (isset($_POST['equipshield'])) {
	$equipshield=str_replace("_", " ", $_POST['shieldtoequip']);
	foreach ($_SESSION['shieldholster'] as $key => $value) {
		if ($equipshield===$value[0]) {
			$_SESSION['shieldslotequipped']=$key;
			$_SESSION['shieldtype']=$_SESSION['shieldholster'][$key][2];
			$_SESSION['shieldequipped']=$_SESSION['shieldholster'][$key][0];
			$_SESSION['shieldequippedID']=$_SESSION['shieldholster'][$key][3];
			$_SESSION['shieldpower']=$_SESSION['shieldholster'][$key][1];
		}
	}
	unset($_SESSION['inventory']);
	echo "<p>You equipped " . $_SESSION['shieldequipped'] . "</p>";
	echo "<form name='formvictory' action='index.php' method='post'>
	<input type='submit' name='continue' value='Continue'/></form></center>";
	include 'inc_charactersave.php';
}
/******
 * Throw away
 ******
 * If a character choses to toss an item, whether it be a weapon, shield
 * or simply an item, remove the item from the array then rearrange.
 * 
 * If the item to be tossed is fists or bare arms, do not allow. Come on,
 * it's impossible to drop your fists or arms.
 */
else if (isset($_POST['throwaway'])) {
	//If throwing away a weapon, come in here
	if (isset($_POST['weapontoequip'])) {
		$weapontoequip=str_replace("_", " ", $_POST['weapontoequip']);
		if ($weapontoequip==="Fists") {
			echo "<p>You can't seem to throw away your Fists as hard as you try.</p>";
		}
		else {
			foreach ($_SESSION['weaponholster'] as $key => $value) {
				if ($value[0]===$weapontoequip) {
					unset($_SESSION['weaponholster'][$key]);
					array_values($_SESSION['weaponholster']);
					echo "<p>Threw away " . $weapontoequip . ".</p>";
					//If thrown away weapon is the one equipped, switch to Fists
					if ($key===$_SESSION['weaponslotequipped']) {
						$_SESSION['weaponslotequipped']=0;
						$_SESSION['weaponequipped']=$_SESSION['weaponholster'][0][0];
						$_SESSION['weapontype']=$_SESSION['weaponholster'][0][3];
						$_SESSION['weaponlow']=$_SESSION['weaponholster'][0][1];
						$_SESSION['weaponhigh']=$_SESSION['weaponholster'][0][2];
						$_SESSION['weaponequippedID']=$_SESSION['weaponholster'][0][4];
					}
					include 'inc_charactersave.php';
					break;
				}
			}
		}
	}
	//If throwing away a shield, come in here
	else if (isset($_POST['shieldtoequip'])) {
		$shieldtoequip=str_replace("_", " ", $_POST['shieldtoequip']);
		if ($shieldtoequip==="Bare Arms") {
			echo "<p>You can't seem to throw away your Bare Arms as hard as you try.</p>";
		}
		else {
			foreach ($_SESSION['shieldholster'] as $key => $value) {
				if ($value[0]===$shieldtoequip) {
					unset($_SESSION['shieldholster'][$key]);
					array_values($_SESSION['shieldholster']);
					echo "Threw away " . $shieldtoequip . ".";
					//If thrown away shield is the one equipped, switch to Bare Arms
					if ($key===$_SESSION['shieldslotequipped']) {
						$_SESSION['shieldslotequipped']=0;
						$_SESSION['shieldequipped']=$_SESSION['shieldholster'][0][0];
						$_SESSION['shieldtype']=$_SESSION['shieldholster'][0][2];
						$_SESSION['shieldpower']=$_SESSION['shieldholster'][0][1];
						$_SESSION['shieldequippedID']=$_SESSION['shieldholster'][0][3];
					}
					include 'inc_charactersave.php';
					break;
				}
			}
		}
	}
	//If throwing away items, come in here
	else if (isset($_POST['items'])) {
		foreach ($_SESSION['itemholster'] as $key => $value) {
			if ($value[0]===$_POST['items']) {
				unset($_SESSION['itemholster'][$key]);
				array_values($_SESSION['itemholster']);
				echo "<p>Threw away " . $_POST['items'] . ".</p>";
				include 'inc_charactersave.php';
				break;
			}
		}
	}
	unset($_SESSION['inventory']);
	echo "<form name='formvictory' action='index.php' method='post'>
	<input type='submit' name='continue' value='Continue'/></form>";
}
else {
	echo "<p>Here's your potion holster:</p>";
	
	echo "<form name='potion' action='inventory.php' method='post'><select name='potion'>";

	foreach ($_SESSION['potionholster'] as $value) {
		echo "<option value='" . $value[0] . "'>" . $value[0] . " - " . $value[1] . "</option> ";
	}
	
	echo "</select><input type='submit' name='usepotion' value='Use'/></form>";

	//Weapon Stuff
	echo "<p>Currently equipped weapon: " . $_SESSION['weaponequipped'] . "</p>";
	echo "<p>Here's your weapon slots:</p>";	
	echo "<form name='equipweapon' action='inventory.php' method='post'><select name='weapontoequip'>";
	
	foreach ($_SESSION['weaponholster'] as $value => $key) {
		echo "<option value='" . $key[0] . "'>" . $key[0] . " - " . $key[3] . "</option>";
	}
	echo "<input type='submit' name='equipweapon' value='Equip'/><input type='submit' name='throwaway' value='Throw Away'/></form>";
	
	//Shield Stuff
	echo "<p>Currently equipped shield: " . $_SESSION['shieldequipped'] . "</p>";
	echo "<p>Here's your shield slots:</p>";
	echo "<form name='equipshield' action='inventory.php' method='post'>";
	echo "<select name='shieldtoequip'>";
	foreach ($_SESSION['shieldholster'] as $value => $key) {
		echo "<option value='" . $key[0] . "'>" . $key[0] . " - " . $key[2] . "</option>";
	}
	echo "<input type='submit' name='equipshield' value='Equip'/><input type='submit' name='throwaway' value='Throw Away'/></form>";
	
	echo "<p>Here's your item bag:</p>";
	echo "<form name='item' action='inventory.php' method='post'><select name='items'>";

	    foreach ($_SESSION['itemholster'] as $value) {
		echo "<option value='" . $value[0] . "'>" . $value[0] . "</option> ";
	}
	
	echo "</select><input type='submit' name='throwaway' value='Throw Away'/></form>";
	
	echo "<p>Gold in bag: " . $_SESSION['gold'] . "</p>";

	
	echo "<form name='formvictory' action='inventory.php' method='post'>
	<input type='submit' name='continue' value='Continue'/></form></center>";
	
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