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
    return $absolutePath;
}

function getFileContent(string $absolutePath)
{
    if (!file_exists($absolutePath)) {
        throw new \Exception("File do not found: \"{$absolutePath}\"!");
    } elseif (filesize($absolutePath) == 0) {
        $pathBaseName = pathinfo($absolutePath, PATHINFO_BASENAME);
        throw new \Exception("File \"{$pathBaseName}\" is empty.");
    }
    return file_get_contents($absolutePath, true);
}

function parseFile(string $pathToFile)
{
    $fileExtention = pathinfo($pathToFile, PATHINFO_EXTENSION);
    $content = getFileContent($pathToFile);
    if ($fileExtention === 'json') {
        if ($content === false) {
            throw new \Exception('Error when turning value into string');
        }
        return json_decode($content, true);
    } elseif ($fileExtention === 'yaml' || $fileExtention === 'yml') {
        $contentOfYaml = Yaml::parseFile($pathToFile);
        return $contentOfYaml;
    } else {
        throw new \Exception("Unknow file extention: \"{$fileExtention}\"!");
    }
}
