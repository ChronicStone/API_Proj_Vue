<?php
require('dbconnect.php');

$tables = array('employee');
$columns = array('id', 'name', 'salary', 'seniority');

function update($table, $filterfield, $filtervalue, $updatefield, $updatevalue)
{
	global $db, $tables, $columns;
	if (!in_array($table, $tables) || !in_array($filterfield, $columns) || !in_array($updatefield, $columns))
		return true;
	$qry = $db->prepare('UPDATE ' . $table . ' SET ' . $updatefield . '=? WHERE ' . $filterfield . '=?');
	$qry->execute(array($updatevalue, $filtervalue));
	return false;
}
?>