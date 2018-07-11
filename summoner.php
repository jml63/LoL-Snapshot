<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title></title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB" crossorigin="anonymous">
        
        <link href="customstyle.css" rel="stylesheet">
        
    </head>

    <body>
    
        
        <div class="jumbotron">
          <div class="container">
              <div class="row">
                <?php
                session_start();
                $apikey = "";

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
                        
                        $rankJson = curl('https://oc1.api.riotgames.com/lol/league/v3/positions/by-summoner/'.$summonerId.'?api_key='.$apikey.'');
                        
                        echo "<div class='col-sm-4'><img src='http://ddragon.leagueoflegends.com/cdn/$version/img/profileicon/$icon.png' class='rounded-circle shadow p-2 mb-5 rounded' style='width:65%'></div>";
                        echo "<div class='col-sm-8 display-1 align-self-center'><h1>".$accName."</h1>";
                        echo "<p class ='h4 text-muted'>Lvl ".$accLevel."</p>";
                        echo "<p class ='h4 text-muted lead'>".$rankJson[0]['tier']." ".$rankJson[0]['rank']." (".$rankJson[0]['wins']."W/".$rankJson[0]['losses']."L)
                        </p></div>";
                    }
                    else {
                        echo "Invalid username";
                    }
                }
                ?>
            </div>
          </div>
        </div>

        <div class="row">
        <div class='col-sm-6'>
        <div class="container align-self-center">
            <?php
                $json = curl('https://oc1.api.riotgames.com/lol/champion-mastery/v3/champion-masteries/by-summoner/'.$summonerId.'?api_key='.$apikey.'');
                echo "
                    <div class='row justify-content-center' style='height:75%'>
                        <div class='col-md-4'>
                            <div class='card'>
                              "."<img src='http://stelar7.no/cdragon/latest/loading-screen/".$json[0]['championId']."/0.png' class='img-fluid card-img-top'>"."
                              "."<div class='card-body'>Level ".$json[0]['championLevel']." Champion Mastery</div>"."
                            </div>
                        </div>
                        <div class='col-md-4'>
                            <div class='card'>
                              "."<img src='http://stelar7.no/cdragon/latest/loading-screen/".$json[1]['championId']."/0.png' class='img-fluid card-img-top'>"."
                              "."<div class='card-body'>Level ".$json[1]['championLevel']." Champion Mastery</div>"."
                            </div>
                        </div>
                        <div class='col-md-4'>
                            <div class='card'>
                              "."<img src='http://stelar7.no/cdragon/latest/loading-screen/".$json[2]['championId']."/0.png' class='img-fluid card-img-top'>"."
                              "."<div class='card-body'>Level ".$json[2]['championLevel']." Champion Mastery</div>"."
                            </div>
                        </div>
                    </div>
                    ";

            ?>   
        </div>
        </div>
        <div class='col-sm-6'>
        <div class="container align-self-center overFlowClass" style="height:50%;">
            <?php
                $json = curl('https://oc1.api.riotgames.com/lol/match/v3/matchlists/by-account/'.$accID.'?api_key='.$apikey.'');
                //for i recent games
                for ($i=0; $i<=10; $i++) {
                    $gameID = $json['matches'][$i]['gameId'];
                    $match = curl('https://oc1.api.riotgames.com/lol/match/v3/matches/'.$gameID.'?api_key='.$apikey.''); //get ith game

                    //only want stats if it was a ranked game
                    if ($match['queueId'] == 420) {
                        echo "<div class='row match alert' style='height: 80px;'>";
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
                                echo "<p class='font-weight-bold'>";
                                echo $match['participants'][$ii]['stats']['kills']."/".$match['participants'][$ii]['stats']['deaths']."/".$match['participants'][$ii]['stats']['assists'];
                                echo "</p>";
                            }
                        }
                        echo "</div>";
                    }
                }
            ?>
        </div>
        </div>
        </div>
        
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js" integrity="sha384-smHYKdLADwkXOn1EmN1qk/HfnUcbVRZyYmZ4qpPea6sjB/pTJ0euyQp0Mk8ck+5T" crossorigin="anonymous"></script>
    </body>
</html>
