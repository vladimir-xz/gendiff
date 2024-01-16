<?php

namespace Differ\Processing;

function getJsonContent($pathToFile)
{
    if (file_exists($pathToFile)) {
        $content = file_get_contents($pathToFile, true);
        return json_decode($content, true);
    }
    throw new \Exception("File do not found: \"{$pathToFile}\"!");
}
