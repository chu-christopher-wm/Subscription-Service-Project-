<?php
require_once('connect.php');

if (@$_POST['add']) {
    $email = $_POST['email'];

    $query = "INSERT INTO users (email) VALUES (:email)";
    $stmt = $dbh->prepare($query);
    $stmt->execute(array("email"=>$email));
}

function login($conn) {
    setcookie('token', "", 0, "/");
    $email = $_POST['email'];
    $password = sha1($_POST['password']);
    $sql = 'SELECT * FROM users WHERE email = ? AND password = ?';
    $stmt = $conn->prepare($sql);
    if ($stmt->execute(array($email, $password))) {
        $valid = false;
        while ($row = $stmt->fetch()) {
            $valid = true;
            $token = generateToken();
            $sql = 'UPDATE users SET token = ? WHERE email = ?';
            $stmt1 = $conn->prepare($sql);
            if ($stmt1->execute(array($token, $email))) {
                setcookie('token', $token, 0, "/");
                echo 'Login Successful';
            }
        }
        if(!$valid) {
            echo 'Email or Password Incorrect';
        }
    }
}

function register($conn) {
    $password = sha1($_POST['password']);
    $email = $_POST['email'];
    $token = generateToken();
    $sql = 'INSERT INTO users (password, email, token) VALUES (?, ?, ?)';
    $stmt = $conn->prepare($sql);
    try {
        if ($stmt->execute(array($password, $email, $token))) {
            setcookie('token', $token, 0, "/");
            $sql = 'INSERT INTO orders (users_id, status) (SELECT u.id, "new" FROM users u WHERE u.token = ?)';
            $stmt1 = $conn->prepare($sql);
            if ($stmt1->execute(array($token))) {
                echo 'Account Registered';
            }
        }
    }
    catch (PDOException $e) {
        echo $e->getMessage();
    }
}

function generateToken() {
    $date = date(DATE_RFC2822);
    $rand = rand();
    return sha1($date.$rand);
}
if(isset($_POST['login'])) {
    login($dbh);
}

if(isset($_POST['register'])) {
    register($dbh);
}


function getToken() {
    if (isset($_COOKIE['token'])) {
        return $_COOKIE['token'];
    }
    else {
    }
}

?>




<!DOCTYPE html>
<html lang="en">
<head>
    <title>C.C Bakery</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
    <style>
        /* Remove the navbar's default margin-bottom and rounded borders */

        body{
            margin-top: 5%;

        }

        .navbar {
            margin-bottom: 0;
            border-radius: 0;
            text-align: center;
        }

        /* Set height of the grid so .sidenav can be 100% (adjust as needed) */
        .row.content {height: 450px}

        /* Set gray background color and 100% height */
        .sidenav {
            padding-top: 20px;
            background-color: #;
            height: 100%;
        }

        /* Set black background color, white text and some padding */
        footer {
            background-color: lightcoral;
            color: white;
            padding: 15px;
        }

        /* On small screens, set height to 'auto' for sidenav and grid */
        @media screen and (max-width: 767px) {
            .sidenav {
                height: auto;
                padding: 15px;
            }
            .row.content {height:auto;}
        }


        .navbar-default .navbar-nav > li > a {
            color: #FCCDCD;

        ; /*Change active text color here*/
        }
        .navbar-default {
            background-color: #68AB9F;
            position:fixed;
            width: 100%;
            opacity:1;
            z-index: 4;
            top:0%


        }

        #bodyOne {
            margin-left: 15%;
        }

        #fixMe {
            position: relative;
            left: 15%;
        }
        
        #Round{
            position: relative;
            left: 10%;
            
        }
        
        #Break {
            position: relative;
            left: 10%;
        }
        .loginmodal-container {
            padding: 30px;
            max-width: 350px;
            width: 100% !important;
            background-color: #F7F7F7;
            margin: 0 auto;
            border-radius: 2px;
            box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            font-family: roboto;
        }

        .loginmodal-container h1 {
            text-align: center;
            font-size: 1.8em;
            font-family: roboto;
        }

        .loginmodal-container input[type=submit] {
            width: 100%;
            display: block;
            margin-bottom: 10px;
            position: relative;
        }

        .loginmodal-container input[type=text], input[type=password] {
            height: 44px;
            font-size: 16px;
            width: 100%;
            margin-bottom: 10px;
            -webkit-appearance: none;
            background: #fff;
            border: 1px solid #d9d9d9;
            border-top: 1px solid #c0c0c0;
            /* border-radius: 2px; */
            padding: 0 8px;
            box-sizing: border-box;
            -moz-box-sizing: border-box;
        }

        .loginmodal-container input[type=text]:hover, input[type=password]:hover {
            border: 1px solid #b9b9b9;
            border-top: 1px solid #a0a0a0;
            -moz-box-shadow: inset 0 1px 2px rgba(0,0,0,0.1);
            -webkit-box-shadow: inset 0 1px 2px rgba(0,0,0,0.1);
            box-shadow: inset 0 1px 2px rgba(0,0,0,0.1);
        }

        .loginmodal {
            text-align: center;
            font-size: 14px;
            font-family: 'Arial', sans-serif;
            font-weight: 700;
            height: 36px;
            padding: 0 8px;
            /* border-radius: 3px; */
            /* -webkit-user-select: none;
              user-select: none; */
        }

        .loginmodal-submit {
            /* border: 1px solid #3079ed; */
            border: 0px;
            color: #fff;
            text-shadow: 0 1px rgba(0,0,0,0.1);
            background-color: #4d90fe;
            padding: 17px 0px;
            font-family: roboto;
            font-size: 14px;
            /* background-image: -webkit-gradient(linear, 0 0, 0 100%,   from(#4d90fe), to(#4787ed)); */
        }

        .loginmodal-submit:hover {
            /* border: 1px solid #2f5bb7; */
            border: 0px;
            text-shadow: 0 1px rgba(0,0,0,0.3);
            background-color: #357ae8;
            /* background-image: -webkit-gradient(linear, 0 0, 0 100%,   from(#4d90fe), to(#357ae8)); */
        }

        .loginmodal-container a {
            text-decoration: none;
            color: #666;
            font-weight: 400;
            text-align: center;
            display: inline-block;
            opacity: 0.6;
            transition: opacity ease 0.5s;
        }

        .login-help{
            font-size: 12px;
        }

    </style>
