<?php
@define('ITERATIONS_COUNT', 1000);

$tinyint = 42;

$integer = 1234567890;

$float = 3.141592;

$string_3 = 'foo';

for($string_128 = ''; strlen($string_128) < 128; $string_128 .= md5(microtime()));
for($string_512 = ''; strlen($string_512) < 512; $string_512 .= md5(microtime()));
for($string_4096 = ''; strlen($string_4096) < 4096; $string_4096 .= md5(microtime()));
for($string_10240 = ''; strlen($string_10240) < 10240; $string_10240 .= md5(microtime()));

$array = array(1 => $integer, 'foo' => $float, $string_3);

class Object {};

$object = new Object;
$object->id = 11;
$object->email = 'jim_hawkins@admiral_benbow.com';
$object->name = 'Jim Hawkins';
$object->hashed_password = '5ebe2294ecd0e0f08eab7690d2a6ee69';
$object->generated_password = false;
$object->login = 'jimmy';
$object->public_email = 'jim_hawkins@admiral_benbow.com';
$object->nick = 'Jim Hawkins';
$object->country_id = 0;
$object->birth_date = '1101774844';
$object->icq = 1726362;
$object->www = 'http://domain.com';
$object->city = 16254;
$object->city_district = 125341;
$object->phone = 5374832;
$object->avatar_extension = 3;
$object->info = 'С рождения Бобби пай-мальчиком был,
Имел Бобби хобби - он деньги любил,
Любил и копил.
Все дети, как дети - живут без забот,8
А Боб на диете - не ест и не пьёт,
В копилку кладёт.';
$object->is_active = 1;
$object->ctime = 1201774844;
$object->utime = 1202737264;
$object->member_type = 1;
$object->blocked_date = false;
$object->is_blocked = false;
$object->status = 10;
$object->kind = 'Member|Registered|';
$object->name_index = 'Jim Hawkins Jim Hawkins';
$object->is_mail_replies = true;
$object->is_req_cooperation = false;
$object->last_activity_time = '1202737264';
$object->rating_avg_recommendations = 42;


$data = array(
  'tinyint' => $tinyint,
  'integer' => $integer,
  'float'   => $float,
  'string 3 chars'  => $string_3,
  'string 128 chars'  => $string_128,
  'string 512 chars'  => $string_512,
  'string 4096 chars'  => $string_4096,
  'string 10240 chars'  => $string_10240,  
//  'array'   => $array,  
//  'object'  => $object
);

$operations = array(
  'add' => 5,
  'get' => 77,
  'set' => 5,
  'delete' => 5,
  'lock' => 1,
  'unlock' => 1,
  'increment' => 1,
  'decrement' => 1,
  'safeIncrement' => 2,
  'safeDecrement' => 2
);
$operations_with_data = array('add', 'set');
