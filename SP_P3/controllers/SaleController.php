<?php




require_once './models/Sale.php';
require_once './models/UploadManager.php';
require_once './interfaces/IApiUsable.php';

class SaleController extends Sale implements IApiUsable{

    public function LoadOne($request, $response, $args) {
        
        $directory = "./CryptoPictures/";
        $fileManager = new UploadManager($directory);

        $params = $request->getParsedBody();
        $file = $request->getUploadedFiles();

        if(array_key_exists('crypto_name', $params)
            && array_key_exists('amount', $params)
            && array_key_exists('customer', $params)
            && array_key_exists('user', $params)){
            
            $sale = Sale::createEntity(
                date('Y-m-d H:i:s'), 
                $params['crypto_name'], 
                $params['amount'], 
                $params['customer'], 
                $params['user']);

            $fileManager->saveFileIntoDir($sale, $file);
            $sale->setImage($fileManager->getNewFileName());
            var_dump($sale);
                $sale_id = Sale::insertEntity($sale);
            if ($sale_id > 0) {

                $sale = Sale::getEntityById($sale_id);
                echo "<h2>Sale created successfully</h2><br>";
                $sale->printSingleEntityAsTable();

                $payload = json_encode(array("message" => "Sale Inserted"));
                $response->getBody()->write("Sale created successfully");
            }else {
                $response->getBody()->write("Something failed while creating the Sale");
            }
        }else{
            $payload = json_encode(array("message" => "Parameters missing"));
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function GetOneById($request, $response, $args){
        
    }

    public function GetAllByCountry($request, $response, $args) {
        $country = $args['country'];
        $dateFrom = $args['from'];
        $dateTo = $args['to'];

        $sales = Sale::getEntitiesByCountryBetween($country, $dateFrom, $dateTo);
        if (!is_null($sales)) {

            echo "<h2>Sales found</h2><br>";
            Sale::printDataAsTable($sales);

            $payload = json_encode(array("Sales" => $sales));
        }else{
            $payload = json_encode(array("message" => "Sales not found"));
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function GetAllByCryptoName($request, $response, $args) {
        $params = $args['crypto_name'];

        $users = User::getEntitiesByCrypto($params);
        if (!is_null($users)) {

            echo "<h2>Users that bough ".$params."</h2><br>";
            User::printDataAsTable($users);

            $payload = json_encode(array("Users" => $users));
        }else{
            $payload = json_encode(array("message" => "Users not found"));
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function GetAll($request, $response, $args) {
        $sales = Sale::getAllEntities();
        if (isset($sales)) {

            echo "<h2>Sales found</h2><br>";
            Sale::printDataAsTable($sales);
            
            $payload = json_encode(array("Sales" => $sales));
        }else{
            $payload = json_encode(array("message" => "Sales not found"));
        }
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function DeleteOne($request, $response, $args) {}

    public function ModifyOne($request, $response, $args){}
}
