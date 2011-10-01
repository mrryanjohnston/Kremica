		<div id="navigation">
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
	/*
	 * If the user is forced to log out, call logout session
	 * User is forced to log out when a user is logged on from another place
	 * Also redirect to index.php
	 */
	if(isForcedLoggedOut($_SESSION['username'], session_id())) {
		logout($_SESSION['username'], TRUE);
		echo "<p>Someone logged into your account from a different location. Logged you out.</p>\n";
		echo "<form name='formvictory' action='index.php' method='post'>\n
		<p><input type='submit' name='continue' value='Continue'/></p>\n
		</form>\n";
		exit;
	}
	echo "\t\t\t<p class=\"greeting\">Hi, <span class=\"boldtext\">" . $_SESSION['username'] . "</span>!</p>";
	if (isset($_SESSION['monstername'])) {
		echo "<p><span class=\"warning\">Warning! Logging out in-battle will kill your character!</span></p>";
	}
	echo "\n";

?>
			<ul>
				<li><a href="index.php">Game</a></li>
				<li><a href="mail.php">Mail</a></li>
				<li><a href="profile.php">My Profile</a></li>
				<li><a href="annals.php">Annals of Adventurers</a></li>
				<li><a href="index.php?logout=true">Logout</a></li>
			</ul>

		</div>