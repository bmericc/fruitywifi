<?php
$version="v2.4";
$regex=1; // 1 (on) | 0 (off) >> web interface input validation.
$regex_extra=" _-.[]*"; // extra characters allowed (input validation).
$log_path="/usr/share/fruitywifi/logs";
$io_in_iface_extra="wlan0";
$iface_supplicant="wlan1";
$supplicant_ssid="";
$supplicant_psk="";
$hostapd_ssid="FruityWifi";
$hostapd_secure="1";
$hostapd_wpa_passphrase="FruityWifi";
$url_rewrite_program="pasarela_xss.js";
$dnsmasq_domain="bahri.info";
//------
$io_mode="3";
$io_in_iface="wlan0";
$io_in_set="0";
$io_in_ip="10.42.0.1";
$io_in_mask="255.255.255.0";
$io_in_gw="";
$io_out_iface="wlan1";
$io_out_set="0";
$io_out_ip="192.168.0.1";
$io_out_mask="255.255.255.0";
$io_out_gw="192.168.0.1";

$ap_mode="1";
$io_action="wlan1";
//------
$api_token="e5dab9a69988dd65e578041416773149ea57a054";
?>
