<?php

namespace DgoraWcas\Engines\TNTSearchMySQL\Libs\TNTSearch\Stemmer;

class NoStemmer implements Stemmer
{
    public static function stem($word)
    {
        return $word;
    }
}
