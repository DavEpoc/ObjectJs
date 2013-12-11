<?php

$numeri = array(5, -3, 5, -8.6, 9, -3, -8.3, -8.5, 6, 10, 12.4, "59ciao", "-1.5casa");

function ordina(&$arr) {
    $cont;
    $long = count($arr);

    foreach ($arr as $key => $obj) {

        for ($i = 0; $i < $long - $key; $i++) {
            if ($arr[$key] > $arr[$key + $i]) {

                $cont = $arr[$key + $i];
                $arr[$key + $i] = $arr[$key];
                $arr[$key] = $cont;
            }
            settype($arr[$key], 'int');
        }
    }
    for ($i = 0; $i < $long; $i++) {
        echo ' ' . "$arr[$i]";
    }
}

ordina($numeri);
