<?php
/*
 * This file is part of the SGLScraper package.
 *
 * (c) Simon Guillem-Lessard <s.g.lessard@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

include('SGL/Lib/Autoloader.php');
spl_autoload_register(['SGL\Lib\Autoloader', 'loader']);

use SGL\Lib\ExampleScraper;

$format = 'html'; // Available : 'xml', 'html'

$scraper = new ExampleScraper();

$data = $scraper->fetch([
    'http://www.theglobeandmail.com/report-on-business/top-business-stories/oecd-slashes-outlook-for-canadas-economy-but-is-more-upbeat-than-many/article28793301/',
    'http://www.theglobeandmail.com/report-on-business/international-business/oil-extends-rally-after-iran-welcomes-output-freeze/article28793390/',
    'http://www.theglobeandmail.com/globe-drive/news/recalls/toyota-recalling-29-million-vehicles-globally-over-seatbelt-issue/article28793403/'
], $format);


header("Content-type: text/$format");
echo $data;