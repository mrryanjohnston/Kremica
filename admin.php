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

/*Username must be admin*/
session_start();
if (!isset($_SESSION['username']) || $_SESSION['username'] != "admin") {
	//header( 'Location: http://www.kremica.com' ) ;
	header( 'Location: index.php' ) ;
	exit;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>Kremica: Admin Panel</title>
<link rel="stylesheet" type="text/css" href="stylesheet.css" />
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<meta name="generator" content="Geany 0.19.1" />
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
		<h2>Admin Panel</h2>
		<div id="adminpanel">
			<div id="adminpanel_inner">
				<ul>
					<li><a href="admin.php?action=monstereditor">Monster Editor</a></li>
					<li><a href="#">User controls</a></li>
				</ul>
			</div>
		</div>

	</div>
	
	<div id="footer">
		<div id="footer_inner">
	<?php	include('inc_footer.html'); ?>
		</div>
	</div>
	
</div>