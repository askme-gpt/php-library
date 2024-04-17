<?php

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

// 需要使用phpmailer/phpmailer包，先进行包的引用
require 'vendor/autoload.php';

// 配置备份目录和邮件配置
$backupDir = '/www/wordpress5.6';  // 需要备份的目录

$backupFile = 'backup-' . date('Ymd') . 'shopnbfulicn.zip';  // 备份文件名
$zipPassword = '';  // 可选的zip文件密码，为空表示不加密
$emailHost = 'smtp.163.com';  // 邮箱SMTP服务器地址
$emailUsername = 'wechatvip@163.com';  // 发件人邮箱
$emailPassword = 'BURUTKRTKSWOFEPI';  // 发件人邮箱密码
$emailPort = 465;  // 邮箱SMTP服务器端口号
$emailEncryption = 'ssl';  // 邮箱SMTP服务器加密方式
$recipient = '1037547965@qq.com';  // 收件人邮箱
$subject = 'Backup ' . $backupFile;  // 邮件主题
$body = '项目备份。';  // 邮件正文

// 创建zip文件
$zip = new ZipArchive();
$zipfile = $backupDir . '/' . $backupFile;

if ($zip->open($zipfile, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($backupDir), RecursiveIteratorIterator::LEAVES_ONLY);

    foreach ($files as $name => $file) {
        if (!$file->isDir()) {
            $filePath = $file->getRealPath();
            $relativePath = str_replace($backupDir . '/', '', $filePath);
            $zip->addFile($filePath, $relativePath);
        }
    }

    $zip->setArchiveComment('Backup created on ' . date('Y-m-d H:i:s'));
    $zip->close();

    // 加密zip文件
    if (!empty($zipPassword)) {
        $encryptedZipfile = $zipfile . '.encrypted';
        $zip = new ZipArchive();
        if ($zip->open($encryptedZipfile, ZipArchive::CREATE) === TRUE) {
            $zip->setPassword($zipPassword);
            $zip->addFile($zipfile, $backupFile);
            $zip->close();
            unlink($zipfile);  // 删除原始备份文件
            $zipfile = $encryptedZipfile;
        } else {
            echo 'Failed to create encrypted zip file.';
            exit;
        }
    }
} else {
    echo 'Failed to create backup zip file.';
    exit;
}

// 发送邮件
$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = $emailHost;
    $mail->SMTPAuth = true;
    $mail->Username = $emailUsername;
    $mail->Password = $emailPassword;
    $mail->SMTPSecure = $emailEncryption;
    $mail->Port = $emailPort;
    $mail->setFrom($emailUsername, 'Backup');
    $mail->addAddress($recipient);
    $mail->addAttachment($zipfile);
    $mail->Subject = $subject;
    $mail->Body = $body;
    $mail->send();
    echo 'Backup file sent successfully!';
} catch (Exception $e) {
    echo 'Backup file sending failed. Error: ', $mail->ErrorInfo;
}

// 本地存储
$localPath = '/tmp/projects_backup/';
if (!file_exists($localPath)) {
    if (!mkdir($localPath, 0777, true) && !is_dir($localPath)) {
        throw new \RuntimeException(sprintf('Directory "%s" was not created', $localPath));
    }
}

copy($zipfile, $localPath . '/' . $backupFile);

// 删除原始备份文件
unlink($zipfile);


