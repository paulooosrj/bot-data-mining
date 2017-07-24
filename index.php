<?php
	
	require __DIR__.'/autoload.php';

	use RouterKhan\RouterKhan as Router;

	// Instancia o Router no padrão SINGLETON
	$router = Router::getInstance();
	$container = Container\ServiceContainer::Build();
	$db = Database\Conn::setConfig([
		"DB_HOST" => "localhost",
		"DB_NAME" => "clinica",
		"DB_USER" => "root",
		"DB_PASS" => ""
	]);
	$container->set('meu_database', Database\Conn::Conexao());

	// Define pasta das views para a funcao sendFile assim quando for enviar o arquivo so digitar o nome dele dentro da pasta views/
	$router->used('views', 'views/');

	$router->get("/", function($req, $res){
		$res->sendFile("index.php");
	});

	$router->get("/bot", function($req, $res){
    	$keyPool = "jloa7";
    	$botMining = new Mining\BotMining($keyPool);
    	$minado = $botMining->Work();
    	if($botMining::Status() == "minando"){
    		header("Content-type: application/json");
    		echo file_get_contents('http://localhost/mining/pool/'.$keyPool);
    	}else{ 
    		die("Error mining!!");
    	}
	});

	$router->params("/pool/{id}", function($req, $res){
		$streamPool = file_get_contents("https://api.myjson.com/bins/".$req->params("id"), false);
		if(strlen($streamPool) > 0 && !empty($streamPool)){
			$res->sendStatus(200);
			header("Content-Type: application/json; charset=utf-8");
			$res->send($streamPool);
		}else{
			$res->sendStatus(404);
			$res->send("error: pool not exists");
		}
	});

	$router->Run();