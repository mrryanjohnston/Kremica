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
//So a player who is signed in can access mail. The player does not need a character alive.
if (!isset($_SESSION['username'])) {
		//header( 'Location: http://www.kremica.com' ) ;
		header( 'Location: index.php' ) ;
		exit;
}
//If a player is mid-battle, he/she will be sent back to the fight page automatically.
//Similarly, if a player hasn't set attributes for a character yet, send back to fight page
//automatically
if (isset($_SESSION['name']) && (isset($_SESSION['monstername']) || ($_SESSION['strength']+$_SESSION['defense']<(3*$_SESSION['level'])-1))) {
		//header( 'Location: http://www.kremica.com' ) ;
		header( 'Location: fight.php' ) ;
		exit;
}

$username=$_SESSION['username'];
//Database Connection Stuff
//By default, show current user's profile. If set, use the username= username.


if (isset($_GET['username'])) {
	$username=$_GET['username'];
	
}
include 'inc_connection.php';
	
$userquery="SELECT * FROM users WHERE users.username='" . mysql_real_escape_string($username) . "'";
$userqueryresult=mysql_query($userquery);
$userqueryarray=mysql_fetch_assoc($userqueryresult);
	
$characterquery="SELECT * FROM users, characters WHERE users.userID=characters.userID AND users.username='" . mysql_real_escape_string($username) . "'";
$characterresult=mysql_query($characterquery);
	
//Selects all relationships for which current user and target user are a part of
//relationshipqueryarray returns FALSE UNLESS the number of rows returned are greated than 0, which means
//there is some sort of relation between the two characters
$relationshipquery="SELECT * FROM relationship WHERE (initiatorID='" . $userqueryarray['userID'] . "' AND receiverID='" . $_SESSION['userID'] . "') OR (initiatorID='" . $_SESSION['userID'] . "' AND receiverID='" . $userqueryarray['userID'] . "')";
$relationshipqueryresult=mysql_query($relationshipquery);
$relationshipqueryarray=mysql_fetch_assoc($relationshipqueryresult);

