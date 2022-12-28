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
overflow:auto;
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
	text-align:center;
}


.messages_content{
	color:white;
	width:700px;
	margin:auto;
}

</style>
</head>

<body>		
			<h1 class="cms">CMS HOME Status: <?php echo $status; ?> </h1><br/><br/>
			<div style="width:100px; margin:auto;"><a class="logout"href="cms_logout.php" class="back"><h2 class="back">LOG OUT</h2></a></div></br>   
            <div style="width:100px; margin:auto;"><a class="logout"href="cms_home.php" class="back"><h2 class="back">CMS HOME</h2></a></div> </br>  </br>  
			
			<!--<?php include 'dir.php';?>-->
			<div class="messages_content">  
			<?php
			if(count($files)==2){echo '<h1 style="text-align:center;color:#fff;font-size:30px; font-family:sans-serif;">No Messages</h1>';}
			$number=2;
			while($number<count($files)){
			echo ('<h1 style="font-weight:bold;font-family:sans-serif; font-size:20px; color:#ffa ">Message "'. $files[$number].'"</br> Content :</h1>'.file_get_contents($maildir.$files[$number]));
			echo '<hr></br></br>';
			$number++;
			}
			
			?></div>
        
		
	
</body>
</html>