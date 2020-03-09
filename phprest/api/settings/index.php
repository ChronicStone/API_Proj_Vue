<?php
require('../includes/jsonheader.php');
require('../includes/parse_delete_put.php');
require('../includes/dbtools.php');

$data = json_decode(file_get_contents("php://input"));


if($_SERVER['REQUEST_METHOD'] == 'GET') {
		
	// Récup des Batch
	$qry = $db->prepare('SELECT * FROM batch');
	$qry->execute();
	$ans = $qry->fetchAll();
	$batch = array_map(function($dbentry) { return array(
		'id' => intval($dbentry['id']),
		'date' => $dbentry['date'],
		'label' => $dbentry['label'],
		'deleted' => $dbentry['deleted'],
		'state' => $dbentry['state']); 
	}, $ans);

	// Récup des departments
	$qry = $db->prepare('SELECT * FROM department');
	$qry->execute();
	$ans = $qry->fetchAll();
	$department = array_map(function($dbentry) { return array(
		'id' => intval($dbentry['id']),
		'name' => $dbentry['name'],
		'deleted' => $dbentry['deleted'],
		'state' => $dbentry['state']);
	}, $ans);

	// Récup des Organisations
	$qry = $db->prepare('SELECT * FROM organisation');
	$qry->execute();
	$ans = $qry->fetchAll();
	$organisation = array_map(function($dbentry) { return array(
		'id' => intval($dbentry['id']),
		'name' => $dbentry['name'],
		'deleted' => $dbentry['deleted'],
		'state' => $dbentry['state']);
	}, $ans);

	$settings = array(
		'batch' => $batch, 
		'department' => $department, 
		'organisation' => $organisation);
		echo json_encode(array('settings' => $settings));
}


