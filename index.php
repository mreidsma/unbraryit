<?php

if (isset($_GET['s'])) {
	
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
  		$term = strtolower($term);
  
  		if ( $search == $term){
  			
			$term_match = $term;
  			$definition = $row->{'gsx$definition'}->{'$t'};
  
		}
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
		if(!isset($_GET['s'])) { // No search. Show the search field.
		?>
		
		<form class="lib-form" method="get" action="">
		<label for="s">What jargon do you need un-libraried?</label>
		<input type="text" name="s" <? if(isset($_GET['s'])) { echo 'value="' . $_GET["s"] . '"'; } ?>/>
		<input type="submit" value="Unbrary It" class="lib-button-small" />
		</form>
		
		<?php 
		
		} else { // Search has been done, show the "New search" link.
			
		echo '<p class="newsearch-link"><a href="/">New Search</a></p>';
		
		}
		
		if(isset($_GET['s'])) {
		
		if((isset($term_match)) && (isset($definition)) && ($definition != "")) { // We've done a search
		
		echo '<dl>
			<dt>' . $term_match . '</dt>
			<dd><p>' . $definition . '</p></dd>
		</dl>';
		
		} else { // no terms match that search
			
			echo '<p class="whoops">Wow. You&#8217;ve stumped us.</p>';
			echo '<p style="margin:1em 0;">We&#8217;ll go ask a librarian and get a definition up soon.</p>';
			
			// Give them the option of submitting a definition or asking the "Lazy Web"
			
		?>
		
			<div class="line">
				<div class="size1of4 unit">
					<?php
					echo '<a href="http://twitter.com/?status=Lazy%20Web!%20How%20would%20you%20define%20' . urlencode($search) . '%20%23unbraryit" class="centered lib-button-small stumped-button">Ask the Lazy Web</a>';
					?>
				</div>
				<div class="size1of4 unit">
					<?php
					echo '<a href="/suggest/' . $search . '" class="centered lib-button-small stumped-button">Suggest a definition</a>';
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
		?>
		
	</div>
</div>
		
	</div>
	
	<footer>
		<p class="twitter"><a href="http://twitter.com/unbraryit">Follow @UnbraryIt on Twitter</a></p>
		
		<p class="bylabs">A <a href="http://gvsu.edu/library/labs"><abbr title="Grand Valley State University">GVSU</abbr> Library Labs</a> project, inspired by <a href="http://unsuck-it.com">Unsuck It</a>.</p>
		<!--p><small>Built by <a href="http://abbybedford.com">Abby Bedford</a>, Ala Alluhaidan, Mary Morgan, Patrick Roth, and <a href="http://matthewreidsma.com">Matthew Reidsma</a> for <a href="http://gvsu.edu/library">Grand Valley State University Libraries</a>.</p-->
		<p>Licensed under the <a href="http://www.gnu.org/copyleft/gpl.html"><abbr title="GNU General Public License">GPL</abbr></a>. Source code available on <a href="https://github.com/mreidsma/unbraryit">Github</a>.</small></p>

	</footer>

	<script src="js/respond.js"></script>
	<script src="js/scripts.js"></script>
		
	</footer>

</body>
</html>