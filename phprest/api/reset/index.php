
<?php
require('../includes/jsonheader.php');
require('../includes/parse_delete_put.php');
require('../includes/dbtools.php');

$qry = $db->prepare('DELETE FROM clients');
$qry->execute();
echo json_encode(array('status' => 'success'));

$qry = $db->prepare('DELETE FROM employees');
$qry->execute();
echo json_encode(array('status' => 'success'));

$qry = $db->prepare('DELETE FROM projects');
$qry->execute();
echo json_encode(array('status' => 'success'));