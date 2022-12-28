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
<link rel="stylesheet" type="text/css" href="pages/styles/home.css" />
<link rel="stylesheet" type="text/css" href="pages/preloader/preloader.css" />
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
		<img class="background" src="pages/styles/images/bg.jpg"  alt="Andrej Vyšný">
	</div>	
</div>
<script src="pages/preloader/preloader.js"></script>
</body>
</html>