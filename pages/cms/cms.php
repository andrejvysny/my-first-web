<?php 


require_once 'cms_config.php';

if (isset($_POST["usernamein"], $_POST["passwordin"])){
if($_POST["usernamein"]===$username and $_POST["passwordin"]===$password){
	

	session_start();
	$_SESSION['status'] = 1;
	header("Location: pages/cms/cms_home.php");
}}



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

a{
	color:black;
	text-decoration: none;
	
}
a:visited {
  color: black;
  text-decoration: none;
}



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
	margin-top:30px;
	font-size:15px;
	font-family:sans-serif;
	font-weight: normal;
	text-align:center;
	color:#fff;
	cursor:pointer;
    border:1px solid white;
	padding:14px 0 14px 0;
}
input[type=text] {
  width: 100px;
  margin: auto;
  margin-top:20px;
  padding: 12px 20px;
  display:block;
  color:#fff;
  font-size:14px;font-weight: bold;
  background:transparent;
  border:none;
  outline:none;
  border-bottom:1px solid white;
}
input[type=password] {
  width: 100px;
  margin: auto;
  margin-top:20px;
  padding: 12px 20px;
  display:block;
  color:#fff;
  font-size:14px;font-weight: bold;
  background:transparent;
  border:none;
  outline:none;
  border-bottom:1px solid white;
}
input[type=submit] {
  width: 100px;
  height:auto;
  padding:15px;
  color:#fff;
  margin:auto;
  margin-top:20px;
  display:block;
  background:none;
  border:1px solid white;
  cursor:pointer;
  font-weight: bold;
}


</style>
</head>

<body>		
			<h1 class="cms">CMS Status: <?php echo $status?></h1><br/><br/>
			
			
			
             
        
          
        
            <div class="login">
			
				<form action="#" method="post">
					<input placeholder="name" name="usernamein" type="text"><br>
					<input placeholder="password" name="passwordin" type="password">
					<input type="submit" name="login" value="LOGIN">
				</form>
            
			</div>
         <div style="width:100px; margin:auto;"><a href="index.php" ><h2 class="back">HOME</h2></a></div>
		
	
</body>
</html>