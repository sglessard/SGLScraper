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

use DOMDocument;
use DOMXPath;
use SimpleXMLElement;

abstract class SGLScraper {

    /**
     * @var $uri
     */
    private $uri;

    /**
     * @var $xpath
     */
    public $xpath;

    /**
     * @param string $response
     */
    abstract protected function parse($response);

    /**
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * @param $uri
     * @return $this
     */
    public function setUri($uri)
    {
        $this->uri = $uri;
        
        return $this;
    }
    
    /**
    * Fetches a URI using curl and parses it with DOMXPath
    *
    * @param string $uri
    * @return array
    */
    public function getContent($uri)
    {
        $this->setUri($uri); 

        $curl = curl_init($uri);

        curl_setopt($curl, CURLOPT_FAILONERROR, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 15);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);

        $response = curl_exec($curl);

        curl_close($curl);

        libxml_use_internal_errors(true); // hide DOMDocument errors

        if(!empty($response)) {

            return $this->parse($response);

        } else {

            $this->error('Page not found');

        }
    }

    /**
     * @param array $uris
     * @param string $format [table, xml]
     * @return array
     */
    public function fetch($uris, $format = 'html')
    {
        $data = [];

        if (!is_array($uris)) {
            $uris = [$uris];
        }

        foreach ($uris as $uri) {
            
            $data[$uri] = $this->getContent($uri);
            
        }
        
        switch ($format) {
            case 'html':  return $this->generateTable($data); break;
            case 'xml':    return $this->generateXml($data);
        }
        
        $this->error(sprintf("Invalid format '%s'", $format), 500);
    }

    /**
     * @param $response
     * @return void
     */
    public function init($response)
    {
        $doc = new DOMDocument();
        $doc->loadHTML($response);
        
        $this->xpath = $this->getXPath($doc);
    }

    /**
     * @param  DOMDocument $doc
     * @return DOMXPath
     */
    public function getXPath(DOMDocument $doc)
    {
        $xpathsearch = new DOMXPath($doc);
        
        return $xpathsearch;
        
    }

    /**
     * @param $pages
     * @return SimpleXMLElement
     */
    public function generateXml($pages)
    {
        
        $xml = new SimpleXMLElement('<?xml version="1.0" standalone="yes"?><pages/>');

        foreach ($pages as $page) {
            
            $xmlPage = $xml->addChild('page');
            
            foreach ($page as $attribute => $value) {

                $value = preg_replace('/&/', '&amp;', $value);
                
                $xmlPage->addChild($attribute, nl2br($value));
            }
        }
        
        return $xml->asXML();
    }

    /**
     * @param array $pages
     * @return string
     */
    public function generateTable($pages)
    {
        $htmlTable = "<table><tbody>";
        
        foreach ($pages as $uri => $page) {
            
            $htmlTable .= "<tr><th colspan=2><h1><a href=\"$uri\">$uri</a></h1></th></tr>";
            
            foreach ($page as $attribute => $value) {
                $htmlTable .= "<tr><th>$attribute</th><td>" . nl2br($value) . "</td></tr>";
            }
            
        }
        $htmlTable .= "</tbody></table>";
        
        return $htmlTable;
    }
    
    /**
     * @param $errmsg
     * @param int $code
     * @return void
     */
    protected function error($errmsg, $code = 404)
    {
        switch ($code) {
            case 500: header("HTTP/1.0 500 Error"); break;
            default:  header("HTTP/1.0 404 Not Found");
        }
        
        echo '<h1>' . $errmsg . '</h1>';
    }
}