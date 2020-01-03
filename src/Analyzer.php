<?php

namespace Differ\Analyzer;

//Accepts array of two elements in JSON format. Return difference.
//If key=>value from first element exist in second element, key=>value will be keept unchanged in result element;
//If key=>value from first element doesn't exist in second element, result element will kept with '-' sign;
//If key=>value exist only on second element, it will be added to result element with '+' sign;
function genDiff($contents)
{
    [$json1, $json2] = $contents;
    //convert to array
    $decoded1 = json_decode($json1, true);
    $decoded2 = json_decode($json2, true);
    //extract equal values
    $equals = array_intersect_assoc($decoded1, $decoded2);
    //extract added values
    $pluses = array_diff_assoc($decoded2, $decoded1);
    //extract disappeared values
    $minuses = array_diff_assoc($decoded1, $decoded2);

    //adding signs
    $equalsWSign = array_flatten(array_map(function ($key, $value) {
        $checked = boolezator($value);
        return ["{$key}: {$checked}" => ' '];
    }, array_keys($equals), $equals));
    
    $plusesWSign = array_flatten(array_map(function ($key, $value) {
        $checked = boolezator($value);
        return ["{$key}: {$checked}" => '+'];
    }, array_keys($pluses), $pluses));
    
    $minusesWSign = array_flatten(array_map(function ($key, $value) {
        $checked = boolezator($value);
        return ["{$key}: {$checked}" => '-'];
    }, array_keys($minuses), $minuses));
    
    //merge resulted
    $merged = array_merge($equalsWSign, $plusesWSign, $minusesWSign);
    
    //sort by key
    $sorted = $merged;
    ksort($sorted);
    
    //create array of strings
    $strings = array_map(function ($value, $key) {
        return "  {$value} {$key}";
    }, $sorted, array_keys($sorted));
    
    //add brackets
    array_unshift($strings, '{');
    array_push($strings, '}');
    
    //toString
    $result = implode("\n", $strings);
    
    return $result;
}

function array_flatten($array)
{
    if (!is_array($array)) {
        return false;
    }
    $result = array();
    foreach ($array as $key => $value) {
        if (is_array($value)) {
            $result = array_merge($result, array_flatten($value));
        } else {
            $result = array_merge($result, array($key => $value));
        }
    }
    return $result;
}

function boolezator($value)
{
    if (gettype($value) === 'boolean') {
        return $value ? 'true' : 'false';
    } else {
        return $value;
    }
}
