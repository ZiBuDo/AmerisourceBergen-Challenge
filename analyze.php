<?php 
ini_set('memory_limit', '30000M');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'file.php';
require 'database.php';
//load into memory for fast processing
$conn = connect();

//Module 1 -- Scatter Plot of top 250 products by revenue per category plotted on average price versus volume per transaction
/*
$stmt = $conn->prepare("SELECT DISTINCT `PROD_NBR` FROM `POSTrans`");
$stmt->execute();
$prods = $stmt->fetchAll();
foreach($prods as $p){
	$prices = 0;
	$volumes = 0;
	$revenues = 0;
	$first = true;
	$major = "";
	$cat = "";
	$sub = "";
	$seg = "";
	$p = $p['PROD_NBR'];
	$stmt = $conn->prepare("SELECT * FROM `POSTrans` WHERE `PROD_NBR` = '$p'");
	$stmt->execute();
	$products = $stmt->fetchAll();
	echo "$p\n";
	foreach($products as $prod){
		if($prod['SLS_QTY'] == 0){
			$prod['SLS_QTY'] = 1;
		}
		if($first){
			$prices = $prod['POS_AMT']/$prod['SLS_QTY'];
			$volumes = $prod['SLS_QTY'];
			$revenues = $prod['POS_AMT'];
			$first = false;
		}else{
			$prices = (($prod['POS_AMT']/$prod['SLS_QTY']) + $prices)/2;
			$volumes = ($prod['SLS_QTY'] + $volumes)/2;
			$revenues = ($prod['POS_AMT'] + $revenues)/2;
		}
	}
	$stmt = $conn->prepare("SELECT * FROM `ProductMaster` WHERE `PROD_NBR` = '$p'");
	$stmt->execute();
	$master = $stmt->fetchAll()[0];
	$a = $master['MAJOR_CAT_CD'];
	$stmt = $conn->prepare("SELECT `MAJOR_CAT_DESC` FROM `MajorProductCategory` WHERE `MAJOR_CAT_CD` = '$a'");
	$stmt->execute();
	$major = $stmt->fetchAll()[0][0];
	$a = $master['CAT_CD'];
	$stmt = $conn->prepare("SELECT `CAT_DESC` FROM `ProductCategory` WHERE `CAT_CD` = '$a'");
	$stmt->execute();
	$cat = $stmt->fetchAll()[0][0];
	$a = $master['SUB_CAT_CD'];
	$stmt = $conn->prepare("SELECT `SUB_CAT_DESC` FROM `ProductSubCategory` WHERE `SUB_CAT_CD` = '$a'");
	$stmt->execute();
	$sub = $stmt->fetchAll()[0][0];
	$a = $master['SEGMENT_CD'];
	$stmt = $conn->prepare("SELECT `SEG_DESC` FROM `ProductSegment` WHERE `SEG_CD` = '$a'");
	$stmt->execute();
	$seg = $stmt->fetchAll()[0][0];
	$prices = round($prices,2);
	$volumes = round($volumes,2);
	$revenues = round($revenues,2);
	$stmt = $conn->prepare("INSERT INTO `ScatterPlot`(`Product`, `Price`, `Volume`, `Revenue`, `Major`, `Category`, `Subcategory`, `Segment`) VALUES ('$p',$prices,$volumes,$revenues,'$major','$cat','$sub','$seg')");
	$stmt->execute();
}
*/
//Calculate Statistics of high and low categories. Find % Revenue and Revenue number based on Scatter Plot Table, Calculate for Major, Cat, Sub, and SEGMENT_CD
/*
$stmt = $conn->prepare("SELECT * FROM `ScatterPlot`");
$stmt->execute();
$prods = $stmt->fetchAll();
$majors = array();
$cats = array();
$subs = array();
$segs = array();
foreach($prods as $p){
	$major = $p["Major"];
	$cat = $p["Category"];
	$sub = $p["Subcategory"];
	$seg = $p["Segment"];
	$revenue = $p["Revenue"];
	if(isset($majors[$major])){
		$majors[$major] += $revenue;
	}else{
		$majors[$major] = $revenue;
	}
	if(isset($cats[$cat])){
		$cats[$cat] += $revenue;
	}else{
		$cats[$cat] = $revenue;
	}
	if(isset($subs[$sub])){
		$subs[$sub] += $revenue;
	}else{
		$subs[$sub] = $revenue;
	}
	if(isset($segs[$seg])){
		$segs[$seg] += $revenue;
	}else{
		$segs[$seg] = $revenue;
	}
}
$majSum = 0;
foreach($majors as $m){
		$majSum += $m;
	}
foreach($majors as $key => $val){
	$percent = round((($val / $majSum) * 100),2);
	$val = round($val,2);
	$stmt = $conn->prepare("INSERT INTO `ScatterStats`(`Level`, `Name`, `Percentage`, `Revenue`) VALUES ('Major','$key','$percent',$val)");
	$stmt->execute();
}

foreach($majors as $key => $val){
	$catSum = 0;
	$stmt = $conn->prepare("SELECT DISTINCT `Category` FROM `ScatterPlot` WHERE `Major` = '$key'");
	$stmt->execute();
	$categories = $stmt->fetchAll();
	foreach($categories as $c){
		$catSum += $cats[$c[0]];
	}
	if($catSum != 0){
		foreach($cats as $key2 => $val2){
			$percent = round((($val2 / $catSum) * 100),2);
			$val2 = round($val2,2);
			$stmt = $conn->prepare("INSERT INTO `ScatterStats`(`Level`, `Name`, `Percentage`, `Revenue`) VALUES ('Category','$key2','$percent',$val2)");
			$stmt->execute();
		}
	}
}

foreach($cats as $key => $val){
	$subSum = 0;
	$stmt = $conn->prepare("SELECT DISTINCT `Subcategory` FROM `ScatterPlot` WHERE `Category` = '$key'");
	$stmt->execute();
	$subcats = $stmt->fetchAll();
	foreach($subcats as $c){
		$subSum += $subs[$c[0]];
	}
	if($subSum != 0){
		foreach($subs as $key2 => $val2){
			$percent = round((($val2 / $subSum) * 100),2);
			$val2 = round($val2,2);
			$stmt = $conn->prepare("INSERT INTO `ScatterStats`(`Level`, `Name`, `Percentage`, `Revenue`) VALUES ('Subcategory','$key2','$percent',$val2)");
			$stmt->execute();
		}
	}
}

foreach($subs as $key => $val){
	$segSum = 0;
	$stmt = $conn->prepare("SELECT DISTINCT `Segment` FROM `ScatterPlot` WHERE `Subcategory` = '$key'");
	$stmt->execute();
	$segments = $stmt->fetchAll();
	foreach($segments as $c){
		$segSum += $segs[$c[0]];
	}
	if($segSum != 0){
		foreach($segs as $key2 => $val2){
			$percent = round((($val2 / $segSum) * 100),2);
			$val2 = round($val2,2);
			$stmt = $conn->prepare("INSERT INTO `ScatterStats`(`Level`, `Name`, `Percentage`, `Revenue`) VALUES ('Segment','$key2','$percent',$val2)");
			$stmt->execute();
		}
	}
}
*/

