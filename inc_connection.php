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
 * This is the connection file. Include this on all pages that need to connect to the database
 * include 'inc_connection.php';
 * 
 * Everytime you want to close the connection:
 * include 'inc_close_connection.php';
 */

$hostname="";
$dbusername="";
$dbpassword="";
$database="";

$con = mysql_connect($hostname, $dbusername, $dbpassword);
if (!$con) {
	die('Could not connect: ' . mysql_error());
}
mysql_select_db($database, $con);
?>