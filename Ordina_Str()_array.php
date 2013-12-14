<?php

$stringhe = array("", "Aria", "Bello", "Casa", "Asta", "devon", "amico", "vere", "a",
    "borse", "/cahihihihi", "Dante", "", " deSi", "verea", "ciao-bella", "", "", 1, 2.3,
    "cami/cie", "camicie", "ciaobella", "ciao bella", "vere 1", "");

function ordina(&$arr, $tolerance) {
    $cont;
    $long = count($arr);

    for ($k = 0; $k < $tolerance; $k++) {
        foreach ($arr as $key => $obj) {

            for ($i = 0; $i < $long - $key; $i++) {
                if ($arr[$key] == "") {
                    $arr[$key] = "//Empty";
                }

                $j = substr(trim($arr[$key]), 0, $tolerance);
                $m = substr(trim($arr[$key + $i]), 0, $tolerance);

                if (empty($j[$k])) {
                    $j[$k] = " ";
                }
                if (empty($m[$k])) {
                    $m[$k] = " ";
                }
                if (strtoupper($j[$k]) > strtoupper($m[$k])) {

                    if ($k != 0 && strtoupper(substr($j, 0, $k)) == strtoupper(substr($m, 0, $k))) {

                        $cont = $arr[$key + $i];
                        $arr[$key + $i] = $arr[$key];
                        $arr[$key] = $cont;
                    } else if ($k == 0) {

                        $cont = $arr[$key + $i];
                        $arr[$key + $i] = $arr[$key];
                        $arr[$key] = $cont;
                    }
                }
                settype($arr[$key], 'string');
            }
        }
    }

    for ($i = 0; $i < $long; $i++) {
        echo '</br>' . "$arr[$i]";
    }
}

ordina($stringhe, 6);


