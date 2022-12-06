<?php

require_once 'Currency.php';

class UploadManager
{

    private $_DIR_TO_SAVE;
    private $_DIR_BACKUP;
    private $_fileExtension;
    private $_newFileName;
    private $_pathToSaveImage;

    public function __construct($dirToSave){
        $this->setDirectoryToSave($dirToSave);
        self::createDirIfNotExists($dirToSave);
    }
    
    private function printMessage($message, $variable){
        echo $message.' '.$variable.' <br>';
    }

    public function setDirectoryToSave($dirToSave){
        $this->_DIR_TO_SAVE = $dirToSave;
    }

    public function setDirectoryBackup($dirBackup){
        $this->_DIR_BACKUP = $dirBackup;
    }

    public function setFileExtension($fileExtension = 'png'){
        $this->_fileExtension = $fileExtension;
    }

    public function setNewFileName($obj){
        $filename = 'None';
        if(is_a($obj, 'Sale')){
            $filename = $obj->getCryptoName().'_'.$obj->getCustomer().'_'.$obj->replaceDate();
        }else if(is_a($obj, 'Currency')){
            $filename = $obj->getName();
        }
        $this->_newFileName = $filename.'.'.$this->getFileExtension();
        
        $this->setPathToSaveImage();
    }

    public function setPathToSaveImage()
    {
        $this->_pathToSaveImage = $this->getDirectoryToSave().$this->getNewFileName();
    }
    

    public function getFileExtension()
    {
        return $this->_fileExtension;
    }


    public function getNewFileName()
    {
        return $this->_newFileName;
    }


    public function getPathToSaveImage(){
        return $this->_pathToSaveImage;
    }


    public function getDirectoryToSave()
    {
        return $this->_DIR_TO_SAVE;
    }

 
    public function getDirectoryBackup()
    {
        return $this->_DIR_BACKUP;
    }


    public static function createDirIfNotExists($dirToSave)
    {
    
        if (!file_exists($dirToSave)) {
            mkdir($dirToSave, 0777, true);
        }
    }


    public function saveFileIntoDir($obj, $FILES):bool
    {
        $success = false;
        
        try 
        {
            $check = $this->checkIfExist($obj);
            if(empty($check))
            {
                $this->printMessage('File original', $FILES['image']['tmp_name']);
                if ($this->moveUploadedFile($FILES['image']['tmp_name'])) 
                {
                    $success = true;
                }
            }
            else
            {
                self::moveImageFromTo($this->getDirectoryToSave(), $this->getDirectoryBackup(), $this->getNewFileName());
                $success = true;
            }
        } 
        catch (\Throwable $th) 
        {
            echo $th->getMessage();
        }
        finally
        {
            return $success;
        }
    }


    public function checkIfExist($obj){
        $this->setFileExtension();
        $this->setNewFileName($obj);
        $this->setPathToSaveImage();
        
        $this->printMessage('Path to save image', $this->getPathToSaveImage());
        $exist = file_exists($this->getPathToSaveImage());
        $this->printMessage('Exist: ', $exist);
        
        return $exist;
    }


    public function moveUploadedFile($tmpFileName){
        return move_uploaded_file($tmpFileName, $this->getPathToSaveImage());
    }


    public static function moveImageFromTo($oldDir, $newDir, $fileName){
        self::createDirIfNotExists($newDir);
        return rename($oldDir, $newDir.$fileName);
    }
}

?>