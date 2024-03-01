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

include "../../ap/includes/ws.php";

class WebServiceExtendedPlus extends WebServiceExtended {
	
	public function scanRecon()
	{
		//include "functions.php";
		
		$exec = "python scan-recon.py -i mon0 ";
		//$out = exec_fruitywifi($exec);
				
		$data = open_file("/usr/share/fruitywifi/logs/wifirecon.log");
		$out = explode("\n", $data);
		
		$output = [];
		
		for ($i=0; $i < count($out); $i++) {
			
			$ap = [];
			$temp = explode(",", $out[$i]);
			//if ($temp[0] != "")
			//{
				foreach ($temp as &$value) {
					$ap[] = $value;
				}				
				$output[] = $ap;
			//}
		}
		
		echo json_encode($output);
	}
	
}
?>