# php-library
自己写的一些php工具类






## 1.Tiny File Manager 是一个简单的文件管理器，用 PHP 编写，具有以下功能：

1. **文件管理**：允许用户浏览服务器上的文件和文件夹，包括创建、删除、重命名和移动文件和文件夹。

2. **上传和下载**：支持文件上传和下载，允许用户将文件上传到服务器或从服务器下载文件到本地计算机。

3. **编辑文件**：支持文本文件的在线编辑，可以直接在浏览器中编辑文本文件并保存更改。

4. **文件搜索**：提供简单的文件搜索功能，用户可以通过文件名搜索文件。

5. **多语言支持**：支持多种语言，用户可以选择他们喜欢的语言界面。

6. **轻量级**：Tiny File Manager 是一个轻量级的文件管理器，使用简单，易于安装和配置。

7. **密码保护**：支持设置密码保护，用户需要输入密码才能访问文件管理器。

总的来说，Tiny File Manager 提供了一个简单而有效的方式来管理服务器上的文件和文件夹，适用于小型网站或个人项目。

仓库地址：https://github.com/prasathmani/tinyfilemanager



## 2.`flozz/p0wny-shell` 是一个 PHP 项目的webshell

`flozz/p0wny-shell` 是一个 PHP 项目，用于创建一个简单但功能强大的 web shell。Web shell 是一个运行在 Web 服务器上的脚本，可以通过浏览器或其他远程工具进行访问和控制，允许用户执行各种操作，包括文件管理、命令执行等。

具体来说，`flozz/p0wny-shell` 可以用于以下目的：

1. **远程文件管理**：允许用户通过 Web 界面浏览、上传、下载和删除文件。

2. **命令执行**：可以执行系统命令，并返回结果给用户。

3. **信息收集**：可以获取关于服务器环境的信息，如操作系统、PHP 配置等。

4. **权限提升**：如果 Web 服务器的权限不够高，可以尝试通过 Web shell 进行权限提升，获取更高的权限。

5. **后门访问**：Web shell 可以作为后门，允许攻击者随时访问受感染的服务器。

需要注意的是，虽然 Web shell 在某些情况下可能是有用的，比如在进行网络渗透测试或者管理远程服务器时，但它也可能被不法分子用于非法目的，因此在使用和部署时务必谨慎，并遵守法律法规。

仓库地址：https://github.com/flozz/p0wny-shell/blob/master/shell.php



## 3.单文件博客

`oink.php` 是一个免费开源的 PHP API 包装器，它以单个文件的形式提供。

- **安装**：只需下载 `oink.php` 文件并包含到项目中即可。它不依赖于其他库，兼容 PHP 8.0 及以上版本。

- **基本用法**：通过调用 `Oink\serve('endpoints.php')` 来创建一个简单的博客 API。在 `endpoints.php` 文件中定义的函数会成为 API 的端点。

- **路由**：`serve` 函数会根据 `endpoints.php` 文件中定义的函数名创建端点。你可以自定义路由。

- **参数处理**：端点函数中通过调用不同的参数类型函数来读取请求参数。例如 `str("tag", optional: true)` 可以读取名为 `tag` 的参数，并确保其为字符串类型。

- **响应**：端点函数返回的 JSON 对象或数组作为响应。如果参数验证失败或检查失败，会返回 400 错误；如果出现异常，会返回 500 错误。

`oink.php` 的目标是简单易用，快速开发为主。如果需要高度可定制性、模块化和可扩展性的解决方案，建议使用像 Laravel、Symfony 或 Lumen 这样的完整框架。

https://github.com/jcarlosroldan/oink



## 4.单文件数据库操作

catfan/Medoo 是一个用于 PHP 的轻量级数据库框架，它提供了简洁的 API 来执行数据库操作，如插入、更新、删除和查询。Medoo 的目标是提供简单、快速和安全的数据库操作方式，同时尽可能减少开发人员的工作量和学习曲线。它支持多种数据库，包括 MySQL、SQLite、MariaDB、Microsoft SQL Server 等。

仓库地址：https://github.com/catfan/Medoo/blob/master/src/Medoo.php

