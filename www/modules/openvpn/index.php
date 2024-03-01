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
include "../../login_check.php";
include "../../config/config.php";
include "_info_.php";
include "../../functions.php";

// Checking POST & GET variables...
if ($regex == 1) {
	regex_standard($_POST["newdata"], "msg.php", $regex_extra);
    regex_standard($_GET["logfile"], "msg.php", $regex_extra);
    regex_standard($_GET["action"], "msg.php", $regex_extra);
    regex_standard($_POST["service"], "msg.php", $regex_extra);
	regex_standard($_POST["macaddress"], "msg.php", $regex_extra);
	regex_standard($_POST["ss_iface"], "msg.php", $regex_extra);
}

$newdata = $_POST['newdata'];
$logfile = $_GET["logfile"];
$action = $_GET["action"];
$tempname = $_GET["tempname"];
$service = $_POST["service"];
$macaddress = $_POST["macaddress"];
$check_time = $_POST["check_time"];
$ss_iface = $_POST["ss_iface"];

// DELETE LOG
if ($logfile != "" and $action == "delete") {
    $exec = "$bin_rm ".$mod_logs_history.$logfile.".log";
    exec_fruitywifi($exec);
}

// SET MODE
if ($_POST["change_iface"] == "1") {
    $mod_iface = $ss_iface;
    $exec = "/bin/sed -i 's/mod_iface.*/mod_iface = \\\"".$mod_iface."\\\";/g' _info_.php";
    $output = exec_fruitywifi($exec);
}

if ($_POST["change_macaddress"] == "1") {
    $ss_mode = $service;
    $exec = "/bin/sed -i 's/ss_macaddress.*/ss_macaddress = \\\"".$macaddress."\\\";/g' _info_.php";
    $output = exec_fruitywifi($exec);
	$ss_macaddress = $macaddress;
	
	$exec = "/bin/sed -i 's/ss_time.*/ss_time = \\\"".$check_time."\\\";/g' _info_.php";
    $output = exec_fruitywifi($exec);
	$ss_time = $check_time;
	
}


?>

<div class="rounded-top" align="left"> &nbsp; <b><?php echo $mod_alias?></b> </div>
<div class="rounded-bottom">

    &nbsp; version <?php echo $mod_version?><br>
    <?php 
    if (file_exists($bin_openvpn)) { 
        echo "&nbsp;&nbsp;$mod_alias <font style='color:lime'>installed</font><br>";
    } else {
        echo "&nbsp;&nbsp;$mod_alias <a href='includes/module_action.php?install=install_$mod_name' style='color:red'>install</a><br>";
    } 
    ?>
	<?php 
    if (file_exists("includes/client.ovpn")) { 
        echo "&nbsp;&nbsp; Config <font style='color:lime'>uploaded</font><br>";
    } else {
        echo "&nbsp;&nbsp; Config <font style='color:red'>.ovpn required</font><br>";
    } 
    ?>
    <?php
    $ismoduleup = exec("$mod_isup");
    if ($ismoduleup != "") {
        echo "&nbsp; $mod_alias  <font color=\"lime\"><b>enabled</b></font>&nbsp; | <a href=\"includes/module_action.php?service=$mod_name&action=stop&page=module\"><b>stop</b></a>";
    } else { 
        echo "&nbsp; $mod_alias  <font color=\"red\"><b>disabled</b></font> | <a href=\"includes/module_action.php?service=$mod_name&action=start&page=module\"><b>start</b></a>"; 
    }
    ?>
	
	
</div>

<br>

<div id="msg" style="font-size:largest;">
Loading, please wait...
</div>

