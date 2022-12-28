<?php
require_once 'cms_config.php';
session_start();
$status = $_SESSION['status'];
if($status !== 1){
    session_destroy;
    header("location: cms.php");
}

if (isset($_POST['submit'])) {
    echo 'pressed';
    /*
        $file_name = $_FILES['file']['name'];
        $file_type = $_FILES['file']['type'];
        $file_tmp = $_FILES['file']['tmp_name'];
        $file_size = $_FILES['file']['size'];
        $file_store = "uploads/".$file_name;
        move_uploaded_file($file_tmp, $file_store);
    */
    echo $_POST['destination'];
    echo $_POST['imagetype'];



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
        a.logout{padding:0;}
        form{
            margin: 0 500px;
            background: gray;
            color: white;

        }

    </style>
</head>

<body>
<h1 class="cms">CMS HOME Status: <?php echo $status; ?> </h1><br/><br/>
<div style="width:100px; margin:auto;"><a class="logout"href="cms_logout.php" class="back"><h2 class="back">LOG OUT</h2></a></div></br>
<div style="width:100px; margin:auto;"><a class="logout"href="cms_home.php" class="back"><h2 class="back">CMS HOME</h2></a></div> </br>  </br>

<form action="?" method="post" enctype="multipart/form-data">
    Select image to upload:
    <input type="file" value="File" name="file"><br><br>
    Select Destination:<br>
    <input type="radio" name="destination" value="Country" checked> Country<br>
    <input type="radio" name="destination" value="BW"> BW<br><br>

    Select Type:<br>
    <input type="radio" name="imagetype" value="Image" checked> Image<br>
    <input type="radio" name="imagetype" value="Thumbnails"> Thumbnail<br><br>

    <input type="submit" value="Upload" name="submit">
</form>



</body>
</html>