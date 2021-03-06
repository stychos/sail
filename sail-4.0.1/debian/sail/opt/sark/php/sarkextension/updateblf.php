<?php
  require_once "../srkDbClass";
  require_once "../srkHelperClass";

  $id = $_REQUEST['id'] ;
  $value = strip_tags($_REQUEST['value']) ;
  $column = $_REQUEST['columnName'] ;
  $columnPosition = $_REQUEST['columnPosition'] ;
  $columnId = $_REQUEST['columnId'] ;
  $rowId = $_REQUEST['rowId'] ;
   
  /* Update a record using information about id, columnName (property
     of the object or column in the table) and value that should be
     set */ 
  $helper = new helper;
  $dbh = DB::getInstance();
  
  $helper->removeLrCr($value);
  
  preg_match( '/^(\d+)~(\d+).*$/',$id,$matches);
  $pkey = $matches[2];
  $seq = $matches[1];
/*
 * call PDO
 */
  try {
	$res=$dbh->prepare('UPDATE IPphone_Fkey SET ' . $column . '=? WHERE pkey=? and seq=?');
	$res->execute(array($value,$pkey,$seq ));	
  }
  catch (PDOException $e) {
    	echo $e->getMessage();	
  }
  
 
  echo $_REQUEST['value'];

?>
