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
                    echo "<img src='http://stelar7.no/cdragon/latest/champion-icons/$champID.png' class='mr-3 align-middle rounded-circle border border-dark' style='height:48px;'>";
                    //show kda
                    echo "<p class='font-weight-light align-middle'>";
                    echo $match['participants'][$ii]['stats']['kills']."/".$match['participants'][$ii]['stats']['deaths']."/".$match['participants'][$ii]['stats']['assists'];
                    echo "</p>";
                }
            }
            echo "</div>";
        }
    }
?>