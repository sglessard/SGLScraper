<?php
/*
 * This file is part of the SGLScraper package.
 *
 * (c) Simon Guillem-Lessard <s.g.lessard@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SGL\Lib;

class ExampleScraper extends SGLScraper {

    /**
    * Parses HTML
    *
    * @param string $response
    * @return array
    */
    public function parse($response)
    {
        $this->init($response);
        
        $xpathsearch = $this->xpath;
        
        $data = [];
        
        $data['uri'] = $this->getUri();
        $data['title'] = trim($xpathsearch->query("//h1[contains(@class,'entry-title')]/text()")->item(0)->nodeValue);
        $data['img'] = trim($xpathsearch->query("//article//header//figure//img//@src")->item(0)->nodeValue);
        
        return $data;
    }
}