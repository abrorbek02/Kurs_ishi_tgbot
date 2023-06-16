<?php
require 'vendor/autoload.php';
require_once 'dotenv.php';

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Client;
function getGroups3():array{


        $client = new Client(["verify" => false]);
        $headers = [
            'Authorization' => 'Bearer ' . $_ENV['HEMIS_TOKEN'],
        ];
        $request = new Request('GET', 'https://student.ubtuit.uz/rest/v1/data/group-list?limit=200', $headers);
        $res = $client->sendAsync($request)->wait();
        $data = json_decode($res->getBody());
        $gr = $data->data->items;
        $groups = [];
        foreach ($gr as $group) {
            $groups[$group->id] = $group->name;
        }
        return $groups;

}function getGroups2():array{
    try {


        $client = new Client(["verify" => false]);
        $headers = [
            'Authorization' => 'Bearer ' . $_ENV['HEMIS_TOKEN'],
        ];
        $request = new Request('GET', 'https://student.ubtuit.uz/rest/v1/data/group-list?limit=200', $headers);
        $res = $client->sendAsync($request)->wait();
        $data = json_decode($res->getBody());
        $gr = $data->data->items;
        $groups = [];
        foreach ($gr as $group) {
            $groups[$group->id] = $group->name;
        }
        return $groups;
    } catch (Exception $e) {
        return getGroups3();
    }
}
function getGroups(): array
{
    try {


        $client = new Client(["verify" => false]);
        $headers = [
            'Authorization' => 'Bearer ' . $_ENV['HEMIS_TOKEN'],
        ];
        $request = new Request('GET', 'https://student.ubtuit.uz/rest/v1/data/group-list?limit=200', $headers);
        $res = $client->sendAsync($request)->wait();
        $data = json_decode($res->getBody());
        $gr = $data->data->items;
        $groups = [];
        foreach ($gr as $group) {
            $groups[$group->id] = $group->name;
        }
        return $groups;
    } catch (Exception $e) {
        return getGroups2();
    }

}

function getLessonsByDate($date, $group)
{
    try {


    $client = new Client(["verify" => false]);
    $headers = [
        'Authorization' => 'Bearer ' . $_ENV['HEMIS_TOKEN'],
    ];
    $uri = "https://student.ubtuit.uz/rest/v1/data/schedule-list?lesson_date_from=$date&lesson_date_to=$date&_group=$group";
    $request = new Request('GET', $uri, $headers);
    $res = $client->sendAsync($request)->wait();
    $data = json_decode($res->getBody());

    return $data->data->items;
    }catch (Exception $e){
        return getLessonsByDate2($date,$group);
    }


}
function getLessonsByDate2($date,$group){
    try {


        $client = new Client(["verify" => false]);
        $headers = [
            'Authorization' => 'Bearer ' . $_ENV['HEMIS_TOKEN'],
        ];
        $uri = "https://student.ubtuit.uz/rest/v1/data/schedule-list?lesson_date_from=$date&lesson_date_to=$date&_group=$group";
        $request = new Request('GET', $uri, $headers);
        $res = $client->sendAsync($request)->wait();
        $data = json_decode($res->getBody());

        return $data->data->items;
    }catch (Exception $e){
        return getLessonsByDate3($date,$group);
    }
}
function getLessonsByDate3($date,$group){

        $client = new Client(["verify" => false]);
        $headers = [
            'Authorization' => 'Bearer ' . $_ENV['HEMIS_TOKEN'],
        ];
        $uri = "https://student.ubtuit.uz/rest/v1/data/schedule-list?lesson_date_from=$date&lesson_date_to=$date&_group=$group";
        $request = new Request('GET', $uri, $headers);
        $res = $client->sendAsync($request)->wait();
        $data = json_decode($res->getBody());

        return $data->data->items;

}

function getWeekLessons($group)
{

    try {

        $start = getStartAndEndDateOfWeek()[0];
        $finish = getStartAndEndDateOfWeek()[1];
        $client = new Client(["verify" => false]);
        $headers = [
            'Authorization' => 'Bearer ' . $_ENV['HEMIS_TOKEN'],
        ];
        $uri = "https://student.ubtuit.uz/rest/v1/data/schedule-list?lesson_date_from=$start&lesson_date_to=$finish&_group=$group&limit=200";
        $request = new Request('GET', $uri, $headers);
        $res = $client->sendAsync($request)->wait();
        $data = json_decode($res->getBody());
        return $data->data->items;
    }catch (Exception $e){
        return getWeekLessons2($group);
    }
}function getWeekLessons2($group)
{

    try {
        $start = getStartAndEndDateOfWeek()[0];
        $finish = getStartAndEndDateOfWeek()[1];

        $client = new Client(["verify" => false]);
        $headers = [
            'Authorization' => 'Bearer ' . $_ENV['HEMIS_TOKEN'],
        ];
        $uri = "https://student.ubtuit.uz/rest/v1/data/schedule-list?lesson_date_from=$start&lesson_date_to=$finish&_group=$group&limit=200";
        $request = new Request('GET', $uri, $headers);
        $res = $client->sendAsync($request)->wait();
        $data = json_decode($res->getBody());
        return $data->data->items;
    }catch (Exception $e){
        return getWeekLessons3($group);
    }
}function getWeekLessons3($group)
{
    $start = getStartAndEndDateOfWeek()[0];
    $finish = getStartAndEndDateOfWeek()[1];

        $client = new Client(["verify" => false]);
        $headers = [
            'Authorization' => 'Bearer ' . $_ENV['HEMIS_TOKEN'],
        ];
        $uri = "https://student.ubtuit.uz/rest/v1/data/schedule-list?lesson_date_from=$start&lesson_date_to=$finish&_group=$group&limit=200";
        $request = new Request('GET', $uri, $headers);
        $res = $client->sendAsync($request)->wait();
        $data = json_decode($res->getBody());
        return $data->data->items;

}

function getStartAndEndDateOfWeek(): array
{
    date_default_timezone_set('Asia/Tashkent');
    $staticfinish = date('Y-m-d', strtotime('next Sunday'));
    $staticstart = date('Y-m-d', strtotime($staticfinish . '-6 days'));
    return array(strtotime($staticstart), strtotime($staticfinish));
}


