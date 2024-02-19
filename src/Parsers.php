<?php

namespace Differ\Parsers;

use Symfony\Component\Yaml\Yaml;

function parseFile(string $extention, string $content)
{
    if ($extention === 'json') {
        if ($content == false) {
            throw new \Exception('Error when turning value into string');
        }
        return json_decode($content, true);
    } elseif ($extention === 'yaml' || $extention === 'yml') {
        return Yaml::parse($content);
    } else {
        throw new \Exception("Unknow file extention: \"{$extention}\"!");
    }
}
