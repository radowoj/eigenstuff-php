<?php

namespace Radowoj\Eigenstuff;

class HtmlResult
{
    protected $results = [];


    public function __construct(array $results)
    {
        $this->results = $results;
    }


    public function render()
    {
        extract(['results' => $this->results]);
        ob_start();
        include('html-result.phtml');
        return ob_get_clean();
    }


}
