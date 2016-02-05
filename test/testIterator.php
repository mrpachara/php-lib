<?php
	header('Content-Type: text/plain; charset=utf8');

	$a = [1,2,3,4,5,6,7,8,9,10];

	for($i = 0; $i < count($a); $i++){
		if(($i % 5) == 0) $a[] = $a[$i];

		echo $a[$i].PHP_EOL;
	}

	var_dump($a);
?>
