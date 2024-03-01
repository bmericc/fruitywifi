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
<?php
//include "../login_check.php";
include "../../../config/config.php";
include "../_info_.php";
include "../../../functions.php";

include "options_config.php";

// Checking POST & GET variables...
if ($regex == 1) {
    regex_standard($_GET["service"], "../msg.php", $regex_extra);
    regex_standard($_GET["action"], "../msg.php", $regex_extra);
    regex_standard($_GET["page"], "../msg.php", $regex_extra);
    regex_standard($iface_wifi, "../msg.php", $regex_extra);
    regex_standard($_GET["install"], "../msg.php", $regex_extra);
    regex_standard($_GET["change_js"], "../msg.php", $regex_extra);
}

$service = $_GET['service'];
$action = $_GET['action'];
$page = $_GET['page'];
$install = $_GET['install'];
$change_js = $_GET['change_js'];


if($service == "squid3") {
    if ($action == "start") {
        
        // CREATE LOG FILE
        if (!file_exists($filename)) {
            $exec = "$bin_touch $mod_logs";
            //exec("$bin_danger \"$exec\"" ); //DEPRECATED
            exec_fruitywifi($exec);
                
            $exec = "$bin_chmod 666 $mod_logs";
            //exec("$bin_danger \"$exec\"" ); //DEPRECATED
            exec_fruitywifi($exec);
        }
        
        // COPY LOG
        if ( 0 < filesize( $mod_logs ) ) {
            $exec = "$bin_cp $mod_logs $mod_logs_history/".gmdate("Ymd-H-i-s").".log";
            //exec("$bin_danger \"$exec\"" ); //DEPRECATED
            exec_fruitywifi($exec);
                
            $exec = "$bin_echo '' > $mod_logs";
            //exec("$bin_danger \"$exec\"" ); //DEPRECATED
            exec_fruitywifi($exec);
        }
        
        $exec = "if [ ! -d '/var/www/tmp-squid' ]; then mkdir /var/www/tmp-squid; chmod 777 /var/www/tmp-squid; fi;";
        //exec("$bin_danger \"$exec\"" ); //DEPRECATED
        exec_fruitywifi($exec);
	
        $exec = "$bin_sed -i 's/print \\\"http.*tmp/print \\\"http:\\/\\/$io_in_ip:80\\/tmp/g' $mod_path/includes/inject/poison.pl";
        //exec("$bin_danger \"$exec\"" ); //DEPRECATED
        exec_fruitywifi($exec);
        // print "http://10.0.0.1:80/tmp/$pid-$count.js\n";
	
        //$exec = "$bin_squid3 -f /usr/share/FruityWifi/conf/squid.conf &";
        $exec = "$bin_squid3 -f $mod_path/includes/squid.conf &";
        //exec("$bin_danger \"$exec\"" ); //DEPRECATED
        exec_fruitywifi($exec);
        $exec = "$bin_iptables -t nat -A PREROUTING -i $io_action -p tcp --dport 80 -j REDIRECT --to-port 3128";
        //exec("$bin_danger \"$exec\"" ); //DEPRECATED
        exec_fruitywifi($exec);
    } else if($action == "stop") {
        
        // COPY LOG
        if ( 0 < filesize( $mod_logs ) ) {
            $exec = "$bin_cp $mod_logs $mod_logs_history/".gmdate("Ymd-H-i-s").".log";
            //exec("$bin_danger \"$exec\"" ); //DEPRECATED
            exec_fruitywifi($exec);
            
            $exec = "$bin_echo '' > $mod_logs";
            //exec("$bin_danger \"$exec\"" ); //DEPRECATED
            exec_fruitywifi($exec);
        }
        
        // STOP MODULE
        $exec = "$bin_killall squid3";
        //exec("$bin_danger \"$exec\"" ); //DEPRECATED
        exec_fruitywifi($exec);
        $exec = "/etc/init.d/squid3 stop";
        //exec("$bin_danger \"$exec\"" ); //DEPRECATED
        exec_fruitywifi($exec);
        $exec = "$bin_iptables -t nat -D PREROUTING -i $io_action -p tcp --dport 80 -j REDIRECT --to-port 3128";
        //exec("$bin_danger \"$exec\"" ); //DEPRECATED
        exec_fruitywifi($exec);
	
        $exec = "if [ -d '/var/www/tmp-squid' ]; then rm -R /var/www/tmp-squid; fi;";
        //exec("$bin_danger \"$exec\"" ); //DEPRECATED
        exec_fruitywifi($exec);
	
    }
}

if($service == "url_rewrite") {
    if ($action == "start") {
        $exec = "$bin_sed -i 's/^\#url_rewrite_program/url_rewrite_program/g' $mod_path/includes/squid.conf";
        //exec("$bin_danger \"$exec\"" ); //DEPRECATED
        exec_fruitywifi($exec);
        $exec = "$bin_squid3 -k reconfigure";
        //exec("$bin_danger \"$exec\"" ); //DEPRECATED
        exec_fruitywifi($exec);
    } else if($action == "stop") {
        $exec = "$bin_sed -i 's/^url_rewrite_program/\#url_rewrite_program/g' $mod_path/includes/squid.conf";
        //exec("$bin_danger \"$exec\"" ); //DEPRECATED
        exec_fruitywifi($exec);
        $exec = "$bin_squid3 -k reconfigure";
        //exec("$bin_danger \"$exec\"" ); //DEPRECATED
        exec_fruitywifi($exec);
    }
        header('Location: ../index.php');
        exit;
}

if($service == "iptables") {
    if ($action == "start") {
        $exec = "$bin_iptables -t nat -A PREROUTING -i $io_action -p tcp --dport 80 -j REDIRECT --to-port 3128";
        //exec("$bin_danger \"$exec\"" ); //DEPRECATED
        exec_fruitywifi($exec);
    } else if($action == "stop") {
        $exec = "$bin_iptables -t nat -D PREROUTING -i $io_action -p tcp --dport 80 -j REDIRECT --to-port 3128";
        //exec("$bin_danger \"$exec\"" ); //DEPRECATED
        exec_fruitywifi($exec);
    }
    header('Location: ../index.php');
    exit;
}

if($change_js == "1") {
    $exec = "$bin_sed -i 's/url_rewrite_program=.*/url_rewrite_program=\\\"".$action."\\\";/g' ../_info_.php";
    //exec("$bin_danger \"$exec\"" ); //DEPRECATED
    exec_fruitywifi($exec);
    
    $exec = "$bin_cp $mod_path/includes/templates/$action $mod_path/includes/inject/pasarela.js";
    //exec("$bin_danger \"$exec\"" ); //DEPRECATED
    exec_fruitywifi($exec);
    
    header('Location: ../index.php');
    exit;
}


if ($install == "install_squid3") {

    $exec = "chmod 755 install.sh";
    //exec("$bin_danger \"$exec\"" ); //DEPRECATED
    exec_fruitywifi($exec);
    
    $exec = "$bin_sudo ./install.sh > $log_path/install.txt &";
    //exec("$bin_danger \"$exec\"" ); //DEPRECATED
    exec_fruitywifi($exec);

    header('Location: ../../install.php?module=squid3');
    exit;
}

if ($page == "status") {
    header('Location: ../../../action.php');
} else {
    header('Location: ../../action.php?page=squid3');
}
//header('Location: ../../action.php?page=squid3');

?>
