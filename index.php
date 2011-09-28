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


//Session starts
session_start();

/*
 * Function necessary to run Kremica
 */
require_once('inc_functions.php');

/******
 * Login Attempt 
 ******
 * If the user is submitting a "username," pass his/her credentials and session ID to the database.
 */
if (isset($_POST['username'])) {
	
	if (login($_POST['username'], $_POST['userpassword'], session_id())) {
		$loginstatus="<p class=\"loginerror\">Logged in!</p>\n";
	}
	else {
		$loginstatus="<p class=\"loginerror\">Incorrect password.</p>\n";
	}
}

/******
 * Character Name Creation Validation
 ******
 * If the user is signed in (aka their username is set in the session), check to see if they are
 * trying to create a new character (aka charactername will be set)
 */
else if (isset($_SESSION['username']) && isset($_POST['charactername'])) {
		
	/*
	 * We're going to trim the input name first
	 */	
	
	$pendingname=trim($_POST['charactername']);
	
	/*
	 * First we need to make sure no one is trying to break our database. Run a preg_match against
	 * the attempted charactername. Character names may only contain spaces or
	 * lowercase and uppercase letters. So, this preg_match checks to see if any matches return for
	 * non-alphabetical or space characters (a-zA-Z\s). The if statement also checks to see if the name
	 * starts with an alphabetical character.
	 */
	if (preg_match('/[^a-zA-Z\s]+/',$pendingname) || !preg_match('/^[a-zA-Z]+/', $pendingname)) {
		if (preg_match('/[^a-zA-Z\s]+/',$pendingname)) {
			$invalidnamereason="You must only use letter characters for adventurer names.";
		}
		else {
			$invalidnamereason="Your name must start with a letter.";
		}
	}
	
	/*
	 * If $invalidnamereason is not set yet, go through this loop. Otherwise, continue on
	 */
	if (!isset($invalidnamereason)) {
		
		/*
		 * Connect to the database, then run a query to grab all character names from the character table
		 * that match the one submitted via $_POST['charactername']
		 * 
		 * Also run it through mysql_real_escape_string()
		 */
		include 'inc_connection.php';
		$pendingname=mysql_real_escape_string($pendingname);
		if (isset($_POST['gender'])) {
			$gender=mysql_real_escape_string($_POST['gender']);
		}
		else {
			$gender='male';
		}
		$searchquery="SELECT name FROM characters WHERE name='" . $pendingname . "'";
		$myquery=mysql_query($searchquery);
		if (!$myquery) {
			die('Something bad happened: ' . mysql_error());
		}
		include 'inc_close_connection.php';
		
		/*
		 * If $myquery has any results in it, set $invalidnamereason. Otherwise, create the new character.
		 */
		if (mysql_num_rows($myquery)!==0) {
			$invalidnamereason="Sorry, this character name is already taken.";
		}	
		else {
			createcharacter($pendingname, $gender);
		}
	}
}


/******
 * Logout
 ******
 * If the username is set in the session (aka the user is logged in) AND $_GET['logout'] is set,
 * first check to see if there is a character set. If there is, check to see if it is in battle. If it is, kill
 * the character. If not, save the character. The character will be loaded the next time the user
 * logs in.
 */
else if (isset($_SESSION['username']) && isset($_GET['logout'])) {
	$logoutstatus="";
	if (isset($_SESSION['name'])) {
		if (isset($_SESSION['monstername'])) {
			include 'inc_dead.php';
			$logoutstatus.="<p class=\"logoutfirst\">Killed your character.</p>\n";
		}
		else {
			include 'inc_charactersave.php';
			$logoutstatus.="<p class=\"logoutfirst\">Saved your character.</p>\n";
		}
	}
	logout($_SESSION['username'], FALSE);
	$logoutstatus.="<p class=\"logout\">Logged out!</p>\n";
}

