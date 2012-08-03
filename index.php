<?php

if(isset($_GET['s'])) {
	
	include 'url.php';
	
	$search = $_GET['s'];
	$search = trim($search,"\x22\x27"); // Trim quotes ad single quotes from around phrases
	$search = str_replace('"', "", $search); // Remove all double quotes
	$search = strtolower($search);
	$file = file_get_contents($url);
	$json = json_decode($file);
	$rows = $json->{'feed'}->{'entry'};

	foreach($rows as $row) {
		$term = $row->{'gsx$term'}->{'$t'};
  		$lower_term = strtolower($term);
  
  		if ( $search == $lower_term){
  			
			$term_match = $term;
  			$definition = $row->{'gsx$definition'}->{'$t'};
  
		}
  	}
}

if(isset($_POST['addnew'])) {
	
	$m = NULL;
	
	// Send an email to the admin account
	
	include 'url.php';
	
	$name = stripslashes($_POST['name']);
	$email = stripslashes($_POST['email']);
	$term = stripslashes($_POST['term']);
	$definition = stripslashes($_POST['definition']);
	$agree = $_POST['agree'];
	
	if($agree != 1) {
		
		$m = '<p class="lib-error">You must agree to the terms.</p>';
		
	} else {
		
		if($name == NULL) {
			
			$m = '<p class="lib-error">You need to tell us your name.</p>';
			
		} else {
			
			if($email == NULL) {

				$m = '<p class="lib-error">You need to include your email address.</p>';

			} else {
				
				if($term == NULL) {

					$m = '<p class="lib-error">How do I know what term you want to define? You forgot to tell me.</p>';

				} else {
					
					if($definition == NULL) {

						$m = '<p class="lib-error">That&#8217;s hardly a definition.</p>';

					}
				}}}}
				
	if($m == NULL) { // No errors, send the email
		
		$message = $term . ': ' . $definition . '\n\n';
		$message .= 'Submitted by: ' . $name . ' ' . $email;
		$subject = "New Definition";
		
		// compose headers
		$headers = "From: hello@unbraryit.com\r\n";
		$headers .= "Reply-To: hello@unbraryit.com\r\n";
		$headers .= "X-Mailer: PHP/".phpversion();
		
		mail($to, $subject, $message, $headers);
		
		$m = '<p class="lib-success">Your suggestion has been sent. Thanks!</p>';
		 
	}
}
?>

<html lang="en">

<head>
	
	<meta name="viewport" content="width=device-width, initial-scale=1">
	
	<title>Unbrary it: demystify Library Jargon</title>
	
	<link rel="stylesheet" type="text/css" href="http://gvsu.edu/cms3/assets/741ECAAE-BD54-A816-71DAF591D1D7955C/libui.css" />
	<link rel="stylesheet" type="text/css" href="css/unbrary-styles.css" />
</head>

<body>
	
	<div class="wrap">
	
	<header>
		<h1><img src="img/logo.png" alt="Unbrary It: Demystify Library Jargon" /></h1>
		
	</header>
	
	<div class="line" role="main">
		
		<div class="size">
		
		<?php
		if((!isset($_GET['s'])) && (!isset($_GET['a']))) { // No search and no add. Show the search field.
		?>
		
		<form class="lib-form jargon" method="get" action="">
		<label for="s">What jargon do you need un-libraried?</label>
		<input type="text" name="s" <? if(isset($_GET['s'])) { echo 'value="' . $_GET["s"] . '"'; } ?>/>
		<input type="submit" value="Unbrary It" class="lib-button-small" />
		</form>
		
		<?php 
		
		} else { // Search has been done, show the "New search" link.
		
			echo '<p class="newsearch-link lib-button-small-grey"><a href="/">New Search</a></p>';
	
		}
		
		if(isset($_GET['s'])) {
		
		if((isset($term_match)) && (isset($definition)) && ($definition != "")) { // We've done a search
		
		echo '<dl>
			<dt>' . $term_match . '</dt>
			<dd>
				<p>' . $definition . '</p>
			</dd>
		</dl>';
		
		} else { // no terms match that search
			
			echo '<p class="whoops">Wow. You&#8217;ve stumped us.</p>';
			echo '<p style="margin:1em 0;">We&#8217;ll go ask a librarian and get a definition up soon.</p>';
			
			// Give them the option of submitting a definition or asking the "Lazy Web"
			
		?>
		
			<div class="line">
				<div class="size1of4 unit">
					<?php
					echo '<a href="http://twitter.com/?status=Lazy%20Web!%20How%20would%20you%20define%20' . str_replace(" ", "%2B", $search) . '%3F%20%23unbraryit" class="centered lib-button-small stumped-button">Ask the Lazy Web</a>';
					?>
				</div>
				<div class="size1of4 unit">
					<?php
					echo '<a href="?a=' . $search . '" class="centered lib-button-small stumped-button">Suggest a definition</a>';
					?>
				</div>
				<div class="size1of2 unit lastUnit">
				</div>
			</div>
		
		<?php
			
			// Record this term so we know someone searched for it
			
			$data = array($_GET['s']);
			if (!$DataFile = fopen("notfound.csv", "a")) {echo "Failure: cannot open file"; die;};
			if (!fputcsv($DataFile, $data)) {echo "Failure: cannot write to file"; die;};
			fclose($DataFile);
			
		}	
		
		}
		
		if(isset($_GET['a'])) { // User is suggesting a new definition
			
			echo $m; // Show any errors
			
		?>
		
			<form class="lib-form" name="add-definition" method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
				<label for="term">Your Jargon:</label>
				<input type="text" name="term" required="required" value="<?php echo $_GET['a']; ?>" />
				<label for="definition">Unbrary it for us:</label>
				<textarea name="definition" required="required"><?php if(isset($definition)) { echo $definition; } ?></textarea>
				<label for="name">Your Name:</label>
				<input type="text" name="name" required="required" <?php if(isset($name)) { echo 'value="' . $name . '"'; } ?> />
				<label for="email">Your email:</label>
				<input type="email" name="email" required="required" <?php if(isset($email)) { echo 'value="' . $email . '"'; } ?> />
				<p><small><strong>Terms:</strong> By submitting this, you agree that you have permission to post it and that UnbraryIt might use your definition as-is or edited without crediting you. Because really, you do not want your name associated with this. Trust us.</small></p>
				<input type="checkbox" name="agree" required="required" value="1" /> <label class="lib-inline" for="agree">I agree to the terms</label>
				<input class="lib-button-small" type="submit" name="addnew" value="Suggest" />
			</form>
		
		<?php
			
		}
		?>
		
	</div>
</div>
		
	</div>
	
	<footer>
		<p class="twitter"><a href="http://twitter.com/unbraryit">Follow @UnbraryIt on Twitter</a></p>
		
		<p>Licensed under the <a href="http://www.gnu.org/copyleft/gpl.html"><abbr title="GNU General Public License">GPL</abbr></a>. Source code available on <a href="https://github.com/mreidsma/unbraryit">Github</a>.</small></p>

	</footer>

	<script src="js/respond.js"></script>
	<script src="js/scripts.js"></script>
		
	</footer>

</body>
</html>