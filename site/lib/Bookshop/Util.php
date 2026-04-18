<?php 
namespace Bookshop;

class Util {

    public static function escape(string $input) : string {
        return nl2br(htmlentities($input));
    }

}
