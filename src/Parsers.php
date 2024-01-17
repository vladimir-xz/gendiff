<?php

namespace Differ\Processing;

use Symfony\Component\Yaml\Yaml;

function parseFile($pathToFile)
{
    if (!file_exists($pathToFile)) {
        throw new \Exception("File do not found: \"{$pathToFile}\"!");
    }
    if (filesize($pathToFile) == 0) {
        $pathBaseName = pathinfo($pathToFile, PATHINFO_BASENAME);
        throw new \Exception("File \"{$pathBaseName}\" is empty.");
    }
    $fileExtention = pathinfo($pathToFile, PATHINFO_EXTENSION);
    switch ($fileExtention) {
        case 'json':
            $content = file_get_contents($pathToFile, true);
            return json_decode($content, true);
        case 'yaml':
            $content = Yaml::parseFile($pathToFile);
            return $content;
        default:
            throw new \Exception("Unknow file extention: \"{$fileExtention}\"!");
    }
}
