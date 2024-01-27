<?php

namespace Differ\Parsers;

use Symfony\Component\Yaml\Yaml;

function parseFile(string $pathToFile)
{
    $absolutePath = realpath($pathToFile) ? realpath($pathToFile) : __DIR__ . $pathToFile;
    if (!file_exists($absolutePath)) {
        throw new \Exception("File do not found: \"{$pathToFile}\"!");
    } elseif (filesize($absolutePath) == 0) {
        $pathBaseName = pathinfo($absolutePath, PATHINFO_BASENAME);
        throw new \Exception("File \"{$pathBaseName}\" is empty.");
    }
    $fileExtention = pathinfo($absolutePath, PATHINFO_EXTENSION);
    if ($fileExtention === 'json') {
        $content = file_get_contents($absolutePath, true);
        if ($content === false) {
            throw new \Exception("Unknow file extention: \"{$content}\"!");
        }
        return json_decode($content, true);
    } elseif ($fileExtention === 'yaml' || $fileExtention === 'yml') {
        $content = Yaml::parseFile($absolutePath);
        return $content;
    } else {
        throw new \Exception("Unknow file extention: \"{$fileExtention}\"!");
    }
}
