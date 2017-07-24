<?php
	
	require __DIR__.'/autoload.php';

	use RouterKhan\RouterKhan as Router;

	// Instancia o Router no padrão SINGLETON
	$router = Router::getInstance();
	$container = Container\ServiceContainer::Build();

	// Define pasta das views para a funcao sendFile assim quando for enviar o arquivo so digitar o nome dele dentro da pasta views/
	$router->used('views', 'views/');

	$router->get("/", function($req, $res){
		// Rota Padrão Para Gerar Sua $keyPool
		$res->sendFile("index.php");
	});

	$router->get("/bot", function($req, $res){
    		$keyPool = "SUA KEY GERADA NA PAGINA : index.php na rota padrão acima desta";
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
