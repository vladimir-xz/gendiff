<?php

namespace Differ\FilesProcessing;

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

function getFilesContent(string $absolutePath)
{
    if (!file_exists($absolutePath)) {
        throw new \Exception("File do not found: \"{$absolutePath}\"!");
    } elseif (filesize($absolutePath) == 0) {
        $pathBaseName = pathinfo($absolutePath, PATHINFO_BASENAME);
        throw new \Exception("File \"{$pathBaseName}\" is empty.");
    }
    return file_get_contents($absolutePath, true);
}
