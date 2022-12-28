<!DOCTYPE html>
<html>
<head>    
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="My Portfolio">
<meta name="keywords" content="Portfolio,Videos,Fotos,Photoshop">
<meta name="author" content="Andrej Vyšný">
 
<link rel="shortcut icon" href="pages/styles/images/favicon.png" />
<link rel="stylesheet" type="text/css" href="pages/styles/mainstyle.css" />
<link rel="stylesheet" type="text/css" href="pages/styles/portfolio.css" />
<link rel="stylesheet" type="text/css" href="pages/preloader/preloader.css" />
<link href="pages/styles/lightbox/lightbox.css" rel="stylesheet" />
<?php echo $fontslink?> 

<title>Andrej Vyšný</title>
</head>

<body>
<div id="preloader"><div class="spinner"></div></div>
    <nav class="mobilmenu">
		<a href="index.php"><img class="navlogo" src="pages/styles/images/logo.png"></a>
        <label for="toggle">&#9776;</label>
        <input type="checkbox" id="toggle"/>
        <div class="navmenu">
            <?php
				foreach($menupages as $link => $title) {
				echo '<a href="'.$link.'">'.$title.'</a>';}
			?>
        </div>
    </nav>
<div class="container">
    <nav class="sidemenu">
        <div class="logo"><a href="index.php"><img class="logoimage" src="pages/styles/images/logo.png"  alt="Andrej Vyšný"></a></div>
		<h1 class="name">Andrej Vyšný</h1>
		<div class="menu">
			<?php
				foreach($menupages as $link => $title) {
				echo '<a href="'.$link.'">'.$title.'</a>';}
			?>	
		</div>
    </nav>


	<div class="right">
	<article>
		<h1 class="somemywork" >Some my work</h1>
		
		<div class="gallery">
			<?php
			

$gallery_folder='pages/gallery/';
$folds=2;
$time=0;
while ($time<(count(scandir($gallery_folder))-2)){
	$time++;
	$foldernames = (scandir($gallery_folder));
	echo '<h1 class="photoname">'.substr($foldernames[$folds],2).'</h1>';
	$x=0;
	$y=(count(scandir($gallery_folder.'/'.$foldernames[$folds]))-3);		
	while($x<$y){
		$x++;
		echo '<a target="_blank" href="'.$gallery_folder.$foldernames[$folds].'/image ('.$x.').jpg" data-lightbox="'.substr($foldernames[$folds],2).'" data-title="'.substr($foldernames[$folds],2).'" > <img  src="'.$gallery_folder.$foldernames[$folds].'/Thumbnails/image ('.$x.').jpg"></a>';
	}
	$folds++;
}



			?>
		</div>
		</article>
		<article class="videos">
		<h1 class="somemywork">Videos</h1>
		<iframe  src="https://www.youtube.com/embed/k3QVFJ24Xoc" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
		<iframe  src="https://www.youtube.com/embed/g50W96tNlNs" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
		<iframe  src="https://www.youtube.com/embed/v_YeQ0HYGv0" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
	</article>
		
	<div class="footer"></div>	
		
	</div>	
</div>
<script src="pages/styles/lightbox/lightbox-plus-jquery.js"></script>
<script src="pages/preloader/preloader.js"></script>
</body>
</html>