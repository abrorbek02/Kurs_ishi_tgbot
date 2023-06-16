<?php
require 'vendor/autoload.php';
require_once 'db.php';
require_once 'User.php';
require_once 'Telegram.php';
require_once 'functions.php';
$telegram=new Telegram($_ENV['TELEGRAM_BOT_TOKEN']);
//check is bot added new group
$data = $telegram->getData();
$chat_id=$telegram->ChatID();
$requested_text=$telegram->Text();
$is_group=$telegram->messageFromGroup();

if (isset($data['my_chat_member'])) {
    if ($data['my_chat_member']['new_chat_member']['status'] == 'administrator'  || $data['my_chat_member']['new_chat_member']['status'] == 'member'){
        $user=new User($data['my_chat_member']['chat']['id'],$data['my_chat_member']['chat']['title']);
        $user->set('step',1);
        $telegram->sendMessage([
            'chat_id' => $chat_id,
            'text' => "Dars jadvalini ko'rish uchun guruhingizni yozib yuboring. Masalan: 942 yoki 942-20",
        ]);
    }
    exit();
}

//$telegram->sendMessage(
//    [
//        'chat_id' => '1490424185',
//        'text' => json_encode($telegram->getData(),JSON_PRETTY_PRINT)." ishladi"
//
//    ]
//);

if ($is_group){
    $user=new User($chat_id,$telegram->messageFromGroupTitle());
} else {
    $user=new User($chat_id,$telegram->FirstName());
}
$callback_query=$telegram->Callback_Query();
$callback_data=$telegram->Callback_Data();

