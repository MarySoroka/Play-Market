<?php
session_start();


require 'vendor/autoload.php';

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

$isAuthorize = true;
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
        $product_id = intval($_POST['product_id']);
        $id = intval($id);
        $sth = $dbh->prepare("DELETE FROM `basket` WHERE `product_id` = :id1 AND `user_id` = :id2 LIMIT 1");
        $sth->execute(array('id1' => $product_id, 'id2' => $id));
        $sth->fetchAll(PDO::FETCH_ASSOC);
    }
    $sth = $dbh->prepare("SELECT  `playMarket`.`id`, `playMarket`.`image`,`playMarket`.`name`, `playMarket`.`genre`, `playMarket`.`cost` 
    FROM `playMarket` INNER JOIN `basket` ON `playMarket`.`id`=`basket`.`product_id` WHERE `user_id` = ?");
    $sth->execute(array($id));
    $products = $sth->fetchAll(PDO::FETCH_ASSOC);
    $loader = new FilesystemLoader('templates');
    $twig = new Environment($loader);
    $patternForMobile = '/((\+375)(-|\s)?(29|44|33)(-|\s)?([1-9][0-9]{2})(-|\s)?([0-9]{2})(-|\s)?([0-9]{2}))/';
    $patternForCity = '/((((80)-?(17)-?))([1-9][0-9]{2})(\-)?([0-9]{2})(\-)?([0-9]{2}))/';
    $phoneType = ["+375(-)(29|44|33)(-)xxx(-)xx(-)xx", "80(-)17(-)xxx(-)(xx)(-)xx"];
    $mobilePhone = [];
    $cityPhone = [];

    if (isset($_POST['create'])) {
        $phone = $_POST['phone'];
        preg_match_all($patternForMobile, $phone, $mobilePhone);
        preg_match_all($patternForCity, $phone, $cityPhone);
    }

}
echo $twig->render('basket.html.twig', ['userName' => $_SESSION['name'], 'phoneType' => $phoneType, 'mobilePhone' => $mobilePhone[0], 'cityPhone' => $cityPhone[0], 'header' => 'Basket', 'authorize' => $isAuthorize, 'products' => $products, 'actionPage' => '../basket.php']);
