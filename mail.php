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

/******
 * Access
 ******
 * Only users are able to access the mail system. They do not need a
 * character to be alive.
 */
if (!isset($_SESSION["username"])) {
	//header( 'Location: http://www.kremica.com' ) ;
	header( 'Location: index.php' ) ;
	exit;
}

/******
 * In-Battle Check
 ******
 * If a player is mid-battle, he/she will be sent back to the fight page automatically.
 * Similarly, if a player hasn't set attributes for a character yet, send back to fight page
 * automatically
 */
if (isset($_SESSION['name']) && (isset($_SESSION['monstername']) || ($_SESSION['strength']+$_SESSION['defense']+$_SESSION['speed']<(6*$_SESSION['level'])-3))) {
		//header( 'Location: http://www.kremica.com' ) ;
		header( 'Location: fight.php' ) ;
		exit;
}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
	<title>Kremica: Mail</title>
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
		<div id="leftside2">
			<div id='leftside2_inner'>
			<h2>Mail</h2>
				<ul>
					<li><a href='mail.php'>Inbox</a></li>
					<li><a href='mail.php?compose=new'>Compose</a></li>
					<li><a href='mail.php?sentbox=true'>Sentbox</a></li>
				</ul>
			</div>
		</div>
		<div id="rightside2">
					<div id="rightside2_inner">

<?php

include 'inc_connection.php';

//If a sentbox value is specified, show the sentbox for current user
if (isset($_GET['sentbox'])) {
		echo "
		<h2>Sentbox</h2>
		";
	$sentboxquery="SELECT * FROM users, sentbox LEFT JOIN inbox ON inbox.mailnumber=sentbox.mailnumber WHERE sentbox.sender='" . $_SESSION['userID'] . "' AND users.userID=inbox.recipient AND sentbox.deleted='no'";
	$sentboxresult=mysql_query($sentboxquery);
	
	echo "
		<form name='sentbox' method='post' action='mail.php'>
		<table class='stripeme'>
			<tr>
				<th>Subject</th>
				<th>Recipient</th>
				<th>Date Sent</th>
			</tr>
			";
	
	//If no results, return no sent messages
	if (!$sentboxresult) {
		die('Something bad happened: ' . mysql_error());
	}
	
	while ($sentboxarray=mysql_fetch_assoc($sentboxresult)) {
		//This is all stuff to determine how long ago the message was sent.
			$timeagosent=time()-$sentboxarray['datesent'];
			$minutes=floor($timeagosent/60);
			$hours=floor($timeagosent/3600);
			$days=floor($timeagosent/86400);
			$timeagostring="";
			//Show days if sent days ago, show hours if sent less than a day ago, show minutes if sent
			//less than an hour ago, show seconds if sent less than a minute ago.
			if ($days>0) $timeagostring=$days . " days ago";
			else if ($hours>0) $timeagostring=$hours . " hours ago";
			else if ($minutes>0) $timeagostring=$minutes . " minutes ago";
			else $timeagostring=$timeagosent . " seconds ago";
			
			echo "
			<tr>
				<td><input type='checkbox' name='sentbox[]' value='" . $sentboxarray['mailnumber'] . "'/> <a href='mail.php?message=" . $sentboxarray['mailnumber'] . "'>" . $sentboxarray['subject'] . "</a></td>
				<td>" . $sentboxarray['username'] . "</td>
				<td>" . $timeagostring . "</td>
			</tr>
			";
	}
	echo "
		</table>
		<br/>
		<input type='submit' value='Delete Selected' name='delete'/>
		</form>
		";
}

