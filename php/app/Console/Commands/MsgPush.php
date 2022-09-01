<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Workerman\Worker;
use Workerman\Lib\Timer;
use PHPSocketIO\SocketIO;
use App\Models\Base\Apidate;

class MsgPush extends Command
{
    protected $signature = 'msg-push
    {action=start : start | restart | reload(平滑重启) | stop | status | connetions}
    {--d : deamon or debug}';

    protected $description = 'web消息推送服务';
    // 全局数组保存empno在线数据
    private static $empnoConnectionCounter = [];
    // 广播的在线用户数,一个empno代表一个用户
    private static $empnoInfo = [];
    // 广播的在线用户数,一个empno代表一个用户
    private static $onlineCount = 0;
    // 广播的在线页面数,同一个empno可能开启多个页面
    private static $onlinePageCount = 0;


    private static $senderIo = null;

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        global $argv;
        //启动php脚本所需的命令行参数
        $argv[0] = 'MsgPush';
        $argv[1] = $this->argument('action'); // start | restart | reload(平滑重启) | stop | status | connetions
        $argv[2] = $this->option('d') ? '-d' : ''; // 守护进程模式或调试模式启动

        // PHPSocketIO服务
        // self::$senderIo = new SocketIO(3210);
        self::$senderIo  = new SocketIO(3210);
        self::$senderIo->on('connection', function ($socket) {
            $socket->on('login', function ($info) use ($socket) {
                // 已经登录过了     
                $arr = json_decode($info, true);

                if (isset($socket->empno)) return;
                // 更新对应empno的在线数据 
                $empno =  $arr['empno'];
                echo ($empno);
                // 这个empno有self::$empnoConnectionCounter[$empno]个socket连接
                self::$empnoConnectionCounter[$empno] = isset(self::$empnoConnectionCounter[$empno]) ? self::$empnoConnectionCounter[$empno] + 1 : 1;

                self::$empnoInfo[$empno] = $arr;
                self::$senderIo->emit("welcome", self::$empnoInfo);

                // 将这个连接加入到empno分组，方便针对empno推送数据
                $socket->join($empno);
                $socket->empno = $empno;

                // 更新这个socket对应页面的在线数据
                self::emitOnlineCount();
                //self::$senderIo->to("EF786")->emit("chat", "asd");

                $socket->on('chat', function ($info) use ($socket) {
                    self::$senderIo->to($info['myempno'])->emit("chat", $info);
                    $info['type'] = "user";
                    self::$senderIo->to($info['toempno'])->emit("chat", $info); 
                });
                // Timer::add(3, function () use ($io) {
                // }, null, true); //false :运行一次 true：定时 
                //$model = new Apidate();
                // $io->emit("chatserver",  $model->x("getiotTH",$params));

                //$io->emit('chatserver',  $model->x("selectmodel", null));
            });
            $socket->on('disconnect', function () use ($socket) { 
                if (!isset($socket->empno)) {
                    return;
                }

                // 将empno的在线socket数减一
                if (--self::$empnoConnectionCounter[$socket->empno] <= 0) {
                    unset(self::$empnoConnectionCounter[$socket->empno]);
                    unset(self::$empnoInfo[$socket->empno]);
                    self::emitOnlineCount();
                    self::$senderIo->emit("welcome", self::$empnoInfo);
                }
            });
        });

        Worker::runAll();
    }

    public static function emitOnlineCount()
    {
        // $newOnlineCount = count(self::$empnoConnectionCounter);
        // $newOnlinePageCount = array_sum(self::$empnoConnectionCounter);

        // // 只有在客户端在线数变化了才广播，减少不必要的客户端通讯
        // if ($newOnlineCount != self::$onlineCount || $newOnlinePageCount != self::$onlinePageCount) {
        //            var_dump('emitOnlineCount: ', self::$empnoConnectionCounter);
        //将在线数变化推送给所有登录端
        self::$senderIo->emit(
            'update_online_count',
            [
                'onlineCount' => count(self::$empnoConnectionCounter),
                'onlinePageCount' => array_sum(self::$empnoConnectionCounter)
            ]
        );
        //     self::$onlineCount = $newOnlineCount;
        //     self::$onlinePageCount = $newOnlinePageCount;
        // }
    }
}