/******
 * In-Battle Check 
 ******
 * If the character is in the middle of a fight (ie if the monstername is set), OR if they have leveled and not yet set their attribute points
 * (ie if their strength, defense, and speed are all combined less than 6 times that of their level (minus 3)), transfer back to the fight page
 */
if (isset($_SESSION['name']) && (isset($_SESSION['monstername']) || ($_SESSION['strength']+$_SESSION['defense']+$_SESSION['speed']<(6*$_SESSION['level'])-3))) {
	//header( 'Location: http://www.kremica.com' ) ;
	header( 'Location: fight.php' ) ;
	exit;
}
/******
 * Game Consistency Checks
 ******
 * If a character should be on a page that pertains to the life of a character (rather than index.php),
 * send them there.
 */
if (isset($_SESSION['merchantname'])) {
	//header( 'Location: http://www.kremica.com' ) ;
	header( 'Location: merchant.php' ) ;
	exit;
}
if (isset($_SESSION['inventory'])) {
	//header( 'Location: http://www.kremica.com' ) ;
	header( 'Location: inventory.php' ) ;
	exit;
}
if (isset($_SESSION['randomencounter'])) {
	//header( 'Location: http://www.kremica.com' ) ;
	header( 'Location: random.php' );
	exit;
}
if (isset($_SESSION['infuser'])) {
	//header( 'Location: http://www.kremica.com' ) ;
	header( 'Location: infuser.php' ) ;
	exit;
}

/******
 * Character Is Created
 ******
 * If a character is created/currently active, go here
 */
