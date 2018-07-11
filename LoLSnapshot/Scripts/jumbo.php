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
            echo "<p class ='h4 '>Lvl ".$accLevel."</p>";
            echo "<p class ='h4 lead'>".$rankJson[0]['tier']." ".$rankJson[0]['rank']." (".$rankJson[0]['wins']."W/".$rankJson[0]['losses']."L)
            </p></div>";
        }
        else {
            echo "Invalid username";
        }
    }
?>
