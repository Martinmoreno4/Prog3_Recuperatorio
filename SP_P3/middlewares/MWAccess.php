<?php


use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class MWAccess{
    
    public function validateToken($request, $rHandler){
        $header = $request->getHeaderLine('Authorization');
        $response = new Response();
        if (!empty($header)) {
            $token = trim(explode("Bearer", $header)[1]);
            JWTAuthenticator::verifyToken($token);
            $response = $rHandler->handle($request);
        } else {
            $response->getBody()->write(json_encode(array("Token error" => "You need the token")));
            $response = $response->withStatus(401);
        }
        return  $response->withHeader('Content-Type', 'application/json');
    }

    public function isAdmin($request, $handler) {
        $header = $request->getHeaderLine('authorization');
        $response = new Response();
        if (!empty($header)) {
            $token = trim(explode("Bearer", $header)[1]);
            $data = JWTAuthenticator::getTokenData($token);
            if ($data->type == "Admin") {
                $response = $handler->handle($request);
            } else {
                $response->getBody()->write(json_encode(array("error" => "Only admin has access")));
                $response = $response->withStatus(401);
            }
        } else {
            $response->getBody()->write(json_encode(array("Admin error" => "You need the token as Admin")));
            $response = $response->withStatus(401);
        }

        return $response->withHeader('Content-Type', 'application/json');
    }


    public function isAdminOrCustomer($request, $handler) {
        $header = $request->getHeaderLine('authorization');
        $response = new Response();
        try {
            if (!empty($header)) {
                $token = trim(explode("Bearer", $header)[1]);
                $data = JWTAuthenticator::getTokenData($token);
                if ($data->type == "Customer" || $data->type == "Admin") {
                    if ($data->type == "Customer") {
                        // Verificar token de la compra
                        JWTAuthenticator::verifyToken($token);
                        $response = $handler->handle($request);
                    }else{
                        $response = $handler->handle($request);
                    }
                } else {
                    $response->getBody()->write(json_encode(array("error" => "Only registered personnel have access")));
                    $response = $response->withStatus(401);
                }
            } else {
                $response->getBody()->write(json_encode(array("error" => "You need the token")));
                $response = $response->withStatus(401);
            }
        } catch (\Throwable $th) {
            echo $th->getMessage();
        }
        return $response->withHeader('Content-Type', 'application/json');
    }
}
