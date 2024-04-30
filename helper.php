<?php
/**
 * 用给定的字符作为键分隔符压平数组
 * @param array $items
 * @param string $delimiter
 * @param string $prepend
 */

function flatten($items = [], $delimiter = '.', $prepend = '')
{
    $flatten = [];

    if (empty($items)) {
        return null;
    }

    foreach ($items as $key => $value) {
        if (is_array($value) && !empty($value)) {
            $flatten[] = flatten($value, $delimiter, $prepend . $key . $delimiter);
        } else {
            $flatten[] = [$prepend . $key => $value];
        }
    }

    return array_merge(...$flatten);
}

/**
 * 提取数组的json_scheme
 * @author Bruce 2024/4/30
 * @param $visited
 * @param $json
 * @return array
 */
function generateJsonSchema($data, &$visited = [])
{
    $schema = [];

    foreach ($data as $key => $value) {
        if (is_array($value)) {
            if (isset($visited[$key])) {
                $schema[$key] = $visited[$key];
            } else {
                if (count($value) > 0 && is_array($value[0])) {
                    $visited[$key] = generateJsonSchema([$value[0]], $visited);
                    $schema[$key] = $visited[$key];
                } else {
                    $schema[$key] = generateJsonSchema($value, $visited);
                }
            }
        } else {
            $schema[$key] = gettype($value);
        }
    }

    return $schema;
}

/**
 * 递归的获取数组的所有键名，不包括数字键名
 * @author Bruce 2024/4/30
 * @param $array
 * @return array
 */
function extractKeysRecursive($array)
{
    $keys = [];

    foreach ($array as $key => $value) {
        if (!is_numeric($key)) {
            $keys[] = $key;
        }

        if (is_array($value)) {
            $nestedKeys = extractKeysRecursive($value);
            $keys = array_merge($keys, $nestedKeys);
        }
    }

    return array_unique($keys);
}

/**
 * 根据给定的数组创建目录
 * @author Bruce 2024/4/30
 * @param $basePath
 * @param $array
 */
function createDirectoriesFromArrays($array, $basePath = '')
{
    foreach ($array as $key => $value) {
        // 构建当前目录路径
        $currentPath = $basePath . '/' . $key;

        // 如果当前值是数组，则递归创建目录
        if (is_array($value)) {
            // 创建当前目录
            if (!file_exists($currentPath)) {
                mkdir($currentPath, 0777, true);
            }

            // 递归创建子目录
            createDirectoriesFromArrays($value, $currentPath);
        } else {
            $currentPath = $basePath . '/' . $value;
            if (!file_exists($currentPath)) {
                mkdir($currentPath, 0777, true);
            }
        }
    }
}

/**
 * 递归地获取多维数组里面所有的指定键名的所有值
 * @author Bruce 2024/4/30
 * @param $targetKey
 * @param $result
 * @param $array
 * @return array|mixed
 */
function getValuesByKey($array, $targetKey, &$result = [])
{
    foreach ($array as $key => $value) {
        if ($key === $targetKey) {
            $result[] = $value;
        } elseif (is_array($value)) {
            getValuesByKey($value, $targetKey, $result);
        }
    }
    return $result;
}

/**
 * 递归的删除指定的目录及下面的文件
 * @author Bruce 2024/4/30
 * @param $directory
 */
function deleteDirectoryRecursive($directory)
{
    if (!file_exists($directory)) {
        return;
    }

    $files = array_diff(scandir($directory), ['.', '..']);

    foreach ($files as $file) {
        $path = $directory . '/' . $file;

        if (is_dir($path)) {
            deleteDirectoryRecursive($path);
        } else {
            unlink($path);
        }
    }

    rmdir($directory);
}

/**
 * 递归地创建目录
 * @author Bruce 2024/4/30
 * @param $directory
 */
function createDirectoryRecursive($directory)
{
    if (!file_exists($directory)) {
        mkdir($directory, 0777, true);
    }
}

/**
 * 递归的复制目录和文件到另一个地方
 * @param string $source
 * @param string $dest
 * @param bool $overwrite
 * @author Bruce 2024/4/30
 */
function copyDir(string $source, string $dest, bool $overwrite = false)
{
    if (is_dir($source)) {
        if (!is_dir($dest)) {
            mkdir($dest);
        }
        $files = scandir($source);
        foreach ($files as $file) {
            if ($file !== "." && $file !== "..") {
                copyDir("$source/$file", "$dest/$file", $overwrite);
            }
        }
    } elseif (file_exists($source) && ($overwrite || !file_exists($dest))) {
        copy($source, $dest);
    }
}

/**
 * 把数组输出成tree形式
 * @author Bruce 2024/4/30
 * @param $indent
 * @param $result
 * @param $array
 */
function generateTree($array, $indent = 0, &$result = '')
{
    foreach ($array as $key => $value) {
        // 如果键名为空，则直接跳过
        if (is_int($key)) {

            $line = str_repeat("  ", $indent) . "|-- " . (is_array($value) ? "" : $value) . PHP_EOL;
        } else {
            $line = str_repeat("  ", $indent) . "|-- " . $key . ": " . (is_array($value) ? "" : $value) . PHP_EOL;

        }
        // 构建当前行的字符串

        // 将当前行添加到结果中
        $result .= $line;

        // 如果当前值是数组，则递归调用 generateTree
        if (is_array($value)) {
            generateTree($value, $indent + 1, $result);
        }
    }
}

