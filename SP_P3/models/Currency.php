<?php


require_once './db/DataAccess.php';

class Currency{

    public $id;
    public $name;
    public $image;
    public $origin;
    public $price;

    public function __construct(){}


    public static function createEntity($name, $image, $origin, $price){
        $currency = new Currency();
        $currency->setName($name);
        $currency->setImage($image);
        $currency->setOrigin($origin);
        $currency->setPrice($price);
        return $currency;
    }

    //--- Getters ---//


    public function getId(){
        return $this->id;
    }


    public function getName(){
        return $this->name;
    }


    public function getImage(){
        return $this->image;
    }


    public function getOrigin(){
        return $this->origin;
    }

 
    public function getPrice(){
        return $this->price;
    }

    //--- Setters ---//


    public function setId($id){
        $this->id = $id;
    }

 
    public function setName($name){
        $this->name = $name;
    }


    public function setImage($image){
        $this->image = $image;
    }


    public function setOrigin($origin){
        $this->origin = $origin;
    }


    public function setPrice($price){
        $this->price = $price;
    }

    //--- Methods ---//


    public static function printDataAsTable($listObjects){
        echo "<table border='2'>";
        echo '<caption>Cryptocurrencies List</caption>';
        echo "<th>[ID]</th><th>[NAME]</th><th>[IMAGE]</th><th>[ORIGIN]</th><th>[PRICE]</th>";
        foreach($listObjects as $object){
            echo "<tr align='center'>";
            echo "<td>[".$object->getId()."]</td>";
            echo "<td>[".$object->getName()."]</td>";
            echo "<td>[".$object->getImage()."]</td>";
            echo "<td>[".$object->getOrigin()."]</td>";
            echo "<td>[".$object->getPrice()."]</td>";
            echo "</tr>";
        }
        echo "</table>" ;
    }

    public function printSingleEntityAsTable(){
        echo "<table border='2'>";
        echo '<caption>Cryptocurrencies List</caption>';
        echo "<th>[ID]</th><th>[NAME]</th><th>[IMAGE]</th><th>[ORIGIN]</th><th>[PRICE]</th>";
        echo "<tr align='center'>";
        echo "<td>[".$this->getId()."]</td>";
        echo "<td>[".$this->getName()."]</td>";
        echo "<td>[".$this->getImage()."]</td>";
        echo "<td>[".$this->getOrigin()."]</td>";
        echo "<td>[".$this->getPrice()."]</td>";
        echo "</tr>";
        echo "</table>" ;
    }


    public static function insertEntity($entity){
        $objDataAccess = DataAccess::getInstance();
        $sql = "INSERT INTO `currencies` (`name`, `image`, `origin`, `price`) VALUES (:name, :image, :origin, :price)";
        $query = $objDataAccess->prepareQuery($sql);
        $query->bindParam(':name', $entity->getName());
        $query->bindParam(':image', $entity->getImage());
        $query->bindParam(':origin', $entity->getOrigin());
        $query->bindParam(':price', $entity->getPrice());
        $query->execute();

        return $objDataAccess->getLastInsertedId();
    }


    public static function updateEntity($entity){
        $objDataAccess = DataAccess::getInstance();
        $sql = "UPDATE `currencies` 
        SET `name` = :name, `image` = :image, `origin` = :origin, `price` = :price 
        WHERE `id` = :id";
        $query = $objDataAccess->prepareQuery($sql);
        $query->bindParam(':id', $entity->getId());
        $query->bindParam(':name', $entity->getName());
        $query->bindParam(':image', $entity->getImage());
        $query->bindParam(':origin', $entity->getOrigin());
        $query->bindParam(':price', $entity->getPrice());
        $query->execute();

        return $query->rowCount();
    }

    public static function deleteEntity($entity){
        $objDataAccess = DataAccess::getInstance();
        $sql = "DELETE FROM `currencies` WHERE `id` = :id";
        $query = $objDataAccess->prepareQuery($sql);
        $query->bindParam(':id', $entity->getId());
        $query->execute();

        return $query->rowCount();
    }

 
    public static function getEntityById($id){
        $objDataAccess = DataAccess::getInstance();
        $sql = "SELECT * FROM `currencies` WHERE id = :id";
        $query = $objDataAccess->prepareQuery($sql);
        $query->bindParam(':id', $id);
        $query->execute();

        $entity = $query->fetchObject('Currency');
        if(is_null($entity)){
            throw new Exception("The entity doesn't exist.");
        }
        
        return $entity;
    }


    public static function getEntiyByImage($path){
        $objDataAccess = DataAccess::getInstance();
        $sql = "SELECT * FROM `currencies` WHERE image = :image";
        $query = $objDataAccess->prepareQuery($sql);
        $query->bindParam(':image', $path, PDO::PARAM_STR);
        $query->execute();

        $entity = $query->fetchObject('Currency');
        if (is_null($entity)) {
            throw new Exception("The entity doesn't exist.");
        }

        return $entity;
    }

    public static function getAllEntities(){
        $objDataAccess = DataAccess::getInstance();
        $sql = "SELECT * FROM `currencies`";
        $query = $objDataAccess->prepareQuery($sql);
        $query->execute();

        $entities = $query->fetchAll(PDO::FETCH_CLASS, 'Currency');
        return $entities;
    }

    public static function getEntitiesByCountry($country){
        $objDataAccess = DataAccess::getInstance();
        $sql = "SELECT * FROM `currencies` WHERE origin = :origin";
        $query = $objDataAccess->prepareQuery($sql);
        $query->bindParam(':origin', $country);
        $query->execute();

        $entities = $query->fetchAll(PDO::FETCH_CLASS, 'Currency');
        return $entities;
    }


    public static function getEntitiesByOrigin($origin){
        $objDataAccess = DataAccess::getInstance();
        $sql = "SELECT * FROM `currencies` WHERE origin = :origin";
        $query = $objDataAccess->prepareQuery($sql);
        $query->bindParam(':origin', $origin);
        $query->execute();

        $entities = $query->fetchAll(PDO::FETCH_CLASS, 'Currency');
        return $entities;
    }
}