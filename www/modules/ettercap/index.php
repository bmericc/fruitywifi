<?php 
/*
    Copyright (C) 2013-2014 xtr4nge [_AT_] gmail.com

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
}

$newdata = $_POST['newdata'];
$logfile = $_GET["logfile"];
$action = $_GET["action"];
$tempname = $_GET["tempname"];
$service = $_POST["service"];

// DELETE LOG
if ($logfile != "" and $action == "delete") {
    $exec = "$bin_rm ".$mod_logs_history.$logfile.".log";
    //exec("$bin_danger \"$exec\"", $dump); //DEPRECATED
    exec_fruitywifi($exec);
}

// SET MODE
if ($_POST["change_mode"] == "1") {
    $ss_mode = $service;
    $exec = "$bin_sed -i 's/ss_mode.*/ss_mode = \\\"".$ss_mode."\\\";/g' includes/options_config.php";
    //exec("$bin_danger \"$exec\"", $output); //DEPRECATED
    $output = exec_fruitywifi($exec);
}

include "includes/options_config.php";

?>

<div class="rounded-top" align="left"> &nbsp; <b><?php echo $mod_name?></b> </div>
<div class="rounded-bottom">

    &nbsp;&nbsp;version <?php echo $mod_version?><br>
    <?php 
    if (file_exists("/usr/sbin/ettercap")) { 
        echo "&nbsp;$mod_name <font style='color:lime'>installed</font><br>";
    } else {
        echo "&nbsp;$mod_name <a href='includes/module_action.php?install=install_$mod_name' style='color:red'>install</a><br>";
    } 
    ?>
    
    <?php
    $ismoduleup = exec("ps auxww | grep $mod_name | grep -v -e 'grep $mod_name'");
    if ($ismoduleup != "") {
        echo "&nbsp;$mod_name  <font color=\"lime\"><b>enabled</b></font>.&nbsp; | <a href=\"includes/module_action.php?service=$ss_mode&action=stop&page=module\"><b>stop</b></a>";
        echo "<input type='hidden' name='action' value='stop'>";
        echo "<input type='hidden' name='page' value='module'>";
    } else { 
        echo "&nbsp;$mod_name  <font color=\"red\"><b>disabled</b></font>. | <a href=\"includes/module_action.php?service=$ss_mode&action=start&page=module\"><b>start</b></a>"; 
        echo "<input type='hidden' name='action' value='start'>";
        echo "<input type='hidden' name='page' value='module'>";
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
            <li><a href="#result-2">Options</a></li>
	    <li><a href="#result-3">Filters</a></li>
            <li><a href="#result-4">History</a></li>
	    <li><a href="#result-5">About</a></li>
        </ul>
        <div id="result-1">
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
                //$data_array = explode("\n", $data);
                //$data = implode("\n",array_reverse($data_array));
                
            ?>
            <textarea id="output" class="module-content" style="font-family: courier;"><?php echo htmlspecialchars($data)?></textarea>
            <input type="hidden" name="type" value="logs">
            </form>
            
        </div>

        <!-- OPTIONS -->

        <div id="result-2">
            <form id="formInject" name="formInject" method="POST" autocomplete="off" action="includes/save.php">
            <input type="submit" value="save">
            <br><br>
			
                <div class="module-content">
                <table>
			
                    <?php foreach ($mode_options as $key => $value) { ?>
                    <!-- // OPTION i --> 
                    <tr>
                        <?php $opt = $key; ?>
                        <td><input type="checkbox" name="options[]" value="<?php echo $opt?>" <?php if ($mode_options[$opt][0] == "1") echo "checked" ?> ></td>
                        <td>-<?php echo $opt; ?></td>
                        <td> 
						
                        <?php
                                $m_type[0] = "arp";
                                $m_type[1] = "icmp";
                                $m_type[2] = "dhcp";
                                $m_type[3] = "port";
                        
                                if ($opt == "M") {
                                    echo " <select class='module' name='mitm_type'>";								
                                            for ($i = 0; $i < count($m_type); $i++) {
                                                if ($m_type[$i] == $mode_options["M"][2]) echo "<option selected>"; else echo "<option>"; 
                                                echo $m_type[$i];
                                                echo "</option>";
                                            }
                                    echo " </select>";
                                            
                                    echo "<input class='module' style='width:300px' name='mitm_value' value='".$mode_options["M"][4]."'>";
                                }
                                
                                if ($opt == "F") {
                                    echo " <select class='module' name='filter_name'>";
                                            echo "<option value='0'>-</option>";
                                            
                                            $template_path = "$mod_path/includes/templates/";
                                            $templates = glob($template_path.'*');
                                            
                                            for ($i = 0; $i < count($templates); $i++) {
                                                $filename = str_replace($template_path,"",$templates[$i]);
                                                if ($filename != "filter.ef") {
                                                    if ($filename == $mode_options["F"][2]) echo "<option selected>"; else echo "<option>";
                                                    //echo "<option>"; 
                                                    echo "$filename";
                                                    echo "</option>";
                                                }
                                            }	
                                            
                                    echo " </select>";
                                }
                        
                        ?>
                        <?php echo $mode_options[$opt][3]; ?> 
			</td>
                    </tr>
                    <?php } ?>
                </table>
                </div>
			
            <input type="hidden" name="type" value="mode_ettercap">
            </form>
            <br>
            <?php
                $filename = "$mod_path/includes/mode_d.txt";
                $data = open_file($filename);
            ?>
            
        </div>

	<!-- END OPTIONS -->

        <!-- START LISTS -->
        
        <div id="result-3" >
            <form id="formTemplates" name="formTemplates" method="POST" autocomplete="off" action="includes/save.php">
            <input type="submit" value="save">       
            
            <br><br>
            <?php
                if ($tempname != "") {
                    $filename = "$mod_path/includes/templates/".$tempname;
                    $data = open_file($filename);
                } else {
                    $data = "";
                }
            ?>

	    <textarea id="inject" name="newdata" class="module-content" style="font-family: courier;"><?php echo htmlspecialchars($data)?></textarea>
            <input type="hidden" name="type" value="templates">
            <input type="hidden" name="action" value="save">
            <input type="hidden" name="tempname" value="<?php echo $tempname?>">
            </form>
            
        <br>
            
        <table border=0 cellspacing=0 cellpadding=0>
            <tr>
            <td class="general">
                Template
            </td>
            <td>
            <form id="formTempname" name="formTempname" method="POST" autocomplete="off" action="includes/save.php">
                <select name="tempname" onchange='this.form.submit()'>
                <option value="0">-</option>
                <?php
                $template_path = "$mod_path/includes/templates/";
                $templates = glob($template_path.'*');
                //print_r($templates);

                for ($i = 0; $i < count($templates); $i++) {
                    $filename = str_replace($template_path,"",$templates[$i]);
                    if ($filename != "filter.ef") { 
                        if ($filename == $tempname) echo "<option selected>"; else echo "<option>"; 
                        echo "$filename";
                        echo "</option>";
                    }
                }
                ?>
                </select>
                <input type="hidden" name="type" value="templates">
                <input type="hidden" name="action" value="select">
            </form>
            </td>
            <tr>
            <td class="general" style="padding-right:10px">
                Add/Rename
            </td>
            <td>
            <form id="formTempname" name="formTempname" method="POST" autocomplete="off" action="includes/save.php">
                <select name="new_rename">
                <option value="0">- add template -</option>
                <?php
                $template_path = "$mod_path/includes/templates/";
                $templates = glob($template_path.'*');
                //print_r($templates);

                for ($i = 0; $i < count($templates); $i++) {
                    $filename = str_replace($template_path,"",$templates[$i]);
                    if ($filename != "filter.ef") {
                        echo "<option>"; 
                        //if ($filename == $tempname) echo "<option selected>"; else echo "<option>";
                        echo "$filename";
                        echo "</option>";
                    }
                }
                ?>
                
                </select>
                <input class="ui-widget" type="text" name="new_rename_file" value="" style="width:150px">
                <input type="submit" value="add/rename">
                
                <input type="hidden" name="type" value="templates">
                <input type="hidden" name="action" value="add_rename">
                
            </form>
            </td>
            </tr>
            
            <tr><td><br></td></tr>
            
            <tr>
            <td>
                
            </td>
            <td>
            <form id="formTempDelete" name="formTempDelete" method="POST" autocomplete="off" action="includes/save.php">
                <select name="new_rename">
                <option value="0">-</option>
                <?php
                $template_path = "$mod_path/includes/templates/";
                $templates = glob($template_path.'*');
                //print_r($templates);

                for ($i = 0; $i < count($templates); $i++) {
                    //$filename = $templates[$i];
                    $filename = str_replace($template_path,"",$templates[$i]);
                    echo "<option>"; 
                    echo "$filename";
                    echo "</option>";
                }
                ?>
                
                </select>

                <input type="submit" value="delete">
                
                <input type="hidden" name="type" value="templates">
                <input type="hidden" name="action" value="delete">
                
            </form>
            </td>
            </tr>
        </table>
        </div>

        <!-- END LISTS -->
		
		<!-- HISTORY -->

        <div id="result-4"  class="history">
            <input type="submit" value="refresh">
            <br><br>
            
            <?php
            $logs = glob($mod_logs_history.'*.log');
            print_r($a);

            for ($i = 0; $i < count($logs); $i++) {
                $filename = str_replace(".log","",str_replace($mod_logs_history,"",$logs[$i]));
                echo "<a href='?logfile=".str_replace(".log","",str_replace($mod_logs_history,"",$logs[$i]))."&action=delete&tab=3'><b>x</b></a> ";
                echo $filename . " | ";
                echo "<a href='?logfile=".str_replace(".log","",str_replace($mod_logs_history,"",$logs[$i]))."&action=view'><b>view</b></a>";
                echo "<br>";
            }
            ?>
            
        </div>
        
        <!-- END HISTORY -->
        
        <!-- ABOUT -->

        <div id="result-5"  class="history">
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
