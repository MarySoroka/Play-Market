<?php
require 'vendor/autoload.php';

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

$loader = new FilesystemLoader('templates');
$twig = new Environment($loader);
$emailPattern='/([a-zA-Z]+[a-zA-Z0-9_+.-]*)@([a-z.-]+)/';
$loginPattern='/^[a-zA-Z0-9]+$/';
function sendEmail($email, $id){
    $loader = new FilesystemLoader('templates');
    $twig = new Environment($loader);
    require './vendor/autoload.php';
    require 'PHPMailer/class.phpmailer.php';
    require 'PHPMailer/PHPMailerAutoload.php';
    $file =$twig->render('verify.html.twig', ['id' => $id]);
    $mail = new PHPMailer;

    // $mail->SMTPDebug = 4;                               // Enable verbose debug output

    $mail->isSMTP();                                      // Set mailer to use SMTP
    $mail->Host = 'smtp.mail.ru';  // Specify main and backup SMTP servers
    $mail->SMTPAuth = true;                               // Enable SMTP authentication
    $mail->Username = 'de2com.belarus@mail.ru';                 // SMTP username
    $mail->Password = '5657Trev721qwasZX';                           // SMTP password
    $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
    $mail->Port = 587;                                    // TCP port to connect to

    $mail->setFrom('de2com.belarus@mail.ru', 'Play Market');
    $mail->addAddress($email);     // Add a recipient

    $mail->addReplyTo('de2com.belarus@mail.ru');
    // print_r($_FILES['file']); exit;
    for ($i=0; $i < count($_FILES['file']['tmp_name']) ; $i++) {
        $mail->addAttachment($_FILES['file']['tmp_name'][$i], $_FILES['file']['name'][$i]);
    }
     $mail->isHTML(true);                                  // Set email format to HTML
    $mail->Subject = 'Verify your email';
    $mail->Body    = $file;
    $mail->AltBody = '';

    if(!$mail->send()) {
        echo 'Message could not be sent.';
        echo 'Mailer Error: ' . $mail->ErrorInfo;
    } else {
        echo 'Message has been sent';
    }
}
try {
    $dbh = new PDO('mysql:dbname=playMarket;host=localhost', 'root', '5657Trev721qwasZXflash_mar');
} catch (PDOException $e) {
    die($e->getMessage());
}

if (isset($_POST['registry'])) {
    $login = $_POST['login'];
    $err = '';
    if (!preg_match($loginPattern, $_POST['login'])) {
        $err = "Login must be consist of only letters and numbers";
    }
    if (!preg_match($emailPattern, $_POST['email'])) {
        $err = "Invalid email";
    }
    if (strlen($_POST['login']) < 3 or strlen($_POST['login']) > 30) {
        $err = "Login must be longer then 3 symbols";
    }
    $sth = $dbh->prepare("SELECT * FROM `users` WHERE `user_login` = ?");
    $sth->execute(array($login));
    $array = $sth->fetchAll(PDO::FETCH_ASSOC);
    if (sizeof($array) > 0) {
        $err = "User with this login exists";
    }

    if ($err == '') {
        $password = md5(md5(trim($_POST['password'])));
        $sth = $dbh->prepare("INSERT INTO `users` SET `user_login` = ?, `user_password` = ?,`user_role` = 'USER', `user_email` = ?");
        $sth->execute(array($login, $password, $_POST['email']));
        $sth = $dbh->prepare("SELECT `user_id` FROM `users` WHERE `user_login` = ?");
        $sth->execute(array($login));
        $array = $sth->fetchAll(PDO::FETCH_ASSOC);
        sendEmail($_POST['email'],$array[0]['user_id']);
        header("Location: login.php");
        exit;
    }

}

echo $twig->render('login.html.twig', ['actionPage' => '../registration.php',
    'pageName' => 'Sing In', 'pageAction' => 'sing in', 'pageIsLogIn' => false, 'errors' => $err, 'header' => 'Sing Up']);

