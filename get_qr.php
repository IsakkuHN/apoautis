<?php

    if (isset($_GET['emp']) && $_GET['emp']!='') {
           //set it to writable location, a place for temp generated PNG files
        $PNG_TEMP_DIR = dirname(__FILE__).DIRECTORY_SEPARATOR.'temp'.DIRECTORY_SEPARATOR;

        //html PNG location prefix
        $PNG_WEB_DIR = 'temp/';

        include "phpqrcode.php";

        //ofcourse we need rights to create temp dir
        if (!file_exists($PNG_TEMP_DIR))
            mkdir($PNG_TEMP_DIR);


        $filename = $PNG_TEMP_DIR.'240d439fb2368fec010384629602b382.png';



            //default data
            QRcode::png($_GET['emp'], $filename, 'H', 10, 0);


        //display generated file
            //echo '<img src="'.$PNG_WEB_DIR.basename($filename).'" />';
            //$png = imagecreatefrompng($PNG_WEB_DIR.basename($filename));
            //echo $png;
        //config form
            header('Content-Type: image/jpeg');
            echo file_get_contents($PNG_WEB_DIR.basename($filename));
    }


    // benchmark
