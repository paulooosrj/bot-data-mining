<?php

	namespace Mining\Helpers;
	use Mining\Manipulate as Manipulate;

	class FacebookMining{

		private $request;
		private static $gen_id = 0;

		public function __construct(){
			$this->request = new \Helpers\Request();
			return $this;
		}

    public function encrypt($c){
      return hash("sha256", $c);
    }

		public function trataByte($byte){
    		return utf8_encode(preg_replace(array("/(á|à|ã|â|ä)/","/(Á|À|Ã|Â|Ä)/","/(é|è|ê|ë)/","/(É|È|Ê|Ë)/","/(í|ì|î|ï)/","/(Í|Ì|Î|Ï)/","/(ó|ò|õ|ô|ö)/","/(Ó|Ò|Õ|Ô|Ö)/","/(ú|ù|û|ü)/","/(Ú|Ù|Û|Ü)/","/(ñ)/","/(Ñ)/"),explode(" ","a A e E i I o O u U n N"), $byte));
		}

		public function genPessoa(){
      $file_contents = file_get_contents("http://www.wjr.eti.br/nameGenerator/index.php?q=1&o=json");
			self::$gen_id = mt_rand(1000, 10000).mt_rand(1000, 10000);
      $nome = $this->trataByte(json_decode($file_contents)[0]);
     	if(strlen($nome) > 0 && !empty($nome)){
     			return [
     				"perfil" => $nome,
     				"generate_id" => self::$gen_id,
            "mining_date" => date("d/m/Y"),
            "mining_hash" => $this->encrypt(self::$gen_id.date("d/m/Y"))
     			];
     		}else{
     			die("Erro ao pegar Usuario. Generate id : ".self::$gen_id);
     		}
		}

    public function fbSearch($name){
            $name = (string) urlencode(utf8_encode($name));
            $fbApi = "https://www.google.com.br/search?sclient=psy-ab&biw=1366&bih=662&noj=1&q=pt-br.facebook.com/people/{$name}&oq=pt-br.facebook.com/people/{$name}";
            $file_contents = file_get_contents($fbApi);
            $result = "";
            if(strlen($file_contents) > 0){
                if(preg_match("/<div class=\"g\">(.*?)<\/div>/s", $file_contents)){
                    preg_match_all('/<div class=\"g\">(.*?)<\/div>/s', $file_contents, $resultados);
                    $resultados = $resultados[0];
                    if(count($resultados) > 0){
                        preg_match_all('/<a href=\"(.*?)\">/s', $file_contents, $result);
                        $result = $result[1];
                        foreach ($result as $key => $resultado) {
                            if(preg_match("/pt-br.facebook.com/", $resultado) && preg_match("/url/", $resultado) && preg_match("/people/", $resultado)){
                                $resultado = str_replace("/url?q=", "", $resultado);
                                $resultado = preg_replace("/&(.*)/", "", $resultado);
                                $result[$key] = utf8_encode(htmlspecialchars_decode($resultado));
                            }else{
                                unset($result[$key]);
                            }
                        }
                    }else{
                        die("Nenhum resultado retornado!!");
                    }
                }else{
                    die("Nenhum resultado retornado!!");
                }
            }else{
                  die("Erro ao pesquisar!!");
            }
            return $result;
    }

		public function getUserFB($user_id){
			$conteudo = $this->request->Get([
				"url" => $user_id,
				"user_agent" => "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/59.0.3071.115 Safari/537.36"
			]);
			return (strlen($conteudo) > 0) ? $conteudo : "error";
		}

		function GetInfos($dom){

  			if(preg_match("/html/s", $dom)){
          		$manipulate = new Manipulate($dom);
  				    //file_put_contents('debug.txt', $dom);
          		// PEGA NOME
          		$nome = $this->trataByte($manipulate->find("div")->querySelector("#fb-timeline-cover-name")->nodeValue);
          		// PEGA IMAGEM
          		$imagem = '';
          		if($manipulate->find("img")->querySelector(".profilePic img")){
          			$imagem = $manipulate->find("img")->querySelector(".profilePic img")->getAttribute('src');
          		}
  				    // PEGA LINK DO PERFIL
          		$url_profile = $manipulate->find("a")->querySelector("._2nlw")->getAttribute("href");
          		// CONTAINER DE INFOS
          		$sobre = [];
          		if($manipulate->find("span")->querySelector("._50f5 _50f7")){
            		$sobre["cidade"] = $this->trataByte($manipulate->find("span")->querySelector("._50f5 _50f7")->nodeValue);
          		}

          		foreach ($manipulate->finder->query("//tbody") as $key => $o) {
              		$vs = [];
              		$ks = [];
              		foreach ($manipulate->finder->query("//div[@class='mediaPageName']", $o) as $key => $value) {
              			$vs[$key] = $this->trataByte($value->nodeValue);
              		}
              		foreach($manipulate->finder->query("//div[@class='labelContainer']", $o) as $k => $n){
                		$ks[$k] = $this->trataByte($n->nodeValue);
              		}
              		foreach ($vs as $key => $os) {
              			if(isset($ks[$key])){
              				$sobre[$ks[$key]] = $os;
              			}
              		}
          		}
          		//echo $dom;
  				    return [
            		// PEGA O NOME DA PESSOA
  					    "nome" => $nome,
            		// PEGA IMAGEM DE PERFIL
            		"imagem" => $imagem,
            		"url_profile" => $url_profile,
            		"sobre" => $sobre
  				    ];
  			}

  		}

		public function Receive(){

			$gen = $this->genPessoa();
      $user = end($this->fbSearch($gen["perfil"]));
			$mining = $this->getInfos($this->getUserFB($user));
			if(is_array($mining)){
				return [
          "block" => [
              "mining_id" => $gen["generate_id"],
              "mining_date" => $gen["mining_date"],
              "mining_hash" => $gen["mining_hash"],
              "mining_block" => $mining
          ]
        ];
			}else{
				return "error";
			}

		}

	}