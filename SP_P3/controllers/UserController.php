<?php


require_once './models/User.php';

class UserController extends User {

    /**
     * Register a new user into the database
     * 
     * @param Request $request The request object
     * @param Response $response The response object
     * @param mixed $args The arguments
     * @return Response The response object
     */
    public static function registerUser($request, $response, $args){
        $data = $request->getParsedBody();
        var_dump($data);
        if(array_key_exists('username', $data) 
        && array_key_exists('password', $data)){
            if (array_key_exists('type', $data)) {
                $user = User::createEntity($data['username'], $data['password'], $data['type']);
            } else {
                $user = User::createEntity($data['username'], $data['password']);
            }

            $user_id = User::insertEntity($user);
            if ($user_id > 0) {

                $user = USER::getEntityById($user_id);
                echo "<h2>User created successfully</h2><br>";
                $user->printSingleEntityAsTable();

                $payload = json_encode(array("message" => "User Inserted"));
                $response->getBody()->write("User created successfully");
            }
        }else{
            $payload = json_encode(array("message" => "User not inserted"));
            $response->getBody()->write("User not created");
        }
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    /**
     * Verifies if the user exists in the database, then generates a token for the user
     *
     * @param Request $request The request object
     * @param Response $response The response object
     * @param mixed $args The arguments
     * @return Response The response object
     */
    public function verifyUser($request, $response, $args){
        $params = $request->getParsedBody();
        $username = $params['username'];
        $pass = $params['password'];
        
        $user = User::getEntityByUsername($username);
        $payload = json_encode(array('status' => 'Invalid User'));
        
        if(!is_null($user)){
            if(password_verify($pass, $user->getPassword())){
                $userData = array(
                    'id' => $user->getId(),
                    'username' => $user->getUsername(),
                    'password' => $user->getPassword(),
                    'type' => $user->getType());
                $payload = json_encode(array('Token' => JWTAuthenticator::createToken($userData), 'response' => 'OK', 'UserType' => $user->getType()));
            }
        }
        
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
}

?>
