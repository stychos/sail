<?php

  require "../srkDbClass";
  require "../srkHelperClass";
  
  $helper = new helper;
  $id = $_REQUEST['id'] ;
 
  /* delete a record using information about id, */ 
  $helper->delTuple("lineio",$id); 
  echo "ok";

?>
