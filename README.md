# eigenstuff-php
PHP implementation of erikbern/eigenstuff - basically a toy tool to predict the future from result counts of Google (and other) Web searches.

## Example:
```php
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

```

## Above example will yield a results.html file similar to:

&nbsp; | **to: Magento** | **to: WooCommerce** | **to: Prestashop** | **to: Shopify** | **to: VirtueMart** | **to: Zen Cart** | **to: osCommerce**
--- | --- | --- | --- | --- | --- | --- | ---
from: Magento |	- |	71 |	92 |	78 |	59 |	3 |	53
from: WooCommerce |	48 |	- |	57 |	53 |	3 |	0 |	8
from: Prestashop |	71 |	53 |	- |	14 |	35 |	1 |	29
from: Shopify |	73 |	59 |	39 |	- |	1 |	0 |	0
from: VirtueMart |	58 |	29 |	78 |	1 |	- |	13 |	41
from: Zen Cart |	49 |	43 |	4397 |	19 |	54 |	- |	54
from: osCommerce |	108 |	57 |	66 |	24 |	50 |	45 |	- 


That's it for now, eigenvalues calculations & predictions will be added soon (I hope).
