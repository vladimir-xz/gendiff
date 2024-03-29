<?php

namespace Differ\FileProcessing;

function makePathAbsolute(string $pathToFile): string
{
    $realPath = realpath($pathToFile);
    if ($realPath === false) {
        $absolutePath =  __DIR__ . $pathToFile;
    } else {
        $absolutePath = $realPath;
    }
    return $absolutePath;
}

function getFilesContent(string $absolutePath): string
{
    if (!file_exists($absolutePath)) {
        throw new \Exception("File do not found: \"{$absolutePath}\"!");
    } elseif (filesize($absolutePath) == 0) {
        $pathBaseName = pathinfo($absolutePath, PATHINFO_BASENAME);
        throw new \Exception("File \"{$pathBaseName}\" is empty.");
    }
    $result = file_get_contents($absolutePath, true);
    if ($result === false) {
        throw new \Exception('Error occured when retrieving the content of the file');
    }
    return $result;
}
