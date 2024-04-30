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

6.读取超大文件
```php
function readLargeFile($filePath, $callback, $chunkSize = 10240000)
{
    $fileHandle = fopen($filePath, 'r');
    if (!$fileHandle) {
        throw new Exception("Unable to open file: $filePath");
    }

    while (!feof($fileHandle)) {
        $chunk = fread($fileHandle, $chunkSize);
        if ($chunk === false) {
            throw new Exception('Error reading file');
        }
        call_user_func($callback, $chunk);
    }

    fclose($fileHandle);
}

// 示例用法：
$filePath = 'word.json';

readLargeFile($filePath, function ($chunk) {
    // 处理每个数据块
    file_put_contents('word_new.json', $chunk, FILE_APPEND);
    $memoryAfter = memory_get_usage() / 1024 / 1024; // 转换为MB
    echo $memoryAfter . PHP_EOL;
    // echo '11111111111' .  memory_get_peak_usage() . '11111111111';
});
// 获取执行后的内存使用情况
$peakMemoryAfter = memory_get_peak_usage() / 1024 / 1024; // 转换为MB
echo '最大内存消耗：' . $peakMemoryAfter . PHP_EOL;
```

7.压缩整个目录为压缩文件
```php

function zipFolder($folderPath, $zipFilePath)
{
    // 初始化 ZipArchive 对象
    $zip = new ZipArchive();

    // 打开或创建 ZIP 文件
    if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
        die("无法创建压缩文件\n");
    }

    // 创建递归迭代器
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($folderPath),
        RecursiveIteratorIterator::LEAVES_ONLY
    );

    foreach ($files as $name => $file) {
        // 跳过当前目录和父目录
        if (!$file->isDir()) {
            $filePath = $file->getRealPath();
            $relativePath = substr($filePath, strlen($folderPath) + 1);

            // 将文件添加到 ZIP
            $zip->addFile($filePath, $relativePath);
        }
    }

    // 关闭 ZIP 文件
    $zip->close();

    echo "压缩完成！\n";
}

// 使用示例
$folderPath = './';
$zipFilePath = '../folder.zip';
zipFolder($folderPath, $zipFilePath);
```