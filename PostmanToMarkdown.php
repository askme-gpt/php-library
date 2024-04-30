<?php

namespace Shuiping\Postception;

class PostmanToMarkdown
{
    public $exit_names;
    private $json;
    private $markdown;
    private $param_debug_file;

    public function __construct($jsonFilePath)
    {
        $this->json = json_decode(file_get_contents($jsonFilePath), true);
        // 记录所有参数
        $this->params = [];
        $this->markdown = '';
        $this->exit_names = [];
        // 每次运行前清空调试记录
        $this->param_debug_file = 'params_debug.txt';
        file_put_contents($this->param_debug_file, '');
    }

    public function convert()
    {
        $this->processItem($this->json);
        // 输出所有参数
        $this->paramFile($this->params);
        return $this->markdown;
    }

    /**
     * Flatten an array with the given character as a key delimiter
     * @param array $items
     * @param string $delimiter
     * @param string $prepend
     */

    private function flatten($items = [], $delimiter = '.', $prepend = '')
    {
        $flatten = [];

        if (empty($items)) {
            return null;
        }

        foreach ($items as $key => $value) {
            if (is_array($value) && !empty($value)) {
                $flatten[] = $this->flatten($value, $delimiter, $prepend . $key . $delimiter);
            } else {
                $flatten[] = [$prepend . $key => $value];
            }
        }

        return array_merge(...$flatten);
    }

    /**
     * 生成参数文件，直接进去补充即可
     * @author Bruce 2024/4/29
     * @param $params
     */
    private function paramFile($params)
    {
        $params_filename = __DIR__ . '/templates/params.php';
        $params = $this->reduceArray($params);
        $params = array_unique($params);
        foreach ($params as $param) {
            $new[$param] = '';
        }

        // param.php是空的时候才去覆盖
        if (!file_get_contents($params_filename)) {
            $text = '<?php ' . PHP_EOL . ' return ' . var_export($new, true) . ';';
            file_put_contents($params_filename, $text);
        }
    }

    private function processItem($item)
    {
        if (isset($item['item'])) {
            foreach ($item['item'] as $subItem) {
                $this->processItem($subItem);
            }
        } else {
            $this->processRequest($item);
        }
    }

    /**
     * 给数组降维
     * @author Bruce 2020-07-29
     * @param array $array [description]
     * @return [type] [description]
     */
    public function reduceArray(array $array)
    {
        $return = [];
        array_walk_recursive($array, function ($x, $key) use (&$return) {
            $return[] = $x;
        });
        return $return;
    }

    private function processRequest($request)
    {
        $url_path = $request['request']['url']['path'] ?? $request['request']['url'] ?? [];
        if (empty($url_path)) {
            file_put_contents($this->param_debug_file, json_encode($request['request'], 428), FILE_APPEND);
            echo 'url_path 不存在，请检查文件：' . $this->param_debug_file;
            die();
        }
        if (is_array($url_path)) {
            $url_path = implode('/', $url_path);
        }
        $uni_key = $request['request']['method'] . ' ' . $url_path;
        // 去重，避免多个重复接口分多次出现在接口文档
        if (in_array($uni_key, $this->exit_names)) {
            return;
        }
        $this->exit_names[] = $uni_key;

        $examples = count($request['response']);
        $this->markdown .= "## " . $request['request']['method'] . " " . parse_url($url_path, PHP_URL_PATH) . " (共" . $examples . "个示例)\n\n";

        foreach ($request['response'] as $k => $response) {
            $k++;
            if ($examples > 1) {
                $this->markdown .= "### 示例" . $k . ":\n\n";
            } else {
                $this->markdown .= "**示例" . $k . ":**\n\n";
            }
            $this->markdown .= "```http\n" . $response['originalRequest']['method'] . " " . $url_path . "\n```\n\n";
            $body = $response['originalRequest']['body'] ?? [];
            // 可能存在多种参数格式form-data,x-www-form-urlencoded,raw-json，所以要看body的mode
            $var = ($body && isset($body['mode'])) ? $body[$body['mode']] : [];
            $params = $response['originalRequest']['url']['query'] ?? ($var) ?? [];
            if (empty($params)) {
                $params = [];
                // 说明参数只在url上面，那就获取url上面的参数
                $query = parse_url($url_path, PHP_URL_QUERY);
                parse_str($query, $params_arr);
                foreach ($params_arr as $key => $value) {
                    $params[] = [
                        'key' => $key,
                        'value' => $value,
                    ];
                }
            }

            if (empty($params)) {
                $str = json_encode($response, 428) . PHP_EOL . PHP_EOL;
                file_put_contents($this->param_debug_file, $str, FILE_APPEND);
                echo $uni_key . ' 没有参数，请检查' . PHP_EOL;
                // die();
            }
            $this->markdown .= $this->processParams($params);
            $this->markdown .= "**Response:**\n\n";
            $json_decode = json_decode($response['body'], true);
            $this->markdown .= "```json\n" . json_encode($json_decode, 428) . "\n```\n\n---\n";
            $this->markdown .= $this->processResponseParams($json_decode);
        }
    }

