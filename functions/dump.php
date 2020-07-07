<?php

function dumpData($data)
{
    echo '<pre>';
    var_dump($data);
    echo '</pre>';
}

function dumpToJs($data, $variableName = 'data')
{
    echo '<script> const '.$variableName.'='.json_encode($data).'</script>';
}

function logjs( $data ){
	echo '<script>';
	echo 'console.log('. json_encode( $data ) .');';
	echo '</script>';
}

function debug_to_console($data) {
    $output = $data;
    if (is_array($output))
        $output = implode(',', $output);

    echo "<script>console.log('Debug Objects: " . $output . "' );</script>";
}
