<!DOCTYPE html>
<html lang="pt-BR">
<head>
	<meta charset="UTF-8">
	<title>Gerador de Keys Pools</title>
	<link rel="shootcut icon" href="https://st2.depositphotos.com/1001599/12081/v/950/depositphotos_120813452-stock-illustration-miner-working-with-pickaxe-vector.jpg">
	<style>
		@import url(https://fonts.googleapis.com/css?family=Roboto);*{margin:0;padding:0;font-family:Roboto,sans-serif}body{height:100vh;width:100%;background:url(http://i.imgur.com/29YJqlO.png) center center no-repeat fixed;-webkit-background-size:cover;-moz-background-size:cover;-o-background-size:cover;background-size:cover}.flex-container{display:flex;justify-content:center;align-items:center}.container{width:40%;height:40vh;flex-direction:column}.btn{height:55px;width:250px;border-radius:3px;font-size:18px;background:#1abc9c;color:#ecf0f1;cursor:pointer;border:0;margin-top:10px;box-shadow:0 3px 6px rgba(0,0,0,.16),0 3px 6px rgba(0,0,0,.23);outline:0}.key{height:60px;width:400px;border-radius:3px;border:0;margin-bottom:10px;font-size:20px;text-align:center;color:#000!important;box-shadow:0 10px 20px rgba(0,0,0,.19),0 6px 6px rgba(0,0,0,.23)}
	</style>
</head>
<body class="flex-container">
	<div class="container flex-container">
		<input type="text" class="key" disabled placeholder="Gere a key para usar a Pool">
		<button class="btn">Gerar Key</button>
	</div>	
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
	<script>
		$(document).ready(function(){
			$(".btn").click(function(){
				$.ajax({
        			url: "https://api.myjson.com/bins",
        			type: "POST",
        			data: JSON.stringify({}),
        			contentType: "application/json; charset=utf-8",
        			dataType: "json",
        			success: function (data, textStatus, jqXHR) {
        				var key = data["uri"].split('/')[4];
            			$(".key").val(key);
        			}
    			});
			});
		});
	</script>
</body>
</html>