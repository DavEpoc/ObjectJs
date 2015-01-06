<?php

class Summoner {

//Api Key
    private static $Api_Key = <Api_Key>;

//Inputs
    public $champion;
    public $role;
    public $lane;
    public $summoner;
    public $elo;
    public $queuetype;
    public $NumGamesToCalculate;
// Controls
    public static $UnLimitedApiUsed = 0;
    public static $LimitedApiUsed = 0;
    private static $MatchIndex = 0;
    private static $MatchIndexInCrowler = 0;
    public static $MatchFound = 0;
    public static $MatchUsedByCrawler = array();
    public static $SummonersUsedByCrawler = array();
// StartCrawler Summoners per role
    public static $NewSummonerId = "157126"; //Support Baah
    public static $CacheNewSummoner;
// API variables
    public static $obj;
    public static $obj2;
    public static $obj3;
// API values into Arrays

    public $Deaths = array();

    public function GetInputs() {
        if (count($_POST) > 0) {
            $this->champion = $_POST['champion'];
            $this->role = implode(",", $_POST['role']);
            $this->lane = implode(",", $_POST['lane']);
            $this->summoner = $_POST['summoner'];
            $this->queuetype = implode(",", $_POST['queuetype']);
            $this->usedrank = implode(",", $_POST['usedrank']);
            $this->NumGamesToCalculate = $_POST['NumGamesToCalculate'];
        }
    }

    public function StartingSummonerIds() {
        if ($this->lane === "TOP") {
            self::$NewSummonerId = "21469743";
        } //aAa OG Spontexx
        else if ($this->lane === "MID") {
            self::$NewSummonerId = "56670514";
        } //Apdo Dog2
        else if ($this->lane === "JUNGLE") {
            self::$NewSummonerId = "19751358";
        } // kikis
        else if ($this->lane === "BOT" && $this->role === "DUO_SUPPORT") {
            self::$NewSummonerId = "56670514";
        } //Cloud9 Voidle
        else if ($this->lane === "BOT" && $this->role === "DUO_CARRY") {
            self::$NewSummonerId = "20110160";
        }; //Zvenillan
        self::$SummonersUsedByCrawler[] = self::$NewSummonerId;
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
        return self::$obj->matches[$a]->participants[0]->championId;
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
            if (!in_array(self::$obj3->participantIdentities[$k]->player->summonerId, self::$SummonersUsedByCrawler) && strpos($this->role, self::$obj3->participants[$k]->timeline->role) !== false && strpos($this->lane, substr(self::$obj3->participants[$k]->timeline->lane, 0, 3)) !== false && strpos($this->usedrank, self::$obj3->participants[$k]->highestAchievedSeasonTier) !== false) {
                self::$SummonersUsedByCrawler[] = self::$obj3->participantIdentities[$k]->player->summonerId;
                self::$CacheNewSummoner = self::$obj3->participantIdentities[$k]->player->summonerId;
                echo "[";
                echo self::$obj3->participantIdentities[$k]->player->summonerId;
                echo "]";
                break;
            } else {
                if ($k === 9) {
                    $this->FindNewIdCrawlerLogic();
                    break;
                } echo self::$obj3->participantIdentities[$k]->player->summonerId;
                echo self::$obj3->participants[$k]->timeline->role;
                echo substr(self::$obj3->participants[$k]->timeline->lane, 0, 3);
                continue;
            }
        }
    }

    public function FindNewIdCrawlerLogic() {
        for ($j = 14; $j >= 0; $j--) {
            if (isset(self::$obj->matches[$j]) && !in_array(self::$obj->matches[$j]->matchId, self::$MatchUsedByCrawler) && strpos($this->queuetype, self::$obj->matches[$j]->queueType) !== false) {
                self::$MatchUsedByCrawler[] = self::$obj->matches[$j]->matchId;
                self::$MatchIndexInCrowler = 0;
                usleep(1000500);
                $this->RunNewSummonerId($j);
                echo "(";
                echo self::$obj->matches[$j]->matchId;
                echo ")";
                $this->FindNewSummonerId();
                break;
            } else {
                echo "Match";
                if ($j === 0) {
                    self::$MatchIndexInCrowler = self::$MatchIndexInCrowler + 15;
                    break;
                } continue;
            }
        }
    }

    public function elocheck() {
        $this->RunMatchHistory();
        if (self::$MatchIndex === self::$MatchIndexInCrowler) {
            $this->FindNewIdCrawlerLogic();
        }

        for ($i = 14; $i >= 0; $i--) {
            if (isset(self::$obj->matches[$i]) && self::$MatchFound < $this->NumGamesToCalculate) {
                self::$MatchUsedByCrawler[] = self::$obj->matches[$i]->matchId;
                self::$MatchUsedByCrawler = array_unique(self::$MatchUsedByCrawler);
                echo "</br>  " . (-$i + self::$MatchIndex + 15) . "°)  ";
                if ($this->FindInputChampionId() === $this->FindOthersChampionId($i) && strpos($this->queuetype, self::$obj->matches[$i]->queueType) !== false) {
                    self::$MatchFound = self::$MatchFound + 1;
                    $this->Deaths[] = self::$obj->matches[$i]->participants[0]->stats->deaths;
                    echo self::$obj->matches[$i]->participants[0]->stats->deaths;
                } else {
                    echo "WRONG CHAMPION OR MATCH";
                }
            } else if (!isset(self::$obj->matches[$i]) || self::$MatchFound >= $this->NumGamesToCalculate) {
                echo "FINE DATI DISPONIBILI/DATI CHIESTI RAGGIUNTI";
                break;
            }
        }

        if (self::$MatchFound < $this->NumGamesToCalculate && self::$MatchIndex < (90 - 15) /* Limite alle API chiamate */) {
            self::$MatchIndex = self::$MatchIndex + 15;
            usleep(1000500);
            $this->elocheck();
        } else if (self::$MatchFound < $this->NumGamesToCalculate && self::$MatchIndex >= (90 - 15) && self::$MatchIndexInCrowler < 90) {
            set_time_limit(60);
            self::$MatchIndex = 0;
            self::$NewSummonerId = self::$CacheNewSummoner;
            $this->elocheck();
        }
    }

}

