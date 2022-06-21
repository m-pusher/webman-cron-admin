# webman-cron-admin
cron-admin
# 简介
 
`基于webman-task的任务管理后台，就加了个管理页面`

# 使用方法
首先启用webman-task插件
```php
#配置config/plugin/yzh52521/task/app.php
<?php
return [
    'enable' => true,
    'task'   => [
        'listen'            => '0.0.0.0:2345',
        'crontab_table'     => 'system_crontab', //任务计划表
        'crontab_table_log' => 'system_crontab_log',//任务计划流水表
        'debug'             => true,
    ],
];
>
```
然后启用cron-admin插件
```php
#配置config/plugin/mpusher/cron-admin/app.php
<?php
return [
    'enable' => true,
    'username' => 'root',   //登陆账号
    'password' => '123456', //登陆密码
];
```
登陆地址:{host}/cron-admin/login
