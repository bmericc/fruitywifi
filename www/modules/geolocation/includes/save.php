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

if ($type == "templates" or $type == "path") {
    if ($action == "save") {
        
        if ($tempname != "0") {
            // SAVE TAMPLATE
            if ($newdata != "") {
                //$newdata = ereg_replace(13,  "", $newdata); // DEPRECATED
                //$newdata = preg_replace(chr(13),  "", $newdata);
                $newdata = preg_replace("/[\n\r]/",  "", $newdata);
                $template_path = "$mod_path/includes/$type";
                $exec = "$bin_echo '$newdata' | base64 --decode > $template_path/$tempname";
                exec_fruitywifi($exec);
                
                $exec = "$bin_dos2unix $template_path/$tempname";
                exec_fruitywifi($exec);
            }
        }
        
    } else if ($action == "add_rename") {
    
        if ($new_rename == "0") {
            //CREATE NEW TEMPLATE
            if ($new_rename_file != "") {
                $template_path = "$mod_path/includes/$type";
                $exec = "$bin_touch $template_path/$new_rename_file";
                exec_fruitywifi($exec);

                $tempname=$new_rename_file;
            }
        } else {
            //RENAME TEMPLATE
            $template_path = "$mod_path/includes/$type";
            $exec = "$bin_mv $template_path/$new_rename $template_path/$new_rename_file";
            exec_fruitywifi($exec);

            $tempname=$new_rename_file;
        }
        
    } else if ($action == "delete") {
        if ($new_rename != "0") {
            //DELETE TEMPLATE
            $template_path = "$mod_path/includes/$type";
            $exec = "$bin_rm $template_path/$new_rename";
            exec_fruitywifi($exec);
        }
    }
    if ($type == "templates") header("Location: ../index.php?tab=3&tempname=$tempname");
    if ($type == "path") header("Location: ../index.php?tab=4&tempname=$tempname");
    exit;
}

header('Location: ../index.php');

?>