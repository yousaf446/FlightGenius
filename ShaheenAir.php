<?php

require_once 'vendor/autoload.php';
require_once 'Spider.php';
use Symfony\Component\DomCrawler\Crawler;

class ShaheenAir {

    public function shaheenair_url() {

    }
    public function shaheen_crawler($response) {
        $flightData = array();
        $crawler = new Crawler($response);
        $outbound = $crawler->filter('b.size18')->getNode(0)->textContent;
        $inbound = $crawler->filter('b.size18')->getNode(1)->textContent;
        $mutex = false;
        $flightFilter = [];
        $flightFilter[] = $crawler->filter('ul.flightstable')->each(function(Crawler $node, $i) {
            $li = $node->getNode(0)->getElementsByTagName('li');
            $thisflight = [];
            for ($l = 0; $l < $li->length; $l++) {

                if($i % 2 == 0) {
                    if ($li->item($l)->getAttribute('class') == 'ft4') {
                        $thisflight['flightName'] = $li->item($l)->childNodes->item(2)->textContent;
                    } elseif ($li->item($l)->getAttribute('class') == 'ft2') {
                        $thisflight['leaveDate'] = $li->item($l)->childNodes->item(3)->textContent;
                        $thisflight['leaveTime'] = $li->item($l)->childNodes->item(5)->textContent;
                    } elseif ($li->item($l)->getAttribute('class') == 'ft3') {
                        $thisflight['landDate'] = $li->item($l)->childNodes->item(2)->textContent;
                        $thisflight['landTime'] = $li->item($l)->childNodes->item(4)->textContent;
                    }
                } else {
                    if ($li->item($l)->getAttribute('class') == 'ft3') {
                        $thisflight['fare'] = $li->item($l)->childNodes->item(2)->textContent;
                    } elseif ($li->item($l)->getAttribute('class') == 'ft4') {
                        $thisflight['fee_tax'] = $li->item($l)->childNodes->item(2)->textContent;
                    }
                }
            }
            return $thisflight;
        });
        $flightCount = 0;
        $finalFlight = [];
        foreach($flightFilter[0] as $thisFlight) {
            $finalFlight[] = $thisFlight;
            if($flightCount % 2 == 1) {
                if($flightCount > ($outbound * 2) && !$mutex) {
                    $mutex = true;
                    $flightCount = 1;
                }
                if($flightCount < ($outbound * 2) && !$mutex) {
                    $flightData['depart'][] = $finalFlight;
                }

                if($flightCount < ($inbound * 2) && $mutex) {
                    $flightData['arrive'][] = $finalFlight;
                }
                $finalFlight = [];
            }
            $flightCount++;
        }
        if($this->bDebug) {
            echo "<pre>";
            echo print_r($flightData);
            echo "</pre>";
        }
        $this->flightData['shaheen'] = $flightData;
    }
}