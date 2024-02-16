<?php

namespace Differ\Parsers;

use Symfony\Component\Yaml\Yaml;

function makePathAbsolute(string $pathToFile)
{
    $realPath = realpath($pathToFile);
    if ($realPath === false) {
        $absolutePath =  __DIR__ . $pathToFile;
    } else {
        $absolutePath = $realPath;
    }
    if (!file_exists($absolutePath)) {
        throw new \Exception("File do not found: \"{$pathToFile}\"!");
    } elseif (filesize($absolutePath) == 0) {
        $pathBaseName = pathinfo($absolutePath, PATHINFO_BASENAME);
        throw new \Exception("File \"{$pathBaseName}\" is empty.");
    }
    return $absolutePath;
}

function parseFile(string $pathToFile)
{
    $fileExtention = pathinfo($pathToFile, PATHINFO_EXTENSION);
    if ($fileExtention === 'json') {
        $content = file_get_contents($pathToFile, true);
        return json_decode($content, true);
    } elseif ($fileExtention === 'yaml' || $fileExtention === 'yml') {
        $content = Yaml::parseFile($pathToFile);
        return $content;
    } else {
        throw new \Exception("Unknow file extention: \"{$fileExtention}\"!");
    }
}
