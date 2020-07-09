<?php
// cada vez que se agregue una funcion al archivo de debe ejecutar
// composer dump-autoload

if (! function_exists('hello')) {
    function hello($name = null)
    {
        return 'Hello '.$name;
    }
}


if (! function_exists('myDebug')) {
    function myDebug($input, $rid_content = false)
    {
	$file = __DIR__ . DIRECTORY_SEPARATOR . 'log.html';

	if($rid_content) {
		$fh = fopen($file, 'w');
		fclose($fh);
	}
	// JSON_HEX_TAG or JSON_FORCE_OBJECT or JSON_PRETTY_PRINT
	$output = json_encode($input, JSON_HEX_TAG);
	$fh = fopen($file, 'a');
	fwrite($fh, '<html style="background: black;">');
	fwrite($fh, '<h4 style="color: orange;">'. $output .'</h4>'); // document write
	fwrite($fh, '<script>console.log(' . $output . ')</script>'); // console.log
	fwrite($fh, '</html>');
	fclose($fh);
    }
}

