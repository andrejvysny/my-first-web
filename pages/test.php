<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="My Portfolio">
<meta name="keywords" content="Portfolio,Videos,Fotos,Photoshop">
<meta name="author" content="Andrej Vyšný">

<link rel="shortcut icon" href="pages/styles/images/favicon.png" />
<link rel="stylesheet" type="text/css" href="pages/styles/newmainstyle.css" />
<link rel="stylesheet" type="text/css" href="pages/styles/home.css" />
<link rel="stylesheet" type="text/css" href="pages/preloader/preloader.css" />
<script>
    function show() {
        document.getElementById('navmenu').classList.toggle('active');
        document.getElementById('icon').classList.toggle('active');
        document.getElementById('menubar').classList.toggle('active');
    }
</script>
<?php echo $fontslink?>

<title>Andrej Vyšný</title>
</head>

<body>
<div id="preloader"><div class="spinner"></div></div>
    <nav class="mobilmenu">
<div id="menubar">
    <a href="#"><img class="navlogo" src="pages/styles/images/logo.png"></a>
    <div id="icon" onclick="show()">
        <span class="hamburger"></span>
    </div>

</div>
<section id="navmenu">
    <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="index.php?page=portfolio">Portfolio</a></li>
        <li><a href="index.php?page=aboutme">About Me</a></li>
        <li><a href="index.php?page=contact">Contact</a></li>
    </ul>
</section>
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