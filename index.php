<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        <?php
        for ($var = 1; $var <= 100; $var++) {

            if (is_integer($var / 3) && is_integer($var / 5)) {
                echo '</br>' . ' fizz-buzz ';
            } else if (is_integer($var / 3)) {
                echo '</br>' . ' fizz ';
            } else if (is_integer($var / 5)) {
                echo '</br>' . ' buzz ';
            } else {
                echo '</br>' . "$var";
            }
        }
        for ($i = 1; $i <= 100; $i++) {
            switch ($i) {
                case ($i % 3 == 0 ) && ($i % 5 == 0 ):
                    echo "FizzBuzz";
                    break;
                case $i % 3 == 0:
                    echo "Fizz";
                    break;
                case $i % 5 == 0;
                    echo "Buzz";
                    break;
                default:
                    echo $i;
                    break;
            }
            echo "<br>";
        }
        ?>
    </body>
</html>
