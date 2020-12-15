<?php

use App\Conversations\QuestionsConversation;
use App\Http\Controllers\BotManController;

$botman = resolve('botman');

$botman->hears('Hi', function ($bot) {
    $bot->reply('Hello!');
    $bot->reply('Hi, I’m Kojo. I’m going to ask you some questions. I will use your answers to give you advice about the
    level of medical care you should seek.
    But first, if you are experiencing a life-threatening emergency, please call 911 immediately.
    
    If you are not experiencing a life-threatening emergency, let’s get started.
    v
    During the assessment, you can refresh the page if you need to start again. 
    ');
    $bot->startConversation(new QuestionsConversation());
});
$botman->hears('Start conversation', BotManController::class.'@startConversation');
