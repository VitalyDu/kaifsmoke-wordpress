<?php

namespace DgoraWcas\Engines\TNTSearchMySQL\Libs\TNTSearch\FileReaders;

use SplFileInfo;

interface FileReaderInterface
{
    /**
     * Read the content of a file
     *
     * @param  SplFileInfo  $fileinfo
     * @return string
     */
    public function read(SplFileInfo $fileinfo);
}
