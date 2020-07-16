<?php

namespace app\common;

use Dejurin\GoogleTranslateForFree;
use yii\base\BaseObject;

class GoogleTranslate extends BaseObject
{
   public static function translate($text, $source = 'en', $target = 'ru', $attempts = 3)
   {
       $tr = new GoogleTranslateForFree();
       $result = $tr->translate($source, $target, $text, $attempts);

       return $result;
   }

    public static function translateKeyArr($arr, $source = 'en', $target = 'ru', $attempts = 3)
    {
        $noKey = [];

        foreach ($arr as $k => $v) {
            array_push($noKey, $v);
        }

        $translateArr = self::translate($noKey, $source, $target, $attempts);

        $result = [];

        $count = 0;
        foreach ($arr as $k => $v) {
            array_push($noKey, $v);
            $result[$k] = $translateArr[$count];
            $count++;
        }

        return $result;
    }
}