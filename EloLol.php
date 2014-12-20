<?php

class Summoner {

//Api Key
    private static $Api_Key = <Api_key>;

//Inputs
    public $champion;
    public $role;
    public $lane;
    public $summoner;
    public $elo;
    public $NumGamesToCalculate;
// Controls
    public static $UnLimitedApiUsed = 0;
    public static $LimitedApiUsed = 0;
    private static $MatchIndex = 0;
    public static $MatchFound = 0;
    public static $MatchUsedByCrowler = array();
    public static $SummonersUsedByCrowler = array();
// StartCrowler Summoners per role
    public static $NewSummonerId = "157126"; //Support
// API variables
    public static $obj;
    public static $obj2;
    public static $obj3;
// API values into Arrays

    public $Deaths = array();

    public function __construct() {
        if (count($_POST) > 0) {
            $this->champion = $_POST['champion'];
            $this->role = $_POST['role'];
            $this->lane = $_POST['lane'];
            $this->summoner = $_POST['summoner'];
            $this->NumGamesToCalculate = $_POST['NumGamesToCalculate'];
        }
    }

    public function AverageValue($array) {
        if (is_array($array)) {
            for ($i = 1; $i < count($array); $i++) {

                $array[$i] = $array[$i - 1] + $array[$i];
            }
            if (count($array) - 1 >= 0) {
                return $array[count($array) - 1] / count($array);
            }
        }
    }

    public static function RunMatchHistory() {
        self::$LimitedApiUsed = self::$LimitedApiUsed + 1;
        $json = file_get_contents('http://euw.api.pvp.net/api/lol/euw/v2.2/matchhistory/' . self::$NewSummonerId . '?beginIndex=' . self::$MatchIndex . '&endIndex=' . (self::$MatchIndex + 15) . '&api_key=' . self::$Api_Key . '');
        $obj = json_decode($json);
        if ($obj) {
            self::$obj = $obj;
        }
    }

    public function FindOthersChampionId($a) {
        if (self::$obj->matches[$a]) {
            return self::$obj->matches[$a]->participants[0]->championId;
        } else {
            exit("FINE DATI DISPONIBILI");
        }
    }

    public static function RunInputChampionId() {
        self::$UnLimitedApiUsed = self::$UnLimitedApiUsed + 1;
        $json2 = file_get_contents('http://global.api.pvp.net/api/lol/static-data/euw/v1.2/champion?api_key=' . self::$Api_Key . '');
        $obj2 = json_decode($json2);
        if ($obj2) {
            self::$obj2 = $obj2;
        }
    }

    public function FindInputChampionId() {
        return self::$obj2->data->{$this->champion}->id;
    }

    public static function RunNewSummonerId($c) {
        self::$LimitedApiUsed = self::$LimitedApiUsed + 1; /* echo self::$obj->matches[$c]->matchId; */
        $json3 = file_get_contents('http://euw.api.pvp.net/api/lol/euw/v2.2/match/' . self::$obj->matches[$c]->matchId . '?api_key=' . self::$Api_Key . '');
        $obj3 = json_decode($json3);
        if ($obj3) {
            self::$obj3 = $obj3;
        }
    }

    public function FindNewSummonerId() {

        for ($k = 0; $k <= 9; $k++) {
            if (!isset(self::$SummonersUsedByCrowler[self::$obj3->participantIdentities[$k]->player->summonerId]) && self::$obj3->participants[$k]->timeline->role === $this->role && substr(self::$obj3->participants[$k]->timeline->lane, 0, 3) === substr($this->lane, 0, 3)) {
                self::$SummonersUsedByCrowler[self::$obj3->participantIdentities[$k]->player->summonerId] = self::$obj3->participantIdentities[$k]->player->summonerId;
                self::$NewSummonerId = self::$obj3->participantIdentities[$k]->player->summonerId;
                $this->elocheck();
                break;
            } else {
                continue;
            }
        }
    }

    public function FindNewIdCrowlerLogic() {
        for ($j = 14; $j >= 0; $j--) {
            if (!isset(self::$MatchUsedByCrowler[self::$obj->matches[$j]->matchId])) {
                self::$MatchUsedByCrowler[self::$obj->matches[$j]->matchId] = self::$obj->matches[$j]->matchId;
                $this->RunNewSummonerId($j);
                break;
            } else {
                continue;
            }
        }
    }

