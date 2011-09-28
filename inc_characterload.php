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
		
		
		//Finds the appropriate character based on username and whether character
		//is dead or not
		$runthisquery="SELECT * FROM characters, users WHERE characters.userID='" . $_SESSION['userID'] . "' AND users.userID='" . $_SESSION['userID'] . "' AND dead='no'";
		
		$characterarray=mysql_fetch_assoc(mysql_query($runthisquery));

		$_SESSION['name'] = $characterarray['name'];
		$_SESSION['hp']=$characterarray['hp'];
		$_SESSION['maxhp']=$characterarray['maxhp'];
		$_SESSION['level']=$characterarray['level'];
		$_SESSION['exp']=$characterarray['exp'];
		$_SESSION['maxexp']=$characterarray['maxexp'];
		$_SESSION['strength']=$characterarray['strength'];
		$_SESSION['accuracy']=$characterarray['accuracy'];
		$_SESSION['agility']=$characterarray['agility'];
		$_SESSION['defense']=$characterarray['defense'];
		$_SESSION['speed']=$characterarray['speed'];
		$_SESSION['weaponequippedID']=$characterarray['weaponequipped'];
		$_SESSION['shieldequippedID']=$characterarray['shieldequipped'];

		//This stuff needs to talk to the database first:
		$findcurrentweapon=mysql_fetch_assoc(mysql_query("SELECT * FROM weapons WHERE weaponID='" . $characterarray['weaponequipped'] . "'"));
		$findcurrentshield=mysql_fetch_assoc(mysql_query("SELECT * FROM shields WHERE shieldID='" . $characterarray['shieldequipped'] . "'")); 
		$_SESSION['weaponequipped']=$findcurrentweapon['name'];
		$_SESSION['shieldequipped']=$findcurrentshield['name'];
		$_SESSION['gold']=$characterarray['gold'];
		$_SESSION['weapontype']=$findcurrentweapon['type'];
		$_SESSION['weaponlow']=$findcurrentweapon['low'];
		$_SESSION['weaponhigh']=$findcurrentweapon['high'];
		$_SESSION['shieldtype']=$findcurrentshield['type'];
		$_SESSION['shieldpower']=$findcurrentshield['power'];
		
		//Holster Feches
		//This stuff will need to be done with other look-ups in other tables
		//While weapon# doesn't equal NULL, look it up in the weapons table
		$x=1;
		$_SESSION['weaponholster']=array();
		while ($x<4 && $characterarray['weapon' . $x] !== NULL) {
			$weaponquery="SELECT * FROM weapons WHERE weaponID='" . $characterarray['weapon' . $x] . "'";
			$weaponarray=mysql_fetch_assoc(mysql_query($weaponquery));
			$_SESSION['weaponholster'][]=array($weaponarray['name'], $weaponarray['low'], $weaponarray['high'], $weaponarray['type'], $weaponarray['weaponID']);
			//While we're here, if the name is equal to the weapon equipped, let's set the other variables
			if ($_SESSION['weaponequipped'] === $weaponarray['name']) {
				$_SESSION['weapontype']=$weaponarray['type'];
				$_SESSION['weaponlow']=$weaponarray['low'];
				$_SESSION['weaponhigh']=$weaponarray['high'];
				$_SESSION['weaponslotequipped']=$x-1;
			}
			$x++;
		}
		//If fisttype is not flesh, we'll need to change that to whatever type it happens to be
		if ($characterarray['fisttype']!="flesh") {
			$_SESSION['weaponholster'][0][3]=$characterarray['fisttype'];
		}
		//If the currently equipped weapon is Fists, we'll need to change the current weapon type, too
		if ($_SESSION['weaponequipped']==$_SESSION['weaponholster'][0][0]) {
			$_SESSION['weapontype']=$_SESSION['weaponholster'][0][3];
		}
		
		//While shield# doesn't equal NULL, look it up in the shields table
		$x=1;
		$_SESSION['shieldholster']=array();
		while ($x<4 && $characterarray['shield' . $x] !== NULL) {
			$shieldquery="SELECT * FROM shields WHERE shieldID='" . $characterarray['shield' . $x] . "'";
			$shieldarray=mysql_fetch_assoc(mysql_query($shieldquery));
			$_SESSION['shieldholster'][]=array($shieldarray['name'], $shieldarray['power'], $shieldarray['type'], $shieldarray['shieldID']);
			//While we're here, if the name is equal to the shield equipped, let's set the other variables
			if ($_SESSION['shieldequipped'] === $shieldarray['name']) {
				$_SESSION['shieldtype']=$shieldarray['type'];
				$_SESSION['shieldpower']=$shieldarray['power'];
				$_SESSION['shieldslotequipped']=$x-1;
			}
			$x++;
		}
		//If barearmstype is not flesh, we'll need to change that to whatever type it happens to be
		if ($characterarray['barearmstype']!="flesh") {
			$_SESSION['shieldholster'][0][2]=$characterarray['barearmstype'];
		}
		//If the currently equipped shield is Bare Arms, we'll need to change the current shield type, too
		if ($_SESSION['shieldequipped']==$_SESSION['shieldholster'][0][0]) {
			$_SESSION['shieldtype']=$_SESSION['shieldholster'][0][2];
		}
		
		//While potion# doesn't equal NULL, look it up in the potions table
		$_SESSION['potionholster']=array();
		$x=1;
		while ($x<6 && $characterarray['potion' . $x] !== NULL) {
			$potionquery="SELECT * FROM potions WHERE potionID='" . $characterarray['potion' . $x] . "'";
			$potionarray=mysql_fetch_assoc(mysql_query($potionquery));
			$_SESSION['potionholster'][]=array($potionarray['name'], $potionarray['power'], $potionarray['potionID']);
			$x++;
		}


		
		//While item# doesn't equal NULL, look it up in the items table
		$_SESSION['itemholster']=array();
		$x=1;
		while ($x<6 && $characterarray['item' . $x] !== NULL) {
			$itemquery="SELECT * FROM items WHERE itemID='" . $characterarray['item' . $x] . "'";
			$itemarray=mysql_fetch_assoc(mysql_query($itemquery));
			$_SESSION['itemholster'][]=array($itemarray['name'], $itemarray['type'], $itemarray['itemID']);
			$x++;
		}
		
		//If questID doesn't equal 0, look it up in the quest table
		if ($characterarray['questID']!=0) {
			$questquery="SELECT * FROM quests WHERE questID='" . $characterarray['questID'] . "'";
			$questarray=mysql_fetch_assoc(mysql_query($questquery));
			$_SESSION['questfile']=$questarray['questfile'];
		}
		$_SESSION['questID']=$characterarray['questID'];
		$_SESSION['queststep']=$characterarray['queststep'];
		$_SESSION['gender']=$characterarray['gender'];
		
?>