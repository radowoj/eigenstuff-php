<?php

require_once(__DIR__ . '/../vendor/autoload.php');

use GuzzleHttp\Client as GuzzleClient;
use Radowoj\Searcher\SearchProvider\Bing;
use Radowoj\Searcher\SearchProvider\Google;
use Radowoj\Searcher\Searcher;
use Radowoj\Eigenstuff\Scraper;

$client = new GuzzleClient();

$config = require(__DIR__ . '/config.php');

$searchProvider = new Bing(
    $client,
    $config['bing-api-key']
);

$searcher = new Searcher($searchProvider);

$results = (new Scraper($searcher))
    ->setItems([
        'Magento',
        'WooCommerce',
        'Prestashop',
        'Shopify',
        'VirtueMart',
        'Zen Cart',
        'osCommerce',
    ])->setQueries([
        "migrate from %s to %s",
        "switch from %s to %s",
    ])
    ->setQueryInterval(500000)
    ->scrape();

var_dump($results);
