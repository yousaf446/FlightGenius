<?php

require_once 'vendor/autoload.php';
require_once 'Spider.php';
use Symfony\Component\DomCrawler\Crawler;

class PIA {

    public function pia_url() {

    }

    public function pia_crawler($response) {
        $flightData = array();
        $crawler = new Crawler($response);
        $flightData['depart'] = $crawler->filter('div#dtcontainer-outbounds')->each(function(Crawler $node, $i) {
            $tr = $node->getNode(0)->getElementsByTagName('tr');
            $aFlight = [];
            for ($l = 1; $l < $tr->length; $l++) {
                $thisflight = [];
                if ($tr->item($l)->childNodes->item(0)->getAttribute('class') == 'flightNumber') {
                    $thisflight['flightName'] = preg_replace('/\s+/', '', $tr->item($l)->childNodes->item(0)->textContent);
                }
                if ($tr->item($l)->childNodes->item(4)->getAttribute('class') == 'departureDate') {
                    $thisflight['leave'] = preg_replace('/\s+/', '', $tr->item($l)->childNodes->item(4)->textContent);
                }
                if ($tr->item($l)->childNodes->item(6)->getAttribute('class') == 'arrivalDate') {
                    $thisflight['land'] = preg_replace('/\s+/', '', $tr->item($l)->childNodes->item(6)->textContent);
                }
                if ($tr->item($l)->childNodes->item(10)->getAttribute('class') == 'totalTripDuration') {
                    $thisflight['duration'] = preg_replace('/\s+/', '', $tr->item($l)->childNodes->item(10)->textContent);
                }
                if ($tr->item($l)->childNodes->item(12)->getAttribute('fare-family-key') == 'CC') {
                    $fare = preg_replace('/\s+/', '', $tr->item($l)->childNodes->item(12)->textContent);
                    $fare = str_replace(chr(194)," ",$fare);
                    $thisflight['business'] = str_replace('PKRNotavailable', '', $fare);
                }
                if ($tr->item($l)->childNodes->item(14)->getAttribute('fare-family-key') == 'EP') {
                    $fare = preg_replace('/\s+/', '', $tr->item($l)->childNodes->item(14)->textContent);
                    $fare = str_replace(chr(194)," ",$fare);
                    $thisflight['economy_plus'] = str_replace('PKRNotavailable', '', $fare);
                }
                if ($tr->item($l)->childNodes->item(16)->getAttribute('fare-family-key') == 'YY') {
                    $fare = preg_replace('/\s+/', '', $tr->item($l)->childNodes->item(16)->textContent);
                    $fare = str_replace(chr(194)," ",$fare);
                    $thisflight['economy'] = str_replace('PKRNotavailable', '', $fare);
                }
                $aFlight[] = $thisflight;
            }
            return $aFlight;
        });
        $flightData['depart'] = $flightData['depart'][0];

        $flightData['arrive'] = $crawler->filter('div#dtcontainer-inbounds')->each(function(Crawler $node, $i) {
            $tr = $node->getNode(0)->getElementsByTagName('tr');
            $aFlight = [];
            for ($l = 1; $l < $tr->length; $l++) {
                $thisflight = [];
                if ($tr->item($l)->childNodes->item(0)->getAttribute('class') == 'flightNumber') {
                    $thisflight['flightName'] = preg_replace('/\s+/', '', $tr->item($l)->childNodes->item(0)->textContent);
                }
                if ($tr->item($l)->childNodes->item(4)->getAttribute('class') == 'departureDate') {
                    $thisflight['leave'] = preg_replace('/\s+/', '', $tr->item($l)->childNodes->item(4)->textContent);
                }
                if ($tr->item($l)->childNodes->item(6)->getAttribute('class') == 'arrivalDate') {
                    $thisflight['land'] = preg_replace('/\s+/', '', $tr->item($l)->childNodes->item(6)->textContent);
                }
                if ($tr->item($l)->childNodes->item(10)->getAttribute('class') == 'totalTripDuration') {
                    $thisflight['duration'] = preg_replace('/\s+/', '', $tr->item($l)->childNodes->item(10)->textContent);
                }
                if ($tr->item($l)->childNodes->item(12)->getAttribute('fare-family-key') == 'CC') {
                    $fare = preg_replace('/\s+/', '', $tr->item($l)->childNodes->item(12)->textContent);
                    $fare = str_replace(chr(194)," ",$fare);
                    $thisflight['business'] = str_replace('PKRNotavailable', '', $fare);
                }
                if ($tr->item($l)->childNodes->item(14)->getAttribute('fare-family-key') == 'EP') {
                    $fare = preg_replace('/\s+/', '', $tr->item($l)->childNodes->item(14)->textContent);
                    $fare = str_replace(chr(194)," ",$fare);
                    $thisflight['economy_plus'] = str_replace('PKRNotavailable', '', $fare);
                }
                if ($tr->item($l)->childNodes->item(16)->getAttribute('fare-family-key') == 'YY') {
                    $fare = preg_replace('/\s+/', '', $tr->item($l)->childNodes->item(16)->textContent);
                    $fare = str_replace(chr(194)," ",$fare);
                    $thisflight['economy'] = str_replace('PKRNotavailable', '', $fare);
                }
                $aFlight[] = $thisflight;
            }
            return $aFlight;
        });
        $flightData['arrive'] = $flightData['arrive'][0];
        /*for($l = 1 ; $l < $tr->length; $l++) {
            $childNodes = $tr->item($l)->childNodes;
            $thisflight = [];
            $thisflight['flightName'] = preg_replace('/\s+/', '', $childNodes->item(0)->);
            $thisflight['leave'] = preg_replace('/\s+/', '', $childNodes->item(4)->textContent);
            $thisflight['land'] = preg_replace('/\s+/', '', $childNodes->item(6)->textContent);
            $thisflight['duration'] = preg_replace('/\s+/', '', $childNodes->item(10)->textContent);
            $thisflight['business'] = $childNodes->item(12)->childNodes->item(16)->textContent;
            $thisflight['economy_plus'] = preg_replace('/\s+/', '', $childNodes->item(14)->textContent);
            $thisflight['economy'] = preg_replace('/\s+/', '', $childNodes->item(16)->textContent);
            print_r($thisflight);
        }*/
        if($this->bDebug) {
            echo "<pre>";
            echo print_r($flightData);
            echo "</pre>";
        }
        $this->flightData['pia'] = $flightData;
    }
}