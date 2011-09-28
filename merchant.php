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
	if (!isset($_SESSION['merchantname'])) {
		//header( 'Location: http://www.kremica.com' ) ;
		header( 'Location: index.php' ) ;
		exit;
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>

<title>Kremica: Merchant</title>
<link rel="stylesheet" type="text/css" href="stylesheet.css" />
</head>

<body>

<div id="wrapper">
	<div id="header">
<?php
include 'inc_userheader.php';
?>
	</div>
	
	<div id="content">

<?php
//If the character doesn't have enough space for the items he or she wishes to buy, go here.
if (isset($_SESSION['toomanyitems'])) {
	unset($_SESSION['toomanyitems']);
	echo "<p>You tried to buy more items than which you have space for. Try again.</p>";
	echo "<form name='formvictory' action='merchant.php' method='post'>
	<p><input type='submit' name='continue' value='Continue'/></p>
	</form>";
	
}
//If the character doesn't have enough gold to purchase the items, go here
else if (isset($_SESSION['notenoughgold'])) {
	unset($_SESSION['notenoughgold']);
	echo "<p>You tried to buy more items than which you can afford. Try again.</p>";
	echo "<form name='formvictory' action='merchant.php' method='post'>
	<p><input type='submit' name='continue' value='Continue'/></p>
	</form>";
	
}
//If the character doesn't want to buy anything, go here
else if (isset($_POST['nothanks'])) {
	unset($_SESSION['merchantname']);
	header( 'Location: index.php' ) ;
	exit;
}
//If none of these errors happened and the character wishes to buy something, go in here
else if (isset($_POST['buy'])) {
	//If this was a potion merchant, enter here
	if ($_SESSION['merchanttype']==='potion') {
		$emptybagslots=(5 - count($_SESSION['potionholster']));
	
		$numberofitems=0;
		$totalcost=0;
		$costarray=array();
		$itemarray=array();
		$i=0;
		$n=0;
		//Add up the total number of items, grab the cost for the items to buy, and finally,
		//the items to buy
		foreach ($_POST as $key => $value) {

			$numberofitems+=$value;
			$costarray[$i]=$value;
			$itemarray[$n]=$key;
			$i++;
			$n++;
			//$totalcost+=($value*$_SESSION['merchantitemarray'][$key]);
		}
		$i=0;
		//Go throuhg the merchant item array as set on index.php
		//Add up the total cost for the items that are going to be bought
		foreach ($_SESSION['merchantitemarray'] as $key => $value) {
			$totalcost+=($value[0]*$costarray[$i]);
			$i++;
		}
		//If there are too many items to be bought, redirect the user back to the merchant page
		//with toomanyitems set
		if ($numberofitems > $emptybagslots) {
			$_SESSION['toomanyitems']='yes';
			//header( 'Location: http://www.kremica.com' ) ;
			header( 'Location: merchant.php' ) ;
			exit;
		}
		//If the total cost of purchase is more than the amount of gold the character has, send them back to
		//the merchant page with notenoughgold set
		else if ($totalcost > $_SESSION['gold']) {
			$_SESSION['notenoughgold']='yes';
			//header( 'Location: http://www.kremica.com' ) ;
			header( 'Location: merchant.php' ) ;
			exit;
		}
		//If else, then add the items to buy into the potion holster
		else {
			$i=0;
			foreach ($_SESSION['merchantitemarray'] as $key => $value) {
				while ($_POST[$itemarray[$i]] > 0) {
					$_SESSION['potionholster'][]=array($key, $value[1]);
					$_POST[$itemarray[$i]]--;
				}
				$i++;
			}
		}
		unset($_SESSION['merchantname']);
		//If the number of items to be purchased is 0, just say they didn't mean to purchase anything
		if ($numberofitems==0) {
			echo "<p>You decided not to buy anything.</p>";
		}
		//Else, buy the items by removing gold from the inventory
		else {
			$_SESSION['gold']-=$totalcost;
			include 'inc_charactersave.php';
			echo "<p>You bought stuff! Congrats!</p>";
		}
		echo "<form name='formvictory' action='index.php' method='post'>
		<p><input type='submit' name='continue' value='Continue'/></p>
		</form>";
	}
	//If the merchant is a weapon merchant, go in here
	else if ($_SESSION['merchanttype']==='weapon') {
		$numberofitems=0;
		$totalcost=0;
		//Go through the post array
		foreach ($_POST as $key => $value) {
			//Don't include the submit button
			if ($key!=='buy') {
				//Tally up the total cost for the merchant items here
				$totalcost+=$_SESSION['merchantitemarray'][str_replace("_", " ", $key)][0]*$value;
			}
		}
		//If the character already has 3 weapons (including fists), they are at their
		//limit for how many weapons they may have
		if (count($_SESSION['weaponholster'])>=3) {
			$_SESSION['toomanyitems']='yes';
			//header( 'Location: http://www.kremica.com' ) ;
			header( 'Location: merchant.php' ) ;
			exit;
		}
		//If the total cost of purchasing the items is more gold than the character has,
		//redirect to the merchant page and set notenough gold to true
		else if ($totalcost > $_SESSION['gold']) {
			$_SESSION['notenoughgold']='yes';
			//header( 'Location: http://www.kremica.com' ) ;
			header( 'Location: merchant.php' ) ;
			exit;
		}
		//Else, add the items to buy into the weaponholster
		else {
			$i=0;
			unset($_SESSION['merchantname']);
			foreach ($_POST as $key => $value) {
				while ($value > 0) {
					$_SESSION['weaponholster'][]=array(str_replace("_", " ", $key), $_SESSION['merchantitemarray'][str_replace("_", " ", $key)][1], $_SESSION['merchantitemarray'][str_replace("_", " ", $key)][2], $_SESSION['merchantitemarray'][str_replace("_", " ", $key)][3], $_SESSION['merchantitemarray'][str_replace("_", " ", $key)][4]);
					$i++;
					$value--;
					$numberofitems++;		
				}
			}
			
		if ($numberofitems==0) {
			echo "<p>You decided not to buy anything.</p>";
		}
		else {
			$_SESSION['gold']-=$totalcost;
			include 'inc_charactersave.php';
			echo "<p>You bought stuff! Congrats!</p>";
		}
		echo "<form name='formvictory' action='index.php' method='post'>
		<p><input type='submit' name='continue' value='Continue'/></p>
		</form>";
		}
	}
	//Else, the merchant must be a shield merchant
	else {
		$numberofitems=0;
		$totalcost=0;
		//Go through the post array
		foreach ($_POST as $key => $value) {
			//Don't include the  submit button
			if ($key!=='buy') {
				//Tally up the total cost for the merchant items here
				$totalcost+=$_SESSION['merchantitemarray'][str_replace("_", " ", $key)][0]*$value;
			}
		}
		//If the character has 3 shields already, they have the limit of shields
		if (count($_SESSION['shieldholster'])>=3) {
			$_SESSION['toomanyitems']='yes';
			//header( 'Location: http://www.kremica.com' ) ;
			header( 'Location: merchant.php' ) ;
			exit;
		}
		//If the total cost is more gold than the character has, they don't have enough gold
		else if ($totalcost > $_SESSION['gold']) {
			$_SESSION['notenoughgold']='yes';
			//header( 'Location: http://www.kremica.com' ) ;
			header( 'Location: merchant.php' ) ;
			exit;
		}
		//Else, grab the shields to purchase and then add them to the inventory
		else {
			$i=0;
			unset($_SESSION['merchantname']);
			//Go through the post array again
			foreach ($_POST as $key => $value) {
				while ($value > 0) {
					$_SESSION['shieldholster'][]=array(str_replace("_", " ", $key), $_SESSION['merchantitemarray'][str_replace("_", " ", $key)][1], $_SESSION['merchantitemarray'][str_replace("_", " ", $key)][2], $_SESSION['merchantitemarray'][str_replace("_", " ", $key)][3]);
					$i++;
					$value--;
					$numberofitems++;		
				}
			}
			//If the number of items to buy is 0, just say they didn't want to buy anything
			if ($numberofitems==0) {
				echo "<p>You decided not to buy anything.</p>";
			}
			//Else, remove the total cost from the gold
			else {
				$_SESSION['gold']-=$totalcost;
				include 'inc_charactersave.php';
				echo "<p>You bought stuff! Congrats!</p>";
			}
			echo "<form name='formvictory' action='index.php' method='post'>
			<p><input type='submit' name='continue' value='Continue'/></p>
			</form>";
		}
	}
}
else {
	echo "<h2>" . $_SESSION['merchantname'] . "</h2>";
	echo "<p>" . $_SESSION['merchantwelcomemessage'] . "</p>";
	echo "
	<p>Gold: $_SESSION[gold]</p>";
	
	echo "<form name='merchant' action='merchant.php' method='post'><table><tr><td>Item</td><td>Power</td><td>Cost</td><td>Amount to Purchase</td></tr>";
	
	$count = count($_SESSION['merchantitemarray']);
	
	foreach ($_SESSION['merchantitemarray'] as $key => $value) {
		echo "<tr><td>" . $key . "</td>";
		echo "<td>" . $value[1];
		if ($_SESSION['merchanttype']==='weapon') { echo "-" . $value[2]; }
		echo "</td>";
		echo "<td>" . $value[0] . "</td>";
		echo "<td><select name='" . $key . "'><option value='0'>--</option><option value='1'>1</option><option value='2'>2</option><option value='3'>3</option></select></td></tr>";
	}
	echo "</table>
			<input type='submit' name='buy' value='Buy!'/>
			<input type='submit' name='nothanks' value='No thanks...'/></form>
	";
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