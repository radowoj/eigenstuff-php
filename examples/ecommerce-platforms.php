<?php

require_once(__DIR__ . '/../vendor/autoload.php');

use GuzzleHttp\Client as GuzzleClient;
use Radowoj\Searcher\SearchProvider\Bing;
use Radowoj\Searcher\SearchProvider\Google;
use Radowoj\Searcher\Searcher;
use Radowoj\Eigenstuff\Scraper;
use Radowoj\Eigenstuff\HtmlResult;

$client = new GuzzleClient();

$config = require(__DIR__ . '/config.php');

$searchProvider = new Bing(
    $client,
    $config['bing-api-key']
);

$searcher = new Searcher($searchProvider);

$results = (new Scraper($searcher))

    //Set items to search for (in original Eigenstuff, programming languages,
    //here for example ecommerce platforms)
    ->setItems([
        'Magento',
        'WooCommerce',
        'Prestashop',
        'Shopify',
        'VirtueMart',
        'Zen Cart',
        'osCommerce',

    //Set the queries to ask, results will be summed for each pair
    //Note that the order of items is always item from -> item to
    //maybe I will add query direction later
    ])->setQueries([
        "migrate from %s to %s",
        "switch from %s to %s",
    ])

    //Don't blow up query limit per second, sleep in microseconds
    ->setQueryInterval(500000)

    ->scrape();

$results = require('results.php');
$html = (new HtmlResult($results))->render();
file_put_contents('results.html', $html);
