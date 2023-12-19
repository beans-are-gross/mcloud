<?php
$freeSpace = round(disk_free_space("/") / 1024 / 1024 / 1024, 1);
$totalSpace = round(disk_total_space("/") / 1024 / 1024 / 1024, 1);
$invertedSpace = $totalSpace - $freeSpace;
echo "
<div id='drive-info'>
<h1>Linux</h1>
<meter value='$invertedSpace' min='0' max='$totalSpace'></meter>
<p>{$freeSpace}GB available</p>
<p>{$totalSpace}GB total</p>
</div>
";