<?php

namespace Differ\Processing;

function getJsonContent($pathToFile)
{
    $newPath = stream_resolve_include_path($pathToFile);
    if (file_exists($newPath)) {
        $content = file_get_contents($newPath, true);
        return json_decode($content, true);
    }
    throw new \Exception("File do not found: \"{$pathToFile}\"!");
}
