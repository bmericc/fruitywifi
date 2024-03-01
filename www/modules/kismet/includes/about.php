<b>Kismet</b> is an 802.11 layer2 wireless network detector, sniffer, and intrusion detection system.  Kismet will work with any wireless card which supports raw monitoring (rfmon) mode, and can sniff 802.11b, 802.11a, and 802.11g traffic.
<br><br>
kismet identifies networks by passively collecting packets and detecting standard named networks, detecting (and given time, decloaking) hidden networks, and inferring the presence of nonbeaconing networks via data traffic. kismet supports logging to the wtapfile packet format (readable by tcpdump and ethereal) and saves detected network information as plaintext, CSV, and XML.  kismet is capable of using any GPS supported by gpsd and logs and plots network data.
<br><br>
kismet is divided into three basic programs, kismet_server kismet_client and gpsmap
<br><br>
If you are using a USB GPS, edit <b>/etc/default/gpsd</b>
<br><br>
<div style="font-family: courier, monospace;">
<b>
DEVICES="/dev/ttyUSB0"
</b>
</div>
