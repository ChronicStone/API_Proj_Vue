
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
	else if($_GET['sort'] == 'gl|asc') {
		$sortType = 10;
		$direct='asc';
	}
	else if($_GET['sort'] == 'gl|desc') {
		$sortType = 10;
		$direct='desc';
	}
	else if($_GET['sort'] == 'status|asc') {
		$sortType = 9;
		$direct='asc';
	}
	else if($_GET['sort'] == 'status|desc') {
		$sortType = 9;
		$direct='desc';
	}
}

$filter=[];
if(isset($_GET['filter'])) {
	$filter= json_decode($_GET['filter']);
	for($i=0;$i<count($filter);$i++) {
		$filter[$i] = '%' . $filter[$i] . '%'; 
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
'direct' =>$direct,
'filter' => $filter);


switch ($_METHOD)
{
	case 'GET':
		if(isset($_GET['filter'])) {
			$qry = $db->prepare("SELECT * FROM tests WHERE name LIKE :filtName AND status LIKE :filtStatus AND department LIKE :filtDept AND batch LIKE :filtBatch AND organisation LIKE :filtOrga AND due_date LIKE :filtDueDate AND level LIKE :filtLevel AND month LIKE :filtMonth  AND year LIKE :filtYear ORDER BY :sort LIMIT :min, :max");
			$qry->bindParam(':sort', $sortType, PDO::PARAM_INT);
			$qry->bindParam(':filtName', $filter[0], PDO::PARAM_STR);
			$qry->bindParam(':filtStatus', $filter[1], PDO::PARAM_STR);
			$qry->bindParam(':filtDept', $filter[2], PDO::PARAM_STR);
			$qry->bindParam(':filtBatch', $filter[3], PDO::PARAM_STR);
			$qry->bindParam(':filtOrga', $filter[4], PDO::PARAM_STR);
			$qry->bindParam(':filtDueDate', $filter[5], PDO::PARAM_STR);
			$qry->bindParam(':filtLevel', $filter[6], PDO::PARAM_STR);
			$qry->bindParam(':filtMonth', $filter[7], PDO::PARAM_STR);
			$qry->bindParam(':filtYear', $filter[8], PDO::PARAM_STR);
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