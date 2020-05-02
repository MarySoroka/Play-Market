<?php
try {
    $dbh = new PDO('mysql:dbname=playMarket;host=localhost', 'root', '5657Trev721qwasZXflash_mar');
} catch (PDOException $e) {
    die($e->getMessage());
}
$sth = $dbh->prepare("UPDATE `users` SET `user_role`='CONF_USER' WHERE `user_id` = ?");
$sth->execute(array(intval($_GET['id'])));
header("Location: login.php");
exit;