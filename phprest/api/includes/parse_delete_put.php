<?php
$_METHOD = $_SERVER['REQUEST_METHOD'];
if ($_METHOD == 'DELETE')
	parse_str(file_get_contents('php://input'), $_DELETE);
else if ($_METHOD == 'PUT')
	parse_str(file_get_contents('php://input'), $_PUT);
	?>