try {
    if($callback_query !== null && $callback_query !="") {
        if(str_contains($callback_data,'selected_group_id')){
            $group_id=explode('=',$callback_data)[1];
            $group_name=getGroups()[$group_id];
            $telegram->answerCallbackQuery([
                'callback_query_id' => $telegram->Callback_ID(),
                'text' => "Siz $group_name guruhini tanladingiz"
            ]);
            $telegram->editMessageText([
                'chat_id' => $chat_id,
                'message_id' => $telegram->MessageID(),
                'text' => "Siz $group_name guruhini tanladingiz"
            ]);
            $user->set('group_id',$group_id);
            $user->set('step',2);
            showAbilities();


        }elseif (str_contains($callback_data,'show_all_groups')){
            showStartPage();
        }elseif (str_contains($callback_data,'enable_notification')){
            $user->set('send',1);
            $telegram->answerCallbackQuery([
                'callback_query_id' => $telegram->Callback_ID(),
                'text' => "Bildirishnomalar yoqildi"
            ]);
            $reply_markup = $telegram->buildInlineKeyBoard([
                [
                    $telegram->buildInlineKeyBoardButton("O'chirish",'',"disable_notification")
                ]
            ]);
            $telegram->editMessageText([
                'chat_id' => $chat_id,
                'message_id' => $telegram->MessageID(),
                'text' => "Dars jadvalini avtomatik ravishda har kuni soat 8:00 da yuborish funksiyasi yoqilgan",
                'reply_markup' => $reply_markup
            ]);
        }elseif (str_contains($callback_data,'disable_notification')) {
            $user->set('send', 0);
            $telegram->answerCallbackQuery([
                'callback_query_id' => $telegram->Callback_ID(),
                'text' => "Bildirishnomalar o'chirildi"
            ]);
            $reply_markup = $telegram->buildInlineKeyBoard([
                [
                    $telegram->buildInlineKeyBoardButton("Yoqish", '', "enable_notification")
                ]
            ]);
            $telegram->editMessageText([
                'chat_id' => $chat_id,
                'message_id' => $telegram->MessageID(),
                'text' => "Dars jadvalini avtomatik ravishda har kuni soat 8:00 da yuborish funksiyasi o'chirilgan",
                'reply_markup' => $reply_markup
            ]);
        }
    }elseif (str_contains($requested_text, '/start')) {
        $user->set('step', 1);
        sendTextOnly("Dars jadvalini ko'rish uchun guruhingizni yozib yuboring. Masalan: 942 yoki 942-20");
    }elseif (str_contains($requested_text, '/guruh')) {
        sendTextOnly("Tanlash uchun guruhingizni yozib yuboring. Masalan: 942 yoki 942-20");
        $user->set('step', 1);
    }elseif (str_contains($requested_text, '/help')) {
        showAbilities();
    }elseif ($user->get('step')==1){
        if (strlen($requested_text)>0){
            showSearchedGroup($requested_text);
        }
    } elseif ($user->get('group_id') == null) {
        $user->set('step', 1);
        sendTextOnly("Guruh tanlanmagan. Iltimos guruh nomini yozib yuboring. Masalan: 942 yoki 942-20");
    } elseif (str_contains($requested_text, '/dars')) {
        showLessonsByDate(strtotime("+3 hours", $telegram->Date()), $user->get('group_id'));
    }elseif (str_contains($requested_text, 'dars') and isRequestingLessons($requested_text) and isTomorrow($requested_text)){
        showLessonsByDate(strtotime("+1 day +3 hours", $telegram->Date()), $user->get('group_id'));
    }elseif (str_contains($requested_text, 'dars') and isRequestingLessons($requested_text)){
        showLessonsByDate(strtotime("+3 hours", $telegram->Date()), $user->get('group_id'));
    } elseif (str_contains($requested_text, '/ertaga')) {
        showLessonsByDate(strtotime("+1 day +3 hours", $telegram->Date()), $user->get('group_id'));
    } elseif (str_contains($requested_text, '/hafta')) {
        showWeekLessons($user->get('group_id'));
    }elseif (str_contains($requested_text, '/dushanba')) {
        if (date('l') == 'Monday') {
            showLessonsByDate(strtotime("+3 hours", $telegram->Date()), $user->get('group_id'));
        } else {
            showLessonsByDate(strtotime("next Monday +3 hours", $telegram->Date()), $user->get('group_id'));
        }
    }elseif (str_contains($requested_text, '/seshanba')) {
        if (date('l') == 'Tuesday') {
            showLessonsByDate(strtotime("+3 hours", $telegram->Date()), $user->get('group_id'));
        } else {
            showLessonsByDate(strtotime("next Tuesday +3 hours", $telegram->Date()), $user->get('group_id'));
        }
    }elseif (str_contains($requested_text, '/chorshanba')) {
        if (date('l') == 'Wednesday') {
            showLessonsByDate(strtotime("+3 hours", $telegram->Date()), $user->get('group_id'));
        } else {
            showLessonsByDate(strtotime("next Wednesday +3 hours", $telegram->Date()), $user->get('group_id'));
        }
    }elseif (str_contains($requested_text, '/payshanba')) {
        if (date('l') == 'Thursday') {
            showLessonsByDate(strtotime("+3 hours", $telegram->Date()), $user->get('group_id'));
        } else {
            showLessonsByDate(strtotime("next Thursday +3 hours", $telegram->Date()), $user->get('group_id'));
        }
    }elseif (str_contains($requested_text, '/juma')) {
        if (date('l') == 'Friday') {
            showLessonsByDate(strtotime("+3 hours", $telegram->Date()), $user->get('group_id'));
        } else {
            showLessonsByDate(strtotime("next Friday +3 hours", $telegram->Date()), $user->get('group_id'));
        }
    }elseif (str_contains($requested_text, '/shanba')) {
        if (date('l') == 'Saturday') {
            showLessonsByDate(strtotime("+3 hours", $telegram->Date()), $user->get('group_id'));
        } else {
            showLessonsByDate(strtotime("next Saturday +3 hours", $telegram->Date()), $user->get('group_id'));
        }
    }elseif (str_contains($requested_text, '/avto_eslatma')) {
        showAvtoEslatma($user);
    }




} catch (Exception $e) {
    $telegram->sendMessage([
        'chat_id' => $chat_id,
        'text' => $e->getMessage(),
        'reply_to_message_id' => $telegram->MessageID()
    ]);
}
