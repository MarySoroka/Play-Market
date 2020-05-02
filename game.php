<?php
session_start();
require 'vendor/autoload.php';
$isAuthorize = true;
$isAdmin = false;
if (!(isset($_SESSION['id']))) {
    $isAuthorize = false;
}
if(isset($_SESSION['admin'])){
    $isAdmin = true;
}
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
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
$sth = $dbh->prepare("SELECT * FROM `playMarket` WHERE `type` ='games'");
$sth->execute();
$products = $sth->fetchAll(PDO::FETCH_ASSOC);

$loader = new FilesystemLoader('templates');
$twig = new Environment($loader);
$header = "Games";
$home= true;
$pages = [
    ['name' => 'Home', 'active' => false, 'url' => '../index.php'],
    ['name' => 'Games', 'active' => true, 'url' => '../game.php'],
    ['name' => 'Movies', 'active' => false, 'url' => '../movies.php'],
    ['name' => 'Music', 'active' => false, 'url' => '../music.php'],
    ['name' => 'Books', 'active' => false, 'url' => '../book.php']
];

$genres = [ 'Arcade',
    'Trivial',
    'Puzzles',
    'Race',
    'Casino',
    'Casual',
    'Card'];
echo $twig->render('template.html.twig',
    ['actionPage'=>'../game.php','home'=>$home, 'pages'=>$pages, 'genres'=>$genres, 'header'=>$header, 'products' => $products,'admin'=>$isAdmin, 'authorize' =>$isAuthorize]);