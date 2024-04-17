<?php

$loader = require 'vendor/autoload.php';

// 设置数据库连接信息
$host = "110.110.92.240";
$user = "aaa";
$password = "xxx#xxx$%";
$database = "xxx askme wordpress yyy ysp-blog";

// 设置备份文件存放目录
$backup_dir = "/tmp/mysql_backup/";

// 设置备份文件名格式（如：20211103_backup.sql）
$backup_filename = date("Ymd") . "_backup.sql";

// // 执行备份
$command = "/usr/local/mysql/bin/mysqldump --routines --user={$user} --password={$password} --host={$host}  --databases {$database} > {$backup_dir}{$backup_filename}";
system($command);

// 压缩备份文件为zip格式
$zip = new ZipArchive();
$zip_filename = $backup_dir . date("Ymd") . '_'.str_replace(' ', '_', $database) . "_backup.zip";
if ($zip->open($zip_filename, ZipArchive::CREATE) !== TRUE) {
    die("Could not open archive");
}
$zip->addFile($backup_dir . $backup_filename, $backup_filename);
$zip->close();

// 发送备份文件至指定邮箱
$to = "1037547965@qq.com";
$subject = "MySQL Backup " . date("Y/m/d");
$body = "MySQL Backup " . date("Y/m/d");
$file = $zip_filename;
$filename = basename($file);

// 初始化phpmailer对象
$mail = new PHPMailer\PHPMailer\PHPMailer();
$mail->isSMTP();
$mail->Host = 'smtp.163.com'; // 设置SMTP服务器
$mail->SMTPAuth = true; // 启用SMTP验证
$mail->Username = 'xxx@163.com'; // SMTP 用户名
$mail->Password = 'xxx'; // SMTP 密码
$mail->SMTPSecure = 'ssl';
$mail->Port = 465;
$mail->From = 'xxx@163.com';
$mail->FromName = 'MySQL Backup';
$mail->addAddress($to);
$mail->addAttachment($zip_filename, $filename); // 添加邮件附件
$mail->isHTML(false);
$mail->Subject = $subject;
$mail->Body = $body;

// 发送邮件
if (!$mail->send()) {
    echo 'Mailer Error: ' . $mail->ErrorInfo;
} else {
    echo 'Message sent!';
}

// 删除旧的备份文件和压缩文件
$old_backup = $backup_dir . date("Ymd", strtotime("-1 day")) . "_backup.sql";
if (file_exists($old_backup)) {
    unlink($old_backup);
}
if (file_exists($zip_filename)) {
    unlink($zip_filename);
}
