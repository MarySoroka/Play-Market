<?php
session_start();
require 'vendor/autoload.php';

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
$isAuthorize = true;
$isAdmin = false;
if (!(isset($_SESSION['id']))) {
    $isAuthorize = false;
}
if(isset($_SESSION['admin'])){
    $isAdmin = true;
}


try {
    $dbh = new PDO('mysql:dbname=playMarket;host=localhost', 'root', '5657Trev721qwasZXflash_mar');
} catch (PDOException $e) {
    die($e->getMessage());
}
$id = $_SESSION['id'];
if (isset($_POST['add'])) {
    $product_id = intval($_POST['product_id']);
    $id = intval($id);
    $sth = $dbh->prepare("SELECT * FROM `basket` WHERE `product_id` = ? AND `user_id` = ?");
    $sth->execute(array($product_id,$id));
    $array = $sth->fetchAll(PDO::FETCH_ASSOC);
    if (count($array) === 0) {
        $sth = $dbh->prepare("INSERT INTO `basket` SET `product_id` = :id1, `user_id` = :id2");
        $sth->execute(array('id1' => $product_id, 'id2' => $id));
        $sth->fetchAll(PDO::FETCH_ASSOC);
    }
}

$sth = $dbh->prepare("SELECT * FROM `playMarket` ORDER BY `type`");
$sth->execute();
$products = $sth->fetchAll(PDO::FETCH_ASSOC);


$loader = new FilesystemLoader('templates');
$twig = new Environment($loader);
$header = "Open your world of environment";
$home = false;
$pages = [
    ['name' => 'Home', 'active' => true, 'url' => '../index.php'],
    ['name' => 'Games', 'active' => false, 'url' => '../game.php'],
    ['name' => 'Movies', 'active' => false, 'url' => '../movies.php'],
    ['name' => 'Music', 'active' => false, 'url' => '../music.php'],
    ['name' => 'Books', 'active' => false, 'url' => '../book.php']
];


echo $twig->render('template.html.twig',
    ['actionPage' => '../index.php','home'=>$home,'pages'=>$pages,'header'=>$header, 'products' => $products,'admin'=>$isAdmin, 'authorize' => $isAuthorize]);