<?php
require_once 'reader.php';
function sendText($text)
{
    global $telegram, $chat_id;
    return $telegram->sendMessage([
        'chat_id' => $chat_id,
        'text' => $text,
    ]);
}
function sendTextOnly($text)
{
    global $telegram, $chat_id;
    return $telegram->sendMessage([
        'chat_id' => $chat_id,
        'text' => $text,
    ]);
}
function showStartPage(): void
{
    global $telegram,$chat_id;
    $groups=getGroups();
    $options=[];
    foreach ($groups as $id=>$name){
        $options[]=[$telegram->buildInlineKeyBoardButton($name,'',"selected_group_id=$id")];
    }
    $reply_markup = $telegram->buildInlineKeyBoard($options);
    $message=$telegram->sendMessage([
        'chat_id' => $chat_id,
        'text' => 'Guruhni tanlang',
        'reply_markup' => $reply_markup
    ]);
    $telegram->sendMessage([
        'chat_id' => $chat_id,
        'text' => "Yuqoridagi guruhlardan birini tanlashingiz yoki guruh nomini yozib yuborishingiz mumkin. Masalan: 942 yoki 942-20",
        'reply_to_message_id' => $message['result']['message_id']
    ]);


}
function showSearchedGroup($group_name): void
{
    global $telegram,$chat_id;
    $message=$telegram->sendMessage([
        'chat_id' => $chat_id,
        'text' => "Hemis serveriga so'rov yuborildi. Iltimos kuting...",
    ]);
    $groups=getGroups();
    $options=[];
    foreach ($groups as $id=>$name){
        if (str_contains($name,$group_name)){
            $options[]=[$telegram->buildInlineKeyBoardButton($name,'',"selected_group_id=$id")];
        }
    }
    if (count($options)==0){
        $options=[[$telegram->buildInlineKeyBoardButton("Barcha guruhlar ro'yhatini ko'rish",'',"show_all_groups")]];
        $reply_markup = $telegram->buildInlineKeyBoard($options);
        $telegram->editMessageText([
            'chat_id' => $chat_id,
            'message_id' => $message['result']['message_id'],
            'text' => "Siz kiritgan guruh topilmadi. Iltimos qaytadan urinib ko'ring. Masalan: 942 yoki 942-20",
            'reply_markup' => $reply_markup
        ]);
    }else {
        $reply_markup = $telegram->buildInlineKeyBoard($options);
        $telegram->editMessageText([
            'chat_id' => $chat_id,
            'message_id' => $message['result']['message_id'],
            'text' => "Guruh tanlang",
            'reply_markup' => $reply_markup
        ]);
    }

}

function showAbilities(): void
{
    $text="Bot yordamida quyidagi imkoniyatlardan foydalanishingiz mumkin:".PHP_EOL;
    $text.="/dars - Bugungi dars jadvalini ko'rish".PHP_EOL;
    $text.="/ertaga - Ertangi dars jadvalini ko'rish".PHP_EOL;
    $text.="/guruh - Guruh tanlash".PHP_EOL;
    $text.="/avto_eslatma - Dars jadvalini avtomatik jo'natish. Har kuni 8:00 da".PHP_EOL;
    $text.="/dushanba - Dushanba kuni dars jadvali".PHP_EOL;
    $text.="/seshanba - Seshanba kuni dars jadvali".PHP_EOL;
    $text.="/chorshanba - Chorshanba kuni dars jadvali".PHP_EOL;
    $text.="/payshanba - Payshanba kuni dars jadvali".PHP_EOL;
    $text.="/juma - Juma kuni dars jadvali".PHP_EOL;
    $text.="/shanba - Shanba kuni dars jadvali".PHP_EOL;
    $text.="/hafta - Haftalik dars jadvalini ko'rish".PHP_EOL;
    $text.="/start - Botni qayta ishga tushirish".PHP_EOL;
    sendText($text);

}
function showLessonsByDate($date,$group): void
{   global $telegram,$chat_id;
    $message=sendText("Hemis serveriga so'rov yuborildi. Iltimos kuting...");
    $lessons=getLessonsByDate($date,$group);
    if (count($lessons)==0){
        $text="📅 ".weekDayInUzbek($date)." ".date('d.m.Y',$date)." sanasida dars yo'q.";
    }else{
        $text="🔹 Guruh:".$lessons[0]->group->name.PHP_EOL;
        $text.="📅 ".weekDayInUzbek($date)." ".date('d.m.Y',$date)." sanasidagi darslar:".PHP_EOL;

    }
    $text = getStr($lessons, $text);
    $telegram->editMessageText([
        'chat_id' => $chat_id,
        'message_id' => $message['result']['message_id'],
        'text' => $text,
    ]);
}

