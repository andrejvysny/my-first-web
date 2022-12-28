<?php 
error_reporting(0);
include 'pages/config.php';


switch ($_GET['page']) {
    case '' :
        require __DIR__ . '/pages/home.php';
        break;
    case 'portfolio' :
        require __DIR__ . '/pages/portfolio.php';
        break;
	case 'aboutme' :
        require __DIR__ . '/pages/aboutme.php';
        break;	
    case 'contact' :
        require __DIR__ . '/pages/contact.php';
        break;
	case 'sendmail' :
        require __DIR__ . '/pages/sendmail.php';
        break;
	case 'cms' :
        require __DIR__ . '/pages/cms/cms.php';
        break;
	case 'test' :
        require __DIR__ . '/pages/test.php';
        break;	
	
    default:
        require __DIR__ . '/pages/404.php';
        break;
}



?>