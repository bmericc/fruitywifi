<?php

//include "../login_check.php";
include "../../../config/config.php";
include "../_info_.php";
include "../../../functions.php";

include "options_config.php";

// Checking POST & GET variables...
if ($regex == 1) {
	regex_standard($_POST['type'], "../../../msg.php", $regex_extra);
	regex_standard($_POST['tempname'], "../../../msg.php", $regex_extra);
	regex_standard($_POST['action'], "../../../msg.php", $regex_extra);
	regex_standard($_GET['mod_action'], "../../../msg.php", $regex_extra);
	regex_standard($_GET['mod_service'], "../../../msg.php", $regex_extra);
	regex_standard($_POST['new_rename'], "../../../msg.php", $regex_extra);
	regex_standard($_POST['new_rename_file'], "../../../msg.php", $regex_extra);
}

$type = $_POST['type'];
$tempname = $_POST['tempname'];
$action = $_POST['action'];
$mod_action = $_GET['mod_action'];
$mod_service = $_GET['mod_service'];
$newdata = html_entity_decode(trim($_POST["newdata"]));
$newdata = base64_encode($newdata);
$new_rename = $_POST["new_rename"];
$new_rename_file = $_POST["new_rename_file"];

$autossh_host = $_POST["autossh_host"];
$autossh_port = $_POST["autossh_port"];
$autossh_listen = $_POST["autossh_listen"];
$autossh_rport = $_POST["autossh_port"];

// autossh settings
if ($type == "settings") {

	
    $exec = "/bin/sed -i 's/autossh_host.*/autossh_host = \\\"".$_POST["autossh_host"]."\\\";/g' options_config.php";
    $output = exec_fruitywifi($exec);

    $exec = "/bin/sed -i 's/autossh_port.*/autossh_port = \\\"".$_POST["autossh_port"]."\\\";/g' options_config.php";
    exec_fruitywifi($exec);

    $exec = "/bin/sed -i 's/autossh_listen.*/autossh_listen = \\\"".$_POST["autossh_listen"]."\\\";/g' options_config.php";
    exec_fruitywifi($exec);
	
	$exec = "/bin/sed -i 's/autossh_rport.*/autossh_rport = \\\"".$_POST["autossh_rport"]."\\\";/g' options_config.php";
    exec_fruitywifi($exec);
	
    header('Location: ../index.php?tab=0');
    exit;

}

// autossh options
if ($type == "mode_ettercap") {

    $tmp = array_keys($mode_options);
    for ($i=0; $i< count($tmp); $i++) {
        //echo $tmp[$i]."<br>";
        
        $exec = "/bin/sed -i 's/mode_options\\[\\\"".$tmp[$i]."\\\"\\]\\[0\\].*/mode_options\\[\\\"".$tmp[$i]."\\\"\\]\\[0\\] = 0;/g' options_config.php";
        $output = exec_fruitywifi($exec);
        
    }

    $tmp = $_POST["options"];
    for ($i=0; $i< count($tmp); $i++) {
        //echo $tmp[$i]."<br>";
        
        $exec = "/bin/sed -i 's/mode_options\\[\\\"".$tmp[$i]."\\\"\\]\\[0\\].*/mode_options\\[\\\"".$tmp[$i]."\\\"\\]\\[0\\] = 1;/g' options_config.php";
        $output = exec_fruitywifi($exec);
        
    }

    header('Location: ../index.php?tab=1');
    exit;

}


// START SAVE LISTS
if ($type == "templates") {
	if ($action == "save") {
		
		if ($tempname != "0") {
			// SAVE TAMPLATE
			if ($newdata != "") {
				//$newdata = ereg_replace(13,  "", $newdata); //DEPRECATED
				$newdata = preg_replace("/[\n\r]/",  "", $newdata);
				$template_path = "$mod_path/includes/templates";
        		$exec = "/bin/echo '$newdata' | base64 --decode > $template_path/$tempname";
                $output = exec_fruitywifi($exec);
    		}
    	}
    	
	} else if ($action == "add_rename") {
	
		if ($new_rename == "0") {
			//CREATE NEW TEMPLATE
			if ($new_rename_file != "") {
				$template_path = "$mod_path/includes/templates";
				$exec = "/bin/touch $template_path/$new_rename_file";
                $output = exec_fruitywifi($exec);

				$tempname=$new_rename_file;
			}
		} else {
			//RENAME TEMPLATE
			$template_path = "$mod_path/includes/templates";
			$exec = "/bin/mv $template_path/$new_rename $template_path/$new_rename_file";
            $output = exec_fruitywifi($exec);

			$tempname=$new_rename_file;
		}
		
	} else if ($action == "delete") {
		if ($new_rename != "0") {
			//DELETE TEMPLATE
			$template_path = "$mod_path/includes/templates";
			$exec = "/bin/rm $template_path/$new_rename";
            $output = exec_fruitywifi($exec);
		}
	}
	header("Location: ../index.php?tab=2&tempname=$tempname");
	exit;
}

header('Location: ../index.php');

?>
