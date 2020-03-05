<?php
try {
    $db = new PDO('mysql:host=localhost;dbname=table_proj_langaj;charset=utf8', 'root', '');
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