if(isset($data->request)) {
	if($data->request == 1) {
		// CAS ou on POST un new BATCH
		if($data->type == 'batch') {
			$id = $data->id;
			$date = $data->date;
			$label = $data->label;
			$post_query = "INSERT INTO batch (id, date, label) VALUES ($id, '$date', '$label')";
			$msg['message'] = $post_query;
			$post_stmt = $db->prepare($post_query);
			if($post_stmt->execute()){
				$msg['message'] = 'Row created successfully';
			}
			else{
				$msg['message'] = 'Row not created';
			}
			echo  json_encode($msg);   
		}
		if($data->type == 'department') {
			$id = $data->id;
			$name = $data->name;
			$post_query = "INSERT INTO department (id, name) VALUES ($id, '$name')";
			$msg['message'] = $post_query;
			$post_stmt = $db->prepare($post_query);
			if($post_stmt->execute()){
				$msg['message'] = 'Row created successfully';
			}
			else{
				$msg['message'] = 'Row not created';
			}
			echo  json_encode($msg);   
		}
		if($data->type == 'organisation') {
			$id = $data->id;
			$name = $data->name;
			$post_query = "INSERT INTO organisation (id, name) VALUES ($id, '$name')";
			$msg['message'] = $post_query;
			$post_stmt = $db->prepare($post_query);
			if($post_stmt->execute()){
				$msg['message'] = 'Row created successfully';
			}
			else{
				$msg['message'] = 'Row not created';
			}
			echo  json_encode($msg);   
		}
	}

	if($data->request == 2) {
		if($data->type == 'batch') {
			$id = $data->id;
			$date = $data->date;
			$label = $data->label;

			// Chech si on a des entrées correspondantes dans la table
			$get_batch = "SELECT * FROM `batch` WHERE id=$id";
			$get_stmt = $db->prepare($get_batch);
			$get_stmt->execute();
			
			//CHECK WHETHER THERE IS ANY POST IN OUR DATABASE
			if($get_stmt->rowCount() > 0){

				$post_query = "UPDATE `batch` SET date = '$date', label = '$label' WHERE id = $id";
				$msg['message'] = $post_query;
				$post_stmt = $db->prepare($post_query);
				if($post_stmt->execute()){
					$msg['message'] = 'Row edited successfully';
				}
				else{
					$msg['message'] = 'Row not edited';
				}
			}
			else{
				$msg['message'] = 'Invlid ID';
			}  
			echo  json_encode($msg);   
		}
		if($data->type == 'department') {
			$id = $data->id;
			$name = $data->name;

			// Chech si on a des entrées correspondantes dans la table
			$get_dept = "SELECT * FROM `department` WHERE id=$id";
			$get_stmt = $db->prepare($get_dept);
			$get_stmt->execute();
			
			//CHECK WHETHER THERE IS ANY POST IN OUR DATABASE
			if($get_stmt->rowCount() > 0){

				$post_query = "UPDATE `department` SET name = '$name' WHERE id = $id";
				$msg['message'] = $post_query;
				$post_stmt = $db->prepare($post_query);
				if($post_stmt->execute()){
					$msg['message'] = 'Row edited successfully';
				}
				else{
					$msg['message'] = 'Row not edited';
				}
			}
			else{
				$msg['message'] = 'Invlid ID';
			}  
			echo  json_encode($msg);   
		}
		if($data->type == 'organisation') {
			$id = $data->id;
			$name = $data->name;

			// Chech si on a des entrées correspondantes dans la table
			$get_orga = "SELECT * FROM `organisation` WHERE id=$id";
			$get_stmt = $db->prepare($get_orga);
			$get_stmt->execute();
			
			//CHECK WHETHER THERE IS ANY POST IN OUR DATABASE
			if($get_stmt->rowCount() > 0){

				$post_query = "UPDATE `organisation` SET name = '$name' WHERE id = $id";
				$msg['message'] = $post_query;
				$post_stmt = $db->prepare($post_query);
				if($post_stmt->execute()){
					$msg['message'] = 'Row edited successfully';
				}
				else{
					$msg['message'] = 'Row not edited';
				}
			}
			else{
				$msg['message'] = 'Invlid ID';
			}  
			echo  json_encode($msg);   
		}
		
	}

	if($data->request == 3) {
		if($data->type == 'batch') {
			$id = $data->id;
			$deleted = $data->deleted;

			// Chech si on a des entrées correspondantes dans la table
			$get_batch = "SELECT * FROM `batch` WHERE id=:id";
			$get_stmt = $db->prepare($get_batch);
			$get_stmt->bindValue(':id', $id,PDO::PARAM_INT);
			$get_stmt->execute();
			
			//CHECK WHETHER THERE IS ANY POST IN OUR DATABASE
			if($get_stmt->rowCount() > 0){
        
				// FETCH POST FROM DATBASE 
				$row = $get_stmt->fetch(PDO::FETCH_ASSOC);
				
				// CHECK, IF NEW UPDATE REQUEST DATA IS AVAILABLE THEN SET IT OTHERWISE SET OLD DATA
				$batch_state = isset($data->deleted) ? $data->deleted : $row['deleted'];
				
				$update_query = "UPDATE `batch` SET deleted = $deleted WHERE id = $id";
						
				$update_stmt = $db->prepare($update_query);
				if($update_stmt->execute()){
					$msg['message'] = 'Data updated successfully';
				}else{
					$msg['message'] = 'data not updated';
				}   
			}
			else{
				$msg['message'] = 'Invlid ID';
			}  
			echo  json_encode($msg);
		}
	}
	if($data->type == 'department') {
		$id = $data->id;
		$deleted = $data->deleted;
		// Check si on a des entrées correspondantes dans la table
		$get_dept = "SELECT * FROM `department` WHERE id=:id";
		$get_stmt = $db->prepare($get_dept);
		$get_stmt->bindValue(':id', $id,PDO::PARAM_INT);
		$get_stmt->execute();
		
		//CHECK WHETHER THERE IS ANY POST IN OUR DATABASE
		if($get_stmt->rowCount() > 0){
			$row = $get_stmt->fetch(PDO::FETCH_ASSOC);		
			$dept_state = isset($data->deleted) ? $data->deleted : $row['deleted'];
			$update_query = "UPDATE `department` SET deleted = $deleted WHERE id = $id";
			$update_stmt = $db->prepare($update_query);
			if($update_stmt->execute()){
				$msg['message'] = 'Data updated successfully';
			}
			else{
				$msg['message'] = 'data not updated';
			}   
		}
		else{
			$msg['message'] = 'Invlid ID';
		}  
		echo  json_encode($msg);
	}
	if($data->type == 'organisation') {
		$id = $data->id;
		$deleted = $data->deleted;

		// Chech si on a des entrées correspondantes dans la table
		$get_orga = "SELECT * FROM `organisation` WHERE id=:id";
		$get_stmt = $db->prepare($get_orga);
		$get_stmt->bindValue(':id', $id,PDO::PARAM_INT);
		$get_stmt->execute();
		
		//CHECK WHETHER THERE IS ANY POST IN OUR DATABASE
		if($get_stmt->rowCount() > 0){
			$row = $get_stmt->fetch(PDO::FETCH_ASSOC);		
			$dept_state = isset($data->deleted) ? $data->deleted : $row['deleted'];
			$update_query = "UPDATE `organisation` SET deleted = $deleted WHERE id = $id";
			$update_stmt = $db->prepare($update_query);
			if($update_stmt->execute()){
				$msg['message'] = 'Data updated successfully';
			}
			else{
				$msg['message'] = 'data not updated';
			}   
		}
		else{
			$msg['message'] = 'Invlid ID';
		}  
		echo  json_encode($msg);
	}
}