/**
 * @param $lessons
 * @param string $text
 * @return string
 */
function getStr($lessons, string $text): string
{
    foreach ($lessons as $lesson) {
        $text .=
            "📘 " .
            $lesson->subject->name . PHP_EOL .
            '🏷 ' . $lesson->trainingType->name . PHP_EOL .
            '🏛 ' . $lesson->auditorium->name . PHP_EOL .
            '👤 ' . $lesson->employee->name . PHP_EOL .
            '⏰ ' . $lesson->lessonPair->start_time .
            '-' . $lesson->lessonPair->end_time . PHP_EOL . PHP_EOL;
    }
    return $text;
}

function weekDayInUzbek($date){
    $weekDays=[
        'Monday'=>'Dushanba',
        'Tuesday'=>'Seshanba',
        'Wednesday'=>'Chorshanba',
        'Thursday'=>'Payshanba',
        'Friday'=>'Juma',
        'Saturday'=>'Shanba',
        'Sunday'=>'Yakshanba',
    ];
    return $weekDays[date('l',$date)];
}
function showWeekLessons($group): void
{
    global $telegram,$chat_id;
    $message=sendText("Hemis serveriga so'rov yuborildi. Iltimos kuting...");
    $lessons=getWeekLessons($group);
    $weekDayLessons=[];
    foreach ($lessons as $lesson){
        $weekDayLessons[weekDayInUzbek($lesson->lesson_date)][]=$lesson;
    }
    $text="📅 ".weekDayInUzbek($lessons[0]->lesson_date)." ".date('d.m.Y',$lessons[0]->lesson_date)." - ".weekDayInUzbek($lessons[count($lessons)-1]->lesson_date)." ".date('d.m.Y',$lessons[count($lessons)-1]->lesson_date)." sanalari darslar:".PHP_EOL;
    foreach ($weekDayLessons as $dayName=>$weekDayLesson) {
        $text.="📅 ".$dayName." ".date('d.m.Y',$weekDayLesson[0]->lesson_date).PHP_EOL;
        $text = getStr($weekDayLesson, $text);

    }

    $telegram->editMessageText([
        'chat_id' => $chat_id,
        'message_id' => $message['result']['message_id'],
        'text' => $text,
    ]);
}
function showAvtoEslatma($user){
    global $telegram,$chat_id;
    if ($user->get('send')==1){
        $options=[[$telegram->buildInlineKeyBoardButton("O'chirish","","disable_notification")]];
        $reply_markup=$telegram->buildInlineKeyBoard($options);
        $telegram->sendMessage([
            'chat_id' => $chat_id,
            'text' => "Dars jadvalini avtomatik ravishda har kuni soat 8:00 da yuborish funksiyasi yoqilgan",
            'reply_markup' => $reply_markup
        ]);
    }else{
        $options=[[$telegram->buildInlineKeyBoardButton("Yoqish","","enable_notification")]];
        $reply_markup=$telegram->buildInlineKeyBoard($options);
        $telegram->sendMessage([
            'chat_id' => $chat_id,
            'text' => "Dars jadvalini avtomatik ravishda har kuni soat 8:00 da yuborish funksiyasi o'chirilgan",
            'reply_markup' => $reply_markup
        ]);
    }
}
function isRequestingLessons($text):bool{
   $words=['nerda', 'novi','kimni', 'qaysi','narda','xona','nuvi','novi','novvi','nard','yoqmi',"yo'qmi",'bomi','boma','bormi','borma'];
    foreach ($words as $word){
         if (str_contains($text,$word)){
              return true;
         }
    }
    return false;
}
function isTomorrow($text):bool{
    $words=['ertang','artang','ertaga'];
    foreach ($words as $word){
        if (str_contains($text,$word)){
            return true;
        }
    }
    return false;

}
function sendAndPinText($text):void{
    global $telegram,$chat_id;
    $message=sendText($text);
    $telegram->pinChatMessage([
        'chat_id' => $chat_id,
        'message_id' => $message['result']['message_id'],
    ]);
}


