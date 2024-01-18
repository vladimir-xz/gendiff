<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use Symfony\Component\Yaml\Yaml;

$content1 = file_get_contents('NestedOne.json', true);
$json1 = json_decode($content1, true);

$content2 = file_get_contents('NestedTwo.json', true);
$json2 = json_decode($content2, true);

$yaml1 = Yaml::dump($json1);
file_put_contents('NestedOne.yaml', $yaml1);
$yaml2 = Yaml::dump($json2);
file_put_contents('NestedTwo.yaml', $yaml2);
