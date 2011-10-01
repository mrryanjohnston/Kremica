<?php
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

<table class="stripeme">
	<tr>
		<th>Character Name</th>
		<th>Level</th>
		<th>User Account</th>
	</tr>
<?php

	if (!$result || mysql_num_rows($result)==0) {
		echo "<tr><td colspan='3'>No character found :(</td></tr>";
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