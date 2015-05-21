<?php
header('Content-type: text/html; charset=Windows-1251');
function getmicrotime() {
    list($usec, $sec) = explode(" ",microtime());
    return ((float)$usec + (float)$sec);
}

$alf_s = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖ×ØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõö÷øùúûüýþÿ';
$alf = array();
$start = 1040;
$j = 0;
for ($i = $start; $i<$start+64; $i++) { 
  $alf[$i] = $alf_s[$j];
  $j++;
}

function entites2text($str) {
  global $alf;
  $str = str_replace(';','',$str);
  $str_a = split('&#',$str);
  $len = count($str_a);
  $res = '';    
  for( $i=1; $i<=$len; $i++ ) {
    if ($str_a[$i]>=1040)
      $res .= $alf[$str_a[$i]];
    else
      $res .=  html_entity_decode('&#'.$str_a[$i].';',ENT_COMPAT,'utf-8');
  }  
  return $res;
}

function uc2html($str) {
	$ret = '';
	for( $i=0; $i<strlen($str)/2; $i++ ) {
		$charcode = ord($str[$i*2])+256*ord($str[$i*2+1]);
		$ret .= '&#'.$charcode.';';
	}
	$ret = entites2text($ret);
	return $ret;
}

function show_time() {
	global $time_start,$time_end;

	$time = $time_end - $time_start;
	//echo "Parsing done in $time seconds<hr size=1><br>";
}

function fatal($msg = '') {
	echo '[Fatal error]';
	if( strlen($msg) > 0 )
		echo ": $msg";
	echo "<br>\nScript terminated<br>\n";
	if( $f_opened) @fclose($fh);
	exit();
};

$err_corr = "Unsupported format or file corrupted";

$excel_file_size;
$excel_file = $_FILES['excel_file'];
if( $excel_file )
	$excel_file = $_FILES['excel_file']['tmp_name'];

if( $excel_file == '' ) fatal("No file uploaded");

$exc = new ExcelFileParser("debug.log", ABC_NO_LOG );//ABC_VAR_DUMP);

$style = $_POST['style'];
if( $style == 'old' )
{
	$fh = @fopen ($excel_file,'rb');
	if( !$fh ) fatal("No file uploaded");
	if( filesize($excel_file)==0 ) fatal("No file uploaded");
	$fc = fread( $fh, filesize($excel_file) );
	@fclose($fh);
	if( strlen($fc) < filesize($excel_file) )
		fatal("Cannot read file");
		
	$time_start = getmicrotime();
	$res = $exc->ParseFromString($fc);
	$time_end = getmicrotime();
}
elseif( $style == 'segment' )
{
	$time_start = getmicrotime();
	$res = $exc->ParseFromFile($excel_file);
	$time_end = getmicrotime();
}

switch ($res) {
	case 0: break;
	case 1: fatal("Can't open file");
	case 2: fatal("File too small to be an Excel file");
	case 3: fatal("Error reading file header");
	case 4: fatal("Error reading file");
	case 5: fatal("This is not an Excel file or file stored in Excel < 5.0");
	case 6: fatal("File corrupted");
	case 7: fatal("No Excel data found in file");
	case 8: fatal("Unsupported file version");

	default:
		fatal("Unknown error");
}
?>