<?php

namespace App\Helpers;

class IpValidator
{
    public function isIpInRange($ip, $ranges)
    {
        foreach ($ranges as $range) {

            if (strpos($range, '-') !== false) {

                // 是起止IP格式
                [$start, $end] = explode('-', $range);

                if ($this->ipInRange($ip, $start, $end)) {
                    return true;
                }

            } else {
                // 是CIDR格式
                [$prefix, $length] = explode('/', $range);

                if ($this->ipInCidr($ip, $prefix, $length)) {
                    return true;
                }

            }

        }

        return false;

    }

    public function ipInRange($ip, $start, $end)
    {
        // 实现逻辑
        return ip2long($ip) >= ip2long($start) && ip2long($ip) <= ip2long($end);

    }

    public function ipInCidr($ip, $net, $bits)
    {
        // 实现逻辑
        return (ip2long($ip) & ~0 << (32 - $bits)) == ip2long($net);

    }

    public function isIpv6InRange($ipv6, $ranges)
    {

        foreach ($ranges as $range) {
            // 提取网络前缀和掩码长度
            [$prefix, $length] = explode('/', $range);

            // 提取给定IPv6和网络前缀的网络部分
            $givenNetPrefix = substr($ipv6, 0, strpos($ipv6, ':'));
            $rangeNetPrefix = substr($prefix, 0, strpos($prefix, ':'));

            // 比较网络前缀是否匹配
            if ($givenNetPrefix === $rangeNetPrefix) {
                // 获取给定IPv6地址长度
                $givenLength = strlen($ipv6) - strpos($ipv6, ':');

                // 判断是否小于等于网络掩码长度
                if ($givenLength <= $length) {
                    return true;
                }
            }
        }

        return false;
    }

    public function separateIpRanges($ipRanges)
    {

        $ipv4Ranges = [];
        $ipv6Ranges = [];

        foreach ($ipRanges as $range) {

            // 按逗号分隔多个范围
            if (strpos($range, '-') !== false) {
                $start_ip = explode('-', $range)[0];
            }
            if (strpos($range, '/') !== false) {
                $start_ip = explode('/', $range)[0];
            }

            $segment = trim($start_ip);

            if (filter_var($segment, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {

                // 是IPv6范围
                $ipv6Ranges[] = $range;

            } else {

                // 是IPv4范围
                $ipv4Ranges[] = $range;

            }

        }

        return [
            'ipv4' => $ipv4Ranges,
            'ipv6' => $ipv6Ranges,
        ];

    }

    public function validateIps($ipArray, $ipRanges)
    {
        $ipv4Ranges = $this->separateIpRanges($ipRanges)['ipv4'];
        $ipv6Ranges = $this->separateIpRanges($ipRanges)['ipv6'];

        $notInIps = [];

        foreach ($ipArray as $ip) {
            $ipVersion = filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) ? 6 : 4;

            if ($ipVersion == 4) {
                $res = $this->isIpInRange($ip, $ipv4Ranges);
            } else {
                $res = $this->isIpv6InRange($ip, $ipv6Ranges);
            }

            if (!$res) {
                $notInIps[] = $ip;
            }
        }

        return $notInIps;
    }

}


$validator = new IpValidator;


$ipArray = [
    '111.122.199.255',
    '2201:8836:1800:2100:0000:0000:0000:11',
    '192.168.1.0',
    '192.168.1.1',
    '2201:8836:1800:2100:0000:0000:0000:11',
    '114.223.18.255',
    '2201:8836:1800:2100:0000:0000:0000:11',
    '61.136.95.0',
    '2201:5689:EFC:1480:0000:0000:0000:1',
    '116.125.105.1',
    '2201:8766:1:D00:0000:0000:0000:1',
];

$ipRanges = [
    '111.122.196.0-111.122.197.255',
    '2201:8836:1800:2100:0000:0000:0000:0000-2201:8836:1800:2100:FFFF:FFFF:FFFF:FFFF',
    '114.223.18.0-114.223.19.255',
    '2201:8836:1800:2000:0000:0000:0000:0000-2201:8836:1800:2000:FFFF:FFFF:FFFF:FFFF',
    '61.136.94.0/23',
    '2201:5689:EFC:1480::/57',
    '61.136.92.0/23',
    '2201:5689:EFC:1480:0000:0000:0000:0000-2201:5689:EFC:1480:FFFF:FFFF:FFFF:FFFF',
    '116.125.104.0/22',
    '2201:8766:1:D00::/56',
];
$notInIps = $validator->validateIps($ipArray, $ipRanges);
