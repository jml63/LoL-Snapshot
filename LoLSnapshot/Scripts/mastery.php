<?php
    $json = curl('https://oc1.api.riotgames.com/lol/champion-mastery/v3/champion-masteries/by-summoner/'.$summonerId.'?api_key='.$apikey.'');
    echo "<script>
            var jumbo = document.getElementsByClassName('jumbotron')[0];
            jumbo.style.backgroundImage = 'linear-gradient(rgba(255,255,255,0.5), rgba(255,255,255,0.5)), url(http://stelar7.no/cdragon/latest/uncentered-splash-art/".$json[0]['championId']."/0.png)';
            jumbo.style.backgroundSize = 'cover';
        </script>";
    echo "
        <div class='row justify-content-center'>
            <div class='col-md-4'>
                <div class='card'>
                  "."<img src='http://stelar7.no/cdragon/latest/loading-screen/".$json[0]['championId']."/0.png' class='img-fluid card-img-top'>"."
                  "."<div class='card-body'>Level ".$json[0]['championLevel']." Mastery</div>"."
                </div>
            </div>
            <div class='col-md-4'>
                <div class='card'>
                  "."<img src='http://stelar7.no/cdragon/latest/loading-screen/".$json[1]['championId']."/0.png' class='img-fluid card-img-top'>"."
                  "."<div class='card-body'>Level ".$json[1]['championLevel']." Mastery</div>"."
                </div>
            </div>
            <div class='col-md-4'>
                <div class='card'>
                  "."<img src='http://stelar7.no/cdragon/latest/loading-screen/".$json[2]['championId']."/0.png' class='img-fluid card-img-top'>"."
                  "."<div class='card-body'>Level ".$json[2]['championLevel']." Mastery</div>"."
                </div>
            </div>
        </div>
        ";

?>   