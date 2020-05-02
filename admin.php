<?php
session_start();


require 'vendor/autoload.php';

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

$isAuthorize = true;
$loader = new FilesystemLoader('templates');
$twig = new Environment($loader);

if (!(isset($_SESSION['id']))) {
    $isAuthorize = false;
    http_response_code(403);
} else {
    try {
        $dbh = new PDO('mysql:dbname=playMarket;host=localhost', 'root', '5657Trev721qwasZXflash_mar');
    } catch (PDOException $e) {
        die($e->getMessage());
    }
    $id = $_SESSION['id'];

    if (isset($_POST['delete'])) {
        $product_id = intval($_POST['id']);
        $id = intval($id);
        $sth = $dbh->prepare("DELETE FROM `playMarket` WHERE `id` = :id1 LIMIT 1");
        $sth->execute(array('id1' => $id));
        $sth->fetchAll(PDO::FETCH_ASSOC);
    }
    if (isset($_POST['save'])) {
        $name =$_POST['name'];
        $genre = $_POST['genre'];
        $cost = intval($_POST['cost']);
        $id = intval($_POST['id']);
        $sth = $dbh->prepare("UPDATE `playMarket` SET `name`=?,`genre`=?,`cost`=? WHERE `id` = ?");
        $sth->execute(array($name, $genre,$cost,$id ));
    }
    $sth = $dbh->prepare("SELECT  * FROM `playMarket`  WHERE 1");
    $sth->execute();
    $products = $sth->fetchAll(PDO::FETCH_ASSOC);

}
echo $twig->render('basket.html.twig', ['userName' => $_SESSION['name'],'header' => 'Products Edit','admin'=>true, 'authorize' => $isAuthorize, 'products' => $products, 'actionPage' => '../admin.php']);
