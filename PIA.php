<?php

require_once 'vendor/autoload.php';
require_once 'Spider.php';
use Symfony\Component\DomCrawler\Crawler;

class PIA {

    private $pia_flights;

    public function pia_url($data) {
        $type = $data['type'];
        $from = $data['from'];
        $to = $data['to'];
        $depart = $data['depart_date'];
        $departFilter = str_replace('/', '-', $depart);
        $aDepart = explode('-', $departFilter);
        $departDate = $aDepart[2] . "-" . $aDepart[0] . "-" . $aDepart[1];

        if($type != 'OW') {
            $arrive = $data['arrive_date'];
            $arriveFilter = str_replace('/', '-', $arrive);
            $aArrive = explode('-', $arriveFilter);
            $arriveDate = $aArrive[2] . "-" . $aArrive[0] . "-" . $aArrive[1];
        } else {
            $arrive = "";
        }
        $url = 'https://wl-prod.havail.sabresonicweb.com/SSW2010/PKPK/webqtrip.html?origin='.$from.'&destination='.$to.'&lang=en_GB';
        $url .= '&departureDate='.$departDate.'&journeySpan='.$type;
        if(!empty($arriveDate)) $url .= '&returnDate='.$arriveDate;
        $url .= '&alternativeLandingPage=true&numAdults=1';
        $pia_data = Spider::spider_call(false, false, $url);
        $this->pia_crawler($pia_data, $depart, $arrive);
        return $this->pia_flights;
    }

