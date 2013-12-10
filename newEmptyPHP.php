<?php

/* a oggetti */

class cambia {

    public $primo = 'primo';
    public $secondo = 'secondo';

}

$obj = new cambia();
$obj2 = new cambia();
$obj2->primo = $obj->secondo;
$obj2->secondo = $obj->primo;
echo $obj2->primo;
echo $obj2->secondo;

/* procedurale */

echo '</br>' . '</br>' . '</br>' . '</br>';

$terzo = 'terzo';
$quarto = 'quarto';

$contenitore = $quarto;
$quarto = $terzo;
$terzo = $contenitore;

echo $terzo;
echo $quarto;

/**
 * Swaps variables passed by reference
 * @param $first
 * @param $second
 */
function swap(&$first, &$second) {
    $aux = $first;
    $first = $second;
    $second = $aux;
}

$primo = 1;
$secondo = 2;

echo "primo: $primo, secondo: $secondo <br>";

swap($primo, $secondo);

echo "primo: $primo, secondo: $secondo <br>";