//If a message value is specified, AND the current user is the correct receiver, show that particular message
else if (isset($_GET['message']) && !isset($_POST['delete'])) {
	//First selects the mail number in the inbox for the current user which mail number is equal to the passed mailnumber in the URL
	//Note: This is done as a failsafe incase the user tries to get a message not in their inbox.
	$inboxmessagequery="SELECT mailnumber FROM inbox WHERE recipient='" . $_SESSION['userID'] . "' AND mailnumber='" . trim(mysql_real_escape_string($_GET['message'])) . "'";
	$inboxmessageresult=mysql_query($inboxmessagequery);
	
	if (!$inboxmessageresult) {
		die('Error: You requested something not in your inbox.');
	}

	//Grabs the content of the message from the sentbox where the mail number is equal to the mail number requested
	//Also we'll want to return the sender's name, so we need to join this to the users table
	$sentboxmessagequery="SELECT * FROM sentbox, users WHERE mailnumber='" . trim(mysql_real_escape_string($_GET['message'])) . "' AND users.userID=sentbox.sender";
	$sentboxmessageresult=mysql_query($sentboxmessagequery);
	
	if (!$sentboxmessageresult) {
		die('Error: You requested something not in your inbox.');
	}
	
	$sentboxmessagearray=mysql_fetch_assoc($sentboxmessageresult);
	
	//Time stuff (copied from below)
	$timeagosent=time()-$sentboxmessagearray['datesent'];
	$minutes=floor($timeagosent/60);
	$hours=floor($timeagosent/3600);
	$days=floor($timeagosent/86400);
	$timeagostring="";
	//Show days if sent days ago, show hours if sent less than a day ago, show minutes if sent
	//less than an hour ago, show seconds if sent less than a minute ago.
	if ($days>0) $timeagostring=$days . " days ago";
	else if ($hours>0) $timeagostring=$hours . " hours ago";
	else if ($minutes>0) $timeagostring=$minutes . " minutes ago";
	else $timeagostring=$timeagosent . " seconds ago";
	
	//Start of Message
	
	echo "<div id='message'>";
	echo "<h3 class='mail_subject'>" . htmlentities($sentboxmessagearray['subject']) . "</h3>";
	echo "<p class='mail_sender'><span class='mail_sendername'>" . $sentboxmessagearray['username'] . "</span> sent this " . $timeagostring . "</p>";
	echo "<div id='message_inner'>";
	echo "<p>" . htmlentities($sentboxmessagearray['body']) . "</p>";
	echo "</div>";
	echo "</div>";
	//End Message

	
}
//This is for deleting messages
else if (isset($_POST['delete'])) {
	//If deleting from the inbox go here
	if (isset($_POST['inbox'])) {
		//Could be multiple deletes
		foreach ($_POST['inbox'] as $key=>$value) {
			$deletequery="UPDATE inbox SET deleted='yes' WHERE mailnumber='" . trim(mysql_real_escape_string($value)) . "' AND recipient='" . $_SESSION['userID'] . "'";
			mysql_query($deletequery);
			if (!$deletequery) {
				die('Error: You tried deleting something not in your inbox.');
			}
		}
		header( 'Location: mail.php' ) ;
	}
	//If deleting from the sentbox go here
	else if (isset($_POST['sentbox'])) {
		//Could be multiple deletes
		foreach ($_POST['sentbox'] as $key=>$value) {
			$deletequery="UPDATE sentbox SET deleted='yes' WHERE mailnumber='" . trim(mysql_real_escape_string($value)) . "' AND sender='" . $_SESSION['userID'] . "'";
			mysql_query($deletequery);
			if (!$deletequery) {
				die('Error: You tried deleting something not in your sendbox.');
			}
		}
		header( 'Location: mail.php?sentbox=true' ) ;
	}
	exit;
}