    public function pia_crawler($response, $departDate, $arriveDate) {
        $flightData = array();
        $crawler = new Crawler($response);
        $flightData['depart'] = $crawler->filter('div#dtcontainer-outbounds')->each(function(Crawler $node, $i) {
            $tr = $node->getNode(0)->getElementsByTagName('tr');
            if($tr->item(1)->childNodes->item(0)->attributes->getNamedItem('class')->textContent == 'yui-dt-empty') {
                return "Flights are not available on ";
            } else {
                $aFlight = [];
                $tr_length = $tr->length;
                for ($l = 1; $l < $tr->length; $l++) {
                    $thisflight = [];
                    if ($tr->item($l)->childNodes->item(0)->getAttribute('class') == 'flightNumber') {
                        $thisflight['flightName'] = preg_replace('/\s+/', '', $tr->item($l)->childNodes->item(0)->textContent);
                    }
                    if ($tr->item($l)->childNodes->item(4)->getAttribute('class') == 'departureDate') {
                        $thisflight['leave'] = preg_replace('/\s+/', '', $tr->item($l)->childNodes->item(4)->textContent);
                        $thisflight['leave'] = str_replace("NextDayIndicator", "", $thisflight['leave']);
                    }
                    if ($tr->item($l)->childNodes->item(6)->getAttribute('class') == 'arrivalDate') {
                        $thisflight['land'] = preg_replace('/\s+/', '', $tr->item($l)->childNodes->item(6)->textContent);
                        $thisflight['land'] = str_replace("NextDayIndicator", "", $thisflight['land']);
                    }
                    if ($tr->item($l)->childNodes->item(10)->getAttribute('class') == 'totalTripDuration') {
                        $thisflight['duration'] = preg_replace('/\s+/', '', $tr->item($l)->childNodes->item(10)->textContent);
                    }
                    if ($tr->item($l)->childNodes->item(12)->getAttribute('fare-family-key') == 'CC') {
                        $fare = $tr->item($l)->childNodes->item(12)->childNodes->item(1)->textContent;
                        $fare = explode(' ', $fare);
                        $thisflight['business'] = $fare[39];
                    }
                    if ($tr->item($l)->childNodes->item(14)->getAttribute('fare-family-key') == 'EP' || $tr->item($l)->childNodes->item(14)->getAttribute('fare-family-key') == 'YY' ) {
                        $fare = $tr->item($l)->childNodes->item(14)->childNodes->item(1)->textContent;
                        $fare = explode(' ', $fare);
                        $thisflight['economy_plus'] = $fare[39];
                    }
                    if ($tr_length > 7) {
                        if ($tr->item($l)->childNodes->item(16)->getAttribute('fare-family-key') == 'YY') {
                            $fare = $tr->item($l)->childNodes->item(16)->childNodes->item(1)->textContent;
                            $fare = explode(' ', $fare);
                            $thisflight['economy'] = $fare[39];
                        }
                    }
                    if(!isset($thisflight['economy'])) $thisflight['economy'] = $thisflight['economy_plus'];
                    $aFlight[] = $thisflight;
                }
                return $aFlight;
            }
        });
        $flightData['depart'] = $flightData['depart'][0];
        if(!empty($arriveDate)) {
            $flightData['arrive'] = $crawler->filter('div#dtcontainer-inbounds')->each(function (Crawler $node, $i) {
                $tr = $node->getNode(0)->getElementsByTagName('tr');
                if($tr->item(1)->childNodes->item(0)->attributes->getNamedItem('class')->textContent == 'yui-dt-empty') {
                    return "Flights are not available on ";
                } else {
                    $aFlight = [];
                    $tr_length = $tr->length;
                    for ($l = 1; $l < $tr->length; $l++) {
                        $thisflight = [];
                        if ($tr->item($l)->childNodes->item(0)->getAttribute('class') == 'flightNumber') {
                            $thisflight['flightName'] = preg_replace('/\s+/', '', $tr->item($l)->childNodes->item(0)->textContent);
                        }
                        if ($tr->item($l)->childNodes->item(4)->getAttribute('class') == 'departureDate') {
                            $thisflight['leave'] = preg_replace('/\s+/', '', $tr->item($l)->childNodes->item(4)->textContent);
                            $thisflight['leave'] = str_replace("NextDayIndicator", "", $thisflight['leave']);
                        }
                        if ($tr->item($l)->childNodes->item(6)->getAttribute('class') == 'arrivalDate') {
                            $thisflight['land'] = preg_replace('/\s+/', '', $tr->item($l)->childNodes->item(6)->textContent);
                            $thisflight['land'] = str_replace("NextDayIndicator", "", $thisflight['land']);
                        }
                        if ($tr->item($l)->childNodes->item(10)->getAttribute('class') == 'totalTripDuration') {
                            $thisflight['duration'] = preg_replace('/\s+/', '', $tr->item($l)->childNodes->item(10)->textContent);
                        }
                        if ($tr->item($l)->childNodes->item(12)->getAttribute('fare-family-key') == 'CC') {
                            $fare = $tr->item($l)->childNodes->item(12)->childNodes->item(1)->textContent;
                            $fare = explode(' ', $fare);

                            $thisflight['business'] = $fare[39];
                        }
                        if ($tr->item($l)->childNodes->item(14)->getAttribute('fare-family-key') == 'EP' || $tr->item($l)->childNodes->item(14)->getAttribute('fare-family-key') == 'YY' ) {
                            $fare = $tr->item($l)->childNodes->item(14)->childNodes->item(1)->textContent;
                            $fare = explode(' ', $fare);
                            $thisflight['economy_plus'] = $fare[39];
                        }
                        if ($tr_length > 7) {
                            if ($tr->item($l)->childNodes->item(16)->getAttribute('fare-family-key') == 'YY') {
                                $fare = $tr->item($l)->childNodes->item(16)->childNodes->item(1)->textContent;
                                $fare = explode(' ', $fare);
                                $thisflight['economy'] = $fare[39];
                            }
                        }
                        if(!isset($thisflight['economy'])) $thisflight['economy'] = $thisflight['economy_plus'];
                        $aFlight[] = $thisflight;
                    }
                    return $aFlight;
                }
            });
            $flightData['arrive'] = $flightData['arrive'][0];
        }
        if(is_string($flightData['depart'])) {
            $departDate = date('l, M, d, Y', strtotime($departDate));
            $str = $flightData['depart'];
            unset($flightData['depart']);
            $flightData['depart']['error'] = $str . $departDate;
        }
        if(is_string($flightData['arrive'])) {
            $arriveDate = date('l, M, d, Y', strtotime($arriveDate));
            $str = $flightData['arrive'];
            unset($flightData['arrive']);
            $flightData['arrive']['error'] = $str . $arriveDate;
        }
        $this->pia_flights = $flightData;
    }
}