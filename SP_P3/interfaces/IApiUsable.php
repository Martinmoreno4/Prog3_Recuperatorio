<?php


interface IApiUsable{
	public function GetOneById($request, $response, $args);
	public function GetAll($request, $response, $args);
	public function LoadOne($request, $response, $args);
	public function DeleteOne($request, $response, $args);
	public function ModifyOne($request, $response, $args);
}
