<?php
/**
 * Created by PhpStorm.
 * User: jim
 * Date: 1.7.15
 * Time: 21:11
 */

class sanitizer {

    private $strictness;

    public function __construct($strict){
        if(isset($strict)) {
            $this->strictness = $strict;
        } else {
            return false;
        }
    }

    public function sanitizeSQL($input){
        if($input == false){
         return false;
        }
        if($this->strictness){
            $count = preg_match("/[\;\$\"\'\}\{,]/", $input);
        } else {
            $input = preg_replace("/[\;\$\"\'\}\{,]/", "", $input, -1,$count);
        }
        if($count > 0){
            return $this->strictness ? false : $this->returner($input);
        }
        else return $this->returner($input);
    }

    public function validateLengths($input){
        if(strlen($input) < 4){
            return false;
        }
        if(strlen($input) > 10){
            return false;
        }
        return $this->returner($input);
    }

    public function numerize($input){
        if(is_numeric($input) && $input > 0){
            return $this->returner($input);
        }
        return false;
    }

    public function validateMail($input){
        $output = filter_var($input, FILTER_VALIDATE_EMAIL);
        return $output;     //pri selhani vraci false tak jako tak
    }

    public function valueFromList($input, $array){
        return array_search($input, $array) ? $input : false;
    }

    private function returner($toReturn){
        //return htmlentities($toReturn, ENT_QUOTES, "utf-8");
        return $toReturn;
    }

}