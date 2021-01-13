<?php
    /**
     * oblicza cene brutto na podstawie ceny netto i vatu
     * @param $n
     * @param $v
     * @return float
     */
    function brutto($n, $v) : float {
            return ceil(100*($n*(1+($v/100))))/100;
    }

    /**
     * funkcja dodaje zera na koncu do liczby złotych
     * @param $s
     * @return string
     */
    function add_zeros($s): string {
        $s = strval($s);
        if(strlen($s) > 3 && $s[strlen($s) - 3] == '.') {
            return "";
        } else if($s[strlen($s) - 2] == '.') {
            return "0";
        }
        else return ".00";
    }





