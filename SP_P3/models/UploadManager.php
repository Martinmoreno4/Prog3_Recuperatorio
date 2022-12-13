<?php


require_once 'Currency.php';

class UploadManager{

    //--- Attributes ---//
    private $_DIR_TO_SAVE;
    private $_DIR_BACKUP;
    private $_fileExtension;
    private $_newFileName;
    private $_pathToSaveImage;

    //--- Constructor ---//
    public function __construct($dirToSave){
        $this->setDirectoryToSave($dirToSave);
        self::createDirIfNotExists($dirToSave);
    }
    
    private function printMessage($message, $variable){
        echo $message.' '.$variable.' <br>';
    }

    //--- Setters ---//

    /**
     * Sets the directory to save the file.
     *
     * @param string $dirToSave The directory to save the file.
     */
    public function setDirectoryToSave($dirToSave){
        $this->_DIR_TO_SAVE = $dirToSave;
    }

    /**
     * Sets the directory to save the backup file.
     *
     * @param string $dirBackup The directory to save the backup file.
     */
    public function setDirectoryBackup($dirBackup){
        $this->_DIR_BACKUP = $dirBackup;
    }

    /**
     * Set the extension of the actual file to upload. Default is png.
     *
     * @param string $fileExtension The extension of the actual file to upload.
     */
    public function setFileExtension($fileExtension = 'png'){
        $this->_fileExtension = $fileExtension;
    }

    /**
     * Set the new file name of the actual file to upload.
     *
     * @param string $newFileName The new name of the actual file to upload.
     */
    public function setNewFileName($obj){
        $filename = 'None';
        if(is_a($obj, 'Sale')){
            $filename = $obj->getCryptoName().'_'.$obj->getCustomer().'_'.$obj->replaceDate();
        }else if(is_a($obj, 'Currency')){
            $filename = $obj->getName();
        }
        $this->_newFileName = $filename.'.'.$this->getFileExtension();
        
        //$this->printMessage('New file name', $this->_newFileName);
        
        $this->setPathToSaveImage();

        //$this->printMessage('Path to save image', $this->_pathToSaveImage);
    }

    /**
     * Set the path to save the image.
     */
    public function setPathToSaveImage(){
        $this->_pathToSaveImage = $this->getDirectoryToSave().$this->getNewFileName();
    }
    
    //--- Getters ---//
    
    /**
     * Get the extension of the actual file to upload.
     * @return string The extension of the actual file to upload.
     */
    public function getFileExtension(){
        return $this->_fileExtension;
    }

    /**
     * Get the new file name of the actual file to upload.
     * @return string The new file name of the actual file to upload.
     */
    public function getNewFileName(){
        return $this->_newFileName;
    }

    /**
     * Get the path to save the image.
     * @return string The path to save the image.
     */
    public function getPathToSaveImage(){
        return $this->_pathToSaveImage;
    }

    /**
     * Gets the directory where the file is going to be saved.
     * @return string The directory where the file is going to be saved.
     */
    public function getDirectoryToSave(){
        return $this->_DIR_TO_SAVE;
    }

    /**
     * Gets the directory where the backup file is going to be saved.
     * @return string The directory where the backup file is going to be saved.
     */
    public function getDirectoryBackup(){
        return $this->_DIR_BACKUP;
    }

    //--- Methods ---//

    /**
     * If not exists the directory to save the file, it creates it.
     * @param string $dirToSave The directory to save the file.
     */
    public static function createDirIfNotExists($dirToSave){
        if (!file_exists($dirToSave)) {
            mkdir($dirToSave, 0777, true);
        }
    }

    /**
     * Save the file into the directory.
     * 
     * @param Object $obj The entity.
     * @param array @FILES The file to upload.
     */
    public function saveFileIntoDir($obj, $FILES):bool{
        $success = false;
        
        try {
            $check = $this->checkIfExist($obj);
            if(empty($check)){
                $this->printMessage('File original', $FILES['image']['tmp_name']);
                if ($this->moveUploadedFile($FILES['image']['tmp_name'])) {
                    $success = true;
                }
            }else{
                self::moveImageFromTo($this->getDirectoryToSave(), $this->getDirectoryBackup(), $this->getNewFileName());
                $success = true;
            }
        } catch (\Throwable $th) {
            echo $th->getMessage();
        }finally{
            return $success;
        }
    }

    /**
     * Checks if the file exist.
     *
     * @param Object $obj The entity to verify if exists.
     * @return bool True if the file exists, false if not.
     */
    public function checkIfExist($obj){
        $this->setFileExtension();
        $this->setNewFileName($obj);
        $this->setPathToSaveImage();
        
        $this->printMessage('Path to save image', $this->getPathToSaveImage());
        $exist = file_exists($this->getPathToSaveImage());
        $this->printMessage('Exist: ', $exist);
        
        return $exist;
    }

    /**
     * Move the file to the directory.
     *
     * @param string $tmpFileName The temporary file name to been saved.
     */
    public function moveUploadedFile($tmpFileName){
        return move_uploaded_file($tmpFileName, $this->getPathToSaveImage());
    }

    /**
     * Moves an image from a directory to another.
     * @param string $oldDir The directory where the image is allocated.
     * @param string $newDir The new directory where the image will be allocate.
     * @param string $imageName The name of the image to be moved.
     * @return bool True if can move the image, false otherwise.
     */
    public static function moveImageFromTo($oldDir, $newDir, $fileName){
        self::createDirIfNotExists($newDir);
        return rename($oldDir, $newDir.$fileName);
    }
}

?>