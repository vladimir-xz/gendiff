<?php

namespace Differ\Parsers;

use Symfony\Component\Yaml\Yaml;

function parseFile(string $pathToFile)
{
    if (!file_exists($pathToFile)) {
        throw new \Exception("File do not found: \"{$pathToFile}\"!");
    }
    if (filesize($pathToFile) == 0) {
        $pathBaseName = pathinfo($pathToFile, PATHINFO_BASENAME);
        throw new \Exception("File \"{$pathBaseName}\" is empty.");
    }
    $fileExtention = pathinfo($pathToFile, PATHINFO_EXTENSION);
    if ($fileExtention === 'yml') {
        $fileExtention = 'yaml';
    }
    switch ($fileExtention) {
        case 'json':
            $content = file_get_contents($pathToFile, true);
            if ($content === false) {
                throw new \Exception("Unknow file extention: \"{$content}\"!");
                 return;
            }
            $contentString = $content;
            return json_decode($contentString, true);
        case 'yaml':
            $content = Yaml::parseFile($pathToFile);
            return $content;
        default:
            throw new \Exception("Unknow file extention: \"{$fileExtention}\"!");
    }
}
