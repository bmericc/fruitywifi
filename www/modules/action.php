<?php
include "../config/config.php";
include "../functions.php";

// Checking POST & GET variables...
if ($regex == 1) {
    regex_standard($_GET["page"], "../msg.php", $regex_extra);
    regex_standard($_GET["wait"], "../msg.php", $regex_extra);
}

$page = $_GET["page"];
$wait = $_GET["wait"];

if ($wait == "") {
	$wait = 1;
}
?>
<html>
<head>
    <meta http-equiv="refresh" content="1; url=./wait.php?page=<?php echo $page?>&wait=<?php echo $wait?>">
</head>
<body bgcolor="black" text="white">
<style>
body {
    background-color:#FCFCFC; /*#EFEFEF*/
    color: #000;
    font-family: monospace, courier;
    font-size: 12px;
    margin: 0px 0px;
}

.module {
    background-color:#F8F8F8; /* #090909 */
    -moz-border-radius: 4px;
    border-radius: 4px;
    border:1px solid;
    border-color:#BAC1C4; /* #E01B46 */
    m-argin-left: 10px;
    margin: 10px;
    padding: 10px;
}

</style>

<div align="" class="module">
<table width="300px">
<tr>
<td>

<pre>
<?phpphp
echo "Please wait...";
?>
</pre>

</td>
</tr>
</table>
</div>
</div>
</body>
</html>
