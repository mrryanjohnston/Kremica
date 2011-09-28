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
 * The Infuser can change the type that the weapon or shield is.
 * This is accomplished by taking the type of an item found after battling
 * a monster, changing the weapon/shield type to it, changing the name of the
 * weapon or shield, and then removing the item used from the inventory.
 */

session_start();
if (!isset($_SESSION['infuser'])) {
	//header( 'Location: http://www.kremica.com' ) ;
	header( 'Location: index.php' ) ;
	exit;
}
if (isset($_SESSION['name']) && (isset($_SESSION['monstername']) || ($_SESSION['strength']+$_SESSION['defense']<(3*$_SESSION['level'])-1))) {
	//header( 'Location: http://www.kremica.com' ) ;
	header( 'Location: fight.php' ) ;
	exit;
}
//If the character choses not to infuse anything, leave this page
if (isset($_POST['nevermind'])) {
	unset($_SESSION['infuser']);
	//header( 'Location: http://www.kremica.com' ) ;
	header( 'Location: index.php' ) ;
	exit;
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>Kremica: Infuser</title>
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
<?php

/******
 * Infusion
 ******
 * If the character decided to infuse an item into his/her weapon/shield,
 * go into here. Change the type of weapon/shield, change the weapon/shield's name,
 * then discard the item.
 */
if (isset($_POST['infuse'])) {

include 'inc_connection.php';
//If item to infuse isn't set, aka they didn't chose an item, give them
//and error and dump them back at the infuser
if (!isset($_POST['itemslottoinfuse'])) {
	echo "Sorry, you can't infuse with nothing.";
}
//If not, they must have chosen an item and a weapon
else {
	unset($_SESSION['infuser']);	
		
	//If a weapon is sent, infuse the weapon
	//Note that this is the slot of the weapon, meaning 0, 1, or 2
	//This correlates directly to the weapon holster slots
	if (isset($_POST['weaponslottoinfuse'])) {
		$weapontoinfuse=$_SESSION['weaponholster'][$_POST['weaponslottoinfuse']][0];
		$itemtypetoinfuse=$_SESSION['itemholster'][$_POST['itemslottoinfuse']][1];
		
		//Now, the sql query to find the NEW weapon
		//If weapon is not fists
		if ($_POST['weaponslottoinfuse']!=="0") {
			//A weapon's prefix defines the power of the weapon itself. Later on, there will be more prefixes,
			//such as intermediate, adept, etc.
			//Find the prefix of the current weapon. Put it in $matches[0]
			preg_match("/^[a-z]+\s/i", $weapontoinfuse, $matches);
			$weaponquery="SELECT * FROM weapons WHERE type='" . $itemtypetoinfuse . "' AND prefix='" . $matches[0] . "'";
			$weaponqueryresult=mysql_query($weaponquery);
			if (!$weaponqueryresult) {
				die('Error: You dun goofed:' . mysql_error());
			}
			$newweaponarray=mysql_fetch_assoc($weaponqueryresult);
			//Then, set the old weapons to info to the new stuff
			//name
			$_SESSION['weaponholster'][$_POST['weaponslottoinfuse']][0]=$newweaponarray['name'];
			//low
			$_SESSION['weaponholster'][$_POST['weaponslottoinfuse']][1]=$newweaponarray['low'];
			//high
			$_SESSION['weaponholster'][$_POST['weaponslottoinfuse']][2]=$newweaponarray['high'];
			//type
			$_SESSION['weaponholster'][$_POST['weaponslottoinfuse']][3]=$newweaponarray['type'];
			//id
			$_SESSION['weaponholster'][$_POST['weaponslottoinfuse']][4]=$newweaponarray['weaponID'];
			//If the weapon slot is the weaponequippedslot:
			//Must only be == because POST is a string while equipped is an int. They are of diff types.
			if ($_POST['weaponslottoinfuse']==$_SESSION['weaponslotequipped']) {
				$_SESSION['weaponequippedID']=$_SESSION['weaponholster'][$_SESSION['weaponslotequipped']][4];
				$_SESSION['weaponequipped']=$_SESSION['weaponholster'][$_SESSION['weaponslotequipped']][0];
				$_SESSION['weapontype']=$_SESSION['weaponholster'][$_SESSION['weaponslotequipped']][3];
				$_SESSION['weaponlow']=$_SESSION['weaponholster'][$_SESSION['weaponslotequipped']][1];
				$_SESSION['weaponhigh']=$_SESSION['weaponholster'][$_SESSION['weaponslotequipped']][2];
			}
		}
		//If weapon IS fists, we must only change the type
		else {
			$_SESSION['weaponholster'][$_POST['weaponslottoinfuse']][3]=$_SESSION['itemholster'][$_POST['itemslottoinfuse']][1];
			//If currently equipped weapon is fists, change the current type
			if ($_POST['weaponslottoinfuse']==$_SESSION['weaponslotequipped']) {
				$_SESSION['weapontype']=$_SESSION['itemholster'][$_POST['itemslottoinfuse']][1];
			}
		}
		//Finally, toss item:
		unset($_SESSION['itemholster'][$_POST['itemslottoinfuse']]);
		array_values($_SESSION['itemholster']);
		
		echo "You infused your weapon!";
		include 'inc_charactersave.php';
	}
	else {
		//we're going to grab item type
		$shieldtoinfuse=$_SESSION['shieldholster'][$_POST['shieldslottoinfuse']][0];
		$itemtypetoinfuse=$_SESSION['itemholster'][$_POST['itemslottoinfuse']][1];
		
		//Now, the sql query to find the NEW shield
		//If shield is not bare arms
		if ($_POST['shieldslottoinfuse']!=="0") {
			//Find the prefix of the current weapon. Put it in $matches[0]
			preg_match("/^[a-z]+\s/i", $shieldtoinfuse, $matches);
			//Then look for the shield that has the type we're infusing and the prefix specified
			$shieldquery="SELECT * FROM shields WHERE type='" . $itemtypetoinfuse . "' AND prefix='" . $matches[0] . "'";
			$shieldqueryresult=mysql_query($shieldquery);
			if (!$shieldqueryresult) {
				die('Error: You dun goofed.');
			}
			$newshieldarray=mysql_fetch_assoc($shieldqueryresult);
			//Then, set the old weapons to info to the new stuff
			//name
			//echo $newshieldarray['name'];
			$_SESSION['shieldholster'][$_POST['shieldslottoinfuse']][0]=$newshieldarray['name'];
			//power
			//echo $newshieldarray['power'];
			$_SESSION['shieldholster'][$_POST['shieldslottoinfuse']][1]=$newshieldarray['power'];
			//type
			//echo $newshieldarray['type'];
			$_SESSION['shieldholster'][$_POST['shieldslottoinfuse']][2]=$newshieldarray['type'];
			//shieldID
			//echo $newshieldarray['shieldID'];
			$_SESSION['shieldholster'][$_POST['shieldslottoinfuse']][3]=$newshieldarray['shieldID'];
			//If the weapon slot is the weaponequippedslot:
			//Must only be == because POST is a string while equipped is an int. They are of diff types.
			if ($_POST['shieldslottoinfuse']==$_SESSION['shieldslotequipped']) {
				//debugging
				//echo "Weapon Slot to Infuse: " . $_POST['weaponslottoinfuse'] . " Weapon Slot Equipped: " . $_SESSION['weaponslotequipped'];
				$_SESSION['shieldequippedID']=$_SESSION['shieldholster'][$_SESSION['shieldslotequipped']][3];
				$_SESSION['shieldequipped']=$_SESSION['shieldholster'][$_SESSION['shieldslotequipped']][0];
				$_SESSION['shieldtype']=$_SESSION['shieldholster'][$_SESSION['shieldslotequipped']][2];
				$_SESSION['shieldpower']=$_SESSION['shieldholster'][$_SESSION['shieldslotequipped']][1];
			}
		}
		//If shield IS bare arms:
		else {
			$_SESSION['shieldholster'][$_POST['shieldslottoinfuse']][2]=$_SESSION['itemholster'][$_POST['itemslottoinfuse']][1];
			//If currently equipped weapon is fists, change the current type
			if ($_POST['shieldslottoinfuse']==$_SESSION['shieldslotequipped']) {
				$_SESSION['shieldtype']=$_SESSION['itemholster'][$_POST['itemslottoinfuse']][1];
			}
			
		}
		//Finally, toss item:
		unset($_SESSION['itemholster'][$_POST['itemslottoinfuse']]);
		array_values($_SESSION['itemholster']);
		
		echo "You infused your shield!";
		include 'inc_charactersave.php';
		}
	}
	echo "<form name='formvictory' action='index.php' method='post'>
	<input type='submit' name='continue' value='Continue'/></form>";
	

}

else {
	//Set a variable to let the game know you hit the infuser first.
	$_SESSION['infuser']=TRUE;
	
	echo '<h2>Infuser</h2>
	<p>You stumble upon a clearing. It is a small clearing, but just large enough for an oddly glowing anvil. As you approach the anvil, you realize with a start that the large mass next to the anvil is human, for he says:</p>
	<p>"Hey. Hey you. What you doing here. Don\'t touch Tur anvil. Mighty anvil. Powerful anvil. Mysterious anvil. Tur Anvil. Anvil anvil."</p>
	<p>After he seems to have finished his word salad, his eyes glaze over and he stares off into space, a line of spittle forming from his lip. You get the feeling that thinking isn\'t the forte of this grotesquely proportioned man.</p>
	<p>Too afraid to make off with the glowing anvil (or to even touch it, for that matter), you decide to take this opportunity to shuffle around your belongings so that they are more comfortable. Afterall, what form of beast would dare disturb you while around this mountain of muscle? As you open your item pouch, the man seems to snap out of his comatose state. He bellows,</p>
	<p>"UHHH. You have items. You are adventurer. I can smell them. I can smell items. You want me infuse? Please let Tur infuse for adventurer."</p>
	';

	echo '<h3>What would you like to infuse?</h3>
	<form name="infuse" action="infuser.php" method="post">
	<p>Weapon: <select name="weaponslottoinfuse">';
	
	foreach ($_SESSION['weaponholster'] as $key => $value) {
		echo "<option value='" . $key . "'>" . $value[0] . " - " . $value[3]  . "</option>";
	}
	
	echo '</select></p>
	<p>Item:<select name="itemslottoinfuse">';
	
	foreach ($_SESSION['itemholster'] as $key => $value) {
		echo "<option value='" . $key . "'>" . $value[0] . " - " . $value[1] . "</option> ";
	}
	
	echo '</select></p>
	<p><input type="submit" name="infuse" value="Infuse!"/></p>
	</form>';

	echo '<form name="infuse" action="infuser.php" method="post">
	<p>Shield: <select name="shieldslottoinfuse">';
	
	foreach ($_SESSION['shieldholster'] as $key => $value) {
		echo "<option value='" . $key . "'>" . $value[0] . " - " . $value[2]  . "</option>";
	}
	
	
	echo '</select></p>
	<p>Item:<select name="itemslottoinfuse">';
	
	foreach ($_SESSION['itemholster'] as $key => $value) {
		echo "<option value='" . $key . "'>" . $value[0] . " - " . $value[1] . "</option> ";
	}
	
	echo '</select></p>
	<p><input type="submit" name="infuse" value="Infuse!"/></p>
	</form>';

	echo '<form name="formvictory" action="infuser.php" method="post">
	<input type="submit" name="nevermind" value="Nevermind"/></form>';
	
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