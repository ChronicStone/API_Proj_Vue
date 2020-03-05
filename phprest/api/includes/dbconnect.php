<?php
try {
    $db = new PDO('mysql:host=mysql-chronicstone.alwaysdata.net;dbname=chronicstone_db_langaj_api;charset=utf8', '201299', 'Legendex$99');
} catch (PDOException $e) {
    print "Erreur !: " . $e->getMessage() . "<br/>";
    die();
}


/*
try {
    $db = new PDO('mysql:host=localhost;dbname=db_api;charset=utf8', 'root', '');
} catch (PDOException $e) {
    print "Erreur !: " . $e->getMessage() . "<br/>";
    die();
} */
?>