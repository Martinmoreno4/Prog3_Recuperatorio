<?php


require_once './db/DataAccess.php';

class Sale {

    public $id;
    public $date;
    public $crypto_name;
    public $amount;
    public $customer;
    public $user;
    public $image;

    public function __construct() {}

     public static function createEntity($date, $crypto_name, $amount, $customer, $user) {
        $sale = new Sale();
        $sale->setDate($date);
        $sale->setCryptoName($crypto_name);
        $sale->setAmount($amount);
        $sale->setCustomer($customer);
        $sale->setUser($user);

        return $sale;
     }

    //--- Getters ---//


    public function getId() {
        return $this->id;
    }


    public function getDate() {
        return $this->date;
    }

  
    public function getCryptoName() {
        return $this->crypto_name;
    }


    public function getAmount() {
        return $this->amount;
    }

    public function getCustomer() {
        return $this->customer;
    }


    public function getUser() {
        return $this->user;
    }

 
    public function getImage() {
        return $this->image;
    }

    //--- Setters ---//

 
    public function setId($id) {
        $this->id = $id;
    }

 
    public function setDate($date) {
        $this->date = $date;
    }

    public function setCryptoName($crypto_name) {
        $this->crypto_name = $crypto_name;
    }

    public function setAmount($amount) {
        $this->amount = $amount;
    }

    public function setCustomer($customer) {
        $this->customer = $customer;
    }

    public function setUser($user) {
        $this->user = $user;
    }

    
    public function setImage($image) {
        $this->image = $image;
    }

    public function replaceDate(){
        $date = str_replace(" ", "__", (string)$this->getDate());
        $date = str_replace(":", "_", $date);
        return $date;
    }


    public static function printDataAsTable($listObjects){
        echo "<table border='2'>";
        echo '<caption>Sales List</caption>';
        echo "<th>[ID]</th><th>[DATE]</th><th>[CRYPTO]</th><th>[AMOUNT]</th><th>[CUSTOMER]</th><th>[USER]</th><th>[IMAGE]</th>";
        foreach($listObjects as $object){
            echo "<tr align='center'>";
            echo "<td>[".$object->getId()."]</td>";
            echo "<td>[".$object->getDate()."]</td>";
            echo "<td>[".$object->getCryptoName()."]</td>";
            echo "<td>[".$object->getAmount()."]</td>";
            echo "<td>[".$object->getCustomer()."]</td>";
            echo "<td>[".$object->getUser()."]</td>";
            echo "<td>[".$object->getImage()."]</td>";
            echo "</tr>";
        }
        echo "</table>" ;
    }


    public function printSingleEntityAsTable(){
        echo "<table border='2'>";
        echo '<caption>Sales List</caption>';
        echo "<th>[ID]</th><th>[DATE]</th><th>[CRYPTO]</th><th>[AMOUNT]</th><th>[CUSTOMER]</th><th>[USER]</th><th>[IMAGE]</th>";
        echo "<tr align='center'>";
        echo "<td>[".$this->getId()."]</td>";
        echo "<td>[".$this->getDate()."]</td>";
        echo "<td>[".$this->getCryptoName()."]</td>";
        echo "<td>[".$this->getAmount()."]</td>";
        echo "<td>[".$this->getCustomer()."]</td>";
        echo "<td>[".$this->getUser()."]</td>";
        echo "<td>[".$this->getImage()."]</td>";
        echo "</tr>";
        echo "</table>" ;
    }

    //--- PDO Methods ---//


    public static function insertEntity($entity){
        $objDataAccess = DataAccess::getInstance();
        $sql = "INSERT INTO `sales` (date, crypto_name, amount, customer, user, image) 
        VALUES (:date, :crypto_name, :amount, :customer, :user, :image)";
        $query = $objDataAccess->prepareQuery($sql);
        $query->bindParam(':date', $entity->getDate());
        $query->bindParam(':crypto_name', $entity->getCryptoName());
        $query->bindParam(':amount', $entity->getAmount());
        $query->bindParam(':customer', $entity->getCustomer());
        $query->bindParam(':user', $entity->getUser());
        $query->bindParam(':image', $entity->getImage());
        $query->execute();

        return $objDataAccess->getLastInsertedId();
    }

    public static function updateEntity($entity)
    {
        $objDataAccess = DataAccess::getInstance();
        $sql = "UPDATE `sales` 
        SET date = :date, crypto_name = :crypto_name, amount = :amount, customer = :customer, user = :user, image = :image 
        WHERE id = :id";
        $query = $objDataAccess->prepareQuery($sql);
        $query->bindParam(':id', $entity->getId());
        $query->bindParam(':date', $entity->getDate());
        $query->bindParam(':crypto_name', $entity->getCryptoName());
        $query->bindParam(':amount', $entity->getAmount());
        $query->bindParam(':customer', $entity->getCustomer());
        $query->bindParam(':user', $entity->getUser());
        $query->bindParam(':image', $entity->getImage());
        $query->execute();

        return $query->rowCount();
    }


    public static function deleteEntity($entity)
    {
        $objDataAccess = DataAccess::getInstance();
        $sql = "DELETE FROM `sales` WHERE id = :id";
        $query = $objDataAccess->prepareQuery($sql);
        $query->bindParam(':id', $entity->getId());
        $query->execute();

        return $query->rowCount();
    }


    public static function getEntityById($id)
    {
        $objDataAccess = DataAccess::getInstance();
        $sql = "SELECT * FROM `sales` WHERE id = :id";
        $query = $objDataAccess->prepareQuery($sql);
        $query->bindParam(':id', $id);
        $query->execute();

        $entity = $query->fetchObject('Sale');
        if(is_null($entity)){
            throw new Exception("The entity doesn't exist.");
        }
        
        return $entity;
    }

    public static function getAllEntities()
    {
        $objDataAccess = DataAccess::getInstance();
        $sql = "SELECT * FROM `sales`";
        $query = $objDataAccess->prepareQuery($sql);
        $query->execute();

        $entities = $query->fetchAll(PDO::FETCH_CLASS, 'Sale');
        return $entities;
    }

    public static function getEntitiesByCountryBetween($country, $start, $end)
    {
        $objDataAccess = DataAccess::getInstance();
        $sql = "SELECT s.id, s.date, s.crypto_name, s.amount, s.customer, s.user, s.image FROM `sales` AS s
        INNER JOIN `currencies` AS c
        ON c.origin = :country AND s.crypto_name = c.name
        WHERE s.date BETWEEN :dateFrom AND :dateTo;";
        $query = $objDataAccess->prepareQuery($sql);
        $query->bindParam(':country', $country);
        $query->bindParam(':dateFrom', $start);
        $query->bindParam(':dateTo', $end);
        $query->execute();

        $entities = $query->fetchAll(PDO::FETCH_CLASS, 'Sale');
        return $entities;
    }

}