include 'inc_close_connection.php';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
	<title>Profile: 
	<?php
	echo $username;
	?>
	</title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<meta name="generator" content="Geany 0.19.1" />
	<link rel="stylesheet" type="text/css" href="stylesheet.css" />
	<script type="text/javascript" src="jquery-1.4.4.js"></script>
	<script type="text/javascript">
	$(function() {
		$('.stripeme tr:even').addClass('alt');
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
<h2>Profile: 
	<?php
	echo $username;
	?>
</h2>
<br/>

<?php

//If you can't find the username, shoot them an error and then have them click a button back to index.php
if (!isset($userqueryarray['username'])) {
	echo "Sorry, couldn't find user " . $username . ".";
	echo "<form name='formvictory' action='index.php' method='post'>
	<input type='submit' name='continue' value='Continue'/></form>";
	exit();
}

//This stuff is for when the user wishes to do something to the account, such as friend or block
if (isset($_GET['relationship'])) {
	/*$con = mysql_connect("localhost","sweetgame","sweetgamepassword");
	if (!$con) {
		die('Could not connect: ' . mysql_error());
	}
	mysql_select_db("sweetgame", $con);*/
	include 'inc_connection.php';
	//First off, you can not be in a relation with yourself, so let's stop that nonsense
	if (!isset($_GET['username']) || $_GET['username']===$_SESSION['username']) {
	echo "Sorry, you can't do that to yourself, silly.";
	echo "<form name='formvictory' action='profile.php' method='post'>
	<input type='submit' name='continue' value='Continue'/></form>";
	exit();
	}
	//If a person is accepting a friend request, they will land here
	if ($_GET['relationship']==="accept") {
		if (!isset($_POST['yes'])) {
			//else if ($relationshipqueryarray['status']==0 && $relationshipqueryarray['receiverID']===$_SESSION['userID'] && $_GET['relationship']==="accept") {
			echo "Are you sure you wish to accept this friend request from " . $username . "?";
			echo "<form action='profile.php?username=" . $_GET['username'] . "&relationship=accept' method='post'>";
			echo "<input type='submit' value='Yes' name='yes'/><input type='submit' value='No' name='no'/>";
			echo "</form>";
		}
		else if (isset($_POST['yes'])) {
			$friendacceptquery="UPDATE relationship SET status='1' WHERE initiatorID='" . $userqueryarray['userID'] . "' AND receiverID='" . $_SESSION['userID'] . "'";
			$friendacceptresult=mysql_query($friendacceptquery);
			if (!$friendacceptresult) {
				die('Ruh roh, something went wrong. What did you break?');
			}
			echo "You're now friends with " . $userqueryarray['username'] . "! YAY!";
			echo "<form name='formvictory' action='profile.php?username=" . $_GET['username'] . "' method='post'>
			<input type='submit' name='continue' value='Continue'/></form>";
			exit();
		}
	}
	//For friend requests:
	if ($_GET['relationship']==="friend") {
		//If the two users are not friends and one is not blocking the other (in other words, they aren't in the relationship table), send a friend request
		if (!$relationshipqueryarray) {
			$friendrequestquery="INSERT INTO relationship VALUES (NULL, '" . $_SESSION['userID'] . "', '" . $userqueryarray['userID'] . "', '0')";
			$friendrequestresult=mysql_query($friendrequestquery);
			if (!$friendrequestresult) {
				die('Ruh roh, something bad happened.');
			}
			echo "Friend request sent!";
			echo "<form name='formvictory' action='profile.php?username=" . $_GET['username'] . "' method='post'>
			<input type='submit' name='continue' value='Continue'/></form>";
			exit();
		}
		//If the user has already sent a friend request (aka relationship status is 0), return that they have already sent a request and do nothing else
		else if ($relationshipqueryarray['status']==0 && $relationshipqueryarray['initiatorID']===$_SESSION['userID']) {
			echo "You have already friend requested this user. Be patient!";
			echo "<form name='formvictory' action='profile.php?username=" . $_GET['username'] . "' method='post'>
			<input type='submit' name='continue' value='Continue'/></form>";
			exit();
		}		
	}
	//A user may cancel a sent friend request
	if ($_GET['relationship']==="cancel") {
		if (!isset($_POST['yes'])) {
			echo "Are you sure you wish to cancel this friend request?";
			echo "<form action='profile.php?username=" . $_GET['username'] . "&relationship=cancel' method='post'>";
			echo "<input type='submit' value='Yes' name='yes'/><input type='submit' value='No' name='no'/>";
			echo "</form>";
		}
		else if (isset($_POST['yes'])) {
			$friendacceptquery="DELETE FROM relationship WHERE initiatorID='" . $_SESSION['userID'] . "' AND receiverID='" . $userqueryarray['userID'] . "'";
			$friendacceptresult=mysql_query($friendacceptquery);
			if (!$friendacceptresult) {
				die('Ruh roh, something went wrong. What did you break?');
			}
			echo "Canceled friend request.";
			echo "<form name='formvictory' action='profile.php?username=" . $_GET['username'] . "' method='post'>
			<input type='submit' name='continue' value='Continue'/></form>";
			exit();
		}
	}
	//A user may decide he or she no longer likes their friend and wants to defriend them
	if ($_GET['relationship']==="remove") {
		if (!isset($_POST['yes'])) {
			echo "Are you sure you wish to remove this friend?";
			echo "<form action='profile.php?username=" . $_GET['username'] . "&relationship=remove' method='post'>";
			echo "<input type='submit' value='Yes' name='yes'/><input type='submit' value='No' name='no'/>";
			echo "</form>";
		}
		else if (isset($_POST['yes'])) {
			$frienddeletequery="DELETE FROM relationship WHERE (initiatorID='" . $userqueryarray['userID'] . "' AND receiverID='" . $_SESSION['userID'] . "') OR (initiatorID='" . $_SESSION['userID'] . "' AND receiverID='" . $userqueryarray['userID'] . "')";
			$frienddeleteresult=mysql_query($frienddeletequery);
			if (!$frienddeleteresult) {
				die('Ruh roh, something went wrong. What did you break?');
			}
			echo "Removed from friends.";
			echo "<form name='formvictory' action='profile.php?username=" . $_GET['username'] . "' method='post'>
			<input type='submit' name='continue' value='Continue'/></form>";
			exit();
		}
	}
	mysql_close($con);
}


//This stuff is just for displaying the list of characters and options for other things to do to this account
//Always return results
echo "<table class='stripeme'>";
echo "<tr><th>Character</th><th>Level</th><th>Strength</th><th>Defense</th><th>Speed</th></tr>";

while ($characterarray=mysql_fetch_assoc($characterresult)) {
	echo "<tr>";
	echo "<td><a href='charsheet.php?charname=" . $characterarray['name'] . "'>" . $characterarray['name'] . "</a></td>";
	echo "<td>" . $characterarray['level'] . "</td>";
	echo "<td>" . $characterarray['strength'] . "</td>";
	echo "<td>" . $characterarray['defense'] . "</td>";	
	echo "<td>" . $characterarray['speed'] . "</td>";
	echo "</tr>";
}

echo "</table>";


//Add options for messaging and friending
echo "<ul>";
	//Only show these three if you're not trying to friend request/block/message yourself
	if ($username!==$_SESSION['username']) {
		echo "<li><a href=\"mail.php?compose=new&to=" . $username . "\">Send message</a></li>";
		//If there was no result, request a friendship
		if (!$relationshipqueryarray) {
			echo "<li><a href=\"profile.php?username=" . $username . "&relationship=friend\">";
			echo "Request friendship";
		}
		else if ($relationshipqueryarray['receiverID']===$_SESSION['userID'] && ($relationshipqueryarray['status']==0)) {
			echo "<li><a href=\"profile.php?username=" . $username . "&relationship=accept\">";
			echo "Accept friend request";
		}
		else if ($relationshipqueryarray['initiatorID']===$_SESSION['userID'] && ($relationshipqueryarray['status']==0)) {
			echo "<li><a href=\"profile.php?username=" . $username . "&relationship=cancel\">";
			echo "Cancel friend request";
		}
		else {
			echo "<li><a href=\"profile.php?username=" . $username . "&relationship=remove\">";
			echo "Remove friendship";
		}
		echo "</a></li>";
		echo "<li><a href=\"profile.php?username=" . $username . "&relationship=block\">Block</a></li>";
	}	
echo "</ul>";





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