<div id="body" style="display:none;">

    <div id="result" class="module">
        <ul>
            <li><a href="#result-1">Output</a></li>
            <li><a href="#result-2">History</a></li>
			<li><a href="#result-3">Config</a></li>
			<li><a href="#result-4">About</a></li>
        </ul>
        
        <!-- OUTPUT -->

        <div id="result-1">
            
			<div>
				<form id="formLogs-Refresh" name="formLogs-Refresh" method="POST" autocomplete="off" action="index.php">
					<input type="submit" value="refresh">
					<br><br>
					<?php
						if ($logfile != "" and $action == "view") {
							$filename = $mod_logs_history.$logfile.".log";
						} else {
							$filename = $mod_logs;
						}
					
						$data = open_file($filename);
						
						// REVERSE
						$data_array = explode("\n", $data);
						$data = implode("\n",array_reverse($data_array));
						
					?>
					<textarea id="output" class="module-content" style="font-family: courier;"><?php echo htmlspecialchars($data)?></textarea>
					<input type="hidden" name="type" value="logs">
				</form>
		
			</div>
            
        </div>
	
		<!-- HISTORY -->

        <div id="result-2" class="history">
            <input type="submit" value="refresh">
            <br><br>
            
            <?php
            $logs = glob($mod_logs_history.'*.log');
            print_r($a);

            for ($i = 0; $i < count($logs); $i++) {
                $filename = str_replace(".log","",str_replace($mod_logs_history,"",$logs[$i]));
                echo "<a href='?logfile=".str_replace(".log","",str_replace($mod_logs_history,"",$logs[$i]))."&action=delete&tab=2'><b>x</b></a> ";
                echo $filename . " | ";
                echo "<a href='?logfile=".str_replace(".log","",str_replace($mod_logs_history,"",$logs[$i]))."&action=view'><b>view</b></a>";
                echo "<br>";
            }
            ?>
            
        </div>

		<!-- END HISTORY -->
		
		<!-- CONFIG -->

        <div id="result-3" class="history">
            <div class="rounded-top" align="center"> OpenVPN Config (.ovpn) </div>
            <div class="rounded-bottom general">
				<br>
                <form action="includes/module_action.php" method="post" enctype="multipart/form-data">
					<input type="file" name="fileToUpload" id="fileToUpload">
					<br>
					<input type="submit" value="upload" name="submit">
					<input type="hidden" name="upload" value="upload">
				</form>
				<br>
					
            </div>
        </div>
        
        <!-- END CONFIG -->
		
		<!-- ABOUT -->

        <div id="result-4" class="history">
            <?php include "includes/about.php"; ?>
        </div>
        
        <!-- END ABOUT -->
        
    </div>

    <div id="loading" class="ui-widget" style="width:100%;background-color:#000; padding-top:4px; padding-bottom:4px;color:#FFF">
        Loading...
    </div>

    <script>
    $('#formLogs').submit(function(event) {
        event.preventDefault();
        $.ajax({
            type: 'POST',
            url: 'includes/ajax.php',
            data: $(this).serialize(),
            dataType: 'json',
            success: function (data) {
                console.log(data);

                $('#output').html('');
                $.each(data, function (index, value) {
                    $("#output").append( value ).append("\n");
                });
                
                $('#loading').hide();
            }
        });
        
        $('#output').html('');
        $('#loading').show()

    });

    $('#loading').hide();

    </script>

    <script>
    $('#form1').submit(function(event) {
        event.preventDefault();
        $.ajax({
            type: 'POST',
            url: 'includes/ajax.php',
            data: $(this).serialize(),
            dataType: 'json',
            success: function (data) {
                console.log(data);

                $('#output').html('');
                $.each(data, function (index, value) {
                    if (value != "") {
                        $("#output").append( value ).append("\n");
                    }
                });
                
                $('#loading').hide();

            }
        });
        
        $('#output').html('');
        $('#loading').show()

    });

    $('#loading').hide();

    </script>

    <script>
    $('#formInject2').submit(function(event) {
        event.preventDefault();
        $.ajax({
            type: 'POST',
            url: 'includes/ajax.php',
            data: $(this).serialize(),
            dataType: 'json',
            success: function (data) {
                console.log(data);

                $('#inject').html('');
                $.each(data, function (index, value) {
                    $("#inject").append( value ).append("\n");
                });
                
                $('#loading').hide();
                
            }
        });
        
        $('#output').html('');
        $('#loading').show()

    });

    $('#loading').hide();

    </script>

    <?php
    if ($_GET["tab"] == 1) {
        echo "<script>";
        echo "$( '#result' ).tabs({ active: 0 });";
        echo "</script>";
    } else if ($_GET["tab"] == 2) {
        echo "<script>";
        echo "$( '#result' ).tabs({ active: 1 });";
        echo "</script>";
    } else if ($_GET["tab"] == 3) {
        echo "<script>";
        echo "$( '#result' ).tabs({ active: 3 });";
        echo "</script>";
    } else if ($_GET["tab"] == 4) {
        echo "<script>";
        echo "$( '#result' ).tabs({ active: 4 });";
        echo "</script>";
    } 
    ?>

</div>

<script type="text/javascript">
$(document).ready(function() {
    $('#body').show();
    $('#msg').hide();
});
</script>

</body>
</html>