</head>
<body>

<!-- Trigger/Open The Modal -->

<!-- The Modal -->

<div class="modal fade" id="login-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="loginmodal-container">
            <h1>Login to Your Account</h1><br>
            <form>
                <input type="text" name="user" placeholder="Username">
                <input type="password" name="pass" placeholder="Password">
                <input type="submit" name="login" class="login loginmodal-submit" value="Login">
            </form>

            <div class="login-help">
                <a href="#">Register</a> - <a href="#">Forgot Password</a>
            </div>
        </div>
    </div>
</div>

<nav class="navbar navbar-default">
    <div class="container-fluid">
        <div class="navbar-header" align="center">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a style="position: relative; left:900%;" class="navbar-brand" href="#">
                <img style="position: relative; left: 240%;"  width="40" height="30" src="http://www.freelargeimages.com/wp-content/uploads/2015/01/Cupcake_Clipart_01.png">
            </a>
        </div>
        <div align="" style="margin-left: 45%;" class="collapse navbar-collapse" id="myNavbar">
            <ul style="background-color: #68AB9F;" class="nav navbar-nav">
                <li><a href="#" data-toggle="modal" data-target="#login-modal">Login</a></li>
            </ul>
            <a class="navbar-brand" href="#">
                <img style="position: relative; left: 30%;" width="40" height="30" src="http://www.freelargeimages.com/wp-content/uploads/2015/01/Cupcake_Clipart_01.png">
            </a>
        </div>
    </div>
</nav>
<center>
    <div id="bodyOne">
<div align="center" class="container-fluid text-center">
    <div class="row content">
        <div class="col-sm-8 text-left">
            <div id="fixMe">
            <h1 style="position: relative;right: 5%;" align="center">Welcome to C.C Bakery</h1>
                
            <br>
            <center>
            <img  style="position: relative;right: 5%;"  src="https://s-media-cache-ak0.pinimg.com/236x/27/d9/f2/27d9f2bf7d9cbad1776ff698dce33220.jpg">
            </center>
            <br><br>
            <p  style="position: relative;right: 5%;"  align="center"><i>We are a unique bakery service that allows premium customers the option for fresh home delivery
            services, we deliver straight to our customers houses and we'll assure its straight out of the oven when we get it to
            their houses. We are not limited to singular orders as we also do parties and larger orders which require mass transportation as
            well as resources. We hope that you can join our premium services as well, so subscribe today for a newsletter for more information
            on how to go premium as well as any new offers we have, and taste wonderfully baked goods
            without having to worry about moving an inch!</i></p></div>
            <div id="Break"><hr><br><br></div>
                
            <div id="Round">
            <div id="carousel-example-generic" class="carousel slide" data-ride="carousel" data-interval="3000">
                <!-- Indicators -->
                <ol class="carousel-indicators">
                    <li data-target="#carousel-example-generic" data-slide-to="0" class="active"></li>
                    <li data-target="#carousel-example-generic" data-slide-to="1"></li>
                    <li data-target="#carousel-example-generic" data-slide-to="2"></li>
                </ol>

                <!-- Wrapper for slides -->
                <div class="carousel-inner">
                    <div class="item active">
                        <img src="http://www.hy-veewaverlycatering.com/webres/Image/interior-banners-1200x315/Sparkling-Berry-Hostess-Platter-1200x315.jpg" alt="...">
                    </div>
                    <div class="item">
                        <img src="http://www.hy-veecedarfallscatering.com/webres/Image/interior-banners-1200x315/CherryCheesecake-1200x315.jpg" alt="...">
                    </div>
                    <div class="item">
                        <img src="http://www.hy-veecrossroadscatering.com/webres/Image/interior-banners-1200x315/Hv_brownieTray_EH_121106_006-Edit-1200x315.jpg" alt="...">
                    </div>
                </div>
            </div>

                <!-- Controls -->
                <a class="left carousel-control" href="#carousel-example-generic" role="button" data-slide="prev">
                    <span class="glyphicon glyphicon-chevron-left"></span>
                </a>
                <a class="right carousel-control" href="#carousel-example-generic" role="button" data-slide="next">
                    <span class="glyphicon glyphicon-chevron-right"></span>
                </a>
            </div> <!-- Carousel -->
            <br><br><hr>
            <center>
            <div style="text-align: center; position: relative; left: -15%;
}" id="'news" class="container">
                <div class="row">
                    <div class="span12">
                        <div style="background-color: #DEC371;" class="thumbnail center well well-small text-center">
                            <h2>Newsletter</h2>

                            <p>Subscribe to our weekly Newsletter and stay tuned.</p>

                            <form action="" method="post">
                                <div class="input-prepend"><span class="add-on"><i class="icon-envelope"></i></span>
                                    <input style="border-radius: 15px;" type="email" id="" name="email" placeholder="your@email.com">
                                </div>
                                <br />
                                <input name="add" type="submit" value="Subscribe Now!" class="btn btn-large" />
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
        </center>

<footer class="container-fluid text-center">
    <p style="color: black;">Copyright © 1624 Christopher Chu</p>
</footer>
</center>


</body>
</html>
