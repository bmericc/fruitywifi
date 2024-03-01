<?php 
/*
	Copyright (C) 2013-2016  xtr4nge [_AT_] gmail.com

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
<!DOCTYPE HTML>
<html lang="en">
<head>
<meta charset="utf-8" />
<title>FruityWifi</title>
<script src="../js/jquery.js"></script>
<script src="../js/jquery-ui.js"></script>
<link rel="stylesheet" href="../css/jquery-ui.css" />
<link rel="stylesheet" href="../css/style.css" />
<link rel="stylesheet" href="../../../style.css" />

<script>
$(function() {
    $( "#action" ).tabs();
    $( "#result" ).tabs();
});

</script>

</head>
<body>

<?php include "../menu.php"; ?>

<br>

<?php

include "../../config/config.php";
include "../../login_check.php";
include "_info_.php";
include "../../functions.php";

// Checking POST & GET variables...
if ($regex == 1) {
    regex_standard($_POST["newdata"], "msg.php", $regex_extra);
    regex_standard($_GET["logfile"], "msg.php", $regex_extra);
    regex_standard($_GET["action"], "msg.php", $regex_extra);
    regex_standard($_POST["service"], "msg.php", $regex_extra);
}

$newdata = $_POST['newdata'];
$logfile = $_GET["logfile"];
$action = $_GET["action"];
$tempname = $_GET["tempname"];
$service = $_POST["service"];

?>

<div class="rounded-top" align="left"> &nbsp; <b>Kismet</b> </div>
<div class="rounded-bottom">

    &nbsp;&nbsp;version <?php echo $mod_version?><br>
    <?php 
    if (file_exists($bin_kismet_server)) { 
        echo "&nbsp;&nbsp;&nbsp;$mod_alias <font style='color:lime'>installed</font><br>";
    } else {
        echo "&nbsp;&nbsp;&nbsp;$mod_alias <a href='includes/module_action.php?install=install_$mod_name' style='color:red'>install</a><br>";
    } 
    ?>
    <?php 
    if (file_exists($bin_gpsd)) { 
        echo "&nbsp;&nbsp;&nbsp;&nbsp; GPSD <font style='color:lime'>installed</font><br>";
    } else {
        echo "&nbsp;&nbsp;&nbsp;&nbsp; GPSD <a href='includes/module_action.php?install=install_$mod_name' style='color:red'>install</a><br>";
    } 
    ?>
    <?php
    $iskismetup = exec("ps auxww | grep kismet_server | grep -v -e grep");
    if ($iskismetup != "") {
        echo "&nbsp;&nbsp;&nbsp;$mod_alias  <font color=\"lime\"><b>enabled</b></font>.&nbsp; | <a href='includes/module_action.php?service=$mod_name&action=stop&page=module'><b>stop</b></a><br />";
    } else { 
        echo "&nbsp;&nbsp;&nbsp;$mod_alias  <font color=\"red\"><b>disabled</b></font>. | <a href='includes/module_action.php?service=$mod_name&action=start&page=module'><b>start</b></a><br />";
    }
    ?>
    <?php
    $isgpsdup = exec("ps auxww | grep gpsd | grep -v -e grep");
    if ($isgpsdup != "") {
        echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;GPSD  <font color=\"lime\"><b>enabled</b></font>.&nbsp; | <a href='includes/module_action.php?service=gpsd&action=stop&page=module'><b>stop</b></a><br />";
    } else { 
        echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;GPSD  <font color=\"red\"><b>disabled</b></font>. | <a href='includes/module_action.php?service=gpsd&action=start&page=module'><b>start</b></a><br />";
    }
    ?>
    <?php
    $isttyusbup = exec("ls /dev/ttyUSB0");
    if ($isttyusbup == "/dev/ttyUSB0") {
        echo "&nbsp;&nbsp;ttyUSB0  <font color=\"lime\"><b>connected</b></font>.<br />";
    } else { 
        echo "&nbsp;&nbsp;ttyUSB0  <font color=\"red\"><b>disconnected</b></font>.<br />";
    }
    ?>
	<?php
	if (isset($_GET["show"])) {
		$exec = "gpspipe -w | grep -m 1 TPV";
		exec($exec, $output);
		
		$json = json_decode($output[0]);
		$LAT = $json->{"lat"};
		$LON = $json->{"lon"};
		
		if ($LAT != "" and $LON != "") {
			echo "&nbsp;Location <font color='black'>$LAT, $LON</font><br />";
		} else {
			echo "&nbsp;Location <font color='black'>not available</font><br />";
		}
    } else { 
        echo "&nbsp;Location <font color='black'>[<a href='?show'>show</a>]</font><br />";
    }
	?>
    
</div>

<br>

<div id="msg" style="font-size:largest;">
Loading, please wait...
</div>

<d-iv id="body" style="display:none;">
<div id="result" class="module">
    <ul>
        <li><a href="#tab-output">Output</a></li>
		<li><a href="#tab-about">About</a></li>
    </ul>

	<!-- OUTPUT -->

    <div id="tab-output" class="history" style='padding:10px;'>

        <a href="includes/output.php?file=all">export all</a>
        <?php
            if (file_exists("includes/logs/output_all.kml")) {
                echo "<a href='includes/logs/output_all.kml'><b>download</b></a>";
                echo " <a href='includes/output.php?file=output_all&action=delete'>(<b>x</b>)</a> ";
            }
        ?>
        <br>

        <?php

        $netxml = glob('includes/logs/*.netxml');

        for ($i = 0; $i < count($netxml); $i++) {
            $filename = str_replace(".netxml","",str_replace("includes/logs/","",$netxml[$i]));
            echo "<a href='includes/output.php?file=".str_replace(".netxml","",str_replace("includes/logs/","",$netxml[$i]))."&action=delete'><b>x</b></a> ";
            echo $filename . " | ";
            echo "<a href='includes/output.php?file=".str_replace(".netxml","",str_replace("includes/logs/","",$netxml[$i]))."'><b>export</b></a> | ";
            if (file_exists("includes/logs/output_$filename.kml")) {
                echo "<a href='includes/logs/output_$filename.kml'><b>download</b></a>";
            }
            echo "<br>";
        }
        ?>

    </div>

	<!-- END OUTPUT -->
	
	<!-- ABOUT -->

	<div id="tab-about" class="history">
		<?php include "includes/about.php"; ?>
	</div>
	
	<!-- END ABOUT -->

</div>

<script type="text/javascript">
$(document).ready(function() {
    $('#body').show();
    $('#msg').hide();
});
</script>

</body>
</html>
