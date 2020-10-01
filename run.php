<?php

date_default_timezone_set('Europe/London');

require __DIR__ . '/vendor/autoload.php';

use GuzzleHttp\Client;
use Symfony\Component\Dotenv\Dotenv;

function videoOrMeme() {
    $now = new \DateTime();
    $day = (int)$now->format('d');
    $month = (int)$now->format('m');

    if (31 === $day && 3 === $month) {
        return 'https://www.youtube.com/watch?v=nLck4R3BcyM';
    }

    if (1 !== $day) {
        return getRedditImage();
    }

    switch ($month) {
        case 1:
            return 'It\'s JANNUUARRRYYYYYYY FERRRSST\nHAPPY NEW YEAR ' . $now->format('Y') .
                '\nhttps://www.youtube.com/watch?v=dxwF1oFRUmg';
        case 2:
            return 'FEBRUARY FEEEEEEEEEEEEEERRRSTT https://www.youtube.com/watch?v=QLe0mdHZmIY';
        case 3:
            return 'MARCH FERST https://www.youtube.com/watch?v=D2-dDC3Qjl0';
        case 4:
            return 'APRIL FOOL, don\'t listen to other people https://www.youtube.com/watch?v=OqjEipldpFc';
        case 5:
            return 'MAY SOMETHINGH https://www.youtube.com/watch?v=jqAaJHP3GQE';
        case 6:
            return 'JUNE FEEEEEEEEEEEEEEEEEEEEEEEEEEERST https://www.youtube.com/watch?v=YarUmcSSmjM';
        case 7:
            return 'JULY https://www.youtube.com/watch?v=7J5QzqiKymI';
        case 8:
            return 'AUGUST FEEEEEERST https://www.youtube.com/watch?v=hkCADaZDpwY';
        case 9:
            return 'https://www.youtube.com/watch?v=DUogXC_Ec40';
        case 10:
            return 'OCTOBER FEST https://www.youtube.com/watch?v=J8f7oKeMstQ';
        case 11:
            return 'NOVENMBERRER FIRSST https://www.youtube.com/watch?v=BCGlmoEKcag';
        case 12:
            return 'CHRISTMAS THE FIRST https://www.youtube.com/watch?v=vZewrglKXHE';
        default:
            throw new \RuntimeException('Months higher than 12 or lower than 1 don\'t exist you shitty language');
    }
}

function getRedditImage() {
    $subreddits = [
        'hmmmgifs',
        'bettereveryloop',
        'formuladank',
        'stonks',
    ];

    $client = new Client();

    foreach ($subreddits as $subreddit) {
        $response = $client->get('https://www.reddit.com/r/' . $subreddit . '/hot.json?limit=1');
        $json = json_decode($response->getBody(), true);
        $imageURL = $json['data']['children'][0]['data']['url'];

        if (!checkForURLPrevious($imageURL)) {
             writeToCSV($imageURL);
             return $imageURL;
        }
    }

    return 'No new hot memes today apparently';
}

function writeToCSV($url) {
    $csv = fopen($_ENV['HISTORY_FILE'], 'ab');
    fputcsv($csv, [$url]);
    fclose($csv);
}

function checkForURLPrevious($url) {
    if (false === $csv = fopen($_ENV['HISTORY_FILE'], 'rb')) {
        touch($_ENV['HISTORY_FILE']);
        return false;
    }

    while (false !== ($data = fgetcsv($csv))) {
        if ($url === $data[0]) {
            return true;
        }
    }

    return false;
}

function postMessageToDiscord($message) {
    $client = new Client();

    $client->post('https://discordapp.com/api/channels/' . $_ENV['CHANNEL_ID'] . '/messages', [
        'headers' => [
            'Authorization' => 'Bot ' . $_ENV['AUTHORISATION'],
            'Content-Type' => 'application/json',
            'User-Agent' => 'DiscordBot (FERST, 1.0)',
        ],
        'body' => json_encode(['content' => $message]),
    ]);
}

$dotenv = new Dotenv();
$dotenv->load(__DIR__.'/.env');

postMessageToDiscord(videoOrMeme());