    public function elocheck() {
        $this->RunMatchHistory();
        if (self::$MatchIndex === 0) {
            $this->FindNewIdCrowlerLogic();
        }

        for ($i = 14; $i >= 0; $i--) {
            echo "</br>  " . (-$i + self::$MatchIndex + 15) . "°)  ";
            if ($this->FindInputChampionId() === $this->FindOthersChampionId($i) && self::$obj->matches[$i]->queueType === "RANKED_SOLO_5x5") {
                self::$MatchFound = self::$MatchFound + 1;
                $this->Deaths[] = self::$obj->matches[$i]->participants[0]->stats->deaths;
                echo self::$obj->matches[$i]->participants[0]->stats->deaths;
            } else {
                echo "WRONG CHAMPION OR MATCH";
            }
        }

        if (self::$MatchFound < $this->NumGamesToCalculate && self::$MatchIndex < 75 /* Limite alle API chiamate */) {
            self::$MatchIndex = self::$MatchIndex + 15;
            usleep(1200000);
            $this->elocheck();
        } else if (self::$MatchFound < $this->NumGamesToCalculate && self::$MatchIndex >= 75) {
            set_time_limit(30);
            self::$MatchIndex = 0;
            $this->FindNewSummonerId();
        }
    }

}

$result = new Summoner;
echo "</br> Elo: ";
if ($result->champion != "" && $result->NumGamesToCalculate != "") {
    $result->RunInputChampionId();
    $result->elocheck();
}

echo "</br> n° Limited API Called: </br> ";
echo Summoner::$LimitedApiUsed;
echo "</br> n° Unlimited API Called: </br> ";
echo Summoner::$UnLimitedApiUsed;
echo "</br> Average Deaths: </br>";
echo $result->AverageValue($result->Deaths);
echo "</br> NumMatchCalculated: </br>";
echo Summoner::$MatchFound;
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//IT">

<html>
    <head>
        <script type="text/javascript">

            window.onload = function start() {
                var ChampionNames = "Aatrox,Ahri,Akali,Alistar,Amumu,Anivia,Annie,Ashe,Azir,Blitzcrank,Brand,Braum,Caitlyn,Cassiopeia,Cho'Gath,Corki,Darius,Diana,Dr. Mundo,\
        Draven,Elise,Evelynn,Ezreal,Fiddlesticks,Fiora,Fizz,Galio,Gangplank,Garen,Gnar,Gragas,Graves,Hecarim,Heimerdinger,Irelia,Janna,\
        Jarvan IV,Jax,Jayce;Jinx,Kalista,Karma,Karthus,Kassadin,Katarina,Kayle,Kennen,Kha'Zix,Kog'Maw,LeBlanc,Lee Sin,Leona,Lissandra,Lucian,\
        Lulu,Lux,Malphite,Malzahar,Maokai,Master Yi,Miss Fortune,Mordekaiser,Morgana,Nami,Nasus,Nautilus,Nidalee,Nocturne,Nunu,\
        Olaf,Orianna,Pantheon,Poppy,Quinn,Rammus,Rek'Sai,Renekton,Rengar,Riven,Rumble,Ryze,Sejuani,Shaco,Shen,Shyvana,Singed,Sion,Sivir,\
        Skarner,Sona,Soraka,Swain,Syndra,Talon,Taric,Teemo,Thresh,Tristana,Trundle,Tryndamere,Twisted Fate,Twitch,Udyr,\
        Urgot,Varus,Vayne,Veigar,Vel'Koz,Vi,Viktor,Vladimir,Volibear,Warwick,Wukong,Xerath,Xin Zhao,Yasuo,Yorick,Zac,Zed,Ziggs,Zilean,Zyra";

                var ChampionNamesIntoArray = ChampionNames.split(",");


                for (i = 0; i < ChampionNamesIntoArray.length; i++) {

                    var mydiv = document.getElementById("Champion");
                    var mycontent = document.createElement("option");
                    mycontent.value = ChampionNamesIntoArray[i];
                    mycontent.appendChild(document.createTextNode(ChampionNamesIntoArray[i]));
                    mydiv.appendChild(mycontent);

                }

            }
        </script>
        <style>label,select {width:150px; display:block; float:left; padding-right:10px; text-align:right;} select{width:155px; direction: rtl; float:none; padding-right:0px;}</style>
    </head>
    <body>

        <h1>Elo FormCheck</h1>

        <form action="elo11.php" method="post">

            <label for="champion">Champion:</label>
            <select name="champion" id="Champion"></select>
            <label for="role">Role:</label>
            <select name="role">
                <option value="DUO">          DUO  	    </option>
                <option value="NONE">         NONE          </option>
                <option value="SOLO">         SOLO          </option>
                <option value="DUO_CARRY">    DUO_CARRY     </option>
                <option value="DUO_SUPPORT">  DUO_SUPPORT   </option>
            </select>
            <label for="role">Lane:</label>
            <select name="lane">
                <option value="TOP">          TOP  	    </option>
                <option value="MID">          MID           </option>
                <option value="JUNGLE">       JUNGLE        </option>
                <option value="BOT">          BOT           </option>
            </select>
            <label for="summoner">Summoner:</label>
            <input type="text" name="summoner" /><br />
            <label for="NumGamesToCalculate">NumGamesToCalculate:</label>
            <input type="text" name="NumGamesToCalculate" /><br />


            <input type="submit" value="Invia" /><br />

        </form>
    </body>

</html>