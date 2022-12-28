<?php
require_once 'cms_config.php';
session_start();
$status = $_SESSION['status'];
if($status !== 1){
	session_destroy;
	header("location: cms.php");
} 


?>


<!DOCTYPE html>
<html>
<head>    
<meta charset="UTF-8">
<title>AV CMS</title>
<style>
body{
overflow:hidden;
margin:0;
padding:0;
background-color:rgba(0,0,0,1);}


h1.cms{
	margin:auto;
	font-size:40px;
	text-align:center;
	margin-top:100px;
	font-family:sans-serif;
	color:#fff;

}

h2.back{
	width:100px;
	margin:auto;
	margin-top:-20px;
	font-size:15px;
	font-family:sans-serif;
	font-weight: normal;
	text-align:center;
	color:#fff;
	cursor:pointer;
    border:1px solid white;
	padding:14px 0 14px 0;
}

a { 
	text-decoration: none;
	font-family:sans-serif;
	font-weight: normal;
	text-align:center;
	width:100px;
	margin:auto;
	color:#fff;
	display:block;
	padding:15px 0 0 0;
}
a:hover{transform:scale(1.1);}
a.logout{ 
	padding:0;
}
a.logout:hover{transform:scale(1);}
.messages{
	color:#fff;
	border-bottom:1px solid white;
	font-weight:bold;
	font-family:sans-serif;
}

.menu{
	margin:auto;
	padding:50px 0 0 0;
	color:#fff;
	width:700px;
	display:block;
	}
.mails{
	paddin-top:50px;
	width:300px;
	margin:auto;
	color:#fff;
	text-align:center;
	
}



</style>
</head>

<body>		
			<h1 class="cms">CMS HOME Status: <?php echo $status; ?> </h1><br/><br/>
			<div style="width:100px; margin:auto;"><a class="logout"href="cms_logout.php" class="back"><h2 class="back">LOG OUT</h2></a></div>       
                
            <div class="menu">
				<a href="cms_messages.php" name="messages">MESSAGES</a>
                <a href="cms_gallery.php" name="gallery">GALLERY</a>
				<a href="cms_aboutme.php" name="aboutme">ABOUT ME</a>
				
						
			</div>
        
		
	
</body>
</html>


