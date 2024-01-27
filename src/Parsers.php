<?php

namespace Differ\Parsers;

use Symfony\Component\Yaml\Yaml;

function parseFile(string $pathToFile)
{
    if (!file_exists($pathToFile)) {
        throw new \Exception("File do not found: \"{$pathToFile}\"!");
    } elseif (filesize($pathToFile) == 0) {
        $pathBaseName = pathinfo($pathToFile, PATHINFO_BASENAME);
        throw new \Exception("File \"{$pathBaseName}\" is empty.");
    }
    $fileExtention = pathinfo($pathToFile, PATHINFO_EXTENSION);
    if ($fileExtention === 'json') {
        $content = file_get_contents($pathToFile, true);
        if ($content === false) {
            throw new \Exception("Unknow file extention: \"{$content}\"!");
        }
        return json_decode($content, true);
    } elseif ($fileExtention === 'yaml' || $fileExtention === 'yml') {
        $content = Yaml::parseFile($pathToFile);
        return $content;
    } else {
        throw new \Exception("Unknow file extention: \"{$fileExtention}\"!");
    }
}
