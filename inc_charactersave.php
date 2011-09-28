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

	if (isset($_SESSION["name"])) {
		$name = $_SESSION['name'];
		include 'inc_connection.php';
		//If there is already a character in the database by this name, just update it. 
		//If there isn't, insert
		//Else, return error.
		//First, run a select to see if there are any results for the charactername
		$searchquery="SELECT name FROM characters WHERE name='" . trim(mysql_real_escape_string($_SESSION['name'])) . "'";
		//If there are no results, insert.
		if (mysql_num_rows(mysql_query($searchquery))==0) {
		
			$runthisquery="INSERT INTO characters VALUES (NULL, '$name', '" . $_SESSION['hp'] . "', '" . $_SESSION['maxhp'] . "', '" . $_SESSION['level'] . "',  '" . $_SESSION['strength'] . "', '" . $_SESSION['defense'] . "', '" . $_SESSION['speed'] . "', '" . $_SESSION['agility'] . "', '" . $_SESSION['accuracy'] . "', '" . $_SESSION['weaponholster'][0][4] . "',";
			//(characterid, name, hp, maxhp, level, strength, defense, weapon1, weapon2, weapon3, shield1, shield2, shield3, username, dead, exp, maxexp) 
			
			//The following if statements are here because the character may or may not
			//have a weapon/shield in slots 1 and 2. He/she will ALWAYS have a weapon
			//in slot 0.
			if (isset($_SESSION['weaponholster'][1][4])) {
				$runthisquery.=" '" . $_SESSION['weaponholster'][1][4] . "',"; 
			}
			else {
				$runthisquery.=" NULL,";
			}
			if (isset($_SESSION['weaponholster'][2][4])) {
				$runthisquery.=" '" . $_SESSION['weaponholster'][2][4] . "',";
			}
			else {
				$runthisquery.=" NULL,";
			}
			//First Shield
			$runthisquery.=" '" . $_SESSION['shieldholster'][0][3] . "',";
			//Then the second and third slots
			if (isset($_SESSION['shieldholster'][1][3])) {
				$runthisquery.=" '" . $_SESSION['shieldholster'][1][3] . "',"; 
			}
			else {
				$runthisquery.=" NULL,";
			}
			if (isset($_SESSION['shieldholster'][2][3])) {
				$runthisquery.=" '" . $_SESSION['shieldholster'][2][3] . "',";
			}
			else {
				$runthisquery.=" NULL,";
			}
			
			$runthisquery.=" 'no', '". $_SESSION['exp'] . "', '" . $_SESSION['maxexp']  . "',";
			
			//Must do this for Potions and Items, too
			//If there are potions in the potion holster, record them. If not, insert NULL.
			//Ends with a comma (,)
			$x=0;
			while (isset($_SESSION['potionholster'][$x][2])) {
				$runthisquery.=" '" . $_SESSION['potionholster'][$x][2] . "',";
				$x++;
			}
			//There may not be 5 potions. If this is the case, x will not reach 4.
			//If there are no potions anymore, send a null until x is 4.
			while ($x<=4) {
				$runthisquery.=" NULL,";
				$x++;
			}
			//If there are items in the item holster, record them. if not, insert NULL.
			//Ends with a comma(,)
			$x=0;
			while (isset($_SESSION['itemholster'][$x][2])) {
				$runthisquery.=" '" . $_SESSION['itemholster'][$x][2] . "',";
				$x++;
			}
			//There might not be 5 items. If this is the case, x will not reach 4.
			//If there are no more items, send a null until x is 4.
			while ($x<=4) {
				$runthisquery.=" NULL,";
				$x++;
			}
			$runthisquery.=" '" . $_SESSION['weaponequippedID'] . "', '" . $_SESSION['shieldequippedID'] . "', '" . $_SESSION['gold'] . "', '" . $_SESSION['userID'] . "',";
			//Quest Stuff
			$runthisquery.=" '" . $_SESSION['questID'] . "', '" . $_SESSION['queststep'] . "', ";
			//Gender Stuff
			$runthisquery.=" '" . $_SESSION['gender'] . "', ";
			//Fist/barearms type (just in case)
			$runthisquery.=" '" . $_SESSION['weaponholster'][0][3] . "', '" . $_SESSION['shieldholster'][0][2] . "'";
			$runthisquery.=")";
			
			//echo $runthisquery;
			
		}
		//If there is exactly 1 result, update
		else if (mysql_num_rows(mysql_query($searchquery))==1){
			$runthisquery="UPDATE characters SET hp='" . $_SESSION['hp'] . "', maxhp='" . $_SESSION['maxhp'] . "', level='" . $_SESSION['level'] . "',  strength='" . $_SESSION['strength'] . "', defense='" . $_SESSION['defense'] . "', speed='" .  $_SESSION['speed'] . "', agility='" . $_SESSION['agility'] . "', accuracy='" . $_SESSION['accuracy'] . "', weapon1='" . $_SESSION['weaponholster'][0][4] . "',";
			//(characterid, name, hp, maxhp, level, strength, defense, weapon1, weapon2, weapon3, shield1, shield2, shield3, username, dead, exp, maxexp, numberofpotionholsterslots, numberofitemsinpotionholster, potion1, potion2, potion3, potion4, potion5, item1, item2, item3, item4, item5, weaponequipped, shieldequipped, gold)
			
			
			//The following if statements are here because the character may or may not
			//have a weapon/shield in slots 1 and 2. He/she will ALWAYS have a weapon
			//in slot 0.
			if (isset($_SESSION['weaponholster'][1][4])) {
				$runthisquery.=" weapon2='" . $_SESSION['weaponholster'][1][4] . "',"; 
			}
			else {
				$runthisquery.=" weapon2=NULL,";
			}
			if (isset($_SESSION['weaponholster'][2][4])) {
				$runthisquery.=" weapon3='" . $_SESSION['weaponholster'][2][4] . "',";
			}
			else {
				$runthisquery.=" weapon3=NULL,";
			}
			//First Shield
			$runthisquery.=" shield1='" . $_SESSION['shieldholster'][0][3] . "',";
			//Then the second and third slots
			if (isset($_SESSION['shieldholster'][1][3])) {
				$runthisquery.=" shield2='" . $_SESSION['shieldholster'][1][3] . "',"; 
			}
			else {
				$runthisquery.=" shield2=NULL,";
			}
			if (isset($_SESSION['shieldholster'][2][3])) {
				$runthisquery.=" shield3='" . $_SESSION['shieldholster'][2][3] . "',";
			}
			else {
				$runthisquery.=" shield3=NULL,";
			}
			
			$runthisquery.=" dead='no', exp='" . $_SESSION['exp'] . "', maxexp='" . $_SESSION['maxexp'] . "',";
			
			//If there are potions in the potion holster, record them. If not, insert NULL.
			//Ends with a comma (,)
			$x=1;
			while (isset($_SESSION['potionholster'][$x-1][2])) {
				$runthisquery.=" potion$x='" . $_SESSION['potionholster'][$x-1][2] . "',";
				$x++;
			}
			//There may not be 5 potions. If this is the case, x will not reach 5.
			//If there are no potions anymore, send a null until x is 5.
			while ($x<=5) {
				$runthisquery.=" potion$x=NULL,";
				$x++;
			}
			//If there are items in the item holster, record them. if not, insert NULL.
			//Ends with a comma(,)
			$x=1;
			//print_r($_SESSION['itemholster']);
			while (isset($_SESSION['itemholster'][$x-1][2])) {
				//echo "while";
				$runthisquery.=" item$x='" . $_SESSION['itemholster'][$x-1][2] . "',";
				$x++;
			}
			//There might not be 5 items. If this is the case, x will not reach 5.
			//If there are no more items, send a null until x is 5.
			while ($x<=5) {
				$runthisquery.=" item$x=NULL,";
				$x++;
			}
			$runthisquery.=" weaponequipped='" . $_SESSION['weaponequippedID'] . "', shieldequipped='" . $_SESSION['shieldequippedID'] . "', gold='" . $_SESSION['gold'] . "', userID='" . $_SESSION['userID'] . "',";
			//Quest Stuff
			$runthisquery.=" questID='" . $_SESSION['questID'] . "', queststep='" . $_SESSION['queststep'] . "',";
			//Gender Stuff
			$runthisquery.=" gender='" . $_SESSION['gender'] . "',";
			//Fist/barearms stuff (just in case)
			$runthisquery.=" fisttype='" . $_SESSION['weaponholster'][0][3] . "', barearmstype='" . $_SESSION['shieldholster'][0][2] . "' ";
			$runthisquery.="WHERE name='" . $_SESSION['name'] . "'";
			//echo $runthisquery;
		}
		//If else, return an error.
		else {
			echo "This shouldn't happen. Contact the admin.";
			exit;
		}
		mysql_query($runthisquery);
		if (!$runthisquery) {
			die('Error: ' . mysql_error());
		}
		//echo $runthisquery;
		/*$savecurrentcharacterquery="UPDATE users SET characterID='" . $_SESSION['characterID'] . "' WHERE username='" . $_SESSION['username'] . "'";
		mysql_query($savecurrentcharacterquery);*/
		
		mysql_close($con);
		//Necessary?
		/*unset($_SESSION["name"]);
		if (isset($_SESSION["monstername"])) {
			unset($_SESSION["monstername"]);
		}*/
		//Must save the username to keep the user logged in
		/*$username=$_SESSION["username"];
		session_destroy();
		//heh
		session_start();
		$_SESSION["username"]=$username;*/
	}
?>