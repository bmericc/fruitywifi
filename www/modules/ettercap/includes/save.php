<?php 
/*
    Copyright (C) 2013-2016 xtr4nge [_AT_] gmail.com

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/ 
?>
<?php
include "../../../login_check.php";
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
$mitm_type = $_POST["mitm_type"];
$mitm_value = $_POST["mitm_value"]; 
$filter_name = $_POST["filter_name"]; 

// ettercap options
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

	// MITM
	$exec = "/bin/sed -i 's/mode_options\\[\\\"M\\\"\\]\\[2\\].*/mode_options\\[\\\"M\\\"\\]\\[2\\] = \\\"$mitm_type\\\";/g' options_config.php";
    $output = exec_fruitywifi($exec);
	
	$mitm_value = str_replace("/","\\\/",$mitm_value);
	
	$exec = "/bin/sed -i 's/mode_options\\[\\\"M\\\"\\]\\[4\\].*/mode_options\\[\\\"M\\\"\\]\\[4\\] = \\\"$mitm_value\\\";/g' options_config.php";
    $output = exec_fruitywifi($exec);
	
	// FILTER
	$exec = "/bin/sed -i 's/mode_options\\[\\\"F\\\"\\]\\[2\\].*/mode_options\\[\\\"F\\\"\\]\\[2\\] = \\\"$filter_name\\\";/g' options_config.php";
    $output = exec_fruitywifi($exec);
    //echo $exec."<br>";

    header('Location: ../index.php?tab=1');
    exit;

}


// START SAVE LISTS
if ($type == "templates") {
	if ($action == "save") {
		
            if ($tempname != "0") {
                // SAVE TAMPLATE
                if ($newdata != "") {
					//$newdata = ereg_replace(13,  "", $newdata); // DEPRECATED
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
                exec_fruitywifi($exec);
            }
	}
	header("Location: ../index.php?tab=2&tempname=$tempname");
	exit;
}

header('Location: ../index.php');

?>
