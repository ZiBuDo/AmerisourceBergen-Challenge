<?php 

$log = "/home/mindsumo/tech/error.log";
$GLOBALS['log'] = $log;

function connect(){
	$config = json_decode(readFileInput('/home/mindsumo/AIG/assets/php/sql.cfg'),true);
	$password = $config[1];
	$username = $config[0];
	try {
    $conn = new PDO("mysql:host=localhost;dbname=Tech;charset=utf8mb4;charset=utf8mb4", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}catch(PDOException $e){
		appendFile($log, "Error: " . $e->getMessage() . "\n");
	}
	return $conn;
}


?>