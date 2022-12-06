<?php

require_once './db/AccesoDatos.php';

class CriptoMoneda
{

    public $id;
    public $nombre; 
    public $foto; 
    public $nacionalidad; 
    public $precio; 

    public function __construct(){}

    /**
     * Creates a entity.
     *
     * @param string $nombre Nombre of the criptoMoneda.
     * @param string $foto foto of the criptoMoneda.
     * @param string $nacionalidad nacionalidad of the criptoMoneda.
     * @param float $precio Precio of the criptoMoneda.
     * @return CriptoMoneda The created entity.
     */
    public static function createEntity($nombre, $foto, $nacionalidad, $precio){
        $criptoMoneda = new CriptoMoneda();
        $criptoMoneda->setNombre($nombre);
        $criptoMoneda->setFoto($foto); 
        $criptoMoneda->setNacionalidad($nacionalidad); 
        $criptoMoneda->setPrecio($precio); 
        return $criptoMoneda;
    }

    //--- Getters ---//

    public function getId()
    {
        return $this->id;
    }

    public function getNombre()
    {
        return $this->nombre;
    }

    public function getFoto()
    { 
        return $this->foto;
    }

    public function getNacionalidad()
    { 
        return $this->nacionalidad;
    }

    public function getPrecio()
    {
        return $this->precio;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }

    public function setFoto($foto)
    {
        $this->foto = $foto;
    }

    public function setNacionalidad($nacionalidad)
    {
        $this->nacionalidad = $nacionalidad;
    }

    public function setPrecio($precio)
    {
        $this->precio = $precio;
    }


    public static function printDataAsTable($listObjects)
    {
        echo "<table border='2'>";
        echo '<caption>Cryptocurrencies List</caption>';
        echo "<th>[ID]</th><th>[Nombre]</th><th>[foto]</th><th>[nacionalidad]</th><th>[Precio]</th>";
        foreach($listObjects as $object)
        {
            echo "<tr align='center'>";
            echo "<td>[".$object->getId()."]</td>";
            echo "<td>[".$object->getNombre()."]</td>";
            echo "<td>[".$object->getFoto()."]</td>";
            echo "<td>[".$object->getNacionalidad()."]</td>";
            echo "<td>[".$object->getPrecio()."]</td>";
            echo "</tr>";
        }
        echo "</table>" ;
    }
    public function printSingleEntityAsTable()
    {
        echo "<table border='2'>";
        echo '<caption>Cryptocurrencies List</caption>';
        echo "<th>[ID]</th><th>[Nombre]</th><th>[foto]</th><th>[nacionalidad]</th><th>[Precio]</th>";
        echo "<tr align='center'>";
        echo "<td>[".$this->getId()."]</td>";
        echo "<td>[".$this->getNombre()."]</td>";
        echo "<td>[".$this->getFoto()."]</td>";
        echo "<td>[".$this->getNacionalidad()."]</td>";
        echo "<td>[".$this->getPrecio()."]</td>";
        echo "</tr>";
        echo "</table>" ;
    }


    public static function insertEntity($entity)
    {
        $objDataAccess = DataAccess::getInstance();
        $sql = "INSERT INTO `criptomonedas` (`nombre`, `foto`, `nacionalidad`, `precio`) VALUES (:nombre, :foto, :nacionalidad, :precio)";
        $query = $objDataAccess->prepareQuery($sql);
        $query->bindParam(':nombre', $entity->getNombre());
        $query->bindParam(':foto', $entity->getFoto());
        $query->bindParam(':nacionalidad', $entity->getNacionalidad());
        $query->bindParam(':precio', $entity->getPrecio());
        $query->execute();

        return $objDataAccess->getLastInsertedId();
    }

    public static function updateEntity($entity)
    {
        $objDataAccess = DataAccess::getInstance();
        $sql = "UPDATE `criptomonedas` 
        SET `nombre` = :nombre, `foto` = :foto, `nacionalidad` = :nacionalidad, `precio` = :precio 
        WHERE `id` = :id";
        $query = $objDataAccess->prepareQuery($sql);
        $query->bindParam(':id', $entity->getId());
        $query->bindParam(':nombre', $entity->getNombre());
        $query->bindParam(':foto', $entity->getFoto());
        $query->bindParam(':nacionalidad', $entity->getNacionalidad());
        $query->bindParam(':precio', $entity->getPrecio());
        $query->execute();

        return $query->rowCount();
    }

    public static function deleteEntity($entity)
    {
        $objDataAccess = DataAccess::getInstance();
        $sql = "DELETE FROM `criptomonedas` WHERE `id` = :id";
        $query = $objDataAccess->prepareQuery($sql);
        $query->bindParam(':id', $entity->getId());
        $query->execute();

        return $query->rowCount();
    }

    public static function getEntityById($id){
        $objDataAccess = DataAccess::getInstance();
        $sql = "SELECT * FROM `criptomonedas` WHERE id = :id";
        $query = $objDataAccess->prepareQuery($sql);
        $query->bindParam(':id', $id);
        $query->execute();

        $entity = $query->fetchObject('CriptoMoneda');
        if(is_null($entity)){
            throw new Exception("The entity doesn't exist.");
        }
        
        return $entity;
    }

    public static function getEntiyByfoto($path)
    {
        $objDataAccess = DataAccess::getInstance();
        $sql = "SELECT * FROM `criptomonedas` WHERE foto = :foto";
        $query = $objDataAccess->prepareQuery($sql);
        $query->bindParam(':foto', $path, PDO::PARAM_STR);
        $query->execute();

        $entity = $query->fetchObject('CriptoMoneda');
        if (is_null($entity)) {
            throw new Exception("The entity doesn't exist.");
        }

        return $entity;
    }

    public static function getAllEntities()
    {
        $objDataAccess = DataAccess::getInstance();
        $sql = "SELECT * FROM `criptomonedas`";
        $query = $objDataAccess->prepareQuery($sql);
        $query->execute();

        $entities = $query->fetchAll(PDO::FETCH_CLASS, 'CriptoMoneda');
        return $entities;
    }

    public static function getEntitiesByCountry($pais) 
    {
        $objDataAccess = DataAccess::getInstance();
        $sql = "SELECT * FROM `criptomonedas` WHERE nacionalidad = :nacionalidad";
        $query = $objDataAccess->prepareQuery($sql);
        $query->bindParam(':nacionalidad', $pais);
        $query->execute();

        $entities = $query->fetchAll(PDO::FETCH_CLASS, 'CriptoMoneda');
        return $entities;
    }

    public static function getEntitiesBynacionalidad($nacionalidad){
        $objDataAccess = DataAccess::getInstance();
        $sql = "SELECT * FROM `criptomonedas` WHERE nacionalidad = :nacionalidad";
        $query = $objDataAccess->prepareQuery($sql);
        $query->bindParam(':nacionalidad', $nacionalidad);
        $query->execute();
        
        $entities = $query->fetchAll(PDO::FETCH_CLASS, 'CriptoMoneda');
        return $entities;
    }
}