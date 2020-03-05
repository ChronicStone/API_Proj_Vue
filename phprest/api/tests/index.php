
<?php
require('../includes/jsonheader.php');
require('../includes/parse_delete_put.php');
require('../includes/dbtools.php');

// Nombre d'éléments affichés par page (pagination)
$rowsperpage = 5;
if(isset($_GET['per_page'])) {
	$rowsperpage = intval($_GET['per_page']);
}
// Récupération du nb d'éléments dans ma table
$sql = "SELECT COUNT(*) AS num FROM tests";
$stmt = $db->prepare($sql);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);

// current page 
$currentpage = 1;
if(isset($_GET['page'])) {
	$currentpage = intval($_GET['page']);
}

// last page
$lastpage= intdiv($row['num'], $rowsperpage);

// next_page_url
$nextpage = 'http://localhost/API_Proj_langaj/phprest/api/tests/index.php' . '?page=' . strval($currentpage+1) . '&per_page=' . strval($rowsperpage);

//prev_page_url
$prevpage = null;
if($currentpage > 1) {
	$prevpage = 'http://localhost/API_Proj_langaj/phprest/api/tests/index.php' . '?page=' . strval($currentpage-1) . '&per_page=' . strval($rowsperpage);
}

//from
$from = 1;
if($currentpage > 1) {
	$from += ($currentpage - 1) * $rowsperpage;
}
$id_from = $from - 1;

//to
$to = $rowsperpage;
if($currentpage > 1) {
	$to += ($currentpage -1) * $rowsperpage;
}
$id_to = $rowsperpage;

//Sort order
$sortType = 2;
$direct="asc";
if(isset($_GET['sort'])) {
	if($_GET['sort'] == 'name|asc') {
		$sortType = 2;	
		$direct='asc';
	}
	else if($_GET['sort'] == 'name|desc') {
		$sortType = 2;
		$direct='desc';
	}
	else if($_GET['sort'] == 'first_name|asc') {
		$sortType = 3;
		$direct='asc';
	}
	else if($_GET['sort'] == 'first_name|desc') {
		$sortType = 3;
		$direct='desc';
	}
}
// LINKS
$links = array(
'total' => intval($row['num']),
'per_page' => $rowsperpage,
'current_page' => $currentpage,
'last_page' => $lastpage + 1,
'next_page_url' => $nextpage,
'prev_page_url' => $prevpage,
'from' => $from,
'to' => $to,
'direct' =>$direct);


switch ($_METHOD)
{
	case 'GET':
		if (isset($_GET['id']))
		{
			$qry = $db->prepare('SELECT * FROM tests WHERE id=?');
			$qry->execute(array($_GET['pseudo']));
			$ans = $qry->fetchAll();
			$infos = array_map(function($dbentry) { return array(
				'id' => $dbentry['id'],
				'name' => $dbentry['name'],
				'first_name' => $dbentry['first_name'],
				'due_date' => $dbentry['due_date'],
				'status' => $dbentry['status'],
				'gl' => $dbentry['gl'],
				'sc' => $dbentry['sc'],
				'of' => $dbentry['of']); }, $ans);
			echo json_encode(array('infos' => $infos));
		}
		else if (isset($_GET['name']))
		{
			$qry = $db->prepare('SELECT * FROM tests WHERE name=?');
			$qry->execute(array($_GET['name']));
			$ans = $qry->fetchAll();
			$infos = array_map(function($dbentry) { return array('id' => intval($dbentry['id']),
																'name' => $dbentry['name'],
																'first_name' => $dbentry['first_name'],
																'organisation' => $dbentry['organisation'],
																'mail' => $dbentry['mail'],
																'batch' => $dbentry['batch'],
																'atc_manager' => $dbentry['atc_manager'],
																'due_date' => $dbentry['due_date'],
																'status' => $dbentry['status'],
																'gl' => $dbentry['gl'],
																'sc' => $dbentry['sc'],
																'of' => $dbentry['of']); }, $ans);
			echo json_encode(array('infos' => $infos));
		}
		else
		{
			$qry = $db->prepare('SELECT * FROM tests ORDER BY :sort '.$direct.' LIMIT :min, :max');
			$qry->bindParam(':sort', $sortType, PDO::PARAM_INT);
			$qry->bindParam(':min', $id_from, PDO::PARAM_INT);
			$qry->bindParam(':max', $id_to, PDO::PARAM_INT);
			$qry->execute();
			$ans = $qry->fetchAll();
			$data = array_map(function($dbentry) { return array(
				'id' => intval($dbentry['id']),
				'name' => $dbentry['name'],
				'first_name' => $dbentry['first_name'],
				'due_date' => $dbentry['due_date'],
				'status' => $dbentry['status'],
				'gl' => $dbentry['gl'],
				'sc' => $dbentry['sc'],
				'of' => $dbentry['of']); }, $ans);
			echo json_encode(array(
				'links' => $links,
				'data' => $data));
		}
		break;
}