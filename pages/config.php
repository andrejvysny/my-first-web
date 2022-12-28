<?php 



$fontslink='<link href="https://fonts.googleapis.com/css?family=Caveat|Indie+Flower|Kaushan+Script|Modak|Pacifico|Saira+Stencil+One|Teko&display=swap" rel="stylesheet"> ';

$menupages = array(
    'index.php' => 'HOME',
    'index.php?page=portfolio' => 'PORTFOLIO',
	'index.php?page=aboutme' => 'ABOUT ME',
	'index.php?page=contact' => 'CONTACT',
	
	
    
);
$textaboutme = file_get_contents('pages/textaboutme.txt');
?>