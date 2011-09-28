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
$nameofpotions=array();
	$i=0;
	foreach ($_SESSION['potionholster'] as $key => $value) {
		$nameofpotions[$i]=$value[0];
		$i++;
	}
	if (in_array($_POST['itemtouse'], $nameofpotions) && $_POST['itemtouse']!="none") {
		$i=0;
		while (strcasecmp($_POST['itemtouse'], $nameofpotions[$i])!=0) {
			$i++;
		}
		if (($_SESSION['maxhp']-$_SESSION['hp'])<=$_SESSION['potionholster'][$i][1]) {
			$_SESSION['hp']=$_SESSION['maxhp'];
		}
		else {
			$_SESSION['hp']+=$_SESSION['potionholster'][$i][1];
		}
		echo "<center>You have been healed!</center>";
		$_POST['attack']="true";
		unset($_SESSION['potionholster'][$i]);
		$_SESSION['potionholster']=array_values($_SESSION['potionholster']);
		$_SESSION['numberofitemsinpotionholster']--;
	}
	else {

		echo "<center>Sorry, you don't have that item anymore!</center>";
	}
?>