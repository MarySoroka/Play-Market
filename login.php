<?php
session_start();
require 'vendor/autoload.php';

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

$loader = new FilesystemLoader('templates');
$twig = new Environment($loader);
$errors = '';
$isAuthorize = false;
$isAdmin = false;
try {
    $dbh = new PDO('mysql:dbname=playMarket;host=localhost', 'root', '5657Trev721qwasZXflash_mar');
} catch (PDOException $e) {
    die($e->getMessage());
}

// Функция для генерации случайной строки
function generateCode($length = 6)
{
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHI JKLMNOPRQSTUVWXYZ0123456789";
    $code = "";
    $clen = strlen($chars) - 1;
    while (strlen($code) < $length) {
        $code .= $chars[mt_rand(0, $clen)];
    }
    return $code;
}


if (isset($_POST['registry'])) {

    $login = $_POST['login'];
    $sth = $dbh->prepare("SELECT `user_id`, `user_password`,`user_role`  FROM `users` WHERE `user_login`= :login");
    $sth->execute(array('login' => $login));
    $data = $sth->fetchAll(PDO::FETCH_ASSOC);
    if (strcmp($data[0]['user_password'], md5(md5(trim($_POST['password'])))) === 0) {
        if ((strcmp($data[0]['user_role'], 'CONF_USER')===0)) {
            $hash = md5(generateCode(10));
            $insip = $_SERVER['REMOTE_ADDR'];
            $sth = $dbh->prepare("UPDATE `users` SET `user_hash` = ?,`user_ip` = ? WHERE `user_id` = ?");
            $sth->execute(array($hash, $insip, $data[0]['user_id']));
            $_SESSION['id'] = $data[0]['user_id'];
            $_SESSION['name'] = $login;
            $isAuthorize = true;
            header("Location: basket.php");
            exit;
        } elseif ((strcmp($data[0]['user_role'], 'ADMIN')===0)) {
            $hash = md5(generateCode(10));
            $insip = $_SERVER['REMOTE_ADDR'];
            $sth = $dbh->prepare("UPDATE `users` SET `user_hash` = ?,`user_ip` = ? WHERE `user_id` = ?");
            $sth->execute(array($hash, $insip, $data[0]['user_id']));
            $_SESSION['id'] = $data[0]['user_id'];
            $_SESSION['name'] = $login;
            $_SESSION['admin'] = true;
            $isAuthorize = true;
            $isAdmin = true;
            header("Location: admin.php");
            exit;
        }else{
            $errors = "Verify your email";

        }
    } else {
        $errors = "You entered invalid login or password";

    }
}


echo $twig->render('login.html.twig', ['actionPage' => '../login.php', 'pageName' => 'Log In', 'pageAction' => 'log in', 'pageIsLogIn' => true, 'errors' => $errors, 'header' => 'Log In', 'authorize' => $isAuthorize]);
