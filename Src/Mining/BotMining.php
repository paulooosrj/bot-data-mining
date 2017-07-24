<?php 

	namespace Mining;

	class BotMining{

		private static $status = "parado";
		private static $mining_service = [];
    private static $pool = null;

		public function __construct($pool = null){
      if($pool){
          self::$pool = $pool;
          $facebookMining = new Helpers\FacebookMining();
          self::$mining_service["facebookMining"] = $facebookMining->Receive();
      }else{
          die("Erro ao passar key do Pool!!");
      }
			return $this;
		}

		public static function Status(){
			return self::$status;
		}

    public function GetPool(){
        $key = self::$pool;
        $endpoint = "https://api.myjson.com/bins/{$key}";
        return (array) json_decode(file_get_contents($endpoint));
    }

    public function UpdatePool($newData = null){
        if($newData){
            $buffer = $this->GetPool();
            array_push($buffer, $newData);
            $key = self::$pool;
            $endpoint = "https://api.myjson.com/bins/{$key}";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $endpoint);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($buffer));
            $headers = array();
            $headers[] = "Accept: application/json";
            $headers[] = "Content-Type: application/json; charset=utf-8";
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            $result = curl_exec($ch);
            if (curl_errno($ch)) {
                return 'Error:' . curl_error($ch);
            }
            curl_close ($ch);
            return (count(json_decode($result)) > 0) ? "sucesso" : "error";
        }
        return "error";
    }

		public function Work(){

			if(count(self::$mining_service) > 0){
        self::$status = "minando";
        foreach (self::$mining_service as $key => $mining) {
            if($this->UpdatePool($mining) == "sucesso"){
                self::$status = "minando";
            }
        }
			}else{
				die("Error DataMining: ".self::$status);
			}
      
		}

  }