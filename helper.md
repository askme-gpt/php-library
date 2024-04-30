### function examples
1. 递归获取多维数组里面的指定的键名的所有的值：
```php
function getValuesByKey($array, $targetKey, &$result = []) {
    foreach ($array as $key => $value) {
        if ($key === $targetKey) {
            $result[] = $value;
        } elseif (is_array($value)) {
            getValuesByKey($value, $targetKey, $result);
        }
    }
    return $result;
}

// 示例用法：
$nestedArray = [
    "name" => "John",
    "age" => 30,
    "address" => [
        "street" => "123 Main St",
        "city" => "New York"
    ],
    "tags" => ["php", "json", "schema"],
    "people" => [
        [
            "name" => "Alice",
            "age" => 25,
            "street" => "3333 Main St",
            'aa'=>[
                "street" => "444 Main St",

            ]

        ],
        [
            "name" => "Bob",
            "age" => 28
        ]
    ]
];

$values = getValuesByKey($nestedArray, "street");
print_r($values);

```
2.递归的删除指定的目录及下面的文件
```php
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

// 示例用法：
$directoryToDelete = './nivel2';
deleteDirectoryRecursive($directoryToDelete);
```
3.递归地创建目录
```php
function createDirectoryRecursive($directory) {
    if (!file_exists($directory)) {
        mkdir($directory, 0777, true);
    }
}

// 示例用法：
$directoryToCreate = '/path/to/directory';

createDirectoryRecursive($directoryToCreate);
```

4.把数组输出成tree形式
```php
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

// 示例用法：
$nestedArray = [
    "folder1" => [
        "file1.txt",
        "subfolder1" => [
            "file2.txt",
            "subsubfolder1" => [
                'ss' => "file3.txt",
                "222.txt",
            ],
        ],
    ],
    "folder2" => "file4.txt",
];

$result = '';
generateTree($nestedArray, 0, $result);

// 输出结果
echo $result;

结果如下：
|-- folder1:
  |-- file1.txt
  |-- subfolder1:
    |-- file2.txt
    |-- subsubfolder1:
      |-- ss: file3.txt
      |-- 222.txt
|-- folder2: file4.txt

```

5.递归拷贝目录和文件
```php
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
    } else if (file_exists($source) && ($overwrite || !file_exists($dest))) {
        copy($source, $dest);
    }
}
copyDir('./form','./to');
```

6.