<?php
session_start();

define('BLUEZY_API_URL', 'https://bluezygpt.space/api/v1');
define('BLUEZY_API_KEY', 'BLUEZY-5f1cc2cc80300ba305bb8b4fabecedfa79d0b4c59a4a1bd5');

define('INWCLOUD_API_URL', 'https://api.inwcloud.shop/v1/truewallet/redeem');
define('INWCLOUD_API_KEY', 'inwcloud_live_6ed435f82c6d426e52a7dfa8391975e946345022');

define('DISCORD_INVITE', 'https://discord.gg/xZc2N4jp3x');

define('FLOATING_IMAGE', 'https://cdn.discordapp.com/attachments/1542585922330034280/1551939160661819442/B1372674-9C5E-411C-83F6-19D911646C67.png?ex=6ab3cb79&is=6ab279f9&hm=381f802854d5fd790aee413b502418417ace8beeb56213ebd7c4431dd01a21a4&');

define('MODELS', [
    'bluezygpt-free'      => ['name' => 'FREE GPT',     'free' => 20, 'desc' => 'โมเดลฟรี ใช้ได้วันละ 20 พ้อยต์'],
    'bluezygpt-flash'     => ['name' => 'ABC FLASH',    'free' => 3,  'desc' => 'ฟรี 3 พ้อยต์ต่อวัน'],
    'bluezygpt-pro'       => ['name' => 'ABC PRO',      'free' => 0,  'desc' => 'ไม่มีฟรีพ้อยต์'],
    'bluezygpt-max'       => ['name' => 'ABC MAX',      'free' => 0,  'desc' => 'ไม่มีฟรีพ้อยต์'],
    'bluezygpt-workpro'   => ['name' => 'ABC WORK-PRO', 'free' => 0,  'desc' => 'ไม่มีฟรีพ้อยต์'],
    'bluezygpt-workflash' => ['name' => 'ABC WORK-MAX', 'free' => 0,  'desc' => 'ไม่มีฟรีพ้อยต์'],
]);

define('POINT_PACKAGES', [
    ['points' => 50,   'price' => 29],
    ['points' => 100,  'price' => 50],
    ['points' => 300,  'price' => 159],
    ['points' => 500,  'price' => 200],
    ['points' => 1000, 'price' => 459],
]);
