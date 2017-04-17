<?php

namespace Radowoj\Eigenstuff;

class ResultRenderer
{
    protected $results = [];

    protected $template = null;

    public function __construct(string $template, array $results)
    {
        $this->template = $template;
        $this->results = $results;
    }


    public function render()
    {
        extract(['results' => $this->results]);
        ob_start();
        include($this->template);
        return ob_get_clean();
    }


}
