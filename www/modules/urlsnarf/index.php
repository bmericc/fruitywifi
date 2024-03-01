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
include "_info_.php";
include "../../login_check.php";
include "../../functions.php";

// Checking POST & GET variables...
if ($regex == 1) {
    regex_standard($_POST["newdata"], "msg.php", $regex_extra);
    regex_standard($_GET["logfile"], "msg.php", $regex_extra);
    regex_standard($_GET["action"], "msg.php", $regex_extra);
}

$newdata = $_POST['newdata'];
$logfile = $_GET["logfile"];
$action = $_GET["action"];

// SAVE DNSSPOOF HOSTS
if ($newdata != "") {
	//$newdata = ereg_replace(13,  "", $newdata); // DEPRECATED
    $newdata = preg_replace("/[\n\r]/",  "", $newdata);
	$exec = "$bin_echo '$newdata' > /usr/share/fruitywifi/conf/spoofhost.conf";
    exec_fruitywifi($exec);
}

// DELETE LOG
if ($logfile != "" and $action == "delete") {
    $exec = "$bin_rm ".$mod_logs_history.$logfile.".log";
    exec_fruitywifi($exec);
}

?>

<div class="rounded-top" align="left"> &nbsp; <?php echo $mod_alias?> </div>
<div class="rounded-bottom">
    &nbsp;&nbsp;&nbsp;version <?php echo $mod_version?><br>
    
    <?php 
    if (file_exists($bin_urlsnarf)) { 
        echo "&nbsp;&nbsp;$mod_alias <font style='color:lime'>installed</font><br>";
    } else {
	echo "&nbsp;&nbsp;$mod_alias <a href='includes/module_action.php?install=install_$mod_name' style='color:red'>install</a><br>";
    } 
    ?>
    
    <?php
    $isurlsnarfup = exec("ps auxww | grep urlsnarf | grep -v -e grep");
    if ($isurlsnarfup != "") {
        echo "&nbsp;&nbsp;$mod_alias  <font color=\"lime\"><b>enabled</b></font>.&nbsp; | <a href='includes/module_action.php?service=urlsnarf&action=stop&page=module'><b>stop</b></a>";
    } else { 
        echo "&nbsp;&nbsp;$mod_alias  <font color=\"red\"><b>disabled</b></font>. | <a href='includes/module_action.php?service=urlsnarf&action=start&page=module'><b>start</b></a>";
    }

    ?>

</div>

<br>

<div id="result" class="module">
    <ul>
        <li><a href="#result-1">Output</a></li>
        <li><a href="#result-2">History</a></li>
		<li><a href="#result-3">About</a></li>
    </ul>
    <div id="result-1" >
        <form id="formLogs" name="formLogs" method="POST" autocomplete="off">
        <input type="submit" value="refresh">
        <br><br>
        <?php
            if ($logfile != "" and $action == "view") {
                $filename = $mod_logs_history.$logfile.".log";
            } else {
                $filename = $mod_logs;
            }

            /*
            $fh = fopen($filename, "r") or die("Could not open file.");
            $data = fread($fh, filesize($filename)) or die("Could not read file.");
            fclose($fh);
            */
            
            $data = open_file($filename);
            
            $data_array = explode("\n", $data);
            $data = implode("\n",array_reverse($data_array));
            
        ?>
        <textarea id="output" class="module-content"><?php echo $data?></textarea>
        <input type="hidden" name="type" value="logs">
        </form>
    </div>
    <div id="result-2" class="history">
        <input type="submit" value="refresh">
        <br><br>
        
        <?php
        $logs = glob($mod_logs_history.'*.log');
        print_r($a);

        for ($i = 0; $i < count($logs); $i++) {
            $filename = str_replace(".log","",str_replace($mod_logs_history,"",$logs[$i]));
            echo "<a href='?logfile=".str_replace(".log","",str_replace($mod_logs_history,"",$logs[$i]))."&action=delete&tab=1'><b>x</b></a> ";
            echo $filename . " | ";
            echo "<a href='?logfile=".str_replace(".log","",str_replace($mod_logs_history,"",$logs[$i]))."&action=view'><b>view</b></a>";
            echo "<br>";
        }
        ?>
        
    </div>
	
	<!-- ABOUT -->

	<div id="result-3" class="history">
		<?php include "includes/about.php"; ?>
	</div>
	
	<!-- END ABOUT -->
</div>

<?php
if ($_GET["tab"] == 1) {
    echo "<script>";
    echo "$( '#result' ).tabs({ active: 1 });";
    echo "</script>";
} else if ($_GET["tab"] == 2) {
    echo "<script>";
    echo "$( '#result' ).tabs({ active: 2 });";
    echo "</script>";
} else if ($_GET["tab"] == 3) {
    echo "<script>";
    echo "$( '#result' ).tabs({ active: 3 });";
    echo "</script>";
} else if ($_GET["tab"] == 4) {
    echo "<script>";
    echo "$( '#result' ).tabs({ active: 4 });";
    echo "</script>";
} else if ($_GET["tab"] == 5) {
    echo "<script>";
    echo "$( '#result' ).tabs({ active: 5 });";
    echo "</script>";
}
?>

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
$('#formHosts').submit(function(event) {
    event.preventDefault();
    $.ajax({
        type: 'POST',
        url: 'includes/ajax.php',
        data: $(this).serialize(),
        dataType: 'json',
        success: function (data) {
            console.log(data);

            $('#hosts').html('');
            $.each(data, function (index, value) {
                $("#hosts").append( value ).append("\n");
            });
            
            $('#loading').hide();
            
        }
    });
    
    $('#output').html('');
    $('#loading').show()

});

$('#loading').hide();

</script>

</body>
</html>
