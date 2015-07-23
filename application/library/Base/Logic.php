<?php
class Base_Logic{    
    public function getprop($colname) {
        $tmp = explode('_', $colname);
        for($i = 1; $i < count($tmp); $i++) {
            $tmp[$i] = ucfirst($tmp[$i]);
        }
        $colname = implode($tmp);
        return $colname;
    }
}