    /**
     * 获取全局默认描述
     * @author Bruce 2020-07-27
     * @param string $value [description]
     * @return [type] [description]
     */
    public function descriptionEnum($key = '')
    {
        # 参数配置文件路径，如果postman的json文件里面没有参数注释，会采用这里的参数注释，如果有的话就是用postman的json文件里面的参数注释
        $config = __DIR__ . './templates/params.php';
        $enums = require $config;
        if (empty($key)) {
            return $enums;
        }
        return $enums[$key] ?? '';
    }

    private function processParams($params)
    {
        $param_markdown = "**Request Params:**\n\n";
        if (empty($params)) {
            return $param_markdown . "无\n\n";
        }
        if (is_string($params)) {
            // 说明是raw格式的json参数，需要美化一下
            $json_decode = json_decode($params, true);
            $this->params[] = array_keys($json_decode);
            $param_markdown .= "```json\n" . json_encode($json_decode, 428) . "\n```\n\n---\n";
            return $param_markdown;
        }
        // 到这里来了就说明拿到的参数是数组形式
        $this->params[] = array_column($params, 'key');
        // echo json_encode($params,320);die();

        $param_markdown .= "|参数名|示例值|描述|类型|是否必须|\n";
        $param_markdown .= "|--|--|--|--|--|\n";

        foreach ($params as $param) {
            // echo json_encode($param,320);die();
            $param_markdown .= "| " . $param['key'] . " | " .
                ($param['value'] ?? '') . " | " .
                ($param['description'] ?? $this->descriptionEnum($param['key']) ?? '') . " | " .
                (is_numeric($param['value']) ? 'number' : 'string') . " | " .
                (empty($param['value']) ? '否' : '是') . " |\n";
        }

        $param_markdown .= "\n";

        return $param_markdown;
    }

    private function processResponseParams($params)
    {
        $params = $this->flatten($params);
        $param_markdown = "**Response Params:**\n\n";
        if (empty($params)) {
            return $param_markdown . "无\n\n";
        }

        $param_markdown .= "|参数名|示例值|描述|类型|是否必现|\n";
        $param_markdown .= "|--|--|--|--|--|\n";

        foreach ($params as $key => $value) {
            if (is_array($value)) {
                $value = json_encode($value, JSON_UNESCAPED_SLASHES);
            }
            // echo json_encode($param,320);die();
            $keys = explode('.', $key);
            $param_markdown .= "| " . $key . " | " .
                $value . " | " .
                $this->descriptionEnum(end($keys)) . " | " .
                (is_numeric($value) ? 'number' : 'string') . " | " .
                (empty($value) ? '否' : '是') . " |\n";
        }

        $param_markdown .= "\n";

        return $param_markdown;
    }
}

# 程序命令行调用
if (count($argv) > 2) {
    die('args error, please input intput_file and output_file path. example as follow: ' . PHP_EOL . PHP_EOL . 'php .\PostmanToMarkdown.php intput_file.json' . PHP_EOL . PHP_EOL);
}

$input_file = $argv[1];
if (!file_exists($input_file)) {
    die($input_file . ' 文件不存在。');
}
# 直接把文件后缀改一下，改成md
$output_file = rtrim($argv[1], 'json') . 'md';

$converter = new PostmanToMarkdown2($input_file);
$markdown = $converter->convert();
file_put_contents($output_file, $markdown);