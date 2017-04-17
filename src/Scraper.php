<?php

namespace Radowoj\Eigenstuff;

use Exception;
use Radowoj\Searcher\ISearcher;

class Scraper
{
    protected $searcher = null;

    protected $items = [];

    protected $queries = [];

    protected $queryIntervalUsec = 0;

    public function __construct(ISearcher $searcher)
    {
        $this->searcher = $searcher;
    }


    public function setItems(array $items)
    {
        array_map(function($item){
            if (!mb_strlen($item)) {
                throw new Exception("Cannot search for empty string.");
            }
        }, $items);

        $this->items = $items;

        return $this;
    }


    /**
     * Sets search queries for this Scaper instance.
     * Each query must consist of at least two %s placeholders
     */
    public function setQueries(array $queries)
    {
        array_map(function($query) {
            if (!preg_match('/\%s.*\%s/', $query)) {
                throw new Exception("Invalid search query: \"{$query}\". Valid search query must contain two %s tokens");
            }
        }, $queries);

        $this->queries = $queries;

        return $this;
    }


    public function setQueryInterval(int $usec)
    {
        $this->queryIntervalUsec = $usec;
        return $this;
    }


    protected function getEmptyResultArray()
    {
        //result array rows (from items)
        $queries = array_combine($this->items, array_fill(0, count($this->items), []));

        //result array columns (to items)
        foreach($queries as &$query) {
            $query = array_combine($this->items, array_fill(0, count($this->items), 0));
        }

        unset($query);

        return $queries;
    }


    protected function sumQueries($itemFrom, $itemTo)
    {
        if ($itemFrom === $itemTo) {
            return 0;
        }

        $sum = 0;

        foreach($this->queries as $query) {
            $query = sprintf($query, $itemFrom, $itemTo);

            $query = "\"{$query}\"";

            $result = $this->searcher->query($query)
                ->find();

            $sum += $result->totalCount();

            echo "Asked for {$query}, got {$result->totalCount()} results" . PHP_EOL;

            usleep($this->queryIntervalUsec);
        }

        return $sum;
    }


    public function scrape()
    {
        if (!$this->queries) {
            throw new Exception("I don't know what to ask!");
        }

        if (!$this->items) {
            throw new Exception("I don't know which items to ask for!");
        }

        $results = $this->getEmptyResultArray();

        foreach($results as $itemFrom => $subResult) {
            $keys = array_keys($subResult);
            foreach($keys as $itemTo) {
                $results[$itemFrom][$itemTo] = $this->sumQueries($itemFrom, $itemTo);
            }
        }

        return $results;
    }
}
