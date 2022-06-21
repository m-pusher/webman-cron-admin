<?php
use Webman\Route;
use yzh52521\Task\Client;
use support\Response;
use Webman\MiddlewareInterface;
use Webman\Http\Request;
use Webman\Http\Response as WebmanResponse;

class Access implements MiddlewareInterface
{
    public function process(Request $request, callable $next) : WebmanResponse
    {
        $user = session('cron-admin-user', null);
        if (null === $user) {
            return json(['code' => 201, 'msg' => '未登陆', 'data' => null]);
        }
        return $next($request);
    }
}

Route::group('/cron-admin', function () {
    //页面
    Route::get('/index', function () {
        $config = config('plugin.mpusher.cron-admin.app');
        $handler = config('view.handler');
        return new Response(200, [], $handler::render('index', $config['assign'], $config['view_path']));
    });
    //登陆
    Route::get('/login', function () {
        $config = config('plugin.mpusher.cron-admin.app');
        $handler = config('view.handler');
        return new Response(200, [], $handler::render('login', $config['assign'], $config['view_path']));
    });
    //api
    Route::post('/api/login', function ($request) { //登陆
        $config = config('plugin.mpusher.cron-admin.app');
        $username = $request->post('username');
        $password = $request->post('password');
        if ($username === $config['username'] && $password === $config['password']) {
            $request->session()->set('cron-admin-user', $username);
            return json(['code' => 200, 'msg' => 'ok', 'data' => null]);
        }

        return json(['code' => 0, 'msg' => '账号或密码错误', 'data' => null]);
    });
    Route::group('/api', function () {
        Route::get('/list/{page}/{size}', function ($request, $page, $size) {//计划任务列表
            $param = [
                'method' => 'crontabIndex',
                'args'   => ['limit' => $size, 'page' => $page, 'where' => $request->get()]
               ];
            $result= Client::instance()->request($param);
            return json($result);
        });
        Route::get('/logs/{id}/{page}/{size}', function ($request, $id, $page, $size) {//计划任务执行日志列表
            $param = [
                'method' => 'crontabLog',
                'args'   => ['limit' => $size, 'page' => $page, 'crontab_id' => $id, 'where' => $request->get()]
               ];
            $result= Client::instance()->request($param);
            return json($result);
        });
        Route::post('/create', function ($request) {//创建定时任务
            $param = [
                'method' => 'crontabCreate',
                'args'   => $request->post(),
               ];
            $result= Client::instance()->request($param);
            return json($result);
        });
        Route::post('/update', function ($request) {//修改定时任务
            $param = [
                'method' => 'crontabUpdate',
                'args'   => $request->post(),
               ];
            $result= Client::instance()->request($param);
            return json($result);
        });
        Route::post('/delete', function ($request) {//删除定时任务
            $param = [
                'method' => 'crontabDelete',
                'args'   => $request->post(),
               ];
            $result= Client::instance()->request($param);
            return json($result);
        });
        Route::post('/reload', function ($request) {//重启定时任务
            $param = [
                'method' => 'crontabReload',
                'args'   => $request->post(),
               ];
            $result= Client::instance()->request($param);
            return json($result);
        });
    })->middleware([
        Access::class,
    ]);
});