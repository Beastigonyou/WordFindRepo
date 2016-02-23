<!doctype html public "-//W3C//DTD HTML 4.0 //EN">
<html>
<head>
    <title>Word Find Answer Key</title>
    <style type = "text/css">
        body{
            background: tan;
        }
    </style>
</head>
<body>

<?php

//answer key for word find
//called from wordFind.php
$key=$_GET["key"];
$puzzleName=$_POST["puzzleName"];
print <<<HERE
<center>
<h1>$puzzleName Answer Key</h1>
$key
</center>
 
HERE;
?>
</body>
</html>