<!DOCTYPE html>
<head>
	<title>LoL Emperor v0.1</title>
	<link rel="stylesheet" type="text/css" href="css/main.css">
	<link rel="stylesheet" type="text/css" href="css/nav.css">
	<link rel="stylesheet" type="text/css" href="css/summoner-information.css">
	<link rel="stylesheet" type="text/css" href="css/captions.css">
	<script src="js/moment.min.js"></script>
	<script src="js/jquery-2.1.1.min.js"></script>
	<script>
   		moment().format();
	</script>
	<script src="js/convertdate.js"></script>
	<link rel="icon"  type="image/png" href="../images/favicon.png">
</head>
<body>
<div id="wrapper">
	<div id="header">
		<ul id="nav">
			<?php
			Print '<li><a href="https://' . $_SERVER['SERVER_NAME']. '" id="small-logo"></a></li>';
			?>
			<li><a></a></li>
			<li><a id="nav-search">	
				<form method="post" action="<?php $_PHP_SELF ?>">					
					<input name="summoner_name" type="text" id="summoner_name" placeholder="Enter a summoner name"/></input>	
					<select name="server">
						<option value="oce">OCE</option>
						<option value="na">NA</option>
						<option value="euw">EUW</option>
						<option value="eune">EUNE</option>
						<option value="br">BR</option>
						<option value="tr">TR</option>
						<option value="ru">RU</option>
						<option value="lan">LAN</option>
						<option value="las">LAS</option>
					</select>
					<button class="searchButton" name="add" type="submit" id="add" value="Submit"><div id="search-ico"></div></button>
				</form>
			</a></li>
		</ul>
	</div>
	<div id="fixed-header-filler">
	</div>
	<div id="content">
	<?php
	if(isset($_POST['add']) || ($_GET['name'] != null && $_GET['server'] != null)) {

		/*----------------------------------
		----------Project Comments----------
		----------------------------------*/
/*		Stat calculator
		So champ base + runes / masteries at lvl 1.
		Then maybe scale as they level.
		Then add items on top.*/

/*		Match History advanced details
		gpm and xppm ggold per min, xp per min*/

		$numOfRequests = 0;

		// Checks if url parameters are not null

		if(isset($_POST['add'])) {
			$summoner = str_replace(' ', '',strtolower($_POST['summoner_name']));
			$server = strtolower($_POST['server']);
			header('Location: https://'. $_SERVER['SERVER_NAME'] .'?name=' . $summoner . '&server=' . $server);
		} else if($_GET['name'] != null && $_GET['server'] != null) {
			$summoner = str_replace(' ', '', strtolower($_GET['name']));
			$server = strtolower($_GET['server']);

		$serverCodes = array("br", "eune", "euw", "lan", "las", "na", "oce", "ru", "tr");
		$serverNames = array("Brazil", "Europe Nordic & East", "Europe West", "Latin America North",
		"Latin America South", "North America", "Oceania", "Russia", "Turkey");

		$playerServer;

		for($i = 0; $i <sizeof($serverCodes); $i++) {
			if($server == $serverCodes[$i]) {
				$playerServer = $serverNames[$i];
			}
		}

		$apiKey = "INSERT RIOT API KEY HERE";
		/*-----------------------------------
		----------Summoner Summary-----------
		-----------------------------------*/
		// HTTP request to get summoner information
		$url = 'https://' . $server . '.api.pvp.net/api/lol/' . $server . '/v1.4/summoner/by-name/' . $summoner .'?api_key=' . $apiKey;
		// Get contents of HTTP request (JSON)		     
		$content = file_get_contents($url);
		$numOfRequests++;

		if($content == null) {
			Print "Summoner does not exist on server: " . $server;
		} else {
			// Store summoner information as an array
			$summonerInfo = json_decode($content, true);

		/*-----------------------------------
		----------Setup initial HTML-----------
		-----------------------------------*/
		$url = 'https://ddragon.leagueoflegends.com/realms/na.json';	     
		$content = file_get_contents($url);
		$imageInfo = json_decode($content, true);

		$summonerId = $summonerInfo[$summoner]['id'];

		Print "<div id=\"summoner-information\">
		<div class=\"content-box\">
		<input type=\"radio\" name=\"tabs\" id=\"tab1\" checked=\"true\"></input>
		<input type=\"radio\" name=\"tabs\" id=\"tab2\"></input>
		<input type=\"radio\" name=\"tabs\" id=\"tab3\"></input>
		<input type=\"radio\" name=\"tabs\" id=\"tab4\"></input>
		<input type=\"radio\" name=\"tabs\" id=\"tab5\"></input>
		<div id=\"summoner-header\">
			<div id=\"header-content\">
				<div id=\"summoner-icon\">
					<img src=\"https://ddragon.leagueoflegends.com/cdn/" . $imageInfo['n']['profileicon'] . "/img/profileicon/" .
					 $summonerInfo[$summoner]['profileIconId'] . ".png\" style=\"width: 100%; height:100%;\"></img>
				</div>
				<div id=\"summoner-profile\">
				<h1>" . $summonerInfo[$summoner]['name'] . "</h1> 
				<p>Level " . $summonerInfo[$summoner]['summonerLevel'] . "</p>
				<h3>" . $playerServer . "</h3>
				</div><div class=\"clear\">
				</div>
			</div>
			<ul id=\"summoner-tabs\">
				<li id=\"summoner-tab1\"><label for=\"tab1\"><a>Summary</a></label></li>
				<li id=\"summoner-tab2\"><label for=\"tab2\"><a>Statistics</a></label></li>
				<li id=\"summoner-tab3\"><label for=\"tab3\"><a>Match History</a></label></li>
				<li id=\"summoner-tab4\"><label for=\"tab4\"><a>Masteries</a></label></li>
				<li id=\"summoner-tab5\"><label for=\"tab5\"><a>Runes</a></label></li>
			</ul>
		</div>
		<div id=\"summoner-content\">
			<div id=\"summoner-summary\">";

			// To get an array dump
/*			Print var_dump($summonerStats['playerStatSummaries'][0]['wins']);*/

			/*-----------------------------------
			------------League Summary------------
			-----------------------------------*/
			// HTTP request to get summoner league information
			$url = 'https://' . $server . '.api.pvp.net/api/lol/' . $server . '/v2.5/league/by-summoner/' . $summonerInfo[$summoner]['id'] .'/entry?api_key=' . $apiKey;
			$numOfRequests++;
			// Get contents of HTTP request (JSON)		     
			$content = file_get_contents($url);
			// Summoner Current League
			$summonerCL = json_decode($content, true);
			// Current highest solo 5v5 rank
			$chs5v5r = array(0, 0);
			// Current highest team 3v3 rank
			$cht3v3r = array(0, 0);
			// Current highest team 5v5 rank
			$cht5v5r = array(0, 0);

			// Converts the league strings into numbers
			// Then validates the highest tier and divisions
			for($i = 0; $i < sizeof($summonerCL[$summonerInfo[$summoner]['id']]); $i++) {
				// Current League Test
				$clt = $summonerCL[$summonerInfo[$summoner]['id']][$i];
				// Current League Test Number Converted
				$cltnc = array(0, 0);
				// BRONZE == 1, SILVER == 2, GOLD == 3, PLATINUM == 4, DIAMOND == 5, MASTER = 6, CHALLENGER = 7
				if($clt['tier'] == "BRONZE") {
					$cltnc[0] = 1;
				} else if($clt['tier'] == "SILVER") {
					$cltnc[0] = 2;
				} else if($clt['tier'] == "GOLD") {
					$cltnc[0] = 3;
				} else if($clt['tier'] == "PLATINUM") {
					$cltnc[0] = 4;
				} else if($clt['tier'] == "DIAMOND") {
					$cltnc[0] = 5;
				} else if($clt['tier'] == "MASTER") {
					$cltnc[0] = 6;
				} else if($clt['tier'] == "CHALLENGER") {
					$cltnc[0] = 7;
				}
				// V == 5, IV == 4, III = 3, II = 2, I = 1
				if($clt['entries'][0]['division'] == "V") {
					$cltnc[1] = 5;
				} else if($clt['entries'][0]['division'] == "IV") {
					$cltnc[1] = 4;
				} else if($clt['entries'][0]['division'] == "III") {
					$cltnc[1] = 3;
				} else if($clt['entries'][0]['division'] == "II") {
					$cltnc[1] = 2;
				} else if($clt['entries'][0]['division'] == "I") {
					$cltnc[1] = 1;
				}

				if($clt['queue'] == "RANKED_SOLO_5x5") {
					if($chs5v5r[0] == 0 && $chs5v5r[1] == 0) {
						$chs5v5r[0] = $cltnc[0];
						$chs5v5r[1] = $cltnc[1];
					} else if($chs5v5r[0] < $cltnc[0]) {
						$chs5v5r[0] = $cltnc[0];
						$chs5v5r[1] = $cltnc[1];
					} else if($chs5v5r[0] == $cltnc[0]) {
						if($chs5v5r[1] > $cltnc[1]) {
							$chs5v5r[1] = $cltnc[1];
						}
					}
				} else if($clt['queue'] == "RANKED_TEAM_3x3") {
					if($cht3v3r[0] == 0 && $cht3v3r[1] == 0) {
						$cht3v3r[0] = $cltnc[0];
						$cht3v3r[1] = $cltnc[1];

					} else if($cht3v3r[0] < $cltnc[0]) {
						$cht3v3r[0] = $cltnc[0];
						$cht3v3r[1] = $cltnc[1];
					} else if($cht3v3r[0] == $cltnc[0]) {
						if($cht3v3r[1] > $cltnc[1]) {
							$cht3v3r[1] = $cltnc[1];
						}
					}
				} else if($clt['queue'] == "RANKED_TEAM_5x5") {
					if($cht5v5r[0] == 0 && $cht5v5r[1] == 0) {
						$cht5v5r[0] = $cltnc[0];
						$cht5v5r[1] = $cltnc[1];
					} else if($cht5v5r[0] < $cltnc[0]) {
						$cht5v5r[0] = $cltnc[0];
						$cht5v5r[1] = $cltnc[1];
					} else if($cht5v5r[0] == $cltnc[0]) {
						if($cht5v5r[1] > $cltnc[1]) {
							$cht5v5r[1] = $cltnc[1];
						}
					}
				}
			}

			// Converts the numbers back into league strings
			for($i = 0; $i < 3; $i++) {
				$tempConverter;
				if($i == 0) {
					$tempConverter = $chs5v5r;
				} else if($i == 1) {
					$tempConverter = $cht3v3r;
				} else if($i == 2) {
					$tempConverter = $cht5v5r;
				}
				// Converts the Tier
				if($tempConverter[0] == 1) {
					$tempConverter[0] = "BRONZE";
				} else if($tempConverter[0] == 2) {
					$tempConverter[0] = "SILVER";
				} else if($tempConverter[0] == 3) {
					$tempConverter[0] = "GOLD";
				} else if($tempConverter[0] == 4) {
					$tempConverter[0] = "PLATINUM";
				} else if($tempConverter[0] == 5) {
					$tempConverter[0] = "DIAMOND";
				} else if($tempConverter[0] == 6) {
					$tempConverter[0] = "MASTER";
				} else if($tempConverter[0] == 7) {
					$tempConverter[0] = "CHALLENGER";
				}
				// Converts the division
				if($tempConverter[1] == 5) {
					$tempConverter[1] = "V";
				} else if($tempConverter[1] == 4) {
					$tempConverter[1] = "IV";
				} else if($tempConverter[1] == 3) {
					$tempConverter[1] = "III";
				} else if($tempConverter[1] == 2) {
					$tempConverter[1] = "II";
				} else if($tempConverter[1] == 1) {
					$tempConverter[1] = "I";
				}

				if($i == 0) {
					$chs5v5r = $tempConverter;
				} else if($i == 1) {
					$cht3v3r = $tempConverter;
				} else if($i == 2) {
					$cht5v5r = $tempConverter;
				}
			}

			$solo5v5 = array("", "", "", "", "", "");
			$team3v3 = array("", "", "", "", "", "");
			$team5v5 = array("", "", "", "", "", "");

			// Using the validated leagues from above get all of that leagues information
			for($i = 0; $i < sizeof($summonerCL[$summonerInfo[$summoner]['id']]); $i++) {
				// Current League Colection;
				$clc = $summonerCL[$summonerInfo[$summoner]['id']][$i];
				if($clc['queue'] == "RANKED_SOLO_5x5") {
					if($clc['tier'] == $chs5v5r[0]) {
						if($clc['entries'][0]['division'] == $chs5v5r[1]) {
							$solo5v5[0] = strtoupper($clc['tier'] . "_" . $clc['entries'][0]['division']);
							$solo5v5[1] = $clc['name'];
							$solo5v5[2] = $clc['tier'] . " " . $clc['entries'][0]['division'];
							$solo5v5[3] = $clc['entries'][0]['leaguePoints'] . " points";
							$solo5v5[4] = $clc['entries'][0]['wins'] . " wins";
							if($clc['entries'][0]['isFreshBlood'] == 1) {
								$solo5v5[5] = "Recently Joined: Yes";
							} else {
								$solo5v5[5] = "Recently Joined: No";
							}
							// Checks if summoner has won atleast 3 games in a row
							if($clc['entries'][0]['isHotStreak'] == 1) {
								$solo5v5[6] = "Hot Streak: Yes";
							} else {
								$solo5v5[6] = "Hot Streak: No";
							}
						}
					}
				} else if($clc['queue'] == "RANKED_TEAM_3x3") {
					if($clc['tier'] == $cht3v3r[0]) {
						if($clc['entries'][0]['division'] == $cht3v3r[1]) {
							$team3v3[0] = strtoupper($clc['tier'] . "_" . $clc['entries'][0]['division']);
							$team3v3[1] = $clc['name'];
							$team3v3[2] = $clc['tier'] . " " . $clc['entries'][0]['division'];
							$team3v3[3] = $clc['entries'][0]['leaguePoints'] . " points";
							$team3v3[4] = $clc['entries'][0]['wins'] . " wins";
							if($clc['entries'][0]['isFreshBlood'] == 1) {
								$team3v3[5] = "Recently Joined: Yes";
							} else {
								$team3v3[5] = "Recently Joined: No";
							}
							// Checks if summoner has won atleast 3 games in a row
							if($clc['entries'][0]['isHotStreak'] == 1) {
								$team3v3[6] = "Hot Streak: Yes";
							} else {
								$team3v3[6] = "Hot Streak: No";
							}
						}
					}
				} else if($clc['queue'] == "RANKED_TEAM_5x5") {
					if($clc['tier'] == $cht5v5r[0]) {
						if($clc['entries'][0]['division'] == $cht5v5r[1]) {
							$team5v5[0] = strtoupper($clc['tier'] . "_" . $clc['entries'][0]['division']);
							$team5v5[1] = $clc['name'];
							$team5v5[2] = $clc['tier'] . " " . $clc['entries'][0]['division'];
							$team5v5[3] = $clc['entries'][0]['leaguePoints'] . " points";
							$team5v5[4] = $clc['entries'][0]['wins'] . " wins";
							if($clc['entries'][0]['isFreshBlood'] == 1) {
								$team5v5[5] = "Recently Joined: Yes";
							} else {
								$team5v5[5] = "Recently Joined: No";
							}
							// Checks if summoner has won atleast 3 games in a row
							if($clc['entries'][0]['isHotStreak'] == 1) {
								$team5v5[6] = "Hot Streak: Yes";
							} else {
								$team5v5[6] = "Hot Streak: No";
							}
						}
					}
				}


				if($i == sizeof($summonerCL[$summonerInfo[$summoner]['id']]) - 1 || $summonerCL[$summonerInfo[$summoner]['id']] == null) {
				}
			}

			// Set defaults if league does not exist
			if($solo5v5[0] == "") {
				$solo5v5[0] = "unknown";
				$solo5v5[1] = "";
				$solo5v5[2] = "Not Ranked";
				$solo5v5[3] = "";
				$solo5v5[4] = "";
				$solo5v5[5] = "";
				$solo5v5[6] = "";
			}
			if($team3v3[0] == "") {
				$team3v3[0] = "unknown";
				$team3v3[1] = "";
				$team3v3[2] = "Not Ranked";
				$team3v3[3] = "";
				$team3v3[4] = "";
				$team3v3[5] = "";
				$team3v3[6] = "";
			}
			if($team5v5[0] == "") {
				$team5v5[0] = "unknown";
				$team5v5[1] = "";
				$team5v5[2] = "Not Ranked";
				$team5v5[3] = "";
				$team5v5[4] = "";
				$team5v5[5] = "";
				$team5v5[6] = "";
			}

			/*---------------------------
			----Ranked Champion Stats----
			---------------------------*/

			// Get Ranked Champion Stats
			$url = 'https://' . $server . '.api.pvp.net/api/lol/' . $server . '/v1.3/stats/by-summoner/' . $summonerInfo[$summoner]['id'] .'/ranked?season=SEASON4&api_key=' . $apiKey;	     
			$numOfRequests++;
			$content = file_get_contents($url);
			$championStats = json_decode($content, true);

			// Get All Champion Information
			$url = 'https://oce.api.pvp.net/api/lol/static-data/oce/v1.2/champion?dataById=true&api_key=' . $apiKey;
			$numOfRequests++;
			$content = file_get_contents($url);
			$championInfo = json_decode($content, true);

			$storedChampStats = array();
			$totalStoredChampStats = array();

			for($i = 0; $i < sizeof($championStats['champions']); $i++) {
				$gamesPlayed;
				if($championInfo['data'][$championStats['champions'][$i]['id']]['name'] == "Kog'Maw") {
					array_push($storedChampStats, array($championStats['champions'][$i]['id'], "KogMaw", 
					$championStats['champions'][$i]['stats']['totalSessionsPlayed'], $championStats['champions'][$i]['stats']['totalSessionsWon'], $championStats['champions'][$i]['stats']['totalSessionsLost'], $championInfo['data'][$championStats['champions'][$i]['id']]['name']));
				} else if($championInfo['data'][$championStats['champions'][$i]['id']]['name'] == "LeBlanc") {
					array_push($storedChampStats, array($championStats['champions'][$i]['id'], "Leblanc", 
					$championStats['champions'][$i]['stats']['totalSessionsPlayed'], $championStats['champions'][$i]['stats']['totalSessionsWon'], $championStats['champions'][$i]['stats']['totalSessionsLost'], $championInfo['data'][$championStats['champions'][$i]['id']]['name']));
				} else if($championInfo['data'][$championStats['champions'][$i]['id']]['name'] == "Fiddlesticks") {
					array_push($storedChampStats, array($championStats['champions'][$i]['id'], "FiddleSticks", 
					$championStats['champions'][$i]['stats']['totalSessionsPlayed'], $championStats['champions'][$i]['stats']['totalSessionsWon'], $championStats['champions'][$i]['stats']['totalSessionsLost'], $championInfo['data'][$championStats['champions'][$i]['id']]['name']));
				} else if($championInfo['data'][$championStats['champions'][$i]['id']]['name'] == "Wukong") {
					array_push($storedChampStats, array($championStats['champions'][$i]['id'], "MonkeyKing", 
					$championStats['champions'][$i]['stats']['totalSessionsPlayed'], $championStats['champions'][$i]['stats']['totalSessionsWon'], $championStats['champions'][$i]['stats']['totalSessionsLost'], $championInfo['data'][$championStats['champions'][$i]['id']]['name']));
				} else if(strpos($championInfo['data'][$championStats['champions'][$i]['id']]['name'],'\'') == true) {
					// Splits the string
					$split = explode('\'', $championInfo['data'][$championStats['champions'][$i]['id']]['name']);
					// Combines the string
					$champion = $split[0] . strtolower($split[1]);

					array_push($storedChampStats, array($championStats['champions'][$i]['id'], $champion, 
					$championStats['champions'][$i]['stats']['totalSessionsPlayed'], $championStats['champions'][$i]['stats']['totalSessionsWon'], $championStats['champions'][$i]['stats']['totalSessionsLost'], $championInfo['data'][$championStats['champions'][$i]['id']]['name']));
				} else if($championInfo['data'][$championStats['champions'][$i]['id']]['name'] == null) {
					array_push($totalStoredChampStats, array($championStats['champions'][$i]['id'], str_replace(array(' ', '\'','.'), '',$championInfo['data'][$championStats['champions'][$i]['id']]['name']), 
					$championStats['champions'][$i]['stats']['totalSessionsPlayed'], $championStats['champions'][$i]['stats']['totalSessionsWon'], $championStats['champions'][$i]['stats']['totalSessionsLost'], $championInfo['data'][$championStats['champions'][$i]['id']]['name']));
				} else {
					array_push($storedChampStats, array($championStats['champions'][$i]['id'], str_replace(array(' ', '\'','.'), '',$championInfo['data'][$championStats['champions'][$i]['id']]['name']), 
					$championStats['champions'][$i]['stats']['totalSessionsPlayed'], $championStats['champions'][$i]['stats']['totalSessionsWon'], $championStats['champions'][$i]['stats']['totalSessionsLost'], $championInfo['data'][$championStats['champions'][$i]['id']]['name']));
				}


			}

			function games_played($a, $b) {
				return strnatcmp($a[2], $b[2]);
			}

			// Sort numerically by games played
			usort($storedChampStats, 'games_played');
			// Reverse the array order
			$storedChampStats = array_reverse($storedChampStats, false);




			Print  "<div class=\"row\">
					<h2>Ranked Summary</h2>
					<div id=\"solo-5x5\" class=\"ranked\">
					<div class=\"ranked-header\">
						<h3>Ranked Solo 5x5</h3>
					</div>
					<div class=\"ranked-badge\">
						<img src=\"images/lol-images/ranked/" . $solo5v5[0] . ".png\"></img>
					</div>
					<div class=\"ranked-information\">
						<h4>" . $solo5v5[1] . "</h4>
						<p>" . $solo5v5[2]. "</p>
						<p>" . $solo5v5[3] . "</p>
						<p>" . $solo5v5[4] . "</p>
						<p>" . $solo5v5[5] . "</p>
						<p>" . $solo5v5[6] . "</p>
					</div>
					</div>
					<div id=\"team-5x5\" class=\"ranked\">
					<div class=\"ranked-header\">
						<h3>Ranked Team 5x5</h3>
					</div>
					<div class=\"ranked-badge\">
						<img src=\"images/lol-images/ranked/" . $team5v5[0] . ".png\"></img>
					</div>
					<div class=\"ranked-information\">
						<h4>" . $team5v5[1] . "</h4>
						<p>" . $team5v5[2] . "</p>
						<p>" . $team5v5[3] . "</p>
						<p>" . $team5v5[4] . "</p>
						<p>" . $team5v5[5] . "</p>
						<p>" . $team5v5[6] . "</p>
					</div>
					</div>
					<div id=\"team-3x3\" class=\"ranked\">
					<div class=\"ranked-header\">
						<h3>Ranked Team 3x3</h3>
					</div>
					<div class=\"ranked-badge\">
						<img src=\"images/lol-images/ranked/" . $team3v3[0]  . ".png\"></img>
					</div>
					<div class=\"ranked-information\">
						<h4>" . $team3v3[1]  . "</h4>
						<p>" . $team3v3[2]  . "</p>
						<p>" . $team3v3[3]  . "</p>
						<p>" . $team3v3[4]  . "</p>
						<p>" . $team3v3[5]  . "</p>
						<p>" . $team3v3[6]  . "</p>
					</div>
					</div>
					<div class=\"clear\"></div>
					</div>
					<div class=\"row\">
					<h2>Most Played Champions</h2>";

					if(sizeof($storedChampStats) == 0) {
							Print 
							"<div class=\"champ\">
								<p>Not Ranked</p>
								<div class=\"clear\"></div>
							</div>";
					} else {
						for($i = 0; $i < 8; $i++) {
								Print 
								"<div class=\"champ  bar hint--top\" data=\"" . $storedChampStats[$i][5] . "\">
									<div class=\"champ-image-container\">
										<img src=\"https://ddragon.leagueoflegends.com/cdn/4.18.1/img/champion/" . $storedChampStats[$i][1] . ".png\" style=\"width: 60px; height: 60px;\"></img>
									</div>
									<div class=\"champ-content\">
										<p>Played: " . $storedChampStats[$i][2] . "</p>
										<p>Won: " . $storedChampStats[$i][3] . "</p>
										<p>Lost: " . $storedChampStats[$i][4] . "</p>
									</div>
									<div class=\"clear\"></div>
								</div>";
						}
					}



					Print "</div>";

			/*-----------------------------------
			------------Stats Summary------------
			-----------------------------------*/
			// Get summoner stats summary (not ranked)
			$url = 'https://' . $server . '.api.pvp.net/api/lol/' . $server . '/v1.3/stats/by-summoner/' . $summonerInfo[$summoner]['id'] . '/summary?season=SEASON4&api_key=' . $apiKey;
			$numOfRequests++;
			$content = file_get_contents($url);
			$summonerStats = json_decode($content, true);

			Print "</div>
			<div id=\"statistics\">
			<div class=\"row\">
			<h2>Stats Summary</h2>
				<table>
					<tr>
						<th>Game Type</th>
						<th>Wins</th>
						<th>Loses</th>
						<th>Kills</th>
						<th>Asists</th>
						<th>Turret Kills</th>
						<th>Jungle Minion Kills</th>
						<th>Enemy Minion Kills</th>
					</tr>";
					for($i = 0; $i < sizeof($summonerStats['playerStatSummaries']); $i++) {
						if($summonerStats['playerStatSummaries'][$i]['aggregatedStats']['totalChampionKills'] != null) {
							Print "<tr>"; 
							if($summonerStats['playerStatSummaries'][$i]['playerStatSummaryType']  == null) {
								Print "<td>" . "-" . "</td>";
							} else {
								Print "<td>" . $summonerStats['playerStatSummaries'][$i]['playerStatSummaryType'] . "</td>";
							}
							Print "<td>" . $summonerStats['playerStatSummaries'][$i]['wins'] . "</td>";
							if($summonerStats['playerStatSummaries'][$i]['losses'] == null) {
								Print "<td>" . "-" . "</td>";
							} else {
								Print "<td>" . $summonerStats['playerStatSummaries'][$i]['losses'] . "</td>";
							}
							if($summonerStats['playerStatSummaries'][$i]['playerStatSummaryType'] == "RankedSolo5x5") {
								$rankedSoloLosses = $summonerStats['playerStatSummaries'][$i]['losses'];
							}
							if($summonerStats['playerStatSummaries'][$i]['aggregatedStats']['totalChampionKills'] == null) {
								Print "<td>" . "-" . "</td>";
							} else {
								Print "<td>" . $summonerStats['playerStatSummaries'][$i]['aggregatedStats']['totalChampionKills'] . "</td>";
							}
							if($summonerStats['playerStatSummaries'][$i]['aggregatedStats']['totalAssists'] == null) {
								Print "<td>" . "-" . "</td>";
							} else {
								Print "<td>" . $summonerStats['playerStatSummaries'][$i]['aggregatedStats']['totalAssists'] . "</td>";								
							}
							if($summonerStats['playerStatSummaries'][$i]['aggregatedStats']['totalTurretsKilled'] == null) {
								Print "<td>" . "-" . "</td>";
							} else {
								Print "<td>" . $summonerStats['playerStatSummaries'][$i]['aggregatedStats']['totalTurretsKilled'] . "</td>";								
							}
							if($summonerStats['playerStatSummaries'][$i]['aggregatedStats']['totalNeutralMinionsKilled'] == null) {
								Print "<td>" . "-" . "</td>";
							} else {
								Print "<td>" . $summonerStats['playerStatSummaries'][$i]['aggregatedStats']['totalNeutralMinionsKilled'] . "</td>";
							}
							if($summonerStats['playerStatSummaries'][$i]['aggregatedStats']['totalMinionKills'] == null) {
								Print "<td>" . "-" . "</td>";
							} else {
								Print "<td>" . $summonerStats['playerStatSummaries'][$i]['aggregatedStats']['totalMinionKills'] . "</td>";
							}
							Print "</tr>"; 
						}
					}
				Print "</table>";
		}

		Print	"</div></div>
			<div id=\"match-history\">";

			/*---------------------------
			------Get Match History------
			---------------------------*/

			$url = 'https://' . $server . '.api.pvp.net/api/lol/' . $server . '/v1.3/game/by-summoner/' . $summonerInfo[$summoner]['id'] .'/recent?api_key=' . $apiKey;	     
			$numOfRequests++;
			$content = file_get_contents($url);
			$matchHistory = json_decode($content, true);

			$matches = $matchHistory['games'];

			// Get all summoner spell info
			$url = 'https://oce.api.pvp.net/api/lol/static-data/oce/v1.2/summoner-spell?dataById=true&api_key=' . $apiKey;
			$numOfRequests++;
			$content = file_get_contents($url);
			$summonerSpells = json_decode($content, true);


		Print 	'<div class="row"><h2>Match History</h2>';
			for($i = 0; $i < sizeof($matches); $i++) {
				$currentMatch = $matches[$i];

				$matchPlayers = $currentMatch['fellowPlayers'];
				$alliedTeam = array();
				$enemyTeam = array();

				for($ii = 0; $ii < sizeof($matchPlayers); $ii++) {
					if($matchPlayers[$ii]['teamId'] == $currentMatch['stats']['team']) {
						array_push($alliedTeam, $matchPlayers[$ii]);
					} else {
						array_push($enemyTeam, $matchPlayers[$ii]);
					}
				}
 
				// Gets the game result eg win is true (1)
				$matchResult = $currentMatch['stats']['win'];
				if($matchResult == 1) {
					$matchResult = "Victory";
				} else {
					$matchResult = "Defeat";
				}

				$gameType = $currentMatch['subType'];
				if($gameType == "NORMAL") {
					$gameType = "Normal 5v5";
				} else if($gameType == "NONE") {
					$gameType = "Custom";
				} else if($gameType == "NORMAL_3x3") {
					$gameType = "Normal 3v3";
				} else if($gameType == "HEXAKILL") {
					$gameType = "Hexakill 6v6";
				} else if($gameType == "RANKED_SOLO_5x5") {
					$gameType = "Ranked Solo 5v5";
				} else if($gameType == "RANKED_TEAM_5x5") {
					$gameType = "Ranked Team 5v5";
				} else if($gameType == "RANKED_TEAM_3x3") {
					$gameType = "Ranked Team 3v3";
				} else if($gameType == "ARAM_UNRANKED_5x5") {
					$gameType = "Howling Abyss 5v5";
				}

				$matchEndDate = $currentMatch['createDate'];
/*				$matchEndDate = gmdate('d/m/y g:i a', $matchEndDate / 1000);*/
/*				$matchEndDate = gmdate('d/m/y h:m', $matchEndDate / 1000);*/

				$matchLength = round($currentMatch['stats']['timePlayed'] / 60, 2);

				$hours = "";
				$minutes = floor( $matchLength );    
				$seconds = $matchLength - $minutes; 

				// To help show a readible version of the minutes/hours
				if($minutes < 60 && $minutes >= 10) {
					$minutes = $minutes;
				} else if($minutes < 10) {
					$minutes = "0" . $minutes;
				} else {
					if($minutes >= 60 && $minutes < 120) {
						$hours = 1 . ":";
						$minutes = ($minutes - 60);
					} else if($minutes >= 120) {
						$hours = 2 . ":";
						$minutes = ($minutes - 120);
					}
				}

				// To help show a readible version of the seconds
				if($seconds == 0) {
					$seconds = ":00";
				} else if(round($seconds * 60, 0) < 10) {
					$seconds = round($seconds * 60, 0);
					$seconds = ":0" . $seconds;
				} else {
					$seconds = round($seconds * 60, 0);
					$seconds = ":" . $seconds;
				}

				// Get summoners champion they played
				$championURL = "https://ddragon.leagueoflegends.com/cdn/4.18.1/img/champion/";

				$matchChampion;
				if($championInfo['data'][$currentMatch['championId']]['name'] == "Kog'Maw") {
					$matchChampion = $championURL . "KogMaw";
				} else if($championInfo['data'][$currentMatch['championId']]['name'] == "LeBlanc") {
					$matchChampion = $championURL . "Leblanc";
				} else if($championInfo['data'][$currentMatch['championId']]['name'] == "Fiddlesticks") {
					$matchChampion = $championURL . "FiddleSticks";
				} else if($championInfo['data'][$currentMatch['championId']]['name'] == "Wukong") {
					$matchChampion = $championURL . "MonkeyKing";
				} else if(strpos($championInfo['data'][$currentMatch['championId']]['name'],'\'') == true) {
					// Splits the string
					$split = explode('\'', $championInfo['data'][$currentMatch['championId']]['name']);
					// Combines the string
					$matchChampion = $championURL . $split[0] . strtolower($split[1]);
				} else {
					$theChamp = str_replace(array(' ', '\'','.'), '',$championInfo['data'][$currentMatch['championId']]['name']);
					$matchChampion = $championURL . $theChamp;
				}

				$aTeamChampions = array();
				// Allied Champions
				for($ii = 0; $ii < sizeof($alliedTeam); $ii++) {
					if($championInfo['data'][$alliedTeam[$ii]['championId']]['name'] == "Kog'Maw") {
						array_push($aTeamChampions, $championURL . "KogMaw");
					} else if($championInfo['data'][$alliedTeam[$ii]['championId']]['name'] == "LeBlanc") {
						array_push($aTeamChampions, $championURL . "Leblanc");
					} else if($championInfo['data'][$alliedTeam[$ii]['championId']]['name'] == "Fiddlesticks") {
						array_push($aTeamChampions, $championURL . "FiddleSticks");
					} else if($championInfo['data'][$alliedTeam[$ii]['championId']]['name'] == "Wukong") {
						array_push($aTeamChampions, $championURL . "MonkeyKing");
					} else if(strpos($championInfo['data'][$alliedTeam[$ii]['championId']]['name'],'\'') == true) {
						// Splits the string
						$split = explode('\'', $championInfo['data'][$alliedTeam[$ii]['championId']]['name']);
						// Combines the string
						$theChamp = $split[0] . strtolower($split[1]);
						array_push($aTeamChampions, $championURL . $theChamp);

					} else {
						$theChamp =  str_replace(array(' ', '\'','.'), '', $championInfo['data'][$alliedTeam[$ii]['championId']]['name']);
						array_push($aTeamChampions, $championURL . $theChamp);
					}
				}

				for($ii = 0; $ii < 4 - sizeof($alliedTeam); $ii++) {
					array_push($aTeamChampions, "images/blank");
				}

				$eTeamChampions = array();
				// Enemy Champions
				for($ii = 0; $ii < sizeof($enemyTeam); $ii++) {
					if($championInfo['data'][$enemyTeam[$ii]['championId']]['name'] == "Kog'Maw") {
						array_push($eTeamChampions, $championURL . "KogMaw");
					} else if($championInfo['data'][$enemyTeam[$ii]['championId']]['name'] == "LeBlanc") {
						array_push($eTeamChampions, $championURL . "Leblanc");
					} else if($championInfo['data'][$enemyTeam[$ii]['championId']]['name'] == "Fiddlesticks") {
						array_push($eTeamChampions, $championURL . "FiddleSticks");
					} else if($championInfo['data'][$enemyTeam[$ii]['championId']]['name'] == "Wukong") {
						array_push($eTeamChampions, $championURL . "MonkeyKing");
					} else if(strpos($championInfo['data'][$enemyTeam[$ii]['championId']]['name'],'\'') == true) {
						// Splits the string
						$split = explode('\'', $championInfo['data'][$enemyTeam[$ii]['championId']]['name']);
						// Combines the string
						$theChamp = $split[0] . strtolower($split[1]);
						array_push($eTeamChampions, $championURL . $theChamp);

					} else {
						$theChamp =  str_replace(array(' ', '\'','.'), '', $championInfo['data'][$enemyTeam[$ii]['championId']]['name']);
						array_push($eTeamChampions, $championURL . $theChamp);
					}
				}

				for($ii = 0; $ii < 5 - sizeof($enemyTeam); $ii++) {
					array_push($eTeamChampions, "images/blank");
				}

				// 6 requests up to this point
				// Gets spell infO
				$spell1 = $summonerSpells['data'][$currentMatch['spell1']];
				$spell2 = $summonerSpells['data'][$currentMatch['spell2']];

				$kda = array(0, 0, 0);

				// Checks that there is a number for the K/D/A
				if($currentMatch['stats']['championsKilled'] != null) {
					$kda[0] = $currentMatch['stats']['championsKilled'];
				} 

				if($currentMatch['stats']['numDeaths'] != null) {
					$kda[1] = $currentMatch['stats']['numDeaths'];
				}

				if($currentMatch['stats']['assists'] != null) {
					$kda[2] = $currentMatch['stats']['assists'];
				}

			// Gets items info
				$items = array("", "", "", "", "", "");
				for($ii = 0; $ii < 7; $ii++) {
					if($currentMatch['stats']['item' . $ii] != null) {
						$items[$ii] = 'https://ddragon.leagueoflegends.com/cdn/4.17.1/img/item/' . $currentMatch['stats']['item' . $ii] . '.png';
					} else {
						$items[$ii] = 'images/blank.png';
					}
					
				}

				if($gameType == "Hexakill 6v6") {
					Print	'<div class="match">
					<div class="match-result ' . $matchResult . '">
						<h3>' . $matchResult . '</h3>
					</div>
					<div class="match-type">
						<div id="date' . $i .'" class="date">' . $matchEndDate . '</div>
						<div class="mode">
							<p>' . $gameType . '</p>
						</div>
						<div class="duration">
							<p>' . $hours . $minutes . $seconds . '</p>
						</div>
						<div class="clear">
						</div>
					</div>
					<div class="match-details">
						<div class="summoner-stats">
							<div class="top-col">
								<div class="champion">
									<img src="' . $matchChampion . '.png" style="width: 85px; height: 85px; border-radius: 5px;"></img>
								</div>
								<div class="summoner-build">
									<div class="utility-spells">
										<div class="spell1 bar hint--top" data="' . $spell1['name'] . '">
											<img src="https://ddragon.leagueoflegends.com/cdn/4.17.1/img/spell/' . $spell1['key'] . '.png" style="width: 50px; height: 50px; border-radius: 5px;"></img>
										</div>
										<div class="spell2 bar hint--top" data="' . $spell2['name'] .'">
											<img src="https://ddragon.leagueoflegends.com/cdn/4.17.1/img/spell/' . $spell2['key'] . '.png" style="width: 50px; height: 50px; border-radius: 5px;"></img>
										</div>
									</div>
									<div class="items">
										<div class="item 1">
											<img src="' . $items[0] . '" style="width: 35px; height: 35px; border-radius: 5px;"></img>
										</div>
										<div class="item 2">
											<img src="' . $items[1] . '"  style="width: 35px; height: 35px; border-radius: 5px;"></img>
										</div>
										<div class="item 3">
											<img src="' . $items[2] . '"  style="width: 35px; height: 35px; border-radius: 5px;"></img>
										</div>
										<div class="item 4">
											<img src="' . $items[3] . '"  style="width: 35px; height: 35px; border-radius: 5px;"></img>
										</div>
										<div class="item 5">
											<img src="' . $items[4] . '"  style="width: 35px; height: 35px; border-radius: 5px;"></img>
										</div>
										<div class="item 6">
											<img src="' . $items[5] . '"  style="width: 35px; height: 35px; border-radius: 5px;"></img>
										</div>
									</div>
								<div class="trinket-slot">
									<div class="trinket">
										<img src="' . $items[6] . '" style="width: 45px; height: 45px; border-radius: 5px;"></img>
									</div>
								</div>
								</div>

								<div class="clear">
								</div>
							</div>
							<div class="bottom-col">
								<div class="kda">
									<p>Kills: ' . $kda[0] . '</p>
									<p>Deaths: ' . $kda[1] .  '</p>
									<p>Assists: ' . $kda[2] .  '</p>
								</div>
								<div class="gold-creeps">
									<p>Gold: ' . $currentMatch['stats']['goldEarned'] . '</p>
									<p>Creeps: ' . ($currentMatch['stats']['minionsKilled'] + $currentMatch['stats']['neutralMinionsKilled']) . '</p>
								</div>
								<div class="clear">
								</div>
							</div>
						</div>
						<div class="team-stats">
							<div class="allied-team">
								<h3>Allied Team</h3>
								<div class="team">
									<div class="team-row">
										<div class="champion-small">
											<img src="' . $matchChampion . '.png" style="width: 30px; height: 30px; border-radius: 5px;"></img>
										</div>
										<div class="team-member 1">
											<p><b>' . $championInfo['data'][$currentMatch['championId']]['name'] . '</b></p>
										</div>
									</div>
									<div class="team-row">
										<div class="champion-small">
											<img src="' . $aTeamChampions[0] . '.png" style="width: 30px; height: 30px; border-radius: 5px;"></img>
										</div>
										<div class="team-member 2">
											<p>' . $championInfo['data'][$alliedTeam[0]['championId']]['name'] . '</p>
										</div>
									</div>
									<div class="team-row">
										<div class="champion-small">
											<img src="' . $aTeamChampions[1] . '.png" style="width: 30px; height: 30px; border-radius: 5px;"></img>
										</div>
										<div class="team-member 3">
											<p>' . $championInfo['data'][$alliedTeam[1]['championId']]['name'] . '</p>
										</div>
									</div>
									<div class="team-row">
										<div class="champion-small">
											<img src="' . $aTeamChampions[2] . '.png" style="width: 30px; height: 30px; border-radius: 5px;"></img>
										</div>
										<div class="team-member 4">
											<p>' . $championInfo['data'][$alliedTeam[2]['championId']]['name'] . '</p>
										</div>
									</div>
									<div class="team-row">
										<div class="champion-small">
											<img src="' . $aTeamChampions[3] . '.png" style="width: 30px; height: 30px; border-radius: 5px;"></img>
										</div>
										<div class="team-member 5">
											<p>' . $championInfo['data'][$alliedTeam[3]['championId']]['name'] . '</p>
										</div>
									</div>
									<div class="team-row">
										<div class="champion-small">
											<img src="' . $aTeamChampions[4] . '.png" style="width: 30px; height: 30px; border-radius: 5px;"></img>
										</div>
										<div class="team-member 5">
											<p>' . $championInfo['data'][$alliedTeam[4]['championId']]['name'] . '</p>
										</div>
									</div>
								</div>
							</div>
							<div class="enemy-team">
								<h3>Enemy Team</h3>
								<div class="team">
									<div class="team-row">
										<div class="champion-small">
											<img src="' . $eTeamChampions[0] . '.png" style="width: 30px; height: 30px; border-radius: 5px;"></img>
										</div>
										<div class="team-member 1">
											<p>' . $championInfo['data'][$enemyTeam[0]['championId']]['name'] . '</p>
										</div>
									</div>
									<div class="team-row">
										<div class="champion-small">
											<img src="' . $eTeamChampions[1] . '.png" style="width: 30px; height: 30px; border-radius: 5px;"></img>
										</div>
										<div class="team-member 2">
											<p>' . $championInfo['data'][$enemyTeam[1]['championId']]['name'] . '</p>
										</div>
									</div>
									<div class="team-row">
										<div class="champion-small">
											<img src="' . $eTeamChampions[2] . '.png" style="width: 30px; height: 30px; border-radius: 5px;"></img>
										</div>
										<div class="team-member 3">
											<p>' . $championInfo['data'][$enemyTeam[2]['championId']]['name'] . '</p>
										</div>
									</div>
									<div class="team-row">
										<div class="champion-small">
											<img src="' . $eTeamChampions[3] . '.png" style="width: 30px; height: 30px; border-radius: 5px;"></img>
										</div>
										<div class="team-member 4">
											<p>' . $championInfo['data'][$enemyTeam[3]['championId']]['name'] . '</p>
										</div>
									</div>
									<div class="team-row">
										<div class="champion-small">
											<img src="' . $eTeamChampions[4] . '.png" style="width: 30px; height: 30px; border-radius: 5px;"></img>
										</div>
										<div class="team-member 5">
											<p>' . $championInfo['data'][$enemyTeam[4]['championId']]['name'] . '</p>
										</div>
									</div>
									<div class="team-row">
										<div class="champion-small">
											<img src="' . $eTeamChampions[5] . '.png" style="width: 30px; height: 30px; border-radius: 5px;"></img>
										</div>
										<div class="team-member 5">
											<p>' . $championInfo['data'][$enemyTeam[5]['championId']]['name'] . '</p>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="clear">
						</div>
					</div></div>';
				} else {

					Print	'<div class="match">
					<div class="match-result ' . $matchResult . '">
						<h3>' . $matchResult . '</h3>
					</div>
					<div class="match-type">
						<div id="date' . $i .'" class="date">
						 	' . $matchEndDate . '
						</div>
						<div class="mode">
							<p>' . $gameType . '</p>
						</div>
						<div class="duration">
							<p>' . $hours . $minutes . $seconds . '</p>
						</div>
						<div class="clear">
						</div>
					</div>
					<div class="match-details">
						<div class="summoner-stats">
							<div class="top-col">
								<div class="champion">
									<img src="' . $matchChampion . '.png" style="width: 85px; height: 85px; border-radius: 5px;"></img>
								</div>
								<div class="summoner-build">
									<div class="utility-spells">
										<div class="spell1 bar hint--top" data="' . $spell1['name'] . '">
											<img src="https://ddragon.leagueoflegends.com/cdn/4.17.1/img/spell/' . $spell1['key'] . '.png" style="width: 50px; height: 50px; border-radius: 5px;"></img>
										</div>
										<div class="spell2 bar hint--top" data="' . $spell2['name'] .'">
											<img src="https://ddragon.leagueoflegends.com/cdn/4.17.1/img/spell/' . $spell2['key'] . '.png" style="width: 50px; height: 50px; border-radius: 5px;"></img>
										</div>
									</div>
									<div class="items">
										<div class="item 1">
											<img src="' . $items[0] . '" style="width: 35px; height: 35px; border-radius: 5px;"></img>
										</div>
										<div class="item 2">
											<img src="' . $items[1] . '"  style="width: 35px; height: 35px; border-radius: 5px;"></img>
										</div>
										<div class="item 3">
											<img src="' . $items[2] . '"  style="width: 35px; height: 35px; border-radius: 5px;"></img>
										</div>
										<div class="item 4">
											<img src="' . $items[3] . '"  style="width: 35px; height: 35px; border-radius: 5px;"></img>
										</div>
										<div class="item 5">
											<img src="' . $items[4] . '"  style="width: 35px; height: 35px; border-radius: 5px;"></img>
										</div>
										<div class="item 6">
											<img src="' . $items[5] . '"  style="width: 35px; height: 35px; border-radius: 5px;"></img>
										</div>
									</div>
								<div class="trinket-slot">
									<div class="trinket">
										<img src="' . $items[6] . '" style="width: 45px; height: 45px; border-radius: 5px;"></img>
									</div>
								</div>
								</div>

								<div class="clear">
								</div>
							</div>
							<div class="bottom-col">
								<div class="kda">
									<p>Kills: ' . $kda[0] . '</p>
									<p>Deaths: ' . $kda[1] .  '</p>
									<p>Assists: ' . $kda[2] .  '</p>
								</div>
								<div class="gold-creeps">
									<p>Gold: ' . $currentMatch['stats']['goldEarned'] . '</p>
									<p>Creeps: ' . ($currentMatch['stats']['minionsKilled'] + $currentMatch['stats']['neutralMinionsKilled']) . '</p>
								</div>
								<div class="clear">
								</div>
							</div>
						</div>
						<div class="team-stats">
							<div class="allied-team">
								<h3>Allied Team</h3>
								<div class="team">
									<div class="team-row">
										<div class="champion-small">
											<img src="' . $matchChampion . '.png" style="width: 30px; height: 30px; border-radius: 5px;"></img>
										</div>
										<div class="team-member 1">
											<p><b>' . $championInfo['data'][$currentMatch['championId']]['name'] . '</b></p>
										</div>
									</div>
									<div class="team-row">
										<div class="champion-small">
											<img src="' . $aTeamChampions[0] . '.png" style="width: 30px; height: 30px; border-radius: 5px;"></img>
										</div>
										<div class="team-member 2">
											<p>' . $championInfo['data'][$alliedTeam[0]['championId']]['name'] . '</p>
										</div>
									</div>
									<div class="team-row">
										<div class="champion-small">
											<img src="' . $aTeamChampions[1] . '.png" style="width: 30px; height: 30px; border-radius: 5px;"></img>
										</div>
										<div class="team-member 3">
											<p>' . $championInfo['data'][$alliedTeam[1]['championId']]['name'] . '</p>
										</div>
									</div>
									<div class="team-row">
										<div class="champion-small">
											<img src="' . $aTeamChampions[2] . '.png" style="width: 30px; height: 30px; border-radius: 5px;"></img>
										</div>
										<div class="team-member 4">
											<p>' . $championInfo['data'][$alliedTeam[2]['championId']]['name'] . '</p>
										</div>
									</div>
									<div class="team-row">
										<div class="champion-small">
											<img src="' . $aTeamChampions[3] . '.png" style="width: 30px; height: 30px; border-radius: 5px;"></img>
										</div>
										<div class="team-member 5">
											<p>' . $championInfo['data'][$alliedTeam[3]['championId']]['name'] . '</p>
										</div>
									</div>
								</div>
							</div>
							<div class="enemy-team">
								<h3>Enemy Team</h3>
								<div class="team">
									<div class="team-row">
										<div class="champion-small">
											<img src="' . $eTeamChampions[0] . '.png" style="width: 30px; height: 30px; border-radius: 5px;"></img>
										</div>
										<div class="team-member 1">
											<p>' . $championInfo['data'][$enemyTeam[0]['championId']]['name'] . '</p>
										</div>
									</div>
									<div class="team-row">
										<div class="champion-small">
											<img src="' . $eTeamChampions[1] . '.png" style="width: 30px; height: 30px; border-radius: 5px;"></img>
										</div>
										<div class="team-member 2">
											<p>' . $championInfo['data'][$enemyTeam[1]['championId']]['name'] . '</p>
										</div>
									</div>
									<div class="team-row">
										<div class="champion-small">
											<img src="' . $eTeamChampions[2] . '.png" style="width: 30px; height: 30px; border-radius: 5px;"></img>
										</div>
										<div class="team-member 3">
											<p>' . $championInfo['data'][$enemyTeam[2]['championId']]['name'] . '</p>
										</div>
									</div>
									<div class="team-row">
										<div class="champion-small">
											<img src="' . $eTeamChampions[3] . '.png" style="width: 30px; height: 30px; border-radius: 5px;"></img>
										</div>
										<div class="team-member 4">
											<p>' . $championInfo['data'][$enemyTeam[3]['championId']]['name'] . '</p>
										</div>
									</div>
									<div class="team-row">
										<div class="champion-small">
											<img src="' . $eTeamChampions[4] . '.png" style="width: 30px; height: 30px; border-radius: 5px;"></img>
										</div>
										<div class="team-member 5">
											<p>' . $championInfo['data'][$enemyTeam[4]['championId']]['name'] . '</p>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="clear">
						</div>
					</div></div>';
				}


			}
		Print	"</div>
		<script>
   			convertDate();
		</script></div>
			<div id=\"masteries\">			
			<div class=\"row\">";

			/*--------------------------------
			-------------Masteries------------
			--------------------------------*/

			// Get player mastery info
			$url = 'https://' . $server . '.api.pvp.net/api/lol/' . $server . '/v1.4/summoner/' . $summonerInfo[$summoner]['id'] .'/masteries?api_key=' . $apiKey;	     
			$numOfRequests++;
			$content = file_get_contents($url);
			$masteries = json_decode($content, true);
			$masteryPages = $masteries[$summonerInfo[$summoner]['id']]['pages'];

			for($i = 0; $i < sizeof($masteryPages); $i++) {
				if($i == 0) {
					Print '<input type="radio" name="mastery-tabs" id="mastery' . $i . '" checked="true"></input>';
				} else {
					Print '<input type="radio" name="mastery-tabs" id="mastery' . $i . '"></input>';
				}
			}


			Print '	<h2>Masteries</h2>
					<div id="mastery-pages"><ul>';

			for($i = 0; $i < sizeof($masteryPages); $i++) {
				// need to check this
				Print ' <li><label id="mastery-tab' . $i .'" for="mastery' . $i . '">' . $masteryPages[$i]['name'] . '</label></li>';					
			}		

			Print '</ul></div><div id="mastery-body">';

					
			for($i = 0; $i < sizeof($masteryPages); $i++) {
				$masteryTotals = array(0, 0, 0);
				$masteryPageRanks = array();
				for($j = 0; $j < 57; $j++) {
					array_push($masteryPageRanks, array(0, "not-ranked"));
				}

				for($ii = 0; $ii < sizeof($masteryPages[$i]['masteries']); $ii++) {
					if($masteryPages[$i]['masteries'][$ii]['id'] == 4111) {
						$masteryPageRanks[0][0] = $masteryPages[$i]['masteries'][$ii]['rank'];
						$masteryPageRanks[0][1] = "";
						$masteryTotals[0] = $masteryTotals[0] + $masteryPages[$i]['masteries'][$ii]['rank'];
					} else if($masteryPages[$i]['masteries'][$ii]['id'] == 4121) {
						$masteryPageRanks[1][0] = $masteryPages[$i]['masteries'][$ii]['rank'];
						$masteryPageRanks[1][1] = "";
						$masteryTotals[0] = $masteryTotals[0] + $masteryPages[$i]['masteries'][$ii]['rank'];
					} else if($masteryPages[$i]['masteries'][$ii]['id'] == 4131) {
						$masteryPageRanks[2][0] = $masteryPages[$i]['masteries'][$ii]['rank'];
						$masteryPageRanks[2][1] = "";
						$masteryTotals[0] = $masteryTotals[0] + $masteryPages[$i]['masteries'][$ii]['rank'];
					} else if($masteryPages[$i]['masteries'][$ii]['id'] == 4141) {
						$masteryPageRanks[3][0] = $masteryPages[$i]['masteries'][$ii]['rank'];
						$masteryPageRanks[3][1] = "";
						$masteryTotals[0] = $masteryTotals[0] + $masteryPages[$i]['masteries'][$ii]['rank'];
					} else if($masteryPages[$i]['masteries'][$ii]['id'] == 4151) {
						$masteryPageRanks[4][0] = $masteryPages[$i]['masteries'][$ii]['rank'];
						$masteryPageRanks[4][1] = "";
						$masteryTotals[0] = $masteryTotals[0] + $masteryPages[$i]['masteries'][$ii]['rank'];
					} else if($masteryPages[$i]['masteries'][$ii]['id'] == 4112) {
						$masteryPageRanks[5][0] = $masteryPages[$i]['masteries'][$ii]['rank'];
						$masteryPageRanks[5][1] = "";
						$masteryTotals[0] = $masteryTotals[0] + $masteryPages[$i]['masteries'][$ii]['rank'];
					} else if($masteryPages[$i]['masteries'][$ii]['id'] == 4122) {
						$masteryPageRanks[6][0] = $masteryPages[$i]['masteries'][$ii]['rank'];
						$masteryPageRanks[6][1] = "";
						$masteryTotals[0] = $masteryTotals[0] + $masteryPages[$i]['masteries'][$ii]['rank'];
					} else if($masteryPages[$i]['masteries'][$ii]['id'] == 4132) {
						$masteryPageRanks[7][0] = $masteryPages[$i]['masteries'][$ii]['rank'];
						$masteryPageRanks[7][1] = "";
						$masteryTotals[0] = $masteryTotals[0] + $masteryPages[$i]['masteries'][$ii]['rank'];
					} else if($masteryPages[$i]['masteries'][$ii]['id'] == 4142) {
						$masteryPageRanks[8][0] = $masteryPages[$i]['masteries'][$ii]['rank'];
						$masteryPageRanks[8][1] = "";
						$masteryTotals[0] = $masteryTotals[0] + $masteryPages[$i]['masteries'][$ii]['rank'];
					} else if($masteryPages[$i]['masteries'][$ii]['id'] == 4152) {
						$masteryPageRanks[9][0] = $masteryPages[$i]['masteries'][$ii]['rank'];
						$masteryPageRanks[9][1] = "";
						$masteryTotals[0] = $masteryTotals[0] + $masteryPages[$i]['masteries'][$ii]['rank'];
					} else if($masteryPages[$i]['masteries'][$ii]['id'] == 4162) {
						$masteryPageRanks[10][0] = $masteryPages[$i]['masteries'][$ii]['rank'];
						$masteryPageRanks[10][1] = "";
						$masteryTotals[0] = $masteryTotals[0] + $masteryPages[$i]['masteries'][$ii]['rank'];
					} else if($masteryPages[$i]['masteries'][$ii]['id'] == 4113) {
						$masteryPageRanks[11][0] = $masteryPages[$i]['masteries'][$ii]['rank'];
						$masteryPageRanks[11][1] = "";
						$masteryTotals[0] = $masteryTotals[0] + $masteryPages[$i]['masteries'][$ii]['rank'];
					} else if($masteryPages[$i]['masteries'][$ii]['id'] == 4123) {
						$masteryPageRanks[12][0] = $masteryPages[$i]['masteries'][$ii]['rank'];
						$masteryPageRanks[12][1] = "";
						$masteryTotals[0] = $masteryTotals[0] + $masteryPages[$i]['masteries'][$ii]['rank'];
					} else if($masteryPages[$i]['masteries'][$ii]['id'] == 4133) {
						$masteryPageRanks[13][0] = $masteryPages[$i]['masteries'][$ii]['rank'];
						$masteryPageRanks[13][1] = "";
						$masteryTotals[0] = $masteryTotals[0] + $masteryPages[$i]['masteries'][$ii]['rank'];
					} else if($masteryPages[$i]['masteries'][$ii]['id'] == 4143) {
						$masteryPageRanks[14][0] = $masteryPages[$i]['masteries'][$ii]['rank'];
						$masteryPageRanks[14][1] = "";
						$masteryTotals[0] = $masteryTotals[0] + $masteryPages[$i]['masteries'][$ii]['rank'];
					} else if($masteryPages[$i]['masteries'][$ii]['id'] == 4114) {
						$masteryPageRanks[15][0] = $masteryPages[$i]['masteries'][$ii]['rank'];
						$masteryPageRanks[15][1] = "";
						$masteryTotals[0] = $masteryTotals[0] + $masteryPages[$i]['masteries'][$ii]['rank'];
					} else if($masteryPages[$i]['masteries'][$ii]['id'] == 4124) {
						$masteryPageRanks[16][0] = $masteryPages[$i]['masteries'][$ii]['rank'];
						$masteryPageRanks[16][1] = "";
						$masteryTotals[0] = $masteryTotals[0] + $masteryPages[$i]['masteries'][$ii]['rank'];
					} else if($masteryPages[$i]['masteries'][$ii]['id'] == 4134) {
						$masteryPageRanks[17][0] = $masteryPages[$i]['masteries'][$ii]['rank'];
						$masteryPageRanks[17][1] = "";
						$masteryTotals[0] = $masteryTotals[0] + $masteryPages[$i]['masteries'][$ii]['rank'];
					} else if($masteryPages[$i]['masteries'][$ii]['id'] == 4144) {
						$masteryPageRanks[18][0] = $masteryPages[$i]['masteries'][$ii]['rank'];
						$masteryPageRanks[18][1] = "";
						$masteryTotals[0] = $masteryTotals[0] + $masteryPages[$i]['masteries'][$ii]['rank'];
					} else if($masteryPages[$i]['masteries'][$ii]['id'] == 4154) {
						$masteryPageRanks[19][0] = $masteryPages[$i]['masteries'][$ii]['rank'];
						$masteryPageRanks[19][1] = "";
						$masteryTotals[0] = $masteryTotals[0] + $masteryPages[$i]['masteries'][$ii]['rank'];
					}
					// End of Offensive Page
					 else if($masteryPages[$i]['masteries'][$ii]['id'] == 4211) {
						$masteryPageRanks[20][0] = $masteryPages[$i]['masteries'][$ii]['rank'];
						$masteryPageRanks[20][1] = "";
						$masteryTotals[1] = $masteryTotals[1] + $masteryPages[$i]['masteries'][$ii]['rank'];
					} else if($masteryPages[$i]['masteries'][$ii]['id'] == 4221) {
						$masteryPageRanks[21][0] = $masteryPages[$i]['masteries'][$ii]['rank'];
						$masteryPageRanks[21][1] = "";
						$masteryTotals[1] = $masteryTotals[1] + $masteryPages[$i]['masteries'][$ii]['rank'];
					} else if($masteryPages[$i]['masteries'][$ii]['id'] == 4231) {
						$masteryPageRanks[22][0] = $masteryPages[$i]['masteries'][$ii]['rank'];
						$masteryPageRanks[22][1] = "";
						$masteryTotals[1] = $masteryTotals[1] + $masteryPages[$i]['masteries'][$ii]['rank'];
					} else if($masteryPages[$i]['masteries'][$ii]['id'] == 4241) {
						$masteryPageRanks[23][0] = $masteryPages[$i]['masteries'][$ii]['rank'];
						$masteryPageRanks[23][1] = "";
						$masteryTotals[1] = $masteryTotals[1] + $masteryPages[$i]['masteries'][$ii]['rank'];
					} else if($masteryPages[$i]['masteries'][$ii]['id'] == 4251) {
						$masteryPageRanks[24][0] = $masteryPages[$i]['masteries'][$ii]['rank'];
						$masteryPageRanks[24][1] = "";
						$masteryTotals[1] = $masteryTotals[1] + $masteryPages[$i]['masteries'][$ii]['rank'];
					} else if($masteryPages[$i]['masteries'][$ii]['id'] == 4212) {
						$masteryPageRanks[25][0] = $masteryPages[$i]['masteries'][$ii]['rank'];
						$masteryPageRanks[25][1] = "";
						$masteryTotals[1] = $masteryTotals[1] + $masteryPages[$i]['masteries'][$ii]['rank'];
					} else if($masteryPages[$i]['masteries'][$ii]['id'] == 4222) {
						$masteryPageRanks[26][0] = $masteryPages[$i]['masteries'][$ii]['rank'];
						$masteryPageRanks[26][1] = "";
						$masteryTotals[1] = $masteryTotals[1] + $masteryPages[$i]['masteries'][$ii]['rank'];
					} else if($masteryPages[$i]['masteries'][$ii]['id'] == 4232) {
						$masteryPageRanks[27][0] = $masteryPages[$i]['masteries'][$ii]['rank'];
						$masteryPageRanks[27][1] = "";
						$masteryTotals[1] = $masteryTotals[1] + $masteryPages[$i]['masteries'][$ii]['rank'];
					} else if($masteryPages[$i]['masteries'][$ii]['id'] == 4242) {
						$masteryPageRanks[28][0] = $masteryPages[$i]['masteries'][$ii]['rank'];
						$masteryPageRanks[28][1] = "";
						$masteryTotals[1] = $masteryTotals[1] + $masteryPages[$i]['masteries'][$ii]['rank'];
					} else if($masteryPages[$i]['masteries'][$ii]['id'] == 4252) {
						$masteryPageRanks[29][0] = $masteryPages[$i]['masteries'][$ii]['rank'];
						$masteryPageRanks[29][1] = "";
						$masteryTotals[1] = $masteryTotals[1] + $masteryPages[$i]['masteries'][$ii]['rank'];
					} else if($masteryPages[$i]['masteries'][$ii]['id'] == 4262) {
						$masteryPageRanks[30][0] = $masteryPages[$i]['masteries'][$ii]['rank'];
						$masteryPageRanks[30][1] = "";
						$masteryTotals[1] = $masteryTotals[1] + $masteryPages[$i]['masteries'][$ii]['rank'];
					} else if($masteryPages[$i]['masteries'][$ii]['id'] == 4213) {
						$masteryPageRanks[31][0] = $masteryPages[$i]['masteries'][$ii]['rank'];
						$masteryPageRanks[31][1] = "";
						$masteryTotals[1] = $masteryTotals[1] + $masteryPages[$i]['masteries'][$ii]['rank'];
					} else if($masteryPages[$i]['masteries'][$ii]['id'] == 4233) {
						$masteryPageRanks[32][0] = $masteryPages[$i]['masteries'][$ii]['rank'];
						$masteryPageRanks[32][1] = "";
						$masteryTotals[1] = $masteryTotals[1] + $masteryPages[$i]['masteries'][$ii]['rank'];
					} else if($masteryPages[$i]['masteries'][$ii]['id'] == 4243) {
						$masteryPageRanks[33][0] = $masteryPages[$i]['masteries'][$ii]['rank'];
						$masteryPageRanks[33][1] = "";
						$masteryTotals[1] = $masteryTotals[1] + $masteryPages[$i]['masteries'][$ii]['rank'];
					} else if($masteryPages[$i]['masteries'][$ii]['id'] == 4253) {
						$masteryPageRanks[34][0] = $masteryPages[$i]['masteries'][$ii]['rank'];
						$masteryPageRanks[34][1] = "";
						$masteryTotals[1] = $masteryTotals[1] + $masteryPages[$i]['masteries'][$ii]['rank'];
					} else if($masteryPages[$i]['masteries'][$ii]['id'] == 4214) {
						$masteryPageRanks[35][0] = $masteryPages[$i]['masteries'][$ii]['rank'];
						$masteryPageRanks[35][1] = "";
						$masteryTotals[1] = $masteryTotals[1] + $masteryPages[$i]['masteries'][$ii]['rank'];
					} else if($masteryPages[$i]['masteries'][$ii]['id'] == 4224) {
						$masteryPageRanks[36][0] = $masteryPages[$i]['masteries'][$ii]['rank'];
						$masteryPageRanks[36][1] = "";
						$masteryTotals[1] = $masteryTotals[1] + $masteryPages[$i]['masteries'][$ii]['rank'];
					} else if($masteryPages[$i]['masteries'][$ii]['id'] == 4234) {
						$masteryPageRanks[37][0] = $masteryPages[$i]['masteries'][$ii]['rank'];
						$masteryPageRanks[37][1] = "";
						$masteryTotals[1] = $masteryTotals[1] + $masteryPages[$i]['masteries'][$ii]['rank'];
					} else if($masteryPages[$i]['masteries'][$ii]['id'] == 4244) {
						$masteryPageRanks[38][0] = $masteryPages[$i]['masteries'][$ii]['rank'];
						$masteryPageRanks[38][1] = "";
						$masteryTotals[1] = $masteryTotals[1] + $masteryPages[$i]['masteries'][$ii]['rank'];
					}
					// End of Defensive Page
					 else if($masteryPages[$i]['masteries'][$ii]['id'] == 4311) {
						$masteryPageRanks[39][0] = $masteryPages[$i]['masteries'][$ii]['rank'];
						$masteryPageRanks[39][1] = "";
						$masteryTotals[2] = $masteryTotals[2] + $masteryPages[$i]['masteries'][$ii]['rank'];
					} else if($masteryPages[$i]['masteries'][$ii]['id'] == 4331) {
						$masteryPageRanks[40][0] = $masteryPages[$i]['masteries'][$ii]['rank'];
						$masteryPageRanks[40][1] = "";
						$masteryTotals[2] = $masteryTotals[2] + $masteryPages[$i]['masteries'][$ii]['rank'];
					} else if($masteryPages[$i]['masteries'][$ii]['id'] == 4341) {
						$masteryPageRanks[41][0] = $masteryPages[$i]['masteries'][$ii]['rank'];
						$masteryPageRanks[41][1] = "";
						$masteryTotals[2] = $masteryTotals[2] + $masteryPages[$i]['masteries'][$ii]['rank'];
					} else if($masteryPages[$i]['masteries'][$ii]['id'] == 4312) {
						$masteryPageRanks[42][0] = $masteryPages[$i]['masteries'][$ii]['rank'];
						$masteryPageRanks[42][1] = "";
						$masteryTotals[2] = $masteryTotals[2] + $masteryPages[$i]['masteries'][$ii]['rank'];
					} else if($masteryPages[$i]['masteries'][$ii]['id'] == 4322) {
						$masteryPageRanks[43][0] = $masteryPages[$i]['masteries'][$ii]['rank'];
						$masteryPageRanks[43][1] = "";
						$masteryTotals[2] = $masteryTotals[2] + $masteryPages[$i]['masteries'][$ii]['rank'];
					} else if($masteryPages[$i]['masteries'][$ii]['id'] == 4332) {
						$masteryPageRanks[44][0] = $masteryPages[$i]['masteries'][$ii]['rank'];
						$masteryPageRanks[44][1] = "";
						$masteryTotals[2] = $masteryTotals[2] + $masteryPages[$i]['masteries'][$ii]['rank'];
					} else if($masteryPages[$i]['masteries'][$ii]['id'] == 4342) {
						$masteryPageRanks[45][0] = $masteryPages[$i]['masteries'][$ii]['rank'];
						$masteryPageRanks[45][1] = "";
						$masteryTotals[2] = $masteryTotals[2] + $masteryPages[$i]['masteries'][$ii]['rank'];
					} else if($masteryPages[$i]['masteries'][$ii]['id'] == 4352) {
						$masteryPageRanks[46][0] = $masteryPages[$i]['masteries'][$ii]['rank'];
						$masteryPageRanks[46][1] = "";
						$masteryTotals[2] = $masteryTotals[2] + $masteryPages[$i]['masteries'][$ii]['rank'];
					} else if($masteryPages[$i]['masteries'][$ii]['id'] == 4362) {
						$masteryPageRanks[47][0] = $masteryPages[$i]['masteries'][$ii]['rank'];
						$masteryPageRanks[47][1] = "";
						$masteryTotals[2] = $masteryTotals[2] + $masteryPages[$i]['masteries'][$ii]['rank'];
					} else if($masteryPages[$i]['masteries'][$ii]['id'] == 4313) {
						$masteryPageRanks[48][0] = $masteryPages[$i]['masteries'][$ii]['rank'];
						$masteryPageRanks[48][1] = "";
						$masteryTotals[2] = $masteryTotals[2] + $masteryPages[$i]['masteries'][$ii]['rank'];
					} else if($masteryPages[$i]['masteries'][$ii]['id'] == 4323) {
						$masteryPageRanks[49][0] = $masteryPages[$i]['masteries'][$ii]['rank'];
						$masteryPageRanks[49][1] = "";
						$masteryTotals[2] = $masteryTotals[2] + $masteryPages[$i]['masteries'][$ii]['rank'];
					} else if($masteryPages[$i]['masteries'][$ii]['id'] == 4333) {
						$masteryPageRanks[50][0] = $masteryPages[$i]['masteries'][$ii]['rank'];
						$masteryPageRanks[50][1] = "";
						$masteryTotals[2] = $masteryTotals[2] + $masteryPages[$i]['masteries'][$ii]['rank'];
					} else if($masteryPages[$i]['masteries'][$ii]['id'] == 4343) {
						$masteryPageRanks[51][0] = $masteryPages[$i]['masteries'][$ii]['rank'];
						$masteryPageRanks[51][1] = "";
						$masteryTotals[2] = $masteryTotals[2] + $masteryPages[$i]['masteries'][$ii]['rank'];
					} else if($masteryPages[$i]['masteries'][$ii]['id'] == 4353) {
						$masteryPageRanks[52][0] = $masteryPages[$i]['masteries'][$ii]['rank'];
						$masteryPageRanks[52][1] = "";
						$masteryTotals[2] = $masteryTotals[2] + $masteryPages[$i]['masteries'][$ii]['rank'];
					} else if($masteryPages[$i]['masteries'][$ii]['id'] == 4314) {
						$masteryPageRanks[53][0] = $masteryPages[$i]['masteries'][$ii]['rank'];
						$masteryPageRanks[53][1] = "";
						$masteryTotals[2] = $masteryTotals[2] + $masteryPages[$i]['masteries'][$ii]['rank'];
					} else if($masteryPages[$i]['masteries'][$ii]['id'] == 4324) {
						$masteryPageRanks[54][0] = $masteryPages[$i]['masteries'][$ii]['rank'];
						$masteryPageRanks[54][1] = "";
						$masteryTotals[2] = $masteryTotals[2] + $masteryPages[$i]['masteries'][$ii]['rank'];
					} else if($masteryPages[$i]['masteries'][$ii]['id'] == 4334) {
						$masteryPageRanks[55][0]= $masteryPages[$i]['masteries'][$ii]['rank'];
						$masteryPageRanks[55][1] = "";
						$masteryTotals[2] = $masteryTotals[2] + $masteryPages[$i]['masteries'][$ii]['rank'];
					} else if($masteryPages[$i]['masteries'][$ii]['id'] == 4344) {
						$masteryPageRanks[56][0] = $masteryPages[$i]['masteries'][$ii]['rank'];
						$masteryPageRanks[56][1] = "";
						$masteryTotals[2] = $masteryTotals[2] + $masteryPages[$i]['masteries'][$ii]['rank'];
					}
					// End of Utility Page
				}


			Print '<div id="mastery-page' . $i . '">';				

			Print '<div id="offense" class="first-col">
							<div class="mastery-padding-box">
								<div class="col">
									<div class="mastery-box '.$masteryPageRanks[0][1].'" style="background: url(https://ddragon.leagueoflegends.com/cdn/4.4.3/img/mastery/4111.png);">
										<div class="rank-box">'
										.$masteryPageRanks[0][0].'/1
										</div>
									</div>
									<div class="mastery-box '.$masteryPageRanks[1][1].'" style="background: url(https://ddragon.leagueoflegends.com/cdn/4.4.3/img/mastery/4121.png);">
										<div class="rank-box">'
										.$masteryPageRanks[1][0].'/1
										</div>
									</div>
									<div class="mastery-box '.$masteryPageRanks[2][1].'" style="background: url(https://ddragon.leagueoflegends.com/cdn/4.4.3/img/mastery/4131.png);">
										<div class="rank-box">
										'.$masteryPageRanks[2][0].'/1
										</div>
									</div>
									<div class="mastery-box '.$masteryPageRanks[3][1].'" style="background: url(https://ddragon.leagueoflegends.com/cdn/4.4.3/img/mastery/4141.png);">
										<div class="rank-box">
										'.$masteryPageRanks[3][0].'/1
										</div>
									</div>
									<div class="mastery-box '.$masteryPageRanks[4][1].'" style="background: url(https://ddragon.leagueoflegends.com/cdn/4.4.3/img/mastery/4151.png);">
										<div class="rank-box">
										'.$masteryPageRanks[4][0].'/1
										</div>
									</div>
								</div>
								<div class="col">
									<div class="mastery-box '.$masteryPageRanks[5][1].'" style="background: url(https://ddragon.leagueoflegends.com/cdn/4.4.3/img/mastery/4112.png);">
										<div class="rank-box">
										'.$masteryPageRanks[5][0].'/4
										</div>
									</div>
									<div class="mastery-box '.$masteryPageRanks[6][1].'" style="background: url(https://ddragon.leagueoflegends.com/cdn/4.4.3/img/mastery/4122.png);">
										<div class="rank-box">
										'.$masteryPageRanks[6][0].'/3
										</div>
									</div>
									<div class="mastery-box '.$masteryPageRanks[7][1].'" style="background: url(https://ddragon.leagueoflegends.com/cdn/4.4.3/img/mastery/4132.png);">
										<div class="rank-box">
										'.$masteryPageRanks[7][0].'/1
										</div>
									</div>
									<div class="mastery-box '.$masteryPageRanks[8][1].'" style="background: url(https://ddragon.leagueoflegends.com/cdn/4.4.3/img/mastery/4142.png);">
										<div class="rank-box">
										'.$masteryPageRanks[8][0].'/3
										</div>
									</div>
									<div class="mastery-box '.$masteryPageRanks[9][1].'" style="background: url(https://ddragon.leagueoflegends.com/cdn/4.4.3/img/mastery/4152.png);">
										<div class="rank-box">
										'.$masteryPageRanks[9][0].'/3
										</div>
									</div>
									<div class="mastery-box '.$masteryPageRanks[10][1].'" style="background: url(https://ddragon.leagueoflegends.com/cdn/4.4.3/img/mastery/4162.png);">
										<div class="rank-box">
										'.$masteryPageRanks[10][0].'/1
										</div>
									</div>
								</div>
								<div class="col">
									<div class="mastery-box '.$masteryPageRanks[11][1].'" style="background: url(https://ddragon.leagueoflegends.com/cdn/4.4.3/img/mastery/4113.png);">
										<div class="rank-box">
										'.$masteryPageRanks[11][0].'/4
										</div>
									</div>
									<div class="mastery-box '.$masteryPageRanks[12][1].'" style="background: url(https://ddragon.leagueoflegends.com/cdn/4.4.3/img/mastery/4123.png);">
										<div class="rank-box">
										'.$masteryPageRanks[12][0].'/3
										</div>
									</div>
									<div class="mastery-box '.$masteryPageRanks[13][1].'" style="background: url(https://ddragon.leagueoflegends.com/cdn/4.4.3/img/mastery/4133.png);">
										<div class="rank-box">
										'.$masteryPageRanks[13][0].'/1
										</div>
									</div>
									<div class="mastery-box '.$masteryPageRanks[14][1].'" style="background: url(https://ddragon.leagueoflegends.com/cdn/4.4.3/img/mastery/4143.png);">
										<div class="rank-box">
										'.$masteryPageRanks[14][0].'/3
										</div>
									</div>
								</div>
								<div class="col">
									<div class="mastery-box '.$masteryPageRanks[15][1].'" style="background: url(https://ddragon.leagueoflegends.com/cdn/4.4.3/img/mastery/4114.png);">
										<div class="rank-box">
										'.$masteryPageRanks[15][0].'/1
										</div>
									</div>
									<div class="mastery-box '.$masteryPageRanks[16][1].'" style="background: url(https://ddragon.leagueoflegends.com/cdn/4.4.3/img/mastery/4124.png);">
										<div class="rank-box">
										'.$masteryPageRanks[16][0].'/1
										</div>
									</div>
									<div class="mastery-box '.$masteryPageRanks[17][1].'" style="background: url(https://ddragon.leagueoflegends.com/cdn/4.4.3/img/mastery/4134.png);">
										<div class="rank-box">
										'.$masteryPageRanks[17][0].'/3
										</div>
									</div>
									<div class="mastery-box '.$masteryPageRanks[18][1].'" style="background: url(https://ddragon.leagueoflegends.com/cdn/4.4.3/img/mastery/4144.png);">
										<div class="rank-box">
										'.$masteryPageRanks[18][0].'/1
										</div>
									</div>
									<div class="mastery-box '.$masteryPageRanks[19][1].'" style="background: url(https://ddragon.leagueoflegends.com/cdn/4.4.3/img/mastery/4154.png);">
										<div class="rank-box">
										'.$masteryPageRanks[19][0].'/1
										</div>
									</div>
								</div>
								<div class="mastery-total">
									<h2>OFFENSE: ' . $masteryTotals[0] . '</h2>
								</div>
							</div>
						</div>
						<div id="defense" class="second-col">
							<div class="mastery-padding-box2">
								<div class="col">
									<div class="mastery-box '.$masteryPageRanks[20][1].'" style="background: url(https://ddragon.leagueoflegends.com/cdn/4.4.3/img/mastery/4211.png);">
										<div class="rank-box">
										'.$masteryPageRanks[20][0].'/2
										</div>
									</div>
									<div class="mastery-box '.$masteryPageRanks[21][1].'" style="background: url(https://ddragon.leagueoflegends.com/cdn/4.4.3/img/mastery/4221.png);">
										<div class="rank-box">
										'.$masteryPageRanks[21][0].'/1
										</div>
									</div>
									<div class="mastery-box '.$masteryPageRanks[22][1].'" style="background: url(https://ddragon.leagueoflegends.com/cdn/4.4.3/img/mastery/4231.png);">
										<div class="rank-box">
										'.$masteryPageRanks[22][0].'/1
										</div>
									</div>
									<div class="mastery-box '.$masteryPageRanks[23][1].'" style="background: url(https://ddragon.leagueoflegends.com/cdn/4.4.3/img/mastery/4241.png);">
										<div class="rank-box">
										'.$masteryPageRanks[23][0].'/3
										</div>
									</div>
									<div class="mastery-box '.$masteryPageRanks[24][1].'" style="background: url(https://ddragon.leagueoflegends.com/cdn/4.4.3/img/mastery/4251.png);">
										<div class="rank-box">
										'.$masteryPageRanks[24][0].'/1
										</div>
									</div>
								</div>
								<div class="col">
									<div class="mastery-box '.$masteryPageRanks[25][1].'" style="background: url(https://ddragon.leagueoflegends.com/cdn/4.4.3/img/mastery/4212.png);">
										<div class="rank-box">
										'.$masteryPageRanks[25][0].'/2
										</div>
									</div>
									<div class="mastery-box '.$masteryPageRanks[26][1].'" style="background: url(https://ddragon.leagueoflegends.com/cdn/4.4.3/img/mastery/4222.png);">
										<div class="rank-box">
										'.$masteryPageRanks[26][0].'/3
										</div>
									</div>
									<div class="mastery-box '.$masteryPageRanks[27][1].'" style="background: url(https://ddragon.leagueoflegends.com/cdn/4.4.3/img/mastery/4232.png);">
										<div class="rank-box">
										'.$masteryPageRanks[27][0].'/1
										</div>
									</div>
									<div class="mastery-box '.$masteryPageRanks[28][1].'" style="background: url(https://ddragon.leagueoflegends.com/cdn/4.4.3/img/mastery/4242.png);">
										<div class="rank-box">
										'.$masteryPageRanks[28][0].'/1
										</div>
									</div>
									<div class="mastery-box '.$masteryPageRanks[29][1].'" style="background: url(https://ddragon.leagueoflegends.com/cdn/4.4.3/img/mastery/4252.png);">
										<div class="rank-box">
										'.$masteryPageRanks[29][0].'/4
										</div>
									</div>
									<div class="mastery-box '.$masteryPageRanks[30][1].'" style="background: url(https://ddragon.leagueoflegends.com/cdn/4.4.3/img/mastery/4262.png);">
										<div class="rank-box">
										'.$masteryPageRanks[30][0].'/1
										</div>
									</div>
								</div>
								<div class="col">
									<div class="mastery-box '.$masteryPageRanks[31][1].'" style="background: url(https://ddragon.leagueoflegends.com/cdn/4.4.3/img/mastery/4213.png);">
										<div class="rank-box">
										'.$masteryPageRanks[31][0].'/2
										</div>
									</div>
									<div class="empty-mastery-box">
									</div>
									<div class="mastery-box '.$masteryPageRanks[32][1].'" style="background: url(https://ddragon.leagueoflegends.com/cdn/4.4.3/img/mastery/4233.png);">
										<div class="rank-box">
										'.$masteryPageRanks[32][0].'/3
										</div>
									</div>
									<div class="mastery-box '.$masteryPageRanks[33][1].'" style="background: url(https://ddragon.leagueoflegends.com/cdn/4.4.3/img/mastery/4243.png);">
										<div class="rank-box">
										'.$masteryPageRanks[33][0].'/1
										</div>
									</div>
									<div class="mastery-box '.$masteryPageRanks[34][1].'" style="background: url(https://ddragon.leagueoflegends.com/cdn/4.4.3/img/mastery/4253.png);">
										<div class="rank-box">
										'.$masteryPageRanks[34][0].'/1
										</div>
									</div>
								</div>
								<div class="col">
									<div class="mastery-box '.$masteryPageRanks[35][1].'" style="background: url(https://ddragon.leagueoflegends.com/cdn/4.4.3/img/mastery/4214.png);">
										<div class="rank-box">
										'.$masteryPageRanks[35][0].'/2
										</div>
									</div>
									<div class="mastery-box '.$masteryPageRanks[36][1].'" style="background: url(https://ddragon.leagueoflegends.com/cdn/4.4.3/img/mastery/4224.png);">
										<div class="rank-box">
										'.$masteryPageRanks[36][0].'/1
										</div>
									</div>
									<div class="mastery-box '.$masteryPageRanks[37][1].'" style="background: url(https://ddragon.leagueoflegends.com/cdn/4.4.3/img/mastery/4234.png);">
										<div class="rank-box">
										'.$masteryPageRanks[37][0].'/3
										</div>
									</div>
									<div class="mastery-box '.$masteryPageRanks[38][1].'" style="background: url(https://ddragon.leagueoflegends.com/cdn/4.4.3/img/mastery/4244.png);">
										<div class="rank-box">
										'.$masteryPageRanks[38][0].'/1
										</div>
									</div>
								</div>
								<div class="mastery-total">
									<h2>DEFENSE: ' . $masteryTotals[1] . '</h2>
								</div>
							</div>
						</div>
						<div id="utility" class="third-col">
							<div class="mastery-padding-box">
								<div class="col">
									<div class="mastery-box '.$masteryPageRanks[39][1].'" style="background: url(https://ddragon.leagueoflegends.com/cdn/4.4.3/img/mastery/4311.png);">
										<div class="rank-box">
										'.$masteryPageRanks[39][0].'/1
										</div>
									</div>
									<div class="empty-mastery-box">
									</div>
									<div class="mastery-box '.$masteryPageRanks[40][1].'" style="background: url(https://ddragon.leagueoflegends.com/cdn/4.4.3/img/mastery/4331.png);">
										<div class="rank-box">
										'.$masteryPageRanks[40][0].'/3
										</div>
									</div>
									<div class="mastery-box '.$masteryPageRanks[41][1].'" style="background: url(https://ddragon.leagueoflegends.com/cdn/4.4.3/img/mastery/4341.png);">
										<div class="rank-box">
										'.$masteryPageRanks[41][0].'/1
										</div>
									</div>
								</div>
								<div class="col">
									<div class="mastery-box '.$masteryPageRanks[42][1].'" style="background: url(https://ddragon.leagueoflegends.com/cdn/4.4.3/img/mastery/4312.png);">
										<div class="rank-box">
										'.$masteryPageRanks[42][0].'/3
										</div>
									</div>
									<div class="mastery-box '.$masteryPageRanks[43][1].'" style="background: url(https://ddragon.leagueoflegends.com/cdn/4.4.3/img/mastery/4322.png);">
										<div class="rank-box">
										'.$masteryPageRanks[43][0].'/3
										</div>
									</div>
									<div class="mastery-box '.$masteryPageRanks[44][1].'" style="background: url(https://ddragon.leagueoflegends.com/cdn/4.4.3/img/mastery/4332.png);">
										<div class="rank-box">
										'.$masteryPageRanks[44][0].'/1
										</div>
									</div>
									<div class="mastery-box '.$masteryPageRanks[45][1].'" style="background: url(https://ddragon.leagueoflegends.com/cdn/4.4.3/img/mastery/4342.png);">
										<div class="rank-box">
										'.$masteryPageRanks[45][0].'/1
										</div>
									</div>
									<div class="mastery-box '.$masteryPageRanks[46][1].'" style="background: url(https://ddragon.leagueoflegends.com/cdn/4.4.3/img/mastery/4352.png);">
										<div class="rank-box">
										'.$masteryPageRanks[46][0].'/1
										</div>
									</div>
									<div class="mastery-box '.$masteryPageRanks[47][1].'" style="background: url(https://ddragon.leagueoflegends.com/cdn/4.4.3/img/mastery/4362.png);">
										<div class="rank-box">
										'.$masteryPageRanks[47][0].'/1
										</div>
									</div>
								</div>
								<div class="col">
									<div class="mastery-box '.$masteryPageRanks[48][1].'" style="background: url(https://ddragon.leagueoflegends.com/cdn/4.4.3/img/mastery/4313.png);">
										<div class="rank-box">
										'.$masteryPageRanks[48][0].'/3
										</div>
									</div>
									<div class="mastery-box '.$masteryPageRanks[49][1].'" style="background: url(https://ddragon.leagueoflegends.com/cdn/4.4.3/img/mastery/4323.png);">
										<div class="rank-box">
										'.$masteryPageRanks[49][0].'/1
										</div>
									</div>
									<div class="mastery-box '.$masteryPageRanks[50][1].'" style="background: url(https://ddragon.leagueoflegends.com/cdn/4.4.3/img/mastery/4333.png);">
										<div class="rank-box">
										'.$masteryPageRanks[50][0].'/3
										</div>
									</div>
									<div class="mastery-box '.$masteryPageRanks[51][1].'" style="background: url(https://ddragon.leagueoflegends.com/cdn/4.4.3/img/mastery/4343.png);">
										<div class="rank-box">
										'.$masteryPageRanks[51][0].'/3
										</div>
									</div>
									<div class="mastery-box '.$masteryPageRanks[52][1].'" style="background: url(https://ddragon.leagueoflegends.com/cdn/4.4.3/img/mastery/4353.png);">
										<div class="rank-box">
										'.$masteryPageRanks[52][0].'/3
										</div>
									</div>
								</div>
								<div class="col">
									<div class="mastery-box '.$masteryPageRanks[53][1].'" style="background: url(https://ddragon.leagueoflegends.com/cdn/4.4.3/img/mastery/4314.png);">
										<div class="rank-box">
										'.$masteryPageRanks[53][0].'/1
										</div>
									</div>
									<div class="mastery-box '.$masteryPageRanks[54][1].'" style="background: url(https://ddragon.leagueoflegends.com/cdn/4.4.3/img/mastery/4324.png);">
										<div class="rank-box">
										'.$masteryPageRanks[54][0].'/1
										</div>
									</div>
									<div class="mastery-box '.$masteryPageRanks[55][1].'" style="background: url(https://ddragon.leagueoflegends.com/cdn/4.4.3/img/mastery/4334.png);">
										<div class="rank-box">
										'.$masteryPageRanks[55][0].'/1
										</div>
									</div>
									<div class="mastery-box '.$masteryPageRanks[56][1].'" style="background: url(https://ddragon.leagueoflegends.com/cdn/4.4.3/img/mastery/4344.png);">
										<div class="rank-box">
										'.$masteryPageRanks[56][0].'/2
										</div>
									</div>
								</div>
								<div class="mastery-total">
									<h2>UTILITY: ' . $masteryTotals[2] . '</h2>
								</div>
							</div>
						</div>
						</div>';
					}

			Print "</div></div></div>
			<div id=\"runes\"><div class=\"row\">";
			/*--------------------------------
			---------------Runes--------------
			--------------------------------*/

			// Get player runes info
			$url = 'https://' . $server . '.api.pvp.net/api/lol/' . $server . '/v1.4/summoner/' . $summonerInfo[$summoner]['id'] .'/runes?api_key=' . $apiKey;	     
			$numOfRequests++;
			$content = file_get_contents($url);
			$runes = json_decode($content, true);
			$runePages = $runes[$summonerInfo[$summoner]['id']]['pages'];

			$url = 'https://ddragon.leagueoflegends.com/cdn/4.18.1/data/en_US/rune.json';
			$content = file_get_contents($url);
			$runeInfo = json_decode($content, true);

			for($i = 0; $i < sizeof($runePages); $i++) {
				if($i == 0) {
					Print '<input type="radio" name="rune-tabs" id="rune' . $i . '" checked="true"></input>';
				} else {
					Print '<input type="radio" name="rune-tabs" id="rune' . $i . '"></input>';
				}
			}

			Print '	<h2>Runes</h2>
					<div id="rune-pages"><ul>';

			for($i = 0; $i < sizeof($runePages); $i++) {
				// need to check this
				Print ' <li><label id="rune-tab' . $i .'" for="rune' . $i . '">' . $runePages[$i]['name'] . '</label></li>';					
			}	

			$playerRuneInfo = array();
			for($i = 0; $i < sizeof($runePages); $i++) {
				$tempPlayerRuneInfo = array();
				for($ii = 0; $ii < 30; $ii++) {
					array_push($tempPlayerRuneInfo, array($runePages[$i]['slots'][$ii]['runeSlotId'], $runePages[$i]['slots'][$ii]['runeId'], $runeInfo['data'][$runePages[$i]['slots'][$ii]['runeId']]['image']['full'], $runeInfo['data'][$runePages[$i]['slots'][$ii]['runeId']]['name'], $runeInfo['data'][$runePages[$i]['slots'][$ii]['runeId']]['description']));	
				}
				array_push($playerRuneInfo, $tempPlayerRuneInfo);
			}

			$orderedRuneInfo = array();

			for($i = 0; $i < sizeof($runePages); $i++) {
				$tempOrderedRuneInfo = array(array(), array(), array(), array(), array(), array(), array(), array(), array(), array(),
					array(), array(), array(), array(), array(), array(), array(), array(), array(), array(),
					array(), array(), array(), array(), array(), array(), array(), array(), array(), array());
				for($ii = 0; $ii < 30; $ii++) {
					if($runePages[$i]['slots'][$ii]['runeSlotId'] == 1) {
						$tempOrderedRuneInfo[0] = array($runePages[$i]['slots'][$ii]['runeSlotId'], $runePages[$i]['slots'][$ii]['runeId'], $runeInfo['data'][$runePages[$i]['slots'][$ii]['runeId']]['image']['full'], $runeInfo['data'][$runePages[$i]['slots'][$ii]['runeId']]['name'], $runeInfo['data'][$runePages[$i]['slots'][$ii]['runeId']]['description']);	
					} else if($runePages[$i]['slots'][$ii]['runeSlotId'] == 2) {
						$tempOrderedRuneInfo[1] = array($runePages[$i]['slots'][$ii]['runeSlotId'], $runePages[$i]['slots'][$ii]['runeId'], $runeInfo['data'][$runePages[$i]['slots'][$ii]['runeId']]['image']['full'], $runeInfo['data'][$runePages[$i]['slots'][$ii]['runeId']]['name'], $runeInfo['data'][$runePages[$i]['slots'][$ii]['runeId']]['description']);	
					} else if($runePages[$i]['slots'][$ii]['runeSlotId'] == 3) {
						$tempOrderedRuneInfo[2] = array($runePages[$i]['slots'][$ii]['runeSlotId'], $runePages[$i]['slots'][$ii]['runeId'], $runeInfo['data'][$runePages[$i]['slots'][$ii]['runeId']]['image']['full'], $runeInfo['data'][$runePages[$i]['slots'][$ii]['runeId']]['name'], $runeInfo['data'][$runePages[$i]['slots'][$ii]['runeId']]['description']);	
					} else if($runePages[$i]['slots'][$ii]['runeSlotId'] == 4) {
						$tempOrderedRuneInfo[3] = array($runePages[$i]['slots'][$ii]['runeSlotId'], $runePages[$i]['slots'][$ii]['runeId'], $runeInfo['data'][$runePages[$i]['slots'][$ii]['runeId']]['image']['full'], $runeInfo['data'][$runePages[$i]['slots'][$ii]['runeId']]['name'], $runeInfo['data'][$runePages[$i]['slots'][$ii]['runeId']]['description']);	
					} else if($runePages[$i]['slots'][$ii]['runeSlotId'] == 5) {
						$tempOrderedRuneInfo[4] = array($runePages[$i]['slots'][$ii]['runeSlotId'], $runePages[$i]['slots'][$ii]['runeId'], $runeInfo['data'][$runePages[$i]['slots'][$ii]['runeId']]['image']['full'], $runeInfo['data'][$runePages[$i]['slots'][$ii]['runeId']]['name'], $runeInfo['data'][$runePages[$i]['slots'][$ii]['runeId']]['description']);	
					} else if($runePages[$i]['slots'][$ii]['runeSlotId'] == 6) {
						$tempOrderedRuneInfo[5] = array($runePages[$i]['slots'][$ii]['runeSlotId'], $runePages[$i]['slots'][$ii]['runeId'], $runeInfo['data'][$runePages[$i]['slots'][$ii]['runeId']]['image']['full'], $runeInfo['data'][$runePages[$i]['slots'][$ii]['runeId']]['name'], $runeInfo['data'][$runePages[$i]['slots'][$ii]['runeId']]['description']);	
					} else if($runePages[$i]['slots'][$ii]['runeSlotId'] == 7) {
						$tempOrderedRuneInfo[6] = array($runePages[$i]['slots'][$ii]['runeSlotId'], $runePages[$i]['slots'][$ii]['runeId'], $runeInfo['data'][$runePages[$i]['slots'][$ii]['runeId']]['image']['full'], $runeInfo['data'][$runePages[$i]['slots'][$ii]['runeId']]['name'], $runeInfo['data'][$runePages[$i]['slots'][$ii]['runeId']]['description']);	
					} else if($runePages[$i]['slots'][$ii]['runeSlotId'] == 8) {
						$tempOrderedRuneInfo[7] = array($runePages[$i]['slots'][$ii]['runeSlotId'], $runePages[$i]['slots'][$ii]['runeId'], $runeInfo['data'][$runePages[$i]['slots'][$ii]['runeId']]['image']['full'], $runeInfo['data'][$runePages[$i]['slots'][$ii]['runeId']]['name'], $runeInfo['data'][$runePages[$i]['slots'][$ii]['runeId']]['description']);	
					} else if($runePages[$i]['slots'][$ii]['runeSlotId'] == 9) {
						$tempOrderedRuneInfo[8] = array($runePages[$i]['slots'][$ii]['runeSlotId'], $runePages[$i]['slots'][$ii]['runeId'], $runeInfo['data'][$runePages[$i]['slots'][$ii]['runeId']]['image']['full'], $runeInfo['data'][$runePages[$i]['slots'][$ii]['runeId']]['name'], $runeInfo['data'][$runePages[$i]['slots'][$ii]['runeId']]['description']);	
					} else if($runePages[$i]['slots'][$ii]['runeSlotId'] == 10) {
						$tempOrderedRuneInfo[9] = array($runePages[$i]['slots'][$ii]['runeSlotId'], $runePages[$i]['slots'][$ii]['runeId'], $runeInfo['data'][$runePages[$i]['slots'][$ii]['runeId']]['image']['full'], $runeInfo['data'][$runePages[$i]['slots'][$ii]['runeId']]['name'], $runeInfo['data'][$runePages[$i]['slots'][$ii]['runeId']]['description']);	
					} else if($runePages[$i]['slots'][$ii]['runeSlotId'] == 11) {
						$tempOrderedRuneInfo[10] = array($runePages[$i]['slots'][$ii]['runeSlotId'], $runePages[$i]['slots'][$ii]['runeId'], $runeInfo['data'][$runePages[$i]['slots'][$ii]['runeId']]['image']['full'], $runeInfo['data'][$runePages[$i]['slots'][$ii]['runeId']]['name'], $runeInfo['data'][$runePages[$i]['slots'][$ii]['runeId']]['description']);	
					} else if($runePages[$i]['slots'][$ii]['runeSlotId'] == 12) {
						$tempOrderedRuneInfo[11] = array($runePages[$i]['slots'][$ii]['runeSlotId'], $runePages[$i]['slots'][$ii]['runeId'], $runeInfo['data'][$runePages[$i]['slots'][$ii]['runeId']]['image']['full'], $runeInfo['data'][$runePages[$i]['slots'][$ii]['runeId']]['name'], $runeInfo['data'][$runePages[$i]['slots'][$ii]['runeId']]['description']);	
					} else if($runePages[$i]['slots'][$ii]['runeSlotId'] == 13) {
						$tempOrderedRuneInfo[12] = array($runePages[$i]['slots'][$ii]['runeSlotId'], $runePages[$i]['slots'][$ii]['runeId'], $runeInfo['data'][$runePages[$i]['slots'][$ii]['runeId']]['image']['full'], $runeInfo['data'][$runePages[$i]['slots'][$ii]['runeId']]['name'], $runeInfo['data'][$runePages[$i]['slots'][$ii]['runeId']]['description']);	
					} else if($runePages[$i]['slots'][$ii]['runeSlotId'] == 14) {
						$tempOrderedRuneInfo[13] = array($runePages[$i]['slots'][$ii]['runeSlotId'], $runePages[$i]['slots'][$ii]['runeId'], $runeInfo['data'][$runePages[$i]['slots'][$ii]['runeId']]['image']['full'], $runeInfo['data'][$runePages[$i]['slots'][$ii]['runeId']]['name'], $runeInfo['data'][$runePages[$i]['slots'][$ii]['runeId']]['description']);	
					} else if($runePages[$i]['slots'][$ii]['runeSlotId'] == 15) {
						$tempOrderedRuneInfo[14] = array($runePages[$i]['slots'][$ii]['runeSlotId'], $runePages[$i]['slots'][$ii]['runeId'], $runeInfo['data'][$runePages[$i]['slots'][$ii]['runeId']]['image']['full'], $runeInfo['data'][$runePages[$i]['slots'][$ii]['runeId']]['name'], $runeInfo['data'][$runePages[$i]['slots'][$ii]['runeId']]['description']);	
					} else if($runePages[$i]['slots'][$ii]['runeSlotId'] == 16) {
						$tempOrderedRuneInfo[15] = array($runePages[$i]['slots'][$ii]['runeSlotId'], $runePages[$i]['slots'][$ii]['runeId'], $runeInfo['data'][$runePages[$i]['slots'][$ii]['runeId']]['image']['full'], $runeInfo['data'][$runePages[$i]['slots'][$ii]['runeId']]['name'], $runeInfo['data'][$runePages[$i]['slots'][$ii]['runeId']]['description']);	
					} else if($runePages[$i]['slots'][$ii]['runeSlotId'] == 17) {
						$tempOrderedRuneInfo[16] = array($runePages[$i]['slots'][$ii]['runeSlotId'], $runePages[$i]['slots'][$ii]['runeId'], $runeInfo['data'][$runePages[$i]['slots'][$ii]['runeId']]['image']['full'], $runeInfo['data'][$runePages[$i]['slots'][$ii]['runeId']]['name'], $runeInfo['data'][$runePages[$i]['slots'][$ii]['runeId']]['description']);	
					} else if($runePages[$i]['slots'][$ii]['runeSlotId'] == 18) {
						$tempOrderedRuneInfo[17] = array($runePages[$i]['slots'][$ii]['runeSlotId'], $runePages[$i]['slots'][$ii]['runeId'], $runeInfo['data'][$runePages[$i]['slots'][$ii]['runeId']]['image']['full'], $runeInfo['data'][$runePages[$i]['slots'][$ii]['runeId']]['name'], $runeInfo['data'][$runePages[$i]['slots'][$ii]['runeId']]['description']);	
					} else if($runePages[$i]['slots'][$ii]['runeSlotId'] == 19) {
						$tempOrderedRuneInfo[18] = array($runePages[$i]['slots'][$ii]['runeSlotId'], $runePages[$i]['slots'][$ii]['runeId'], $runeInfo['data'][$runePages[$i]['slots'][$ii]['runeId']]['image']['full'], $runeInfo['data'][$runePages[$i]['slots'][$ii]['runeId']]['name'], $runeInfo['data'][$runePages[$i]['slots'][$ii]['runeId']]['description']);	
					} else if($runePages[$i]['slots'][$ii]['runeSlotId'] == 20) {
						$tempOrderedRuneInfo[19] = array($runePages[$i]['slots'][$ii]['runeSlotId'], $runePages[$i]['slots'][$ii]['runeId'], $runeInfo['data'][$runePages[$i]['slots'][$ii]['runeId']]['image']['full'], $runeInfo['data'][$runePages[$i]['slots'][$ii]['runeId']]['name'], $runeInfo['data'][$runePages[$i]['slots'][$ii]['runeId']]['description']);	
					} else if($runePages[$i]['slots'][$ii]['runeSlotId'] == 21) {
						$tempOrderedRuneInfo[20] = array($runePages[$i]['slots'][$ii]['runeSlotId'], $runePages[$i]['slots'][$ii]['runeId'], $runeInfo['data'][$runePages[$i]['slots'][$ii]['runeId']]['image']['full'], $runeInfo['data'][$runePages[$i]['slots'][$ii]['runeId']]['name'], $runeInfo['data'][$runePages[$i]['slots'][$ii]['runeId']]['description']);	
					} else if($runePages[$i]['slots'][$ii]['runeSlotId'] == 22) {
						$tempOrderedRuneInfo[21] = array($runePages[$i]['slots'][$ii]['runeSlotId'], $runePages[$i]['slots'][$ii]['runeId'], $runeInfo['data'][$runePages[$i]['slots'][$ii]['runeId']]['image']['full'], $runeInfo['data'][$runePages[$i]['slots'][$ii]['runeId']]['name'], $runeInfo['data'][$runePages[$i]['slots'][$ii]['runeId']]['description']);	
					} else if($runePages[$i]['slots'][$ii]['runeSlotId'] == 23) {
						$tempOrderedRuneInfo[22] = array($runePages[$i]['slots'][$ii]['runeSlotId'], $runePages[$i]['slots'][$ii]['runeId'], $runeInfo['data'][$runePages[$i]['slots'][$ii]['runeId']]['image']['full'], $runeInfo['data'][$runePages[$i]['slots'][$ii]['runeId']]['name'], $runeInfo['data'][$runePages[$i]['slots'][$ii]['runeId']]['description']);	
					} else if($runePages[$i]['slots'][$ii]['runeSlotId'] == 24) {
						$tempOrderedRuneInfo[23] = array($runePages[$i]['slots'][$ii]['runeSlotId'], $runePages[$i]['slots'][$ii]['runeId'], $runeInfo['data'][$runePages[$i]['slots'][$ii]['runeId']]['image']['full'], $runeInfo['data'][$runePages[$i]['slots'][$ii]['runeId']]['name'], $runeInfo['data'][$runePages[$i]['slots'][$ii]['runeId']]['description']);	
					} else if($runePages[$i]['slots'][$ii]['runeSlotId'] == 25) {
						$tempOrderedRuneInfo[24] = array($runePages[$i]['slots'][$ii]['runeSlotId'], $runePages[$i]['slots'][$ii]['runeId'], $runeInfo['data'][$runePages[$i]['slots'][$ii]['runeId']]['image']['full'], $runeInfo['data'][$runePages[$i]['slots'][$ii]['runeId']]['name'], $runeInfo['data'][$runePages[$i]['slots'][$ii]['runeId']]['description']);	
					} else if($runePages[$i]['slots'][$ii]['runeSlotId'] == 26) {
						$tempOrderedRuneInfo[25] = array($runePages[$i]['slots'][$ii]['runeSlotId'], $runePages[$i]['slots'][$ii]['runeId'], $runeInfo['data'][$runePages[$i]['slots'][$ii]['runeId']]['image']['full'], $runeInfo['data'][$runePages[$i]['slots'][$ii]['runeId']]['name'], $runeInfo['data'][$runePages[$i]['slots'][$ii]['runeId']]['description']);	
					} else if($runePages[$i]['slots'][$ii]['runeSlotId'] == 27) {
						$tempOrderedRuneInfo[26] = array($runePages[$i]['slots'][$ii]['runeSlotId'], $runePages[$i]['slots'][$ii]['runeId'], $runeInfo['data'][$runePages[$i]['slots'][$ii]['runeId']]['image']['full'], $runeInfo['data'][$runePages[$i]['slots'][$ii]['runeId']]['name'], $runeInfo['data'][$runePages[$i]['slots'][$ii]['runeId']]['description']);	
					} else if($runePages[$i]['slots'][$ii]['runeSlotId'] == 28) {
						$tempOrderedRuneInfo[27] = array($runePages[$i]['slots'][$ii]['runeSlotId'], $runePages[$i]['slots'][$ii]['runeId'], $runeInfo['data'][$runePages[$i]['slots'][$ii]['runeId']]['image']['full'], $runeInfo['data'][$runePages[$i]['slots'][$ii]['runeId']]['name'], $runeInfo['data'][$runePages[$i]['slots'][$ii]['runeId']]['description']);	
					} else if($runePages[$i]['slots'][$ii]['runeSlotId'] == 29) {
						$tempOrderedRuneInfo[28] = array($runePages[$i]['slots'][$ii]['runeSlotId'], $runePages[$i]['slots'][$ii]['runeId'], $runeInfo['data'][$runePages[$i]['slots'][$ii]['runeId']]['image']['full'], $runeInfo['data'][$runePages[$i]['slots'][$ii]['runeId']]['name'], $runeInfo['data'][$runePages[$i]['slots'][$ii]['runeId']]['description']);	
					} else if($runePages[$i]['slots'][$ii]['runeSlotId'] == 30) {
						$tempOrderedRuneInfo[29] = array($runePages[$i]['slots'][$ii]['runeSlotId'], $runePages[$i]['slots'][$ii]['runeId'], $runeInfo['data'][$runePages[$i]['slots'][$ii]['runeId']]['image']['full'], $runeInfo['data'][$runePages[$i]['slots'][$ii]['runeId']]['name'], $runeInfo['data'][$runePages[$i]['slots'][$ii]['runeId']]['description']);	
					} else if($ii >= 0 && $ii < 9) {
						$tempOrderedRuneInfo[$ii] = array("", "", "mark_blank.png", "", "");
					} else if($ii >= 9 && $ii < 18) {
						$tempOrderedRuneInfo[$ii] = array("", "", "seal_blank.png", "", "");
					} else if($ii >= 18 && $ii < 27) {
						$tempOrderedRuneInfo[$ii] = array("", "", "glyph_blank.png", "", "");
					} else {
						$tempOrderedRuneInfo[$ii] = array("", "", "quintessence_blank.png", "", "");
					}
				}
				array_push($orderedRuneInfo, $tempOrderedRuneInfo);
			}

			$orderedRuneStatNames = array("FlatHPPoolMod", "rFlatHPModPerLevel", "FlatMPPoolMod", "rFlatMPModPerLevel", "PercentHPPoolMod", 
				"PercentMPPoolMod", "FlatHPRegenMod", "rFlatHPRegenModPerLevel", "PercentHPRegenMod", "FlatMPRegenMod", "rFlatMPRegenModPerLevel",
				"PercentMPRegenMod", "FlatArmorMod", "rFaltArmorModPerLevel", "PercentArmorMod", "rFlatArmorPenetrationMod", "rFlatArmorPenetrationModPerLevel",
				"rPercentArmorPenetrationMod", "rPercentArmorPenetrationModPerLevel", "FlatPhysicalDamageMod", "rFlatPhysicalDamageModPerLevel",
				"PercentPhysicalDamageMod", "FlatMagicDamageMod", "rFlatMagicDamageModPerLevel", "PercentMagicDamageMod", "FlatMovementSpeedMod",
				"rFlatMovementSpeedModPerLevel", "PercentMovementSppedMod", "rPercentMovementSpeedModPerLevel", "FlatAttackSpeedMod", "PercentAttackSpeedMod", 
				"rPercentAttackSpeedModPerLevel", "rFlatDodgeMod", "rFlatDodgeModPerLevel", "PercentDodgeMod", "FlatCritChanceMod", "rFlatCritChanceModPerLevel", 
				"PercentCritChanceMod", "FlatCritDamageMod", "rFlatCritDamageModPerLevel", "PercentCritDamageMod", "FlatBlockMod", "PercentBlockMod", "FlatSpellBlockMod",
				"rFlatSpellBlockModPerLevel", "PercentSpellBlockMod", "FlatEXPBonus", "PercentEXPBonus", "rPercentCooldownMod", "rPercentCooldownModPerLevel",
				"rFlatTimeDeadMod", "rFlatTimeDeadModPerLevel", "rPercentTimeDeadMod", "rPercentTimeDeadModPerLevel", "rFlatGoldPer10Mod", "rFlatMagicPenetrationMod",
				"rFlatMagicPenetrationModPerLevel", "rPercentMagicPenetrationMod", "rPercentMagicPenetrationModPerLevel");



			Print '</ul></div>
						<div id="runes-container">';
				for($i = 0; $i < sizeof($orderedRuneInfo); $i++) {
					Print '<div id="rune-page' . $i . '">
								<div id="rune-body">
								<!-- Marks -->
									<div class="small-rune" style="bottom: 10px; left: 20px; background: url(\'images/lol-images/runes/' . $orderedRuneInfo[$i][0][2] . '\'); background-size: 53px 60px; background-repeat: no-repeat; background-position: center; ">
									</div>
									<div class="small-rune" style="bottom: 10px; left: 90px; background: url(\'images/lol-images/runes/' . $orderedRuneInfo[$i][1][2] . '\'); background-size: 53px 60px; background-repeat: no-repeat; background-position: center;">
									</div>
									<div class="small-rune" style="bottom: 8px; left: 170px; background: url(\'images/lol-images/runes/' . $orderedRuneInfo[$i][2][2] . '\'); background-size: 53px 60px; background-repeat: no-repeat; background-position: center;">
									</div>
									<div class="small-rune" style="bottom: 72px; left: 8px; background: url(\'images/lol-images/runes/' . $orderedRuneInfo[$i][3][2] . '\'); background-size: 53px 60px; background-repeat: no-repeat; background-position: center;">
									</div>
									<div class="small-rune" style="bottom: 75px; left: 75px; background: url(\'images/lol-images/runes/' . $orderedRuneInfo[$i][4][2] . '\'); background-size: 53px 60px; background-repeat: no-repeat; background-position: center;">
									</div>
									<div class="small-rune" style="bottom: 60px; left: 135px; background: url(\'images/lol-images/runes/' . $orderedRuneInfo[$i][5][2] . '\'); background-size: 53px 60px; background-repeat: no-repeat; background-position: center;">
									</div>
									<div class="small-rune" style="bottom: 128px; left: 35px; background: url(\'images/lol-images/runes/' . $orderedRuneInfo[$i][6][2] . '\'); background-size: 53px 60px; background-repeat: no-repeat; background-position: center;">
									</div>
									<div class="small-rune" style="bottom: 120px; left: 120px; background: url(\'images/lol-images/runes/' . $orderedRuneInfo[$i][7][2] . '\'); background-size: 53px 60px; background-repeat: no-repeat; background-position: center;">
									</div>
									<div class="small-rune" style="bottom: 170px; left: 80px; background: url(\'images/lol-images/runes/' . $orderedRuneInfo[$i][8][2] . '\'); background-size: 53px 60px; background-repeat: no-repeat; background-position: center;">
									</div>
								<!-- Seals -->
									<div class="small-rune" style="bottom: 215px; left: 40px; background: url(\'images/lol-images/runes/' . $orderedRuneInfo[$i][9][2] . '\'); background-size: 53px 60px; background-repeat: no-repeat; background-position: center;">
									</div>
									<div class="small-rune" style="bottom: 220px; left: 115px; background: url(\'images/lol-images/runes/' . $orderedRuneInfo[$i][10][2] . '\'); background-size: 53px 60px; background-repeat: no-repeat; background-position: center;">
									</div>
									<div class="small-rune" style="bottom: 270px; left: 70px; background: url(\'images/lol-images/runes/' . $orderedRuneInfo[$i][11][2] . '\'); background-size: 53px 60px; background-repeat: no-repeat; background-position: center;">
									</div>
									<div class="small-rune" style="bottom: 295px; left: 130px; background: url(\'images/lol-images/runes/' . $orderedRuneInfo[$i][12][2] . '\'); background-size: 53px 60px; background-repeat: no-repeat; background-position: center;">
									</div>
									<div class="small-rune" style="bottom: 350px; left: 170px; background: url(\'images/lol-images/runes/' . $orderedRuneInfo[$i][13][2] . '\'); background-size: 53px 60px; background-repeat: no-repeat; background-position: center;">
									</div>
									<div class="small-rune" style="bottom: 380px; left: 230px; background: url(\'images/lol-images/runes/' . $orderedRuneInfo[$i][14][2] . '\'); background-size: 53px 60px; background-repeat: no-repeat; background-position: center;">
									</div>
									<div class="small-rune" style="bottom: 400px; left: 290px; background: url(\'images/lol-images/runes/' . $orderedRuneInfo[$i][15][2] . '\'); background-size: 53px 60px; background-repeat: no-repeat; background-position: center;">
									</div>
									<div class="small-rune" style="bottom: 415px; left: 358px; background: url(\'images/lol-images/runes/' . $orderedRuneInfo[$i][16][2] . '\'); background-size: 53px 60px; background-repeat: no-repeat; background-position: center;">
									</div>
									<div class="small-rune" style="bottom: 365px; left: 385px; background: url(\'images/lol-images/runes/' . $orderedRuneInfo[$i][17][2] . '\'); background-size: 53px 60px; background-repeat: no-repeat; background-position: center;">
									</div>
								<!-- Glyphs -->
									<div class="small-rune" style="bottom: 413px; left: 430px; background: url(\'images/lol-images/runes/' . $orderedRuneInfo[$i][18][2] . '\'); background-size: 53px 60px; background-repeat: no-repeat; background-position: center;">
									</div>
									<div class="small-rune" style="bottom: 360px; left: 460px; background: url(\'images/lol-images/runes/' . $orderedRuneInfo[$i][19][2] . '\'); background-size: 53px 60px; background-repeat: no-repeat; background-position: center;">
									</div>
									<div class="small-rune" style="bottom: 413px; left: 500px; background: url(\'images/lol-images/runes/' . $orderedRuneInfo[$i][20][2] . '\'); background-size: 53px 60px; background-repeat: no-repeat; background-position: center;">
									</div>
									<div class="small-rune" style="bottom: 320px; left: 510px; background: url(\'images/lol-images/runes/' . $orderedRuneInfo[$i][21][2] . '\'); background-size: 53px 60px; background-repeat: no-repeat; background-position: center;">
									</div>
									<div class="small-rune" style="bottom: 368px; left: 550px; background: url(\'images/lol-images/runes/' . $orderedRuneInfo[$i][22][2] . '\'); background-size: 53px 60px; background-repeat: no-repeat; background-position: center;">
									</div>
									<div class="small-rune" style="bottom: 413px; left: 593px; background: url(\'images/lol-images/runes/' . $orderedRuneInfo[$i][23][2] . '\'); background-size: 53px 60px; background-repeat: no-repeat; background-position: center;">
									</div>
									<div class="small-rune" style="bottom: 375px; left: 648px; background: url(\'images/lol-images/runes/' . $orderedRuneInfo[$i][24][2] . '\'); background-size: 53px 60px; background-repeat: no-repeat; background-position: center;">
									</div>
									<div class="small-rune" style="bottom: 330px; left: 600px; background: url(\'images/lol-images/runes/' . $orderedRuneInfo[$i][25][2] . '\'); background-size: 53px 60px; background-repeat: no-repeat; background-position: center;">
									</div>
									<div class="small-rune" style="bottom: 275px; left: 625px; background: url(\'images/lol-images/runes/' . $orderedRuneInfo[$i][26][2] . '\'); background-size: 53px 60px; background-repeat: no-repeat; background-position: center;">
									</div>
								<!-- Quints -->
									<div class="large-rune" style="bottom: 335px; left: 30px; background: url(\'images/lol-images/runes/' . $orderedRuneInfo[$i][27][2] . '\'); background-size: 103px; background-repeat: no-repeat; background-position: center;">
									</div>
									<div class="large-rune" style="bottom: 133px; left: 205px; background: url(\'images/lol-images/runes/' . $orderedRuneInfo[$i][28][2] . '\'); background-size: 103px; background-repeat: no-repeat; background-position: center;">
									</div>
									<div class="large-rune" style="bottom: 195px; left: 450px; background: url(\'images/lol-images/runes/' . $orderedRuneInfo[$i][29][2] . '\'); background-size: 103px; background-repeat: no-repeat; background-position: center;">
									</div>
								</div>	
								<div id="rune-statistics">';

								Print '<b>Page Statistics</b><br><hr>';

								$statTotals = array();
								// Gets each stat of every rune and sums the same runes values together
								for($ii = 0; $ii < 30; $ii++) {
									for($iii = 0; $iii < sizeof($orderedRuneStatNames); $iii++) {
										if($runeInfo['data'][$runePages[$i]['slots'][$ii]['runeId']]['stats'][$orderedRuneStatNames[$iii]] != null) {
											if($statTotals != null) {
												$counter = 0;
												for($j = 0; $j < sizeof($statTotals); $j++) {
													if($statTotals[$j][0] == $runeInfo['data'][$runePages[$i]['slots'][$ii]['runeId']]['description']) {
														$statTotals[$j][1] = $statTotals[$j][1] + $runeInfo['data'][$runePages[$i]['slots'][$ii]['runeId']]['stats'][$orderedRuneStatNames[$iii]];
														$counter++;
													}
												}

												if($counter == 0) {
													array_push($statTotals, array($runeInfo['data'][$runePages[$i]['slots'][$ii]['runeId']]['description'] ,$runeInfo['data'][$runePages[$i]['slots'][$ii]['runeId']]['stats'][$orderedRuneStatNames[$iii]]));
												
												}
											} else {
												array_push($statTotals, array($runeInfo['data'][$runePages[$i]['slots'][$ii]['runeId']]['description'] ,$runeInfo['data'][$runePages[$i]['slots'][$ii]['runeId']]['stats'][$orderedRuneStatNames[$iii]]));
											}
										}
									}
								}

								for($ii = 0; $ii < sizeof($statTotals); $ii++) {
									$split = split(' ', $statTotals[$ii][0]);
									for($s = 0; $s < sizeof($split); $s++) {
										if($s != 0) {
											if($split[$s] == "cooldowns") {
												$strchanger = true;
												Print "Cooldown Reduction<br>";
												Print $statTotals[$ii][1] * 100 . '%<br><br>';
												break;
											} else {
												Print ucwords($split[$s]) . ' ';
												$strchanger = false;
											}
										}
									}

									if($strchanger == false) {
										Print '<br>';
										Print $statTotals[$ii][1] . "<br><br>";
									}
								}


								Print '</div>
								<div class="clear">
								</div>
							</div>';
				}				

			Print "</div></div></div>
			</div>
	</div>
</div>";
}
	} else {
	?>
		<?php
			$random = rand(0, 3);

			if($random == 0) {
				Print "<div id=\"search-container\" class=\"summoners-rift\">";
			} else if($random == 1) {
				Print "<div id=\"search-container\" class=\"crystal-scar\">";
			} else if($random == 2) {
				Print "<div id=\"search-container\" class=\"twisted-treeline\">";
			} else if($random == 3) {
				Print "<div id=\"search-container\" class=\"howling-abyss\">";
			}
		?>
		<div class="content-box">
			<div id="search-box">
				<div id="search-box-cell">
					<div id="logo">
					</div>
					<div id="search">
						<form method="post" action="<?php $_PHP_SELF ?>">
						<input name="summoner_name" type="text" id="summoner_name" placeholder="Enter a summoner name"/></input>	
						<select name="server">
							<option value="oce">OCE</option>
							<option value="na">NA</option>
							<option value="euw">EUW</option>
							<option value="eune">EUNE</option>
							<option value="br">BR</option>
							<option value="tr">TR</option>
							<option value="ru">RU</option>
							<option value="lan">LAN</option>
							<option value="las">LAS</option>
						</select>
						<button class="searchButton" name="add" type="submit" id="add" value="Submit">Search</button>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div id="footer">
		<div class="content-box-padding">
			<h2>Server Status</h2>
			<?php
				$serverCodes = array("br", "eune", "euw", "lan", "las", "na", "oce", "ru", "tr");
				$serverNames = array("Brazil", "Europe Nordic & East", "Europe West", "Latin America North",
				"Latin America South", "North America", "Oceania", "Russia", "Turkey");
				$onlineServers = array();
				$offlineServers = array();
				for($i = 0; $i < 9; $i++) {				
					$url = 'http://status.leagueoflegends.com/shards/' . $serverCodes[$i]; 	     
					$content = file_get_contents($url);
					$serverStatus = json_decode($content, true);

					if($serverStatus['services'][1]['status'] == "online") {
						array_push($onlineServers, $serverNames[$i]);
					} else if($serverStatus['services'][1]['status'] == "offline") {
						array_push($offlineServers, $serverNames[$i]);
					}
				}

				// Prints all online server names;
				Print "<div class=\"col-container\">";
				Print "<div class=\"left-col\">";
				Print "<h3 id=\"online\">Online Servers</h3>";
				if(sizeof($onlineServers) == 0) {
					Print "(N/A)";
				} else {
					for($i = 0; $i < sizeof($onlineServers); $i++) {
						Print $onlineServers[$i] . "<br/>";
					}
					Print "</div>";
				}

				// Prints all offline server names;
				Print "<div class=\"right-col\">";
				Print "<h3 id=\"offline\">Offline Servers</h3>";
				if(sizeof($offlineServers) == 0) {
					Print "(N/A)";
				} else {
					for($i = 0; $i < sizeof($offlineServers); $i++) {
						Print $offlineServers[$i] . "<br/>";
					}
				}

				Print "</div>";
				Print "</div>";
			?>
		</div>
	</div>
	<div id="foot-note">
		<div class="content-box-padding">
			<p>LoL Emperor isn't endorsed by Riot Games and doesn't reflect the views or opinions of Riot Games or anyone officially involved in producing or managing League of Legends. League of Legends and Riot Games are trademarks or registered trademarks of Riot Games, Inc. League of Legends &copy Riot Games, Inc.</p>
		</div>
	</div>
<?php
}
?>
</div>
	<div id="copyright">
		<p>&copy 2014 LoL Emperor</p>
	</div>

</div>
</body>
</html>