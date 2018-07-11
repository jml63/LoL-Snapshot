<!DOCTYPE html>
<html lang="en">

<!-- Set up some meta data, title for the page, and link to the stylesheet -->
<head>
    <meta charset="UTF-8">
    <meta name="author" content="Joshua Lawn">
	<title>Home</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB" crossorigin="anonymous">
	<link href="../styles/main.css?v=6" rel="stylesheet" type="text/css">
</head>

<body>
    <!-- Header -->
    <header>
        <h1>myLeague</h1>
    </header>
    
    <!-- Main content
    	Also show whether we are logged in or not
    	-->
    <div id="nav">
        <ul>
          <li><a class="active" href="#home">Home</a></li>
          <li><a href="#champions">Champions</a></li>
          <li><a href="#about">About</a></li>
        </ul>
    </div>
    
    <main>
        <div id="sectioncontainer">
        <?php
            session_start();
            $apikey = "";
        
            //check if we have the static data otherwise retrieve it and store it as a session
            /*if(isset($_SESSION['RIOTSTATICDATACACHE'])) {
                $staticdatajson = json_decode($_SESSION['RIOTSTATICDATACACHE'], true);
            }
            else { //static data can only be accessed 10 times per hour so we need to store it
                $_SESSION['RIOTSTATICDATACACHE'] = json_encode(curl('https://oc1.api.riotgames.com/lol/static-data/v3/champions?locale=en_US&tags=image&dataById=true&api_key='.$apikey.''));
                $staticdatajson = json_decode($_SESSION['RIOTSTATICDATACACHE'], true);
            }*/
        
            //take a url and return decoded json using riot API
            function curl($url) {
                //Initialize curl handle
                $ch = curl_init();
                //Set options
                curl_setopt($ch, CURLOPT_URL, $url);
                //Return instead of output
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                //Execute request
                $output = curl_exec($ch);
                //Check for errors
                if ($output == false) {
                    echo curl_error($ch);
                }
                //Close handle
                curl_close($ch);
                $json = json_decode($output, true);
                return $json;
            }
        
            //If a username has been entered
            if(isset($_GET['username'])) {
                //store it
                $accName = $_GET['username'];
                //find an account by name
                $json = curl('https://oc1.api.riotgames.com/lol/summoner/v3/summoners/by-name/'.$accName.'?api_key='.$apikey.'');
                $version_json = curl('https://ddragon.leagueoflegends.com/api/versions.json');
                $version = $version_json[0];
                //get the account id
                if(isset($json['accountId'])) {
                    $accID = $json['accountId'];
                    $icon = $json['profileIconId'];
                    $accLevel = $json['summonerLevel'];
                    $summonerId = $json['id'];
                    
                    echo "<div id='stats'>";
                        echo "<div id='profile'>";
                        echo "<img src='http://ddragon.leagueoflegends.com/cdn/$version/img/profileicon/$icon.png'>";
                        echo "<p>".$accName."</p>";
                        echo "<p>Lvl ".$accLevel."</p>";
                        echo "</div>";
                    
                        echo "<div id='topchamps'>";
                        $json = curl('https://oc1.api.riotgames.com/lol/champion-mastery/v3/champion-masteries/by-summoner/'.$summonerId.'?api_key='.$apikey.'');
                        echo "<img src='http://stelar7.no/cdragon/latest/loading-screen/".$json[0]['championId']."/0.png'>";
                        echo "<img src='http://stelar7.no/cdragon/latest/loading-screen/".$json[2]['championId']."/0.png'>";
                        echo "<img src='http://stelar7.no/cdragon/latest/loading-screen/".$json[3]['championId']."/0.png'>";
                        echo "</div>";
                    echo "</div>";
                    
                    
                    //get match history
                    echo "<div id='matchhistory'>";
                    $json = curl('https://oc1.api.riotgames.com/lol/match/v3/matchlists/by-account/'.$accID.'?api_key='.$apikey.'');
                    //for i recent games
                    for ($i=0; $i<=10; $i++) {
                        $gameID = $json['matches'][$i]['gameId'];
                        $match = curl('https://oc1.api.riotgames.com/lol/match/v3/matches/'.$gameID.'?api_key='.$apikey.''); //get ith game

                        //only want stats if it was a ranked game
                        if ($match['queueId'] == 420) {
                            echo "<div class='match'>";
                            $champID = $json['matches'][$i]['champion'];
                            //$champ = $staticdatajson['data'][$champID]['image']['full'];
                            //get the champion played and display icon
                            //echo "<img src='http://ddragon.leagueoflegends.com/cdn/$version/img/champion/$champ'>";

                            //get whether it was a win or lose
                            for($ii=0; $ii<count($match['participants']); $ii++) {
                                if ($match['participants'][$ii]['championId'] == $champID) {
                                    if ($match['participants'][$ii]['stats']['win'] == 1) { //if its a win we changed the div background colour
                                        echo "<script>
                                                var matches = document.getElementsByClassName('match')[$i-1];
                                                matches.style.backgroundColor = '#5BCEF9';
                                            </script>";
                                    }
                                    else {
                                        echo "<script>
                                                var matches = document.getElementsByClassName('match')[$i-1];
                                                matches.style.backgroundColor = '#F95B5B';
                                            </script>";
                                    }
                                    //show kda
                                    echo $match['participants'][$ii]['stats']['kills']."/".$match['participants'][$ii]['stats']['deaths']."/".$match['participants'][$ii]['stats']['assists'];
                                }
                            }
                            echo "</div>";
                        }
                    }
                    echo "</div>";
                    
                }
                else {
                    echo "Invalid username";
                }
            }
        ?>
        </div>
    </main>
    
    <script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js" integrity="sha384-smHYKdLADwkXOn1EmN1qk/HfnUcbVRZyYmZ4qpPea6sjB/pTJ0euyQp0Mk8ck+5T" crossorigin="anonymous"></script>
</body>

</html>