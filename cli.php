#!/usr/bin/php -q
<?php
/*
*
*	replace 1.0 
*	script that simply crawl into your folder and change content of files
*
*TODO
*	- replace url for images path
*	- smart crawl with switches
*	- code cleanup
*	- db insert
*
*	todo switches 
*		--enter image path to replace
*		--enter folder to crawl
*todo matrix 
*		./cli.php gen-doctor
*
**********/
echo "\n replace v0.1 ";
echo "\n running this script will change files.";
echo "\n do you want to continue? (n/y)";
$m_stdin = fopen('php://stdin', 'r');
$m_input = fgets($m_stdin,2);
if ($m_input != "y") 
{
	exit;
}
//SET CONSTANTS	
	$m_fldr_path = 'ntl';
	$m_dir = "/private/var/root/Desktop/crawler/$fldr_path";
	$m_host = 'localhost';
	$m_username = 'root';
	$m_password = '';
	$m_database = 'systemteknik_db';
	$m_filePath ='';
	if (is_dir($m_dir)) 
	{
   		$m_linkHost = mysql_connect($m_host,$m_username,$m_password) ;
   		if($m_linkHost) 
   		{
			$m_linkDb = mysql_select_db($m_database,$m_linkHost);	
			if($m_linkDb) {
				echo "database connection has been established\n";
			} else {
				echo "error connecting to database\n";
				exit;
			}
   		} else {
			mysql_error("database connection failed : ".mysql_num()."\n" );
			exit;
  		}
  
   		if($m_hDir = opendir($m_dir)) 
   		{
       		while(($m_file = readdir($m_hDir)) !== false) {
       			if($m_file != '.' && $m_file !='..') {
        			replaceFile($m_file);
         		}
       		}
       	closedir($m_hDir);
   		}
	}


function replaceFile($file)
{
	global $m_fldr_path;
	$m_filePath = "/private/var/root/Desktop/crawler/$fldr_path/".$file;
	$m_dataFile = file_get_contents($m_filePath);
	$m_searchStr =array
	(
'<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">',
'<html>',
'<head>',
'<title>TRACEBoards</title>',
'<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">',
'<link href="styles/system.css" rel="stylesheet" type="text/css">',
'<link href="../../styles/system.css" rel="stylesheet" type="text/css">',
'</head>',
'<body>',
'<div id="logotop">',
'<img src="../../images/systemteknik-logo.jpg"></div>',
'<div id="linktop">',
'<a href="../../main.html">Home</a>',
' | ',
'<a href="../main.html">Products</a>',
'<a href="../../new-devts.html">',
'New Developments</a>', 
'<a href="../../services.html">',
'</a><a href="../../services.html">Services</a>',
'<a href="../../support.html">Support</a>',
'<a href="../../contact.html">Contact Us</a></div>',
'Services</a>',
'<div id="contents">',
'</div>',
'<div id="linkbottom">',
'<a href="../../about.html">About Us</a>',
'<a href="../../csr.html">Corporate Social Responsibility</a>', 
'<a href="../../find.html">Where to Find Us</a>', 
'<a href="../../terms.html">Terms of Use</a>', 
'<a href="../../site-map.html">Site Map</a></div>',
'</body>',
'</html>'

	);
	$m_newFile = str_replace($m_searchStr,"\r\n",$m_dataFile);

	$m_searchUrl = array
	(
		"../$m_fldr_path.html",
		'../main.html',
		'<a href="../../site-map.html">Site Map</a>',
		'<div id="copyright">&copy 2009 SYSTEMTEKNIK INTL. TECHNOLOGIES CORP.',
		"../../images/$m_fldr_path/"
	);

	$m_replacementUrl = array
	(
		"site?m=$m_fldr_path",
		'site?m=products',
		"\r\n",
		"\r\n",
		"/systemteknik/assets/images/$m_fldr_path/"
	);
	$m_resultFile = str_replace($m_searchUrl,$m_replacementUrl,$m_newFile);
	$m_resultDir = '/private/var/root/Desktop/crawler/result/'.$file;

	$m_result = file_put_contents($m_resultDir, trim($m_resultFile));
	if (false == $m_result ) {		
			echo "ERROR WRITING FILES ... $m_file\n";
			exit;		
	} else {
		
			$m_patterns[0] = '/.html/';
			$m_replacements[0] = '';
			$m_finalFile = preg_replace($m_patterns, $m_replacements, $m_file);
			echo $m_finalFile." written .. OK\n";
			$m_content = file_get_contents($m_resultDir);
			saveFile($m_content, $m_finalFile);
	}
}

function saveFile($m_data,$m_filename)
{

	$m_sql =" INSERT INTO `tbl_page` 
			(
				`page_idPK`, 
				`page_menuidFK`, 
				`page_name`, 
				`page_url`, 
				`page_content`
			) VALUES
			(
				NULL,
				1,
				'$m_filename',
				'$m_filename',
				'".mysql_real_escape_string($m_data)."'
			) ";
	$m_result = mysql_query($m_sql);
	if($m_result) {
		echo "file has been written... OK\n";
	}else {
		die(mysql_error());
		exit;
	}
}
?>
