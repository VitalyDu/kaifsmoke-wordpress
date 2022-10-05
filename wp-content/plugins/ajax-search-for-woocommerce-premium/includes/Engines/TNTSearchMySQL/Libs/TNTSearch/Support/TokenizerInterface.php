<?php
namespace DgoraWcas\Engines\TNTSearchMySQL\Libs\TNTSearch\Support;

interface TokenizerInterface
{
    public function tokenize($text, $stopwords);

    public function setContext($context);
}
