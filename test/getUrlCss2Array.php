<?php
/**
 * Created by PhpStorm.
 * User: erdiertas
 * Date: 5.11.2018
 * Time: 00:35
 */

include_once  "../src/Css2Array.php";

$class = new Css2Array();
$array = $class->fileStyle("https://assets-cdn.github.com/assets/frameworks-595acde653bb1d1cb9b98b07b1be666b.css");

print_r($array);