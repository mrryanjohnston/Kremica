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
//If user not logged in, do not show the annals.
if (!isset($_SESSION['username'])) {
		//header( 'Location: http://www.kremica.com' ) ;
		header( 'Location: index.php' ) ;
		exit;
}
include 'inc_connection.php';
//This is the default query just in case they didn't hit the submit button.
$query="SELECT * FROM characters, users WHERE characters.userID=users.userID ORDER BY level DESC LIMIT 0, 10";

//If the user didn't enter a user or account to search for,
//they must have changed the number to display
if (empty($_GET['charactertosearchfor']) && empty($_GET['useraccounttosearchfor']) && !empty($_GET['numbertodisplay'])) {
	$query="SELECT * FROM characters, users WHERE characters.userID=users.userID ORDER BY level DESC LIMIT 0, " . trim(mysql_real_escape_string($_GET['numbertodisplay']));
}
else if (!empty($_GET['charactertosearchfor']) || !empty($_GET['useraccounttosearchfor'])) {
		$query="SELECT * FROM characters, users WHERE characters.userID=users.userID AND ";
		
		if (!empty($_GET['charactertosearchfor'])) {
			$query.= "characters.name='" . trim(mysql_real_escape_string($_GET['charactertosearchfor'])) . "' ";
		}
		
		if (!empty($_GET['charactertosearchfor']) && !empty($_GET['useraccounttosearchfor'])) {
			$query.= " AND ";
		}
		
		if (!empty($_GET['useraccounttosearchfor'])) {
			$query.= "characters.userID=users.userID AND users.username='" . trim(mysql_real_escape_string($_GET['useraccounttosearchfor'])) . "'";
		}
		
		$query.= " ORDER BY level DESC LIMIT 0, " . trim(mysql_real_escape_string($_GET['numbertodisplay']));
}

$result=mysql_query($query);


include 'inc_close_connection.php';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
	<title>Kremica: Annals of Adventurers</title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<meta name="generator" content="Geany 0.19.1" />
	<link rel="stylesheet" type="text/css" href="stylesheet.css" />
	<script type="text/javascript" src="jquery-1.4.4.js"></script>
	<script type="text/javascript">
	$(function() {
		$('.stripeme tr:even').addClass('alt');
	});
	$(function() {
		$('#searchcontrols').hide();
		$('.clicktoexpand').click(function() {
			var $html=$(this).html();
			if ($html=="Click to Search [+]") {
				$('#searchcontrols').slideDown('slow');
				$(this).html("Click to Hide [-]");
			}
			else {
				$('#searchcontrols').slideUp('slow');
				$(this).html("Click to Search [+]");
			}
		});
	});
	
	<!--Ajax-->
	$(function() {
		$('input:submit').click(function() {
			$.get('ajax_annalsresult.php', { charactertosearchfor: $('input[name="charactertosearchfor"]').val(), useraccounttosearchfor: $('input[name="useraccounttosearchfor"]').val(), numbertodisplay: $('select[name="numbertodisplay"]').val(),  } , function(data) {
				$('#resulttable').html(data);
				$('.stripeme tr:even').addClass('alt');
			});
			return false;
		});
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
		<h2>Annals of Adventurers</h2>
			<p class="clicktoexpand">Click to Search [+]</p>
			<div id="searchcontrols">
				<form name="annals" action="annals.php" method="get">
				
				<h3>Criteria</h3>
				
				<div id="searchinner">
				
					<p class="floatleft">Character:<br/><input type="text" name="charactertosearchfor" <?php echo (isset($_GET['charactertosearchfor']) ? "value='". $_GET['charactertosearchfor'] . "'" : "") ?>/></p>
					<p class="floatleft">Username:<br/><input type="text" name="useraccounttosearchfor" <?php echo (isset($_GET['useraccounttosearchfor']) ? "value='". $_GET['useraccounttosearchfor'] . "'" : "") ?>/></p>
					<p class="clear">Number to display per page:<br/> 
					<select name="numbertodisplay">
						<option value="10">10</option>
						<option value="25">25</option>
						<option value="50">50</option>
						<option value="100">100</option>
					</select>
					</p>
				</div>
				<br/>
				<p><input type="submit" value="Search"/></p>
				</form>
			</div>
			<div id="resulttable">
				<table class="stripeme">
					<tr>
						<th>Character Name</th>
						<th>Level</th>
						<th>User Account</th>
					</tr>
<?php

	if (!$result || mysql_num_rows($result)==0) {
		echo "
					<tr><td colspan='3'>No character found :(</td></tr>
		";
		die('Sorry, couldn\'t find your search!');
	}
	else {
		while ($row = mysql_fetch_assoc($result)) {
		echo "
				<tr>
					<td><p><a href='charsheet.php?charname=" . $row['name'] . "'>" . $row['name'] . "</a></p></td>
					<td><p>" . $row['level'] . "</p></td>
					<td><p><a href='profile.php?username=" . $row['username'] . "'>" . $row['username'] . "</a></p></td>
				</tr>
	";
	}
}

echo "
</table>
";
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