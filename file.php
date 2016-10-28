<?php

function writeFile($filename, $input){
	$myfile = fopen($filename, "w");
	fwrite($myfile, $input);
	fclose($myfile);
}

function readFileInput($filename){
	$myfile = fopen($filename, "r");
	$read = fread($myfile,filesize($filename));
	fclose($myfile);
	return $read;
}

function appendFile($filename, $input){
	$myfile = fopen($filename, "a+");
	fwrite($myfile, $input);
	fclose($myfile);
}








?>