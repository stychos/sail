<?php
// sarkpasswd class
// Developed by CoCo
// Copyright (C) 2016 CoCoSoFt
//
// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program.  If not, see <http://www.gnu.org/licenses/>.
//


Class sarkpasswd {
	
	protected $message; 
	protected $myPanel;
	protected $dbh;
	protected $helper;
	protected $validator;
	protected $invalidForm;
	protected $error_hash = array();
	
public function showForm() {

	$this->myPanel = new page;
	$this->dbh = DB::getInstance();
	$this->helper = new helper;	

	echo '<form id="sarkpasswdForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">';
	$this->myPanel->pagename = 'Password change';
	
	if ($_SESSION['nag']) {
		$this->myPanel->pagename = 'Change default Password';
	}
		
	if (isset($_POST['save_x'])) { 
		$this->saveNew();		
	}
	
	$this->showMain();
	
	return;		
}

private function showMain() {
//print_r($_SESSION);

	if (isset($this->message)) {
		$this->myPanel->msg = $this->message;
	} 
	echo '<div class="buttons">';	
	$this->myPanel->Button("save");
	echo '</div>';
		
	$this->myPanel->Heading();
	if (isset($this->message)) {
		foreach ($this->error_hash as $inpname => $inp_err) {
			echo "<p>$inpname : $inp_err</p>\n";
		}       
	}
	echo '<br/><br/><br/><br/>';	
	echo '<div class="globaltabedit">';		
	$this->myPanel->aLabelFor('httppassword');
	echo '<input type="password" name="password" id="password"/>' . PHP_EOL;
	$this->myPanel->aLabelFor('newpassword');
	echo '<input type="password" name="newpass" id="newpass"/>' . PHP_EOL;
	$this->myPanel->aLabelFor('newpassword2');
	echo '<input type="password" name="newpass2" id="newpass2"/>' . PHP_EOL;		
	echo '</div>';		
}

private function saveNew() {
    $tuple = array();

    $this->invalidForm = True;
	$this->message = "<B>  --  Validation Errors!</B>";
	
    if (empty($_POST['password']) ) {
    	$this->error_hash['user'] = "Password not entered!";
    	return;
    }
        
	if (empty($_POST['newpass']) ) {
		$this->error_hash['user'] = "New Password not entered!";
		return;
	}
	
	$newpass = trim($_POST['newpass']);
	$newpass2 = trim($_POST['newpass2']);			 
    if ( $newpass != $newpass2 ) {
    	$this->error_hash['user'] = "New Passwords do not match!";
    	return;
    }
    
	if ( ! preg_match ( ' /(?=^.{8,20}$)/ ',  $newpass )) {
		$this->error_hash['user'] = "Password must contain between 8 and 20 characters!";
		return;
	}
	
	$sql = $this->dbh->prepare("SELECT password,salt FROM user WHERE pkey=?");
	$sql->execute(array($_SESSION['user']['pkey']));
	$row = $sql->fetch();
	$check_password = hash('sha256', $_POST['password'] . $row['salt']); 
	for($round = 0; $round < 65536; $round++) {
			$check_password = hash('sha256', $check_password . $row['salt']); 
	}             
	if( $check_password !== $row['password']) {
		$this->error_hash['user'] = "Old Password incorrect!";
		return;
	}
	
	$salt = dechex(mt_rand(0, 2147483647)) . dechex(mt_rand(0, 2147483647)); 
	$password = hash('sha256', $newpass . $salt); 
	for($round = 0; $round < 65536; $round++) {
		$password = hash('sha256', $password . $salt); 
	}
	$tuple['pkey'] = $_SESSION['user']['pkey']; 
	$tuple['password'] = $password;
	$tuple['salt'] = $salt;
	$ret = $this->helper->setTuple('user',$tuple);
	if ($_SESSION['user']['pkey'] == 'admin') {
		$this->helper->request_syscmd ('echo "root:' . $newpass . '" | chpasswd');
	}
	$this->invalidForm = False;
	$this->message = "Password successfully changed";
	
	unset($_SESSION['user']);
	header("Location: /php/sarklogin/main.php?reset=reset");
	die ("Redirecting to login.php"); 							
	
}

}