if(isset($_SESSION['name'])) {

    /******
     * Action
     ******
     * If this is set, the character wants to do some sort of action.
     * Possible actions are:
     * walk around (and run into monsters, merchants, quests, or the infuser),
     * check their character sheet (which has all stats about the character),
     * or check their inventory.
     */
     if (isset($_GET['action'])) {

	    /******
	     * Walk Around
	     ******
	     * If a character has chosen to walk about, there are several different things that can (randomly)
	     * happen to the character. First, a number is chosen at random from 0-10. A decision is then made
	     * as to where the character has walked to from his current location. Possible locations to walk to are
	     * -a random encounter (which includes quests or other "fun" things that can either help (or hurt) the character),
	     * -a merchant chosen at random from the merchant table (possible merchants are shield, weapon, or potion),
	     * -a monster battle
	     * -the infuser (who can change the types of weapons/shields, providing an element of strategy to the game)
	     * 
	     */
	     if ($_GET['action']=='walk') {
		    $random = mt_rand(0,10);
		    /******
		     * Random Encounter (Quests)
		     ******
		     * Numbers less than 2 (0,1) will be sent to a random encounter
		     * ~18% chance
		     */
		    if ($random<2) {
			    //header( 'Location: http://www.kremica.com' ) ;
			    $_SESSION['randomencounter']="yes";
			    header( 'Location: random.php' ) ;
			    exit;
		    }
		    /******
		     * Merchant
		     ******
		     * Numbers 2-4 will be sent to a merchant
		     * ~18%
		     */
		    else if ($random>=2 && $random<5) {
			    include 'inc_connection.php';
			    $possiblemerchants=mysql_query("SELECT * FROM merchants");
			    $highprobability=mysql_num_rows($possiblemerchants);
			    $merchantnumber=mt_rand(1, $highprobability)-1;
			    $_SESSION['merchantname']=mysql_result($possiblemerchants,$merchantnumber,1);
			    $_SESSION['merchantwelcomemessage']=mysql_result($possiblemerchants,$merchantnumber,2);
			    $_SESSION['merchanttype']=mysql_result($possiblemerchants,$merchantnumber,3);
			    $_SESSION['merchantitemarray']=array();
			    if ($_SESSION['merchanttype']==='potion') {
				    $merchantitems=mysql_query("SELECT * FROM potions WHERE merchantID='1'");
				    while ($row = mysql_fetch_row($merchantitems)) {
					    $_SESSION['merchantitemarray'][$row[1]] = array($row[4], $row[2]);
				    }
			    }
			    else if ($_SESSION['merchanttype']==='weapon') {
				    $merchantitems=mysql_query("SELECT * FROM weapons WHERE merchantID='2'");
				    while ($row = mysql_fetch_row($merchantitems)) {
					    $_SESSION['merchantitemarray'][$row[1]] = array($row[7], $row[2], $row[3], $row[4], $row[0]);
				    }
			    }
			    else if ($_SESSION['merchanttype']==='shield') {
				    $merchantitems=mysql_query("SELECT * FROM shields WHERE merchantID='3'");
				    while ($row = mysql_fetch_row($merchantitems)) {
					    $_SESSION['merchantitemarray'][$row[1]] = array($row[6], $row[2], $row[3], $row[0]);
				    }
			    }
			    mysql_close($con);
			
			    //header( 'Location: http://www.kremica.com' ) ;
			    header( 'Location: merchant.php' ) ;
			    exit;
			
		    }
		    /******
		     * Monster Battle
		     ******
		     * Numbers 5-9 will lead to monster battles
		     * ~45%
		     */
		    else if ($random>=5&&$random<10) {
			    //Since certain aspects of characters stats can be changed mid-battle, we need to be able to re-set them once the battle is over
			    //So, let's save them
			    $_SESSION['originalspeed']=$_SESSION['speed'];
			    createrandommonster();
			    //header( 'Location: http://www.kremica.com' ) ;
			    header( 'Location: fight.php' ) ;
			    exit;
		    }
		    /******
		     * Infuser
		     ******
		     * Number 10 will lead to the infuse
		     * ~9%
		     */
		    else {
			    //header( 'Location: http://www.kremica.com' ) ;
			    $_SESSION['infuser']="yes";
			    header( 'Location: infuser.php' ) ;
			    exit;
		    }
	    }
	    else if ($_GET['action']=='inventory') {
		    $_SESSION['inventory']="yes";
	        //header( 'Location: http://www.kremica.com' ) ;
		    header( 'Location: inventory.php' ) ;
		    exit;
	    }
    }
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>

<title>Kremica</title>

<link rel="stylesheet" type="text/css" href="stylesheet.css" />


<!--
JQuery Swap Image for the gender radio button
-->
<script type="text/javascript" src="jquery-1.4.4.js"></script>
<script type="text/javascript">

$(function() {
	$('input[name=gender]:radio').change(function() {
			$('.createcharacterimage').attr('src', 'images/charactercreate'+this.value+'.jpg').attr('alt', 'Choose the '+this.value+' character?');
	});
});



</script>

</head>


<body>
<div id="wrapper">
	<div id="header">
<?php
if (isset($_SESSION['username'])) include 'inc_userheader.php';
?>
	</div>
	
	<div id="content">
		
<?php

if (isset($_SESSION['username'])) {
	/******
	 * Character Not Created
	 ******
	 * If a character is not created/currently active, go here
	 */
	if (!isset($_SESSION['name'])) {
		
		echo "
		<div id=\"leftside\">
			<div id=\"leftside_inner\">
			
				<h2>Character Creation</h2>
				<div id=\"contentbox\">
					<div id=\"contentbox_inner\">
			";
			
		/******
		 * Character Name Invalid
		 ******
		 * If a character's name was invalid, show this.
		 */
		if (isset($invalidnamereason)) {
			echo "<div class='invalidnameerror'><p>Invalid Character Name: " . $invalidnamereason . "</p></div>\n";
		}
		
		/******
		 * Character Creation Form
		 ******
		 * This is where a user may create a character. It re-directs back to index.php.
		 */
		echo "
						<form name='charactercreation' action='index.php' method='post'>
						<p>Name:</p>
						<p><input type='text' name='charactername' maxlength='15' class='input'/></p>
							<p>Gender</p>
							<p>Male:<input class='maleradio' type='radio' name='gender' value='male'/>
							Female:<input class='femaleradio' type='radio' name='gender' value='female'/></p>
						<div class='charactercreatesubmit'>
							<p><input type='submit' class='submit' value='Adventure!'/></p>
						</div>
						</form>\n";
			
		echo "
					</div>
				</div>
			</div>
		</div>\n";
		
		echo "
		<div id=\"rightside\">
			<div id=\"rightside_inner\">
				<img class=\"createcharacterimage\" src=\"images/charactercreate.jpg\" alt=\"Which will you choose?\"/>
			</div>
		</div>
		";
	}
    else {
        echo "
		        <div id=\"leftside2\">
			        <div id=\"leftside2_inner\">
				        <h2>Actions</h2>
				        <ul>
					        <li><a href='index.php?action=walk'>Walk Around</a></li>
					        <li><a href='index.php?action=inventory'>Inventory</a></li>
					        <li><a href='charsheet.php'>Stats</a></li>
				        </ul>
			        </div>
		        </div>
        ";
        echo "
		        <div id=\"rightside2\">
			        <div id=\"rightside2_inner\">
        ";
        /******
         * Character Default
         ******
         * If action is not chosen, character must chose what he/she wishes to do. He/she can 
         * walk around (and run into monsters, merchants, quests, or the infuser)
         * commit suicide, 
         * check their character sheet (which has all stats about the character),
         * or check their inventory.
         */
        echo "
			        <h3>" . $_SESSION['name'] . "</h3>
        ";
        //Progress Bar for health points
        echo "
			        <div class=\"healthbar\">
				        <p class=\"healthbartext\">HP: </p>
				        <div class=\"healthbarcontainer\">
					        <div style=\"width:" . 100*($_SESSION['hp']/$_SESSION['maxhp']) . "%\">
						        <p class=\"bartext\">" . $_SESSION['hp'] . "/" . $_SESSION['maxhp'] . "</p>
					        </div>
				        </div>
			        </div>
        ";


        //Progress Bar for experience points
        echo "
			        <div class=\"experiencebar\">
				        <p class=\"experiencebartext\">EXP: </p>
				        <div class=\"experiencebarcontainer\">
					        <div style=\"width:" . 100*($_SESSION['exp']/$_SESSION['maxexp']) . "%\">
						        <p class=\"bartext\">" . $_SESSION['exp'] . "/" . $_SESSION['maxexp'] . "</p>
					        </div>
				        </div>
			        </div>

			        <img src=\"images/" . $_SESSION['gender'] . "full.jpg\" alt=\"Male Adventurer\"/>

			        </div>
		        </div>
        ";
    }
}
/******
 * User Default
 ******
 * If the user isn't logged in, show this login prompt
 */
else {
	
	echo "
			<div id='leftside'>
				<div id='leftside_inner'>
					<h2>Login</h2>
					<div id='contentbox'>
						<div id='contentbox_inner'>
	";
	if (isset($logoutstatus)) {
		echo "
							" . $logoutstatus . "
		";
	}
	else if (isset($loginstatus)) {
		echo "
							" . $loginstatus . "
		";
	}
	echo "
							<form name=\"login\" action=\"index.php\" method=\"post\">
								<p>Username:</p> <p><input class=\"input\" type=\"text\" name=\"username\" /></p>
								<p>Password:</p> <p><input class=\"input\" type=\"password\" name=\"userpassword\" /></p>
								<p><input type=\"submit\" class=\"submit\" value=\"Log In!\" name=\"submit\" /></p>
							</form>
						</div>
					</div>
				</div>
			</div>
	";//End of #leftside div
	
	echo "
			<div id='rightside'>
				<img src=\"images/titlelogo.jpg\" alt=\"Characters\"/>
			</div>
	";//End of #rightside div
	
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