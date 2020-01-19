<?php

namespace Differ\Formatters\Json;

use function Differ\Formatters\Common\getKey;
use function Differ\Formatters\Common\getType;
use function Differ\Formatters\Common\getNewValue;
use function Differ\Formatters\Common\getOldValue;
use function Differ\Formatters\Common\getChildren;
use function Differ\Formatters\Common\isChildren;
use function Differ\Formatters\Common\checkForBool;
use function Funct\Collection\flattenAll;

function renderJson($upper, $tab = "    ")
{
    $result = [];
    if ($tab === "    ") {
        $result[] = "{";
    }
    $result[] = array_reduce($upper, function ($acc, $node) use ($tab, $upper) {
        $acc[] = renderKeyStart($node, $tab);
        $acc[] = renderInternalFields($upper, $node, $tab . "    ");
        $acc[] = renderKeyFinish($upper, $node, $tab);
        return $acc;
    }, []);
    if ($tab === "    ") {
        $result[] = "}";
    }
    return implode("\n", flattenAll($result));
}

function isCommaRequired($upper, $node)
{
    $keys = array_map(function ($node) {
        return getKey($node);
    }, $upper);
    $desiredKey = getKey($node);
    $keysCount = count($keys);
    $desiredIndex = array_search($desiredKey, $keys);
    return $desiredIndex < $keysCount - 1 ? true : false;
}

function isCommaRequiredObj($upper, $key)
{
    $keys = array_keys($upper);
    $desiredIndex = array_search($key, $keys);
    return $desiredIndex < count($keys) - 1 ? true : false;
}

function renderKeyStart($node, $tab)
{
    $key = getKey($node);
    return "{$tab}\"{$key}\": {";
}

function renderKeyFinish($upper, $node, $tab)
{
    $comma = isCommaRequired($upper, $node) ? "," : "";
    return "{$tab}}{$comma}";
}

function renderInternalFields($upper, $node, $tab)
{
    $result = [];
    $type = getType($node);
    $result[] = "{$tab}\"type\": \"{$type}\",";
    switch ($type) {
        case "children":
            $result[] = "{$tab}\"children\": {";
            $result[] = renderJson(getChildren($node), $tab . "    ");
            $result[] = "{$tab}}";
            break;
        case "same":
            $value = getNewValue($node);
            $nested = is_array($value) || is_object($value);
            if ($nested) {
                if (is_object($value)) {
                    $result[] = "{$tab}\"value\": {";
                    $result[] = "{$tab}    render object here";
                    $result[] = "{$tab}}";
                }
            } else {
                $endValue = prepareValue($value);
                $result[] = "{$tab}\"value\": {$endValue}";
            }
            break;
        case "removed":
            $value = getOldValue($node);
            $nested = is_array($value) || is_object($value);
            if ($nested) {
                if (is_object($value)) {
                    $result[] = "{$tab}\"value\": {";
                    $result[] = renderObject($value, $tab . "    ");
                    $result[] = "{$tab}}";
                }
            } else {
                $endValue = prepareValue($value);
                $result[] = "{$tab}\"value\": {$endValue}";
            }
            break;
        case "added":
            $value = getNewValue($node);
            $nested = is_array($value) || is_object($value);
            if ($nested) {
                if (is_object($value)) {
                    $result[] = "{$tab}\"value\": {";
                    $result[] = renderObject($value, $tab . "    ");
                    $result[] = "{$tab}}";
                }
            } else {
                $endValue = prepareValue($value);
                $result[] = "{$tab}\"value\": {$endValue}";
            }
            break;
        case "changed":
            $oldValue = getOldValue($node);
            $oldNested = is_array($oldValue) || is_object($oldValue);
            if ($oldNested) {
                if (is_object($oldValue)) {
                    $result[] = "{$tab}\"oldValue\": {";
                    $result[] = renderObject($oldValue, $tab . "    ");
                    $result[] = "{$tab}}";
                }
            } else {
                $endValue = prepareValue($oldValue);
                $result[] = "{$tab}\"oldValue\": {$endValue},";
            }
            $newValue = getNewValue($node);
            $newNested = is_array($newValue) || is_object($newValue);
            if ($newNested) {
                if (is_object($newValue)) {
                    $result[] = "{$tab}\"newValue\": {";
                    $result[] = renderObject($newValue, $tab . "    ");
                    $result[] = "{$tab}}";
                }
            } else {
                $endValue = prepareValue($newValue);
                $result[] = "{$tab}\"newValue\": {$endValue}";
            }
            break;
    }
    return $result;
}

function renderObject($obj, $tab)
{
    $vars = get_object_vars($obj);
    $result = [];
    foreach ($vars as $key => $value) {
        $comma = isCommaRequiredObj($vars, $key) ? "," : "";
        if (is_object($value)) {
            $result[] = "{$tab}\"{$key}\": {";
            $result[] = renderObject($value, $tab . "    ");
            $result[] = "{$tab}}{$comma}";
        } else {
            $endValue = prepareValue($value);
            $result[] = "{$tab}\"{$key}\": {$endValue}{$comma}";
        }
    }
    return $result;
}

function prepareValue($value)
{
    if (is_bool($value)) {
        $endValue = $value ? "true" : "false";
    } elseif (is_int($value)) {
        $endValue = $value;
    } else {
        $endValue = "\"{$value}\"";
    }
    return $endValue;
}
