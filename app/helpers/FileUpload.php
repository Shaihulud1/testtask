<?php
namespace app\helpers;

class FileUpload
{
    private $fileName;
    private $fileNameTmp;

    public function __construct(array $file)
    {   
        $this->fileName =  trim(strip_tags($file['name']));
        $this->fileNameTmp =  trim(strip_tags($file['tmp_name']));
    }

    public function isCorrectExtension(array $posibleExtensions): bool 
    {
        return in_array(pathinfo($this->fileName, PATHINFO_EXTENSION), $posibleExtensions);
    }

    public function getFileHash()
    {
        return hash_file('md5', $this->fileNameTmp);
    }

}