$result = new Summoner;
echo "</br> Elo: ";
$result->GetInputs();
if ($result->champion != "" && $result->NumGamesToCalculate != "") {
    $result->StartingSummonerIds();
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
        JarvanIV,Jax,Jayce,Jinx,Kalista,Karma,Karthus,Kassadin,Katarina,Kayle,Kennen,Kha'Zix,Kog'Maw,LeBlanc,Lee Sin,Leona,Lissandra,Lucian,\
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
        <style>
            label,select {width:150px; display:block; float:left; padding-right:10px; text-align:right;}
            select{width:155px; direction: rtl; float:none; padding-right:0px; margin-left:160px;}
            #queuetype{height:35px;}
            #usedrank{height:52.5px;}
            #role{height:87.5px;}
            #lane{height:70px;}

        </style>
    </head>
    <body>

        <h1>Elo FormCheck</h1>

        <form action="EloLol2.php" method="post">

            <label for="usedrank">UsedRank:</label>
            <select id="usedrank" multiple size="3" name="usedrank[]">
                <option value="CHALLENGER">              CHALLENGER  	            </option>
                <option value="MASTER">                  MASTER                     </option>
                <option value="DIAMOND">                 DIAMOND                    </option>
            </select>

            <label for="queuetype">QueueType:</label>
            <select id="queuetype" multiple size="2" name="queuetype[]">
                <option value="RANKED_SOLO_5x5">          RANKED_SOLO_5x5  	    </option>
                <option value="RANKED_TEAM_5x5">          RANKED_TEAM_5x5           </option>
            </select>
            <label for="champion">Champion:</label>
            <select name="champion" id="Champion"></select>
            <label for="role">Role:</label>
            <select id="role" multiple size="5" name="role[]">
                <option value="DUO">          DUO  	    </option>
                <option value="NONE">         NONE          </option>
                <option value="SOLO">         SOLO          </option>
                <option value="DUO_CARRY">    DUO_CARRY     </option>
                <option value="DUO_SUPPORT">  DUO_SUPPORT   </option>
            </select>
            <label for="lane">Lane:</label>
            <select  id="lane" multiple size="4" name="lane[]">
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