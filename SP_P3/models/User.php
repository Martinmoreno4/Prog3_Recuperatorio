<?php


require_once './db/DataAccess.php';

class User{

    public $id;
    public $username;
    public $type;
    public $password;

    public function __construct(){}

    public static function createEntity($username, $password, $type='Customer'){
        $user = new User();
        $user->setUsername($username);
        $user->setPassword($password);
        $user->setType($type);
        
        return $user;
    }

    //--- Getters ---//


    public function getId(){
        return $this->id;
    }

 
    public function getUsername(){
        return $this->username;
    }


    public function getType(){
        return $this->type;
    }


    public function getPassword(){
        return $this->password;
    }

    //--- Setters ---//


    public function setId($id){
        $this->id = $id;
    }


    public function setUsername($username){
        $this->username = $username;
    }


    public function setType($type){
        $this->type = $type;
    }

 
    public function setPassword($password){
        $this->password = $password;
    }

    //--- Methods ---//


    public function isAdmin(){
        return $this->type == "Admin";
    }

   
    public function isClient(){
        return $this->type == "Customer";
    }
    
 
    public static function printDataAsTable($listObjects){
        echo "<table border='2'>";
        echo '<caption>Users List</caption>';
        echo "<th>[ID]</th><th>[USERNAME]</th><th>[TYPE]</th><th>[PASSWORD]</th>";
        foreach($listObjects as $object){
            echo "<tr align='center'>";
            echo "<td>[".$object->getId()."]</td>";
            echo "<td>[".$object->getUsername()."]</td>";
            echo "<td>[".$object->getType()."]</td>";
            echo "<td>[".$object->getPassword()."]</td>";
            echo "</tr>";
        }
        echo "</table>" ;
    }

 
    public function printSingleEntityAsTable(){
        echo "<table border='2'>";
        echo '<caption>Users List</caption>';
        echo "<th>[ID]</th><th>[USERNAME]</th><th>[TYPE]</th><th>[PASSWORD]</th>";
        echo "<tr align='center'>";
        echo "<td>[".$this->getId()."]</td>";
        echo "<td>[".$this->getUsername()."]</td>";
        echo "<td>[".$this->getType()."]</td>";
        echo "<td>[".$this->getPassword()."]</td>";
        echo "</tr>";
        echo "</table>" ;
    }

    //--- PDO Methods ---//

 
    public static function insertEntity($entity){
        $objDataAccess = DataAccess::getInstance();
        $sql = "INSERT INTO `users` (username, type, password) VALUES (:username, :type, :password);";
        $query = $objDataAccess->prepareQuery($sql);
        
        $username = $entity->getUsername();
        $type = $entity->getType();
        $password = $entity->getPassword();
        $hashPssw = password_hash($password, PASSWORD_DEFAULT);

        $query->bindParam(':username', $username, PDO::PARAM_STR);
        $query->bindParam(':type', $type, PDO::PARAM_STR);
        $query->bindParam(':password', $hashPssw, PDO::PARAM_STR);
        $query->execute();

        return $objDataAccess->getLastInsertedId();
    }

  
    public static function updateEntity($entity){
        $objDataAccess = DataAccess::getInstance();
        $sql = "UPDATE `users` SET username = :username, type = :type, password = :password WHERE id = :id;";
        $query = $objDataAccess->prepareQuery($sql);
        $query->bindParam(':id', $entity->getId());
        $query->bindParam(':username', $entity->getUsername());
        $query->bindParam(':type', $entity->getType());
        $query->bindParam(':password', $entity->getPassword());
        $query->execute();

        return $query->rowCount();
    }

 
    public static function deleteEntity($entity){
        $objDataAccess = DataAccess::getInstance();
        $sql = "DELETE FROM `users` WHERE id = :id;";
        $query = $objDataAccess->prepareQuery($sql);
        $query->bindParam(':id', $entity->getId());
        $query->execute();

        return $query->rowCount();
    }


    public static function getEntityById($id){
        $objDataAccess = DataAccess::getInstance();
        $sql = "SELECT * FROM `users` WHERE id = :id;";
        $query = $objDataAccess->prepareQuery($sql);
        $query->bindParam(':id', $id);
        $query->execute();

        $entity = $query->fetchObject('User');
        if(is_null($entity)){
            throw new Exception("The entity doesn't exist.");
        }
        
        return $entity;
    }


    public static function getEntityByUsername($username){
        $objDataAccess = DataAccess::getInstance();
        $sql = "SELECT * FROM `users` WHERE username = :username;";
        $query = $objDataAccess->prepareQuery($sql);
        $query->bindParam(':username', $username, PDO::PARAM_STR);
        $query->execute();

        $entity = $query->fetchObject('User');
        if (is_null($entity)) {
            throw new Exception("The entity doesn't exist.");
        }

        return $entity;
    }

    public static function getAllEntities(){
        $objDataAccess = DataAccess::getInstance();
        $sql = "SELECT * FROM `users`";
        $query = $objDataAccess->prepareQuery($sql);
        $query->execute();

        $entities = $query->fetchAll(PDO::FETCH_CLASS, 'User');
        return $entities;
    }

    public static function getEntitiesByCrypto($crypto_name){
        $objDataAccess = DataAccess::getInstance();
        $sql = "SELECT DISTINCT u.id, u.username, u.type, u.password
        FROM `users` AS u
        INNER JOIN `sales` as s ON u.username = s.user
        WHERE s.crypto_name = :crypto_name;";
        $query = $objDataAccess->prepareQuery($sql);
        $query->bindParam(':crypto_name', $crypto_name, PDO::PARAM_STR);
        $query->execute();

        $entities = $query->fetchAll(PDO::FETCH_CLASS, 'User');
        return $entities;
    }
}