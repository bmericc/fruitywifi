<?php 
/*
    Copyright (C) 2013-2015 xtr4nge [_AT_] gmail.com

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
    regex_standard($_POST['type'], "../msg.php", $regex_extra);
    regex_standard($_POST['action'], "../msg.php", $regex_extra);
    regex_standard($_POST['mod_name'], "../msg.php", $regex_extra);
}

$type = $_POST['type'];
$action = $_POST['action'];
$newdata = html_entity_decode(trim($_POST["newdata"]));
$newdata = base64_encode($newdata);

$supplicant_ssid = $_POST['supplicant_ssid'];
$supplicant_psk = $_POST['supplicant_psk'];

$mb_apn = $_POST['mb_apn'];
$mb_username = $_POST['mb_username'];
$mb_password = $_POST['mb_password'];

// mobile broadband options
if ($type == "save_mobile_broadband") {

    $exec = "/bin/sed -i 's/^apn=.*/apn=$mb_apn/g' FruityWifi_Mobile";
    $output = exec_fruitywifi($exec);

    $exec = "/bin/sed -i 's/^username=.*/username=$mb_username/g' FruityWifi_Mobile";
    $output = exec_fruitywifi($exec);

    $exec = "/bin/sed -i 's/^password=.*/password=$mb_password/g' FruityWifi_Mobile";
    $output = exec_fruitywifi($exec);
}

header('Location: ../index.php');
exit;

?>
