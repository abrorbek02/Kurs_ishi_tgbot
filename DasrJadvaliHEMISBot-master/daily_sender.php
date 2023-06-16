<?php

require_once 'vendor/autoload.php';
require_once 'reader.php';
require_once 'functions.php';
require_once 'db.php';
require_once 'Telegram.php';
global $conn;
date_default_timezone_set('Asia/Tashkent');

$telegram=new Telegram($_ENV['TELEGRAM_BOT_TOKEN']);
$telegram->sendMessage([
    'chat_id'=>1490424185,
    'text'=>'cron ishga tushdi',
]);
try {
    if (date('w') == 0){
        exit();
    }else{
        $sended_users=0;
        $sql= "SELECT * FROM users where send=1 and group_id is not null and chat_id is not null";
        $result=$conn->query($sql);
        if ($result->num_rows > 0){
            while ($row=$result->fetch_assoc()){
                $group_id=$row['group_id'];
                $chat_id=$row['chat_id'];
                $lessons=getLessonsByDate(strtotime("+5 hours today"),$group_id);
                if (count($lessons)==0){
                    exit();
                }else{
                    $text="🔹 Guruh:".$lessons[0]->group->name.PHP_EOL;
                    $text.="📅 ".weekDayInUzbek(strtotime("+5 hours today"))." ".date('d.m.Y',strtotime("+5 hours today"))." sanasidagi darslar:".PHP_EOL;
                    $text=getStr($lessons,$text);
                    $message=$telegram->sendMessage([
                        'chat_id'=>$chat_id,
                        'text'=>$text,
                    ]);
                    $message_id=$message['result']['message_id'];
                    //pin message
                    $telegram->pinChatMessage([
                        'chat_id'=>$chat_id,
                        'message_id'=>$message_id,
                    ]);
                    $sended_users+=1;
                    if ($sended_users%29==0){
                        sleep(1);
                    }

                }
            }
        }

    }

}catch (Exception $e){
    $telegram->sendMessage([
        'chat_id'=>1490424185,
        'text'=>$e->getMessage(),
    ]);
}


