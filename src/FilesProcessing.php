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

function findDirectory(string $search, string $mainDirectory = "..")
{
    $dirAbsolute = makePathAbsolute($mainDirectory);
    $scannedDirectory = array_diff(scandir($dirAbsolute), array('..', '.'));
    if (in_array($search, $scannedDirectory) && is_dir($mainDirectory . '/' . $search)) {
        return $dirAbsolute . '/' . $search;
    }
    $dir = array_map(function ($file) use ($search, $dirAbsolute) {
        $filePath = $dirAbsolute . '/' . $file;
        if (is_dir($filePath)) {
            return findDirectory($search, $filePath);
        }
    }, $scannedDirectory);
    return implode('', $dir);
}
