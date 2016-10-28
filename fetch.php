<?php
ini_set('memory_limit', '3000M');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'file.php';
require 'database.php';

$request = $_GET['request'];
$conn = connect();
$colors = array("rgba(255, 0, 0, .5)",
	"rgba(255, 128, 0, .5)",
	"rgba(255, 255, 0, .5)",
	"rgba(128, 255, 0, .5)",
	"rgba(0, 255, 0, .5)",
	"rgba(0, 255, 128, .5)",
	"rgba(0, 255, 255, .5)",
	"rgba(0, 128, 255, .5)",
	"rgba(0, 0, 255, .5)",
	"rgba(128, 0, 255, .5)",
	"rgba(204, 0, 255, .5)",
	"rgba(255, 0, 191, .5)",
	"rgba(255, 0, 64, .5)");
if($request == 'scatter'){
	$out = "scatterData = [";
	$stmt = $conn->prepare("SELECT DISTINCT `MAJOR_CAT_DESC` FROM `MajorProductCategory`");
	$stmt->execute();
	$majors = $stmt->fetchAll();
	$co = 0;
	foreach($majors as $m){
		$m = trim($m[0]);
		if($m != '*UNDEFINED*' && $m != '' && $m != 'RX' && $m != 'MANUAL OVERRIDE'){
			$out .= "{";
			$stmt = $conn->prepare("SELECT * FROM `ScatterPlot` WHERE `Major` = '$m' ORDER BY `Revenue` DESC");
			$stmt->execute();
			$rows = $stmt->rowCount();
			$limit = round($rows / 20);
			$stmt = $conn->prepare("SELECT * FROM `ScatterPlot` WHERE `Major` = '$m' ORDER BY `Revenue` DESC LIMIT $limit");
			$stmt->execute();
			$cat = $stmt->fetchAll();
			$rows = $stmt->rowCount();
			$a = $colors[$co];
			$out .= "name: '$m',color: '$a',data:[";
			$co++;
			if($rows > 0){
				foreach($cat as $c){
					$p = $c['Price'];
					$v = $c['Volume'];
					$out .= "[$v,$p],";
				}
				$out = substr($out,0,strlen($out)-1);
				$out .= "]},";
			}else{
				$out .= "]},";
			}
		}
	}
	if(count($majors > 0)){
		$out = substr($out,0,strlen($out)-1);
	}
	$out .= "]";
	echo $out;
}else if($request == "scatterCategory"){
	$type = $_GET["type"];
	$stmt = $conn->prepare("SELECT DISTINCT `Category` FROM `ScatterPlot` WHERE `Major` = '$type'");
	$stmt->execute();
	$cats = $stmt->fetchAll();
	$out = "";
	foreach($cats as $c){
		$c = $c[0];
		$out .= "<option value='$c'>$c</option>";
	}
	$bool = false;
	if($out != ""){
		$out .= "|";
		$bool = true;
	}
	$out .= "scatterData = [";
	$majors = $cats;
	$co = 0;
	foreach($majors as $m){
		$m = trim($m[0]);
		if($m != '*UNDEFINED*' && $m != '' && $m != 'RX' && $m != 'MANUAL OVERRIDE'){
			$out .= "{";
			$stmt = $conn->prepare("SELECT * FROM `ScatterPlot` WHERE `Category` = '$m' AND `Major` = '$type' ORDER BY `Revenue` DESC");
			$stmt->execute();
			$rows = $stmt->rowCount();
			$limit = round($rows / 20);
			$stmt = $conn->prepare("SELECT * FROM `ScatterPlot` WHERE `Category` = '$m' AND `Major` = '$type' ORDER BY `Revenue` DESC LIMIT $limit");
			$stmt->execute();
			$cat = $stmt->fetchAll();
			$rows = $stmt->rowCount();
			$a = $colors[$co];
			$out .= "name: '$m',color: '$a',data:[";
			$co++;
			if($co > 12){
				$co = 0;
			}
			if($rows > 0){
				foreach($cat as $c){
					$p = $c['Price'];
					$v = $c['Volume'];
					$out .= "[$v,$p],";
				}
				$out = substr($out,0,strlen($out)-1);
				$out .= "]},";
			}else{
				$out .= "]},";
			}
		}
	}
	if($bool){
		$out = substr($out,0,strlen($out)-1);
	}
	$out .= "]";
	echo $out;
}else if($request == "scatterSubcategory"){
	$type = $_GET["type"];
	$stmt = $conn->prepare("SELECT DISTINCT `Subcategory` FROM `ScatterPlot` WHERE `Category` = '$type'");
	$stmt->execute();
	$cats = $stmt->fetchAll();
	$out = "";
	foreach($cats as $c){
		$c = $c[0];
		$out .= "<option value='$c'>$c</option>";
	}
	$bool = false;
	if($out != ""){
		$out .= "|";
		$bool = true;
	}
	$out .= "scatterData = [";
	$majors = $cats;
	$co = 0;
	foreach($majors as $m){
		$m = trim($m[0]);
		if($m != '*UNDEFINED*' && $m != '' && $m != 'RX' && $m != 'MANUAL OVERRIDE'){
			$out .= "{";
			$stmt = $conn->prepare("SELECT * FROM `ScatterPlot` WHERE `Subcategory` = '$m' AND `Category` = '$type' ORDER BY `Revenue` DESC");
			$stmt->execute();
			$rows = $stmt->rowCount();
			$limit = round($rows / 20);
			$stmt = $conn->prepare("SELECT * FROM `ScatterPlot` WHERE `Subcategory` = '$m' AND `Category` = '$type' ORDER BY `Revenue` DESC LIMIT $limit");
			$stmt->execute();
			$cat = $stmt->fetchAll();
			$rows = $stmt->rowCount();
			$a = $colors[$co];
			$out .= "name: '$m',color: '$a',data:[";
			$co++;
			if($co > 12){
				$co = 0;
			}
			if($rows > 0){
				foreach($cat as $c){
					$p = $c['Price'];
					$v = $c['Volume'];
					$out .= "[$v,$p],";
				}
				$out = substr($out,0,strlen($out)-1);
				$out .= "]},";
			}else{
				$out .= "]},";
			}
		}
	}
	if($bool){
		$out = substr($out,0,strlen($out)-1);
	}
	$out .= "]";
	echo $out;
}else if($request == "scatterSegment"){
	$type = $_GET["type"];
	$stmt = $conn->prepare("SELECT DISTINCT `Segment` FROM `ScatterPlot` WHERE `Subcategory` = '$type'");
	$stmt->execute();
	$cats = $stmt->fetchAll();
	$out = "scatterData = [";
	$majors = $cats;
	$co = 0;
	foreach($majors as $m){
		$m = trim($m[0]);
		if($m != '*UNDEFINED*' && $m != '' && $m != 'RX' && $m != 'MANUAL OVERRIDE'){
			$out .= "{";
			$stmt = $conn->prepare("SELECT * FROM `ScatterPlot` WHERE `Segment` = '$m' AND `Subcategory` = '$type' ORDER BY `Revenue` DESC");
			$stmt->execute();
			$rows = $stmt->rowCount();
			$limit = round($rows / 4);
			$stmt = $conn->prepare("SELECT * FROM `ScatterPlot` WHERE `Segment` = '$m' AND `Subcategory` = '$type' ORDER BY `Revenue` DESC LIMIT $limit");
			$stmt->execute();
			$cat = $stmt->fetchAll();
			$rows = $stmt->rowCount();
			$a = $colors[$co];
			$out .= "name: '$m',color: '$a',data:[";
			$co++;
			if($co > 12){
				$co = 0;
			}
			if($rows > 0){
				foreach($cat as $c){
					$p = $c['Price'];
					$v = $c['Volume'];
					$out .= "[$v,$p],";
				}
				$out = substr($out,0,strlen($out)-1);
				$out .= "]},";
			}else{
				$out .= "]},";
			}
		}
	}
	if($out != "scatterData = ["){
		$out = substr($out,0,strlen($out)-1);
	}
	$out .= "]";
	echo $out;
}else if($request == "column"){
	$data2 = "columnCats = [";
	$data = "columnData = [";
	$stmt = $conn->prepare("SELECT * FROM `ColumnRange` WHERE `Level` = 'Major' ORDER BY `Name` ASC");
	$stmt->execute();
	$col = $stmt->fetchAll();
	foreach($col as $c){
		$major = $c["Name"];
		if($major != "" && $major != "PHOTO"){
			$data2 .= "'$major',";
			$x = $c["Low"];
			$y = $c["High"];
			if($x == $y){
				$x += -.25;
				$y += .25;
			}
			$data .= "[$x,$y],";
		}
	}
	if($data2 != "columnCats = ["){
		$data2 = substr($data2,0,strlen($data2)-1);
	}
	$data2 .= "];";
	if($data != "columnData = ["){
		$data = substr($data,0,strlen($data)-1);
	}
	$data .= "];";
	$out = $data . $data2;
	echo $out;
	
}else if($request == "columnCategory"){
	$type = $_GET["type"];
	$data3 = "";
	$data2 = "columnCats = [";
	$data = "columnData = [";
	$stmt = $conn->prepare("SELECT DISTINCT `Category` FROM `ScatterPlot` WHERE `Major` = '$type' ORDER BY `Category` ASC");
	$stmt->execute();
	$cats = $stmt->fetchAll();
	foreach($cats as $c){
		$c = $c[0];
		$stmt = $conn->prepare("SELECT * FROM `ColumnRange` WHERE `Level` = 'Category' AND `Name` = '$c' LIMIT 1");
		$stmt->execute();
		$result = $stmt->fetchAll()[0];
		if($c != ""){
			$data2 .= "'$c',";
			$x = $result["Low"];
			$y = $result["High"];
			if($x == $y){
				$x += -.25;
				$y += .25;
			}
			$data .= "[$x,$y],";
			$data3 .= "<option value='$c'>$c</option>";
		}
	}
	if($data2 != "columnCats = ["){
		$data2 = substr($data2,0,strlen($data2)-1);
	}
	$data2 .= "];";
	if($data != "columnData = ["){
		$data = substr($data,0,strlen($data)-1);
	}
	$data .= "];";
	$out = $data3 . "|" . $data . $data2;
	echo $out;
	
	
}else if($request == "columnSubcategory"){
	$type = $_GET["type"];
	$data3 = "";
	$data2 = "columnCats = [";
	$data = "columnData = [";
	$stmt = $conn->prepare("SELECT DISTINCT `Subcategory` FROM `ScatterPlot` WHERE `Category` = '$type' ORDER BY `Subcategory` ASC");
	$stmt->execute();
	$cats = $stmt->fetchAll();
	foreach($cats as $c){
		$c = $c[0];
		$stmt = $conn->prepare("SELECT * FROM `ColumnRange` WHERE `Level` = 'Subcategory' AND `Name` = '$c' LIMIT 1");
		$stmt->execute();
		$result = $stmt->fetchAll()[0];
		if($c != ""){
			$data2 .= "'$c',";
			$x = $result["Low"];
			$y = $result["High"];
			if($x == $y){
				$x += -.25;
				$y += .25;
			}
			$data .= "[$x,$y],";
			$data3 .= "<option value='$c'>$c</option>";
		}
	}
	if($data2 != "columnCats = ["){
		$data2 = substr($data2,0,strlen($data2)-1);
	}
	$data2 .= "];";
	if($data != "columnData = ["){
		$data = substr($data,0,strlen($data)-1);
	}
	$data .= "];";
	$out = $data3 . "|" . $data . $data2;
	echo $out;
	
	
}else if($request == "columnSubcategory"){
	$type = $_GET["type"];
	$data2 = "columnCats = [";
	$data = "columnData = [";
	$stmt = $conn->prepare("SELECT DISTINCT `Segment` FROM `ScatterPlot` WHERE `Subcategory` = '$type' ORDER BY `Segment` ASC");
	$stmt->execute();
	$cats = $stmt->fetchAll();
	foreach($cats as $c){
		$c = $c[0];
		$stmt = $conn->prepare("SELECT * FROM `ColumnRange` WHERE `Level` = 'Segment' AND `Name` = '$c' LIMIT 1");
		$stmt->execute();
		$result = $stmt->fetchAll()[0];
		if($c != ""){
			$data2 .= "'$c',";
			$x = $result["Low"];
			$y = $result["High"];
			if($x == $y){
				$x += -.25;
				$y += .25;
			}
			$data .= "[$x,$y],";
		}
	}
	if($data2 != "columnCats = ["){
		$data2 = substr($data2,0,strlen($data2)-1);
	}
	$data2 .= "];";
	if($data != "columnData = ["){
		$data = substr($data,0,strlen($data)-1);
	}
	$data .= "];";
	$out = $data . $data2;
	echo $out;

}else if($request == "bubble"){
	$out = "dataPoints = [";
	$bot25 = "{name:'30% - 50%',marker: {
			fillColor: {
				radialGradient: { cx: 0.4, cy: 0.3, r: 0.7 },
				stops: [
					[0, 'rgba(255,255,255,0.7)'],
					[1, Highcharts.Color(Highcharts.getOptions().colors[0]).setOpacity(0.8).get('rgba')]
				]
			}
		},data: [";
	$botmid35 = "{name:'15% - 30%',marker: {
			fillColor: {
				radialGradient: { cx: 0.4, cy: 0.3, r: 0.7 },
				stops: [
					[0, 'rgba(255,255,255,0.7)'],
					[1, Highcharts.Color(Highcharts.getOptions().colors[2]).setOpacity(0.8).get('rgba')]
				]
			}
		},data: [";
	$topmid35 = "{name:'5% - 15%',marker: {
			fillColor: {
				radialGradient: { cx: 0.4, cy: 0.3, r: 0.7 },
				stops: [
					[0, 'rgba(255,255,255,0.7)'],
					[1, Highcharts.Color(Highcharts.getOptions().colors[4]).setOpacity(0.8).get('rgba')]
				]
			}
		},data: [";
	$top5 = "{name:'Top 5%',marker: {
			fillColor: {
				radialGradient: { cx: 0.4, cy: 0.3, r: 0.7 },
				stops: [
					[0, 'rgba(255,255,255,0.7)'],
					[1, Highcharts.Color(Highcharts.getOptions().colors[6]).setOpacity(0.8).get('rgba')]
				]
			}
		},data: [";
	
	$stmt = $conn->prepare("SELECT DISTINCT `Major` FROM `ScatterPlot`");
	$stmt->execute();
	$majors = $stmt->fetchAll();
	
	foreach($majors as $m){
		$m = $m[0];
		$stmt = $conn->prepare("SELECT * FROM `ScatterPlot` WHERE `Major` = '$m' ORDER BY `Revenue` DESC");
		$stmt->execute();
		$rows = $stmt->rowCount();
		$limit = round($rows / 20);
		$stmt = $conn->prepare("SELECT * FROM `ScatterPlot` WHERE `Major` = '$m' ORDER BY `Revenue` DESC LIMIT $limit");
		$stmt->execute();
		$cat = $stmt->fetchAll();
		$price = 0;
		$volume = 0;
		$revenue = 0;
		foreach($cat as $c){
			$price += $c["Price"];
			$volume += $c["Volume"];
			$revenue += $c["Revenue"];
		}
		$price = round(($price / $limit),2);
		$volume = round(($volume / $limit),2);
		$revenue = round(($revenue / $limit),2);
		$top5 .= "[$volume,$price,$revenue],";
		
		$lim = $limit * 2;
		$stmt = $conn->prepare("SELECT * FROM `ScatterPlot` WHERE `Major` = '$m' ORDER BY `Revenue` DESC LIMIT $limit,$lim");
		$stmt->execute();
		$cat = $stmt->fetchAll();
		$price = 0;
		$volume = 0;
		$revenue = 0;
		foreach($cat as $c){
			$price += $c["Price"];
			$volume += $c["Volume"];
			$revenue += $c["Revenue"];
		}
		$price = round(($price / $lim),2);
		$volume = round(($volume / $lim),2);
		$revenue = round(($revenue / $lim),2);
		$topmid35 .= "[$volume,$price,$revenue],";
		
		$lim = $limit * 3;
		$a = $limit * 3;
		$stmt = $conn->prepare("SELECT * FROM `ScatterPlot` WHERE `Major` = '$m' ORDER BY `Revenue` DESC LIMIT $a,$lim");
		$stmt->execute();
		$cat = $stmt->fetchAll();
		$price = 0;
		$volume = 0;
		$revenue = 0;
		foreach($cat as $c){
			$price += $c["Price"];
			$volume += $c["Volume"];
			$revenue += $c["Revenue"];
		}
		$price = round(($price / $lim),2);
		$volume = round(($volume / $lim),2);
		$revenue = round(($revenue / $lim),2);
		$botmid35 .= "[$volume,$price,$revenue],";
		
		$lim = $limit * 4;
		$a = $limit * 6;
		$stmt = $conn->prepare("SELECT * FROM `ScatterPlot` WHERE `Major` = '$m' ORDER BY `Revenue` DESC LIMIT $a,$lim");
		$stmt->execute();
		$cat = $stmt->fetchAll();
		$price = 0;
		$volume = 0;
		$revenue = 0;
		foreach($cat as $c){
			$price += $c["Price"];
			$volume += $c["Volume"];
			$revenue += $c["Revenue"];
		}
		$price = round(($price / $lim),2);
		$volume = round(($volume / $lim),2);
		$revenue = round(($revenue / $lim),2);
		$bot25 .= "[$volume,$price,$revenue],";
		
	}
	
	$top5 = substr($top5,0,strlen($top5)-1);
	$top5 .= "]},";
	$topmid35 = substr($topmid35,0,strlen($topmid35)-1);
	$topmid35 .= "]},";
	$botmid35 = substr($botmid35,0,strlen($botmid35)-1);
	$botmid35 .= "]},";
	$bot25 = substr($bot25,0,strlen($bot25)-1);
	$bot25 .= "]}";
	
	$out .= $top5 . $topmid35 . $botmid35 . $bot25 . "]";
	echo $out;
	
	
}else if($request == "svm"){
	$test = array(array(0),array(1),array(2),array(3),array(4));
	$major = $_GET['major'];
	$cats = $_GET['cat'];
	$sub = $_GET['sub'];
	$seg = $_GET['seg'];
	
	//Top 0 - 5, 5 - 15, 15 - 30, 30 - 50, 50 - 100
	$m = $major;
	$top5 = array();
	$top15 = array();
	$top30 = array();
	$top50 = array();
	$bot50 = array();
	
	$stmt = $conn->prepare("SELECT * FROM `ScatterPlot` WHERE `Major` = :m ORDER BY `Revenue` DESC");
	$stmt->bindParam(':m', $m, PDO::PARAM_STR);
	$stmt->execute();
	$rows = $stmt->rowCount();
	$limit = round($rows / 20);
	$stmt = $conn->prepare("SELECT * FROM `ScatterPlot` WHERE `Major` = :m ORDER BY `Revenue` DESC LIMIT $limit");
	$stmt->bindParam(':m', $m, PDO::PARAM_STR);
	$stmt->execute();
	$cat = $stmt->fetchAll();
	foreach($cat as $c){
		if(isset($top5[round($c['Volume'])])){ $top5[round($c['Volume'])] = ($c["Price"] + $top5[round($c['Volume'])])/2;}else{$top5[round($c['Volume'])] = $c["Price"];}
	}
	$lim = $limit * 2;
	$stmt = $conn->prepare("SELECT * FROM `ScatterPlot` WHERE `Major` = :m ORDER BY `Revenue` DESC LIMIT $limit,$lim");
	$stmt->bindParam(':m', $m, PDO::PARAM_STR);
	$stmt->execute();
	$cat = $stmt->fetchAll();
	foreach($cat as $c){
		if(isset($top15[round($c['Volume'])])){ $top15[round($c['Volume'])] = ($c["Price"] + $top15[round($c['Volume'])])/2;}else{$top15[round($c['Volume'])] = $c["Price"];}
	}
	
	$lim = $limit * 3;
	$a = $limit * 3;
	$stmt = $conn->prepare("SELECT * FROM `ScatterPlot` WHERE `Major` = :m ORDER BY `Revenue` DESC LIMIT $a,$lim");
	$stmt->bindParam(':m', $m, PDO::PARAM_STR);
	$stmt->execute();
	$cat = $stmt->fetchAll();
	foreach($cat as $c){
		if(isset($top30[round($c['Volume'])])){ $top30[round($c['Volume'])] = ($c["Price"] + $top30[round($c['Volume'])])/2;}else{$top30[round($c['Volume'])] = $c["Price"];}
	}
	
	$lim = $limit * 4;
	$a = $limit * 6;
	$stmt = $conn->prepare("SELECT * FROM `ScatterPlot` WHERE `Major` = :m ORDER BY `Revenue` DESC LIMIT $a,$lim");
	$stmt->bindParam(':m', $m, PDO::PARAM_STR);
	$stmt->execute();
	$cat = $stmt->fetchAll();
	foreach($cat as $c){
		if(isset($top50[round($c['Volume'])])){ $top50[round($c['Volume'])] = ($c["Price"] + $top50[round($c['Volume'])])/2;}else{$top50[round($c['Volume'])] = $c["Price"];}
	}
	
	$lim = $limit * 10;
	$a = $limit * 10;
	$stmt = $conn->prepare("SELECT * FROM `ScatterPlot` WHERE `Major` = :m ORDER BY `Revenue` DESC LIMIT $a,$lim");
	$stmt->bindParam(':m', $m, PDO::PARAM_STR);
	$stmt->execute();
	$cat = $stmt->fetchAll();
	foreach($cat as $c){
		if(isset($bot50[round($c['Volume'])])){ $bot50[round($c['Volume'])] = ($c["Price"] + $bot50[round($c['Volume'])])/2;}else{$bot50[round($c['Volume'])] = $c["Price"];}
	}	
	$expVol = $_GET['vol'];
	$expPrice = $_GET['price'];
	array_unshift($top5, 4);
	array_unshift($top15, 3);
	array_unshift($top30, 2);
	array_unshift($top50, 1);
	array_unshift($bot50, 0);
	$data = array($bot50,$top50,$top30,$top15,$top5);
	$svm = new SVM();
	if($data == $test){
		$majR = -1;
	}else{
		$model = $svm->train($data);
		$data = array($expVol => $expPrice);
		$majR = $model->predict($data);
	}
	//top
	$m = $cats;
	$top5 = array();
	$top15 = array();
	$top30 = array();
	$top50 = array();
	$bot50 = array();
	
	$stmt = $conn->prepare("SELECT * FROM `ScatterPlot` WHERE `Category` = :m AND `Major` = :major ORDER BY `Revenue` DESC");
	$stmt->bindParam(':major', $major, PDO::PARAM_STR);
	$stmt->bindParam(':m', $m, PDO::PARAM_STR);
	$stmt->execute();
	$rows = $stmt->rowCount();
	$limit = round($rows / 20);
	$stmt = $conn->prepare("SELECT * FROM `ScatterPlot` WHERE `Category` = :m AND `Major` = :major ORDER BY `Revenue` DESC LIMIT $limit");
	$stmt->bindParam(':major', $major, PDO::PARAM_STR);
	$stmt->bindParam(':m', $m, PDO::PARAM_STR);
	$stmt->execute();
	$cat = $stmt->fetchAll();
	foreach($cat as $c){
		if(isset($top5[round($c['Volume'])])){ $top5[round($c['Volume'])] = ($c["Price"] + $top5[round($c['Volume'])])/2;}else{$top5[round($c['Volume'])] = $c["Price"];}
	}
	
	$lim = $limit * 2;
	$stmt = $conn->prepare("SELECT * FROM `ScatterPlot` WHERE `Category` = :m AND `Major` = :major ORDER BY `Revenue` DESC LIMIT $limit,$lim");
	$stmt->bindParam(':major', $major, PDO::PARAM_STR);
	$stmt->bindParam(':m', $m, PDO::PARAM_STR);
	$stmt->execute();
	$cat = $stmt->fetchAll();
	foreach($cat as $c){
		if(isset($top15[round($c['Volume'])])){ $top15[round($c['Volume'])] = ($c["Price"] + $top15[round($c['Volume'])])/2;}else{$top15[round($c['Volume'])] = $c["Price"];}
	}
	
	$lim = $limit * 3;
	$a = $limit * 3;
	$stmt = $conn->prepare("SELECT * FROM `ScatterPlot` WHERE `Category` = :m AND `Major` = :major ORDER BY `Revenue` DESC LIMIT $a,$lim");
	$stmt->bindParam(':major', $major, PDO::PARAM_STR);
	$stmt->bindParam(':m', $m, PDO::PARAM_STR);
	$stmt->execute();
	$cat = $stmt->fetchAll();
	foreach($cat as $c){
		if(isset($top30[round($c['Volume'])])){ $top30[round($c['Volume'])] = ($c["Price"] + $top30[round($c['Volume'])])/2;}else{$top30[round($c['Volume'])] = $c["Price"];}
	}
	
	$lim = $limit * 4;
	$a = $limit * 6;
	$stmt = $conn->prepare("SELECT * FROM `ScatterPlot` WHERE `Category` = :m AND `Major` = :major ORDER BY `Revenue` DESC LIMIT $a,$lim");
	$stmt->bindParam(':major', $major, PDO::PARAM_STR);
	$stmt->bindParam(':m', $m, PDO::PARAM_STR);
	$stmt->execute();
	$cat = $stmt->fetchAll();
	foreach($cat as $c){
		if(isset($top50[round($c['Volume'])])){ $top50[round($c['Volume'])] = ($c["Price"] + $top50[round($c['Volume'])])/2;}else{$top50[round($c['Volume'])] = $c["Price"];}
	}
	
	$lim = $limit * 10;
	$a = $limit * 10;
	$stmt = $conn->prepare("SELECT * FROM `ScatterPlot` WHERE `Category` = :m AND `Major` = :major ORDER BY `Revenue` DESC LIMIT $a,$lim");
	$stmt->bindParam(':major', $major, PDO::PARAM_STR);
	$stmt->bindParam(':m', $m, PDO::PARAM_STR);
	$stmt->execute();
	$cat = $stmt->fetchAll();
	foreach($cat as $c){
		if(isset($bot50[round($c['Volume'])])){ $bot50[round($c['Volume'])] = ($c["Price"] + $bot50[round($c['Volume'])])/2;}else{$bot50[round($c['Volume'])] = $c["Price"];}
	}	
	$expVol = $_GET['vol'];
	$expPrice = $_GET['price'];
	array_unshift($top5, 4);
	array_unshift($top15, 3);
	array_unshift($top30, 2);
	array_unshift($top50, 1);
	array_unshift($bot50, 0);
	$data = array($bot50,$top50,$top30,$top15,$top5);
	$svm = new SVM();
	if($data == $test){
		$catR = -1;
	}else{
		$model = $svm->train($data);
		$data = array($expVol => $expPrice);
		$catR = $model->predict($data);
	}
	//top
	$m = $sub;
	$top5 = array();
	$top15 = array();
	$top30 = array();
	$top50 = array();
	$bot50 = array();
	
	$stmt = $conn->prepare("SELECT * FROM `ScatterPlot` WHERE `Subcategory` = :m AND `Category` = :cats AND `Major` = :major ORDER BY `Revenue` DESC");
	$stmt->bindParam(':major', $major, PDO::PARAM_STR);
	$stmt->bindParam(':cats', $cats, PDO::PARAM_STR);
	$stmt->bindParam(':m', $m, PDO::PARAM_STR);
	$stmt->execute();
	$rows = $stmt->rowCount();
	$limit = round($rows / 20);
	$stmt = $conn->prepare("SELECT * FROM `ScatterPlot` WHERE `Subcategory` = :m AND `Category` = :cats AND `Major` = :major ORDER BY `Revenue` DESC LIMIT $limit");
	$stmt->bindParam(':major', $major, PDO::PARAM_STR);
	$stmt->bindParam(':cats', $cats, PDO::PARAM_STR);
	$stmt->bindParam(':m', $m, PDO::PARAM_STR);
	$stmt->execute();
	$cat = $stmt->fetchAll();
	foreach($cat as $c){
		if(isset($top5[round($c['Volume'])])){ $top5[round($c['Volume'])] = ($c["Price"] + $top5[round($c['Volume'])])/2;}else{$top5[round($c['Volume'])] = $c["Price"];}
	}
	
	$lim = $limit * 2;
	$stmt = $conn->prepare("SELECT * FROM `ScatterPlot` WHERE `Subcategory` = :m AND `Category` = :cats AND `Major` = :major ORDER BY `Revenue` DESC LIMIT $limit,$lim");
	$stmt->bindParam(':major', $major, PDO::PARAM_STR);
	$stmt->bindParam(':cats', $cats, PDO::PARAM_STR);
	$stmt->bindParam(':m', $m, PDO::PARAM_STR);
	$stmt->execute();
	$cat = $stmt->fetchAll();
	foreach($cat as $c){
		if(isset($top15[round($c['Volume'])])){ $top15[round($c['Volume'])] = ($c["Price"] + $top15[round($c['Volume'])])/2;}else{$top15[round($c['Volume'])] = $c["Price"];}
	}
	
	$lim = $limit * 3;
	$a = $limit * 3;
	$stmt = $conn->prepare("SELECT * FROM `ScatterPlot` WHERE `Subcategory` = :m AND `Category` = :cats AND `Major` = :major ORDER BY `Revenue` DESC LIMIT $a,$lim");
	$stmt->bindParam(':major', $major, PDO::PARAM_STR);
	$stmt->bindParam(':cats', $cats, PDO::PARAM_STR);
	$stmt->bindParam(':m', $m, PDO::PARAM_STR);
	$stmt->execute();
	$cat = $stmt->fetchAll();
	foreach($cat as $c){
		if(isset($top30[round($c['Volume'])])){ $top30[round($c['Volume'])] = ($c["Price"] + $top30[round($c['Volume'])])/2;}else{$top30[round($c['Volume'])] = $c["Price"];}
	}
	
	$lim = $limit * 4;
	$a = $limit * 6;
	$stmt = $conn->prepare("SELECT * FROM `ScatterPlot` WHERE `Subcategory` = :m AND `Category` = :cats AND `Major` = :major ORDER BY `Revenue` DESC LIMIT $a,$lim");
	$stmt->bindParam(':major', $major, PDO::PARAM_STR);
	$stmt->bindParam(':cats', $cats, PDO::PARAM_STR);
	$stmt->bindParam(':m', $m, PDO::PARAM_STR);
	$stmt->execute();
	$cat = $stmt->fetchAll();
	foreach($cat as $c){
		if(isset($top50[round($c['Volume'])])){ $top50[round($c['Volume'])] = ($c["Price"] + $top50[round($c['Volume'])])/2;}else{$top50[round($c['Volume'])] = $c["Price"];}
	}
	
	$lim = $limit * 10;
	$a = $limit * 10;
	$stmt = $conn->prepare("SELECT * FROM `ScatterPlot` WHERE `Subcategory` = :m AND `Category` = :cats AND `Major` = :major ORDER BY `Revenue` DESC LIMIT $a,$lim");
	$stmt->bindParam(':major', $major, PDO::PARAM_STR);
	$stmt->bindParam(':cats', $cats, PDO::PARAM_STR);
	$stmt->bindParam(':m', $m, PDO::PARAM_STR);
	$stmt->execute();
	$cat = $stmt->fetchAll();
	foreach($cat as $c){
		if(isset($bot50[round($c['Volume'])])){ $bot50[round($c['Volume'])] = ($c["Price"] + $bot50[round($c['Volume'])])/2;}else{$bot50[round($c['Volume'])] = $c["Price"];}
	}	
	$expVol = $_GET['vol'];
	$expPrice = $_GET['price'];
	array_unshift($top5, 4);
	array_unshift($top15, 3);
	array_unshift($top30, 2);
	array_unshift($top50, 1);
	array_unshift($bot50, 0);
	$data = array($bot50,$top50,$top30,$top15,$top5);
	$svm = new SVM();
	if($data == $test){
		$subR = -1;
	}else{
		$model = $svm->train($data);
		$data = array($expVol => $expPrice);
		$subR = $model->predict($data);
	}
	//top
	$m = $seg;
	$top5 = array();
	$top15 = array();
	$top30 = array();
	$top50 = array();
	$bot50 = array();
	
	$stmt = $conn->prepare("SELECT * FROM `ScatterPlot` WHERE `Segment` = :m AND `Subcategory` = :sub AND `Category` = :cats AND `Major` = :major ORDER BY `Revenue` DESC");
	$stmt->bindParam(':major', $major, PDO::PARAM_STR);
	$stmt->bindParam(':cats', $cats, PDO::PARAM_STR);
	$stmt->bindParam(':sub', $sub, PDO::PARAM_STR);
	$stmt->bindParam(':m', $m, PDO::PARAM_STR);
	$stmt->execute();
	$rows = $stmt->rowCount();
	$limit = round($rows / 20);
	$stmt = $conn->prepare("SELECT * FROM `ScatterPlot` WHERE `Segment` = :m AND `Subcategory` = :sub AND `Category` = :cats AND `Major` = :major ORDER BY `Revenue` DESC LIMIT $limit");
	$stmt->bindParam(':major', $major, PDO::PARAM_STR);
	$stmt->bindParam(':cats', $cats, PDO::PARAM_STR);
	$stmt->bindParam(':sub', $sub, PDO::PARAM_STR);
	$stmt->bindParam(':m', $m, PDO::PARAM_STR);
	$stmt->execute();
	$cat = $stmt->fetchAll();
	foreach($cat as $c){
		if(isset($top5[round($c['Volume'])])){ $top5[round($c['Volume'])] = ($c["Price"] + $top5[round($c['Volume'])])/2;}else{$top5[round($c['Volume'])] = $c["Price"];}
	}
	
	$lim = $limit * 2;
	$stmt = $conn->prepare("SELECT * FROM `ScatterPlot` WHERE `Segment` = :m AND `Subcategory` = :sub AND `Category` = :cats AND `Major` = :major ORDER BY `Revenue` DESC LIMIT $limit,$lim");
	$stmt->bindParam(':major', $major, PDO::PARAM_STR);
	$stmt->bindParam(':cats', $cats, PDO::PARAM_STR);
	$stmt->bindParam(':sub', $sub, PDO::PARAM_STR);
	$stmt->bindParam(':m', $m, PDO::PARAM_STR);
	$stmt->execute();
	$cat = $stmt->fetchAll();
	foreach($cat as $c){
		if(isset($top15[round($c['Volume'])])){ $top15[round($c['Volume'])] = ($c["Price"] + $top15[round($c['Volume'])])/2;}else{$top15[round($c['Volume'])] = $c["Price"];}
	}
	
	$lim = $limit * 3;
	$a = $limit * 3;
	$stmt = $conn->prepare("SELECT * FROM `ScatterPlot` WHERE `Segment` = :m AND `Subcategory` = :sub AND `Category` = :cats AND `Major` = :major ORDER BY `Revenue` DESC LIMIT $a,$lim");
	$stmt->bindParam(':major', $major, PDO::PARAM_STR);
	$stmt->bindParam(':cats', $cats, PDO::PARAM_STR);
	$stmt->bindParam(':sub', $sub, PDO::PARAM_STR);
	$stmt->bindParam(':m', $m, PDO::PARAM_STR);
	$stmt->execute();
	$cat = $stmt->fetchAll();
	foreach($cat as $c){
		if(isset($top30[round($c['Volume'])])){ $top30[round($c['Volume'])] = ($c["Price"] + $top30[round($c['Volume'])])/2;}else{$top30[round($c['Volume'])] = $c["Price"];}
	}
	
	$lim = $limit * 4;
	$a = $limit * 6;
	$stmt = $conn->prepare("SELECT * FROM `ScatterPlot` WHERE `Segment` = :m AND `Subcategory` = :sub AND `Category` = :cats AND `Major` = :major ORDER BY `Revenue` DESC LIMIT $a,$lim");
	$stmt->bindParam(':major', $major, PDO::PARAM_STR);
	$stmt->bindParam(':cats', $cats, PDO::PARAM_STR);
	$stmt->bindParam(':sub', $sub, PDO::PARAM_STR);
	$stmt->bindParam(':m', $m, PDO::PARAM_STR);
	$stmt->execute();
	$cat = $stmt->fetchAll();
	foreach($cat as $c){
		if(isset($top50[round($c['Volume'])])){ $top50[round($c['Volume'])] = ($c["Price"] + $top50[round($c['Volume'])])/2;}else{$top50[round($c['Volume'])] = $c["Price"];}
	}
	
	$lim = $limit * 10;
	$a = $limit * 10;
	$stmt = $conn->prepare("SELECT * FROM `ScatterPlot` WHERE `Segment` = :m AND `Subcategory` = :sub AND `Category` = :cats AND `Major` = :major ORDER BY `Revenue` DESC LIMIT $a,$lim");
	$stmt->bindParam(':major', $major, PDO::PARAM_STR);
	$stmt->bindParam(':cats', $cats, PDO::PARAM_STR);
	$stmt->bindParam(':sub', $sub, PDO::PARAM_STR);
	$stmt->bindParam(':m', $m, PDO::PARAM_STR);
	$stmt->execute();
	$cat = $stmt->fetchAll();
	foreach($cat as $c){
		if(isset($bot50[round($c['Volume'])])){ $bot50[round($c['Volume'])] = ($c["Price"] + $bot50[round($c['Volume'])])/2;}else{$bot50[round($c['Volume'])] = $c["Price"];}
	}	
	$expVol = $_GET['vol'];
	$expPrice = $_GET['price'];
	array_unshift($top5, 4);
	array_unshift($top15, 3);
	array_unshift($top30, 2);
	array_unshift($top50, 1);
	array_unshift($bot50, 0);
	$data = array($bot50,$top50,$top30,$top15,$top5);
	$svm = new SVM();
	if($data == $test){
		$segR = -1;
	}else{
		$model = $svm->train($data);
		$data = array($expVol => $expPrice);
		$segR = $model->predict($data);
	}
	$majOut = "";
	if($majR == 0){
		$majOut = "Bottom 50%";
	}else if($majR == 1){
		$majOut = "Top 30% - 50%";
	}else if($majR == 2){
		$majOut = "Top 15% - 30%";
	}else if($majR == 3){
		$majOut = "Top 5% - 15%";
	}else if($majR == 4){
		$majOut = "Top 5%";
	}else{
		$majOut = "Not Available";
	}
	
	$catOut = "";
	if($catR == 0){
		$catOut = "Bottom 50%";
	}else if($catR == 1){
		$catOut = "Top 30% - 50%";
	}else if($catR == 2){
		$catOut = "Top 15% - 30%";
	}else if($catR == 3){
		$catOut = "Top 5% - 15%";
	}else if($catR == 4){
		$catOut = "Top 5%";
	}else{
		$catOut = "Not Available";
	}
	
	$subOut = "";
	if($subR == 0){
		$subOut = "Bottom 50%";
	}else if($subR == 1){
		$subOut = "Top 30% - 50%";
	}else if($subR == 2){
		$subOut = "Top 15% - 30%";
	}else if($subR == 3){
		$subOut = "Top 5% - 15%";
	}else if($subR == 4){
		$subOut = "Top 5%";
	}else{
		$subOut = "Not Available";
	}
	
	$segOut = "";
	if($segR == 0){
		$segOut = "Bottom 50%";
	}else if($segR == 1){
		$segOut = "Top 30% - 50%";
	}else if($segR == 2){
		$segOut = "Top 15% - 30%";
	}else if($segR == 3){
		$segOut = "Top 5% - 15%";
	}else if($segR == 4){
		$segOut = "Top 5%";
	}else{
		$segOut = "Not Available";
	}
	
	
	$out = "<p>Major Performance: <b>$majOut</b></p>
	<p>Category Performance: <b>$catOut</b></p>
	<p>Subcategory Performance: <b>$subOut</b></p>
	<p>Segment Performance: <b>$segOut</b></p>";
	
	echo $out;
	
	
}else if($request == "svmCat"){
	$type = $_GET['type'];
	$stmt = $conn->prepare("SELECT DISTINCT `Category` FROM `ScatterPlot` WHERE `Major` = '$type'");
	$stmt->execute();
	$cats = $stmt->fetchAll();
	$out = "";
	foreach($cats as $c){
		$c = $c[0];
		$out .= "<option value='$c'>$c</option>";
	}
	echo $out;
}else if($request == "svmSub"){
	$type = $_GET['type'];
	$stmt = $conn->prepare("SELECT DISTINCT `Subcategory` FROM `ScatterPlot` WHERE `Category` = '$type'");
	$stmt->execute();
	$cats = $stmt->fetchAll();
	$out = "";
	foreach($cats as $c){
		$c = $c[0];
		$out .= "<option value='$c'>$c</option>";
	}
	echo $out;
}else if($request == "svmSeg"){
	$type = $_GET['type'];
	$stmt = $conn->prepare("SELECT DISTINCT `Segment` FROM `ScatterPlot` WHERE `Subcategory` = '$type'");
	$stmt->execute();
	$cats = $stmt->fetchAll();
	$out = "";
	foreach($cats as $c){
		$c = $c[0];
		$out .= "<option value='$c'>$c</option>";
	}
	echo $out;
}










?>