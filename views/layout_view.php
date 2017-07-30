<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Smoothrition</title>
    <link rel="icon"
          type="image/png"
          href="images/favicon.png">
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css" />
    <link rel="stylesheet" type="text/css" href="css/select2.min.css" />
    <link rel="stylesheet" type="text/css" href="css/style.css" />
</head>
<body>
<nav class="navbar navbar-inverse">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#">Smoothrition</a>
        </div>
        <div class="collapse navbar-collapse" id="myNavbar">
            <ul class="nav navbar-nav">
                <li data-page="smooth-it" class="menu-item"><a href="#">Smooth-it</a></li>
                <li data-page="individual-calc" class="menu-item"><a href="#">Pojedinačno</a></li>
                <li data-page="food-input" class="menu-item"><a href="#">Unos namirnica</a></li>
                <li data-page="dri-input" class="menu-item"><a href="#">Unos DRI</a></li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <?php
                if (!isset($_SESSION['username']) || $_SESSION['username'] != '') {
                ?>
                    <li><a href="index.php?do=logout"><span class="glyphicon glyphicon-log-out"></span> Logout</a></li>
                <?php } ?>
            </ul>
        </div>
    </div>
</nav>
<div id="wrapper">
    <img src="images/bgn<?=rand(1,5)?>.jpg" />
</div>
<div id="loader">
    <div id="overlay"></div>
    <img src="images/loader.gif" />
</div>
<script src="js/jquery-3.1.0.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/select2.min.js"></script>
<script src="js/main.js"></script>
</body>
</html>