//This is for composing new messages
//Eventually, I'd like to have it be able to send to multiple people
//For now, you can only send to 1 person at a time
else if (isset($_GET['compose'])) {
	echo "
	<h2>New Message</h2>
	";
	//Start new message table and form
	echo "<form name='newmessage' action='mail.php' method='post'>";
	echo "<table>
			<tr>
				<th>To:</th><td><input type='text' name='to' size='15' ";
				
	if (isset($_GET['to'])) {
		echo "value='" . $_GET['to'] . "' ";
	}
				
	echo "/></td>
			</tr>
			<tr>
				<th>Subject:</th><td><input type='text' name='subject' size='45'/></td>
			</tr>
			<tr>
				<th>Message (limit 500 chars):</th><td><textarea name='body' cols='50' rows='10'/></textarea></td>
			</tr>
			<tr>
				<td colspan='2'><input type='submit' name='send' value='Send'/></td>
			</tr>
		  </table>";
	//End new message table 	
}
//This is how the composed message is sent
else if (isset($_POST['send'])) {
	//First first, we'll need to validate their input
	//If an error occurrs, set $error to true;
	$error=FALSE;
	//If the amount of characters typed into the subject body is
	//more than 500 chars, return to this page with an error
	if (strlen($_POST['body'])>500) {
		echo "<p>Sorry, you entered more than 500 characters in your message. Please try again.</p>\n";
		$error=TRUE;
	}
	//If subject is more than 50 characters, return page with an error.
	if (strlen($_POST['subject'])>50) {
		echo "<p>Sorry, you entered more than 50 characters in your subject. Please try again.</p>\n";
		$error=TRUE;
	}
	//If the user left the recipient field blank, return an error
	if (empty($_POST['to'])) {
		echo "<p>Sorry, you need to enter a recipient for this message. Please try again.</p>\n";
		$error=TRUE;
	}
	//If not, they could still have tried sending it to more than 5 people
	else {
		//Remove whitespace from string in "to"
		$to=trim(str_replace(' ', '',  $_POST['to']));
		//Explode and put each recipient into an array separated by commas
		$toarray=explode(",", $to);
		//Let's try to avoid spammers. If there are more than 10 recipients specified,
		//boot them back to mail.php?compose=new, let them know not to specify more than
		//5 recipients, and put the data back in the fields
		if (count($toarray)>5) {
			echo "<p>Sorry, you entered more than 5 recipients. Please try again.</p>\n";
			$error=TRUE;
		}
	}
	//If the user left the subject field blank, return an error
	if (empty($_POST['subject'])){
		echo "<p>Sorry, you left the subject field blank. Please try again.</p>\n";
		$error=TRUE;
	}
	//If the user left the body field blank, return an error
	if (empty($_POST['body'])) {
		echo "<p>Sorry, you left the body field blank. Please try again.</p>\n";
		$error=TRUE;
	}
	//Let's see if the recipient actually exists.
	$userexistsquery="SELECT * FROM users WHERE username='" . trim(mysql_real_escape_string($_POST['to'])) . "'";
	$userexistsresult=mysql_query($userexistsquery);
	if (!$userexistsresult) {
		die('Error:' . mysql_error());
	}
	if (mysql_num_rows($userexistsresult)==0) {
		echo "<p>Sorry, no one by that name exists!</p>";
		$error=TRUE;
	}
	//If a validation error did not occur, continue as normal
	//Note, the user could still try to send it to a character who doesn't exist
	//Enter message into sentbox and inbox(es)
	if (!$error) {
		//Now we need to add this to the sentbox of the sender
		$datesent=time();
		$sentboxquery="INSERT INTO sentbox (`sender`, `subject`, `body`, `datesent`) VALUES ('" . trim(mysql_real_escape_string($_SESSION['userID'])) . "', '" . trim(mysql_real_escape_string($_POST['subject'])) . "', '" . trim(mysql_real_escape_string($_POST['body'])) . "', '" . mysql_real_escape_string($datesent) . "')";
		mysql_query($sentboxquery);
		//Ok, now we'll need to grab the mail number from the sent box
		$mailnumberquery="SELECT mailnumber FROM sentbox WHERE sender='" . mysql_real_escape_string($_SESSION['userID']) . "' AND subject='" . trim(mysql_real_escape_string($_POST['subject'])) . "' AND body='" . trim(mysql_real_escape_string($_POST['body'])) . "' AND datesent='" . $datesent . "'";
		$mailnumberresource=mysql_query($mailnumberquery);
		$mailnumberarray=mysql_fetch_array($mailnumberresource);
		$mailnumber=$mailnumberarray[0];
		
		//Next we need to add this to the inbox for as many recipients there are specified
		foreach ($toarray as $value) {
			//Fetch the user ID from the users table for the username stored in $value,
			//aka one of the receivers of the message
			$usersearch="SELECT userID, username FROM users WHERE username='" . $value . "'";
			$usersearchresult=mysql_query($usersearch);
			$usersearchresultarray=mysql_fetch_array($usersearchresult);
			
			//If the name exists in the users table, send it
			if ($usersearchresultarray[1]===$value) {
				$inboxquery="INSERT INTO inbox (`mailnumber`, `recipient`) VALUES ('" . $mailnumber . "', '" . $usersearchresultarray[0] . "')";
				mysql_query($inboxquery);
				//header( 'Location: http://www.kremica.com' ) ;
				header( 'Location: mail.php' ) ;
				exit;
					
			}
			//If not, return and error and continue trying to send
			else {
				echo "<p>User " . $value . " does not exist!</p>\n";
			}
		}
	}
	//If an error occurred, redisplay the message form
	else {
		//Start new message table and form
		echo "<form name='newmessage' action='mail.php' method='post'>";
		echo "<table>
				<tr>
					<th>To (separate up to 5 with commas):</th><td><input type='text' name='to' size='15' value='" . $_POST['to'] . "' /></td>
				</tr>
				<tr>
					<th>Subject:</th><td><input type='text' name='subject' size='45' value='" . $_POST['subject'] . "'/></td>
				</tr>
				<tr>
					<th>Message (limit 500 chars):</th><td><textarea name='body' cols='50' rows='10' />" . $_POST['body'] . "</textarea></td>
				</tr>
				<tr>
					<td colspan='2'><input type='submit' name='send' value='Send'/></td>
				</tr>
			</table>";
		//End new message table 	
	}
}
//Show Inbox by default
else {
	echo "
		<h2>Inbox</h2>
		";
	//Need info from the inbox
	$inboxquery="SELECT mailnumber FROM inbox WHERE recipient='" . mysql_real_escape_string($_SESSION['userID']) . "' and deleted='no'";
	$resultinbox=mysql_query($inboxquery);
	
	//start inbox table
	echo "
		<form name='inbox' method='post' action='mail.php'>
		<table class='stripeme'>
			<tr>
				<th>Subject</th>
				<th>Sender</th>
				<th>Date Sent</th>
			</tr>";
	
		if (!$resultinbox) {
			echo "<tr><td colspan='3'>No messages :(</td></tr>";
			die('Your mailbox is empty.');
		}
		else {
			while ($row = mysql_fetch_assoc($resultinbox)) {
			//Need info specific to each mailnumber from the sentbox
			$sentboxquery="SELECT * FROM sentbox, users WHERE mailnumber='". $row['mailnumber'] . "' AND users.userID=sentbox.sender";
			$sentboxrow = mysql_fetch_assoc(mysql_query($sentboxquery));
			
			//This is all stuff to determine how long ago the message was sent.
			$timeagosent=time()-$sentboxrow['datesent'];
			$minutes=floor($timeagosent/60);
			$hours=floor($timeagosent/3600);
			$days=floor($timeagosent/86400);
			$timeagostring="";
			//Show days if sent days ago, show hours if sent less than a day ago, show minutes if sent
			//less than an hour ago, show seconds if sent less than a minute ago.
			if ($days>0) $timeagostring=$days . " days ago";
			else if ($hours>0) $timeagostring=$hours . " hours ago";
			else if ($minutes>0) $timeagostring=$minutes . " minutes ago";
			else $timeagostring=$timeagosent . " seconds ago";
			
			echo "
			<tr>
				<td><input type='checkbox' name='inbox[]' value='" . $row['mailnumber'] . "'/> <a href='mail.php?message=" . $row['mailnumber'] . "'>" . $sentboxrow['subject'] . "</a></td>
				<td>" . $sentboxrow['username'] . "</td>
				<td>" . $timeagostring . "</td>
			</tr>";
			}
		}
		
	//end inbox table
	echo "
		</table>
		<br/>
		<input type='submit' value='Delete Selected' name='delete'/>
		</form>
		";
}



?>
			</div>
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