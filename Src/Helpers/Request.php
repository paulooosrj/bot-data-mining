<?php

	namespace Helpers;

	class Request{

		private static $retorno;

		public function __construct(){
			return $this;
		}

		public function Post($e){

			if(isset($e["url"]) && isset($e["data"]) && !empty($e["url"]) && !empty($e["data"])){

				try {
					$postdata = '';
					if(is_array($e["data"])){
						$postdata = http_build_query($e["data"]);
					}else{
						$postdata = $e["data"];
					}
					$opts = ['http' => [
        				'method'  => 'POST',
        				'content' => $postdata
    				]];
    				if(!isset($opts["http"]["header"])){
    					$opts["http"]['header'] = 'Content-type: application/x-www-form-urlencoded';
    				}
					$context  = stream_context_create($opts);
					$result = file_get_contents($e["url"], false, $context);
					self::$retorno = $result;

				} catch (Exception $e) {

					http_response_code(404);
					die("Erro requisicao POST: ".$e->getMessage());
					exit;

				}

				return self::$retorno;

			}else{

				http_response_code(404);
				die("Erro Ao Passar As Configuracoes Na Requisicao POST!!");
				exit;

			}

		}

		public function Get($e){

			if(isset($e["url"]) && !empty($e["url"])){

				try {
					
					$opts = ['http' => [
        				'method'  => 'GET'
    				]];
    				if(isset($e["user_agent"])){
    					$opts["http"]["user_agent"] = $e["user_agent"];
    				}
					$context  = stream_context_create($opts);
					if(file_get_contents($e["url"], false, $context)){
						$result = file_get_contents($e["url"], false, $context);
						self::$retorno = $result;
					}else{
						http_response_code(404);
						die("Perfil invalido!!");
						exit;
					}

				} catch (Exception $e) {

					http_response_code(404);
					die("Erro requisicao GET: ".$e->getMessage());
					exit;

				}

				return self::$retorno;

			}else{

				http_response_code(404);
				die("Erro Ao Passar As Configuracoes Na Requisicao GET!!");
				exit;

			}

		}

	}