//Module 2 Column Range for Average Volume for top 5% by revenue show Major, Cat, Sub, and Segment high lows with drill down like module 1
/*
$stmt = $conn->prepare("SELECT DISTINCT `Major` FROM `ScatterPlot`");
$stmt->execute();
$majors = $stmt->fetchAll();
$stmt = $conn->prepare("SELECT DISTINCT `Category` FROM `ScatterPlot`");
$stmt->execute();
$cats = $stmt->fetchAll();
$stmt = $conn->prepare("SELECT DISTINCT `Subcategory` FROM `ScatterPlot`");
$stmt->execute();
$subs = $stmt->fetchAll();
$stmt = $conn->prepare("SELECT DISTINCT `Segment` FROM `ScatterPlot`");
$stmt->execute();
$segs = $stmt->fetchAll();

foreach($majors as $m){
	$m = $m[0];
	$max = -1000000;
	$min = 1000000;
	$stmt = $conn->prepare("SELECT * FROM `ScatterPlot` WHERE `Major` = '$m' ORDER BY `Revenue` DESC");
	$stmt->execute();
	$rows = $stmt->rowCount();
	$limit = round($rows / 20);
	$stmt = $conn->prepare("SELECT * FROM `ScatterPlot` WHERE `Major` = '$m' ORDER BY `Revenue` DESC LIMIT $limit");
	$stmt->execute();
	$pro = $stmt->fetchAll();
	$rows = $stmt->rowCount();
	if($rows > 0){
		foreach($pro as $p){
			$volume = $p["Volume"];
			if($max < $volume){
				$max = $volume;
			}
			if($min > $volume){
				$min = $volume;
			}
		}
	}
	if($max == -1000000){
		$max = 0;
	}
	if($min == 1000000){
		$min = 0;
	}
	$stmt = $conn->prepare("INSERT INTO `ColumnRange`(`Level`, `Name`, `Low`, `High`) VALUES ('Major','$m',$min,$max)");
	$stmt->execute();
}

foreach($cats as $m){
	$m = $m[0];
	$max = -1000000;
	$min = 1000000;
	$stmt = $conn->prepare("SELECT * FROM `ScatterPlot` WHERE `Category` = '$m' ORDER BY `Revenue` DESC");
	$stmt->execute();
	$rows = $stmt->rowCount();
	$limit = round($rows / 20);
	$stmt = $conn->prepare("SELECT * FROM `ScatterPlot` WHERE `Category` = '$m' ORDER BY `Revenue` DESC LIMIT $limit");
	$stmt->execute();
	$pro = $stmt->fetchAll();
	$rows = $stmt->rowCount();
	if($rows > 0){
		foreach($pro as $p){
			$volume = $p["Volume"];
			if($max < $volume){
				$max = $volume;
			}
			if($min > $volume){
				$min = $volume;
			}
		}
	}
	if($max == -1000000){
		$max = 0;
	}
	if($min == 1000000){
		$min = 0;
	}
	$stmt = $conn->prepare("INSERT INTO `ColumnRange`(`Level`, `Name`, `Low`, `High`) VALUES ('Category','$m',$min,$max)");
	$stmt->execute();
}

foreach($subs as $m){
	$m = $m[0];
	$max = -1000000;
	$min = 1000000;
	$stmt = $conn->prepare("SELECT * FROM `ScatterPlot` WHERE `Subcategory` = '$m' ORDER BY `Revenue` DESC");
	$stmt->execute();
	$rows = $stmt->rowCount();
	$limit = round($rows / 20);
	$stmt = $conn->prepare("SELECT * FROM `ScatterPlot` WHERE `Subcategory` = '$m' ORDER BY `Revenue` DESC LIMIT $limit");
	$stmt->execute();
	$pro = $stmt->fetchAll();
	$rows = $stmt->rowCount();
	if($rows > 0){
		foreach($pro as $p){
			$volume = $p["Volume"];
			if($max < $volume){
				$max = $volume;
			}
			if($min > $volume){
				$min = $volume;
			}
		}
	}
	if($max == -1000000){
		$max = 0;
	}
	if($min == 1000000){
		$min = 0;
	}
	$stmt = $conn->prepare("INSERT INTO `ColumnRange`(`Level`, `Name`, `Low`, `High`) VALUES ('Subcategory','$m',$min,$max)");
	$stmt->execute();
}

foreach($segs as $m){
	$m = $m[0];
	$max = -1000000;
	$min = 1000000;
	$stmt = $conn->prepare("SELECT * FROM `ScatterPlot` WHERE `Segment` = '$m' ORDER BY `Revenue` DESC");
	$stmt->execute();
	$rows = $stmt->rowCount();
	$limit = round($rows / 20);
	$stmt = $conn->prepare("SELECT * FROM `ScatterPlot` WHERE `Segment` = '$m' ORDER BY `Revenue` DESC LIMIT $limit");
	$stmt->execute();
	$pro = $stmt->fetchAll();
	$rows = $stmt->rowCount();
	if($rows > 0){
		foreach($pro as $p){
			$volume = $p["Volume"];
			if($max < $volume){
				$max = $volume;
			}
			if($min > $volume){
				$min = $volume;
			}
		}
	}
	if($max == -1000000){
		$max = 0;
	}
	if($min == 1000000){
		$min = 0;
	}
	$stmt = $conn->prepare("INSERT INTO `ColumnRange`(`Level`, `Name`, `Low`, `High`) VALUES ('Segment','$m',$min,$max)");
	$stmt->execute();
}
*/

//Module 3 Top 10 most prevalent items, bubble chart of different revenue areas of major categories break down of bottom 25%, bot mid 35%, top mid 35%, top 5%
/*
$stmt = $conn->prepare("SELECT DISTINCT `BSKT_ID` FROM `POSTrans`");
$stmt->execute();
$result = $stmt->fetchAll();
$rows = $stmt->rowCount();

$num = $rows; 

$prods = array();
$stmt = $conn->prepare("SELECT DISTINCT `PROD_NBR` FROM `POSTrans`");
$stmt->execute();
$result = $stmt->fetchAll();
foreach($result as $r){
	$r = $r[0];
	$stmt = $conn->prepare("SELECT * FROM `POSTrans` WHERE `PROD_NBR` = '$r'");
	$stmt->execute();
	$rows = $stmt->rowCount();
	$prods[$r] = round((($rows/$num) * 100),2);
}

arsort($prods);
$count = 0;
foreach($prods as $key => $val){
	$stmt = $conn->prepare("SELECT * FROM `ProductMaster` WHERE `PROD_NBR` = '$key'");
	$stmt->execute();
	$result = $stmt->fetchAll()[0]["PROD_DESC"];
	
	
	$stmt = $conn->prepare("INSERT INTO `TopProducts`(`Product`, `Percent`) VALUES ('$result','$val')");
	$stmt->execute();
	$count++;
	if($count > 10){
		break;
	}
}
*/



?>