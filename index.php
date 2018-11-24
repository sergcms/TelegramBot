<?php

use BotMan\BotMan\BotMan;
use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Drivers\DriverManager;

require __DIR__ . DIRECTORY_SEPARATOR . 'vendor'. DIRECTORY_SEPARATOR .'autoload.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'GalaktikaKino.php';

$config = [
     "telegram" => ["token" => "TOKEN"]
];

DriverManager::loadDriver(\BotMan\Drivers\Telegram\TelegramDriver::class);
$botman = BotManFactory::create($config);

$botman->hears('/start', function ($bot) {
    $bot->reply('Привет, я КиноБот.' . PHP_EOL . 'Для того что бы получить список фильмов отправь команду - "/get"');
});

$botman->hears('/get', function ($bot) {
    $kinoteatr = new App\GalaktikaKino();
    $bot->reply($kinoteatr->getShedules());
});

$botman->listen();
