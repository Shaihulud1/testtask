<?php
namespace app\helpers;

class FileUpload
{
    private $fileName;
    private $fileNameTmp;
    private $fileHash;
    private $extension;
    private $savedFile;
    private $mimeType;

    public function __construct(array $file)
    {   
        $this->fileName =  trim(strip_tags($file['name']));
        $this->fileNameTmp =  trim(strip_tags($file['tmp_name']));
        $this->mimeType = trim(strip_tags($file['type']));
        $this->extension = pathinfo($this->fileName, PATHINFO_EXTENSION);
        $this->fileHash = hash_file('md5', (string)$this->fileNameTmp);
    }

    public function isCorrectExtension(array $posibleExtensions): bool 
    {
        return in_array($this->extension, $posibleExtensions);
    }

    public function getFileHash(): string
    {
        return (string)$this->fileHash;
    }

    public function getSavedFile(): string
    {
        return (string)$this->savedFile;
    }

    public function getMime(): string
    {
        return (string)$this->mimeType;
    }

    public function getName(): string
    {
        return (string)$this->fileName;
    }

    public function deleteSavedFile(): void
    {
        unlink($this->savedFile);
    }

    
    public function saveFile(): bool
    {
        $this->savedFile = $_SERVER['DOCUMENT_ROOT'].'/app/photo-temp/' . uniqid().time() . "." . $this->extension;
        return move_uploaded_file($_FILES["photo"]["tmp_name"], $this->savedFile);
    }


}