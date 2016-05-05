<?php

require_once 'vendor/autoload.php';
require_once 'Spider.php';
use Symfony\Component\DomCrawler\Crawler;

class ShaheenAir {

    private $shaheen_flights;

    public function shaheenair_url($data) {
        $type = $data['type'];
        $from = $data['from'];
        $to = $data['to'];
        $depart = $data['depart_date'];
        $arrive = $data['arrive_date'];
        $postFields = array(
            'AvailForm' =>array(
                'FROMCITY' => $from,
                'TOCITY' => $to,
                'FROMDATE' => $depart,
                'TODATE' => $arrive,
                'ADULTCOUNT' => '1',
                'CHILDCOUNT' => '0',
                'INFANTCOUNT' => '0',
                'CURRENCY' => 'PKR'
            )
        );
        Spider::spider_call(false, false, 'https://www.shaheenair.com/index.php', false, http_build_query($postFields));
        $shaheen_data = Spider::spider_call(false, 'https://www.shaheenair.com/index.php?r=member/wait', 'https://www.shaheenair.com/index.php?r=book');
        $this->shaheen_crawler($shaheen_data, $depart, $arrive);
        return $this->shaheen_flights;
    }
    public function shaheen_crawler($response, $depart, $arrive) {
        $flightData = array();
        $crawler = new Crawler($response);
        $outbound = isset($crawler->filter('b.size18')->getNode(0)->textContent) ? $crawler->filter('b.size18')->getNode(0)->textContent : "";
        $inbound = isset($crawler->filter('b.size18')->getNode(1)->textContent) ? $crawler->filter('b.size18')->getNode(1)->textContent : "";
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
                        $fare = str_replace(",", "", $li->item($l)->childNodes->item(2)->textContent);
                        $thisflight['fare'] = intval($fare);
                    } elseif ($li->item($l)->getAttribute('class') == 'ft4') {
                        $fee_tax = str_replace(",", "", $li->item($l)->childNodes->item(2)->textContent);
                        $thisflight['fee_tax'] = intval($fee_tax);
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
        if(empty($flightData)) {
            $departDate = date('l, M, d, Y', strtotime($depart));
            $arriveDate = date('l, M, d, Y', strtotime($arrive));
            $flightData['depart']['error'] = "Flights are not available on ".$departDate;
            $flightData['arrive']['error'] = "Flights are not available on ".$arriveDate;
        }
        $this->shaheen_flights = $flightData;
    }
}