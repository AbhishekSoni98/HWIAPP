<?php
require_once "pdo.php";
session_start();
if ( ! isset($_SESSION['name']) && ! isset($_SESSION['user_id'])){
    die("ACCESS DENIED");
}
if (! isset($_REQUEST['term']) )
{
    die("ACCESS DENIED");
}
$stmt = $pdo->prepare('SELECT name FROM medicine
    WHERE name LIKE :prefix');
$stmt->execute(array( ':prefix' => $_REQUEST['term']."%"));

$retval = array();
while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
    $retval[] = $row['name'];
}

echo(json_encode($retval, JSON_PRETTY_PRINT));
?>