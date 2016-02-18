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

class Autoloader
{
    public static function loader($className)
    {
        $filename = preg_replace('/\\\/', '/', $className) . ".php";
        
        if (is_file($filename)) {
            
            require_once($filename);
            
        }
    }
}