<?php

require_once 'vendor/autoload.php';
require_once 'Spider.php';
use Symfony\Component\DomCrawler\Crawler;

class AirBlue {

    private $airblue_flights;

    public function airblue_url($data) {
        $type = $data['type'];
        $from = $data['from'];
        $to = $data['to'];
        $depart = $data['depart_date'];
        $departFilter = str_replace('/', '-', $depart);
        $aDepart = explode('-', $departFilter);
        $departDate = $aDepart[2] . "-" . $aDepart[0];
        $departDay = $aDepart[1];
        $departBody  = $aDepart[2] . "_" . $aDepart[0] . "_" . $aDepart[1];

        if($type != 'OW') {
            $arrive = $data['arrive_date'];
            $arriveFilter = str_replace('/', '-', $arrive);
            $aArrive = explode('-', $arriveFilter);
            $arriveDate = $aArrive[2] . "-" . $aArrive[0];
            $arriveDay = $aArrive[1];
            $arriveBody = $aArrive[2] . "_" . $aArrive[0] . "_" . $aArrive[1];
        } else {
            $arriveBody = "";
        }
        $url = 'https://www.airblue.com/bookings/flight_selection.aspx?TT='.$type.'&DC='.$from.'&AC='.$to.'&AM='.$departDate.'&AD='.$departDay;
        if(!empty($arriveBody)) $url .= '&RM='.$arriveDate.'&RD='.$arriveDay;
        else $url .= '&RM=&RD=';
        $url .= '&FL=on&CC=Y&CD=&PA=1&PC=&PI=&x=51&y=23';
        $airblue_data = Spider::spider_call(array(), false, $url, false, false);
        $this->airblue_crawler($airblue_data, $departBody, $arriveBody, $depart, $arrive);
        return $this->airblue_flights;

    }
    public function airblue_crawler($airblue_data, $departBody, $arriveBody, $depart, $arrive)
    {
        $flightData = array();
        $crawler = new Crawler($airblue_data);
        $emptyFilter = $crawler->filter('tr.no_flights_found');
        if (preg_replace('/\s+/', '', $emptyFilter->getNode(0)->textContent) == 'Flightsarenotavailableonthedatesselected') {
            $departDate = date('l, M, d, Y', strtotime($depart));
            $arriveDate = date('l, M, d, Y', strtotime($arrive));
            $flightData['depart']['error'] = "Flights are not available on ".$departDate;
            $flightData['arrive']['error'] = "Flights are not available on ".$arriveDate;
        } else {
            $filter = $crawler->filter('#trip_1_date_' . $departBody);
            if ($filter) {
                $tr = $filter->getNode(0)->getElementsByTagName('tr');
                if ($tr->item(3)->attributes->getNamedItem('class')->textContent == 'no_flights_found') {
                    $flightData['depart']['error'] = $tr->item(3)->textContent;
                } else {
                    $tbody = $filter->getNode(0)->getElementsByTagName('tbody');
                    for ($l = 0; $l < $tbody->length; $l++) {
                        $childNodes = $tbody->item(0)->childNodes->item(0)->childNodes;
                        $class = $tbody->item(0)->childNodes->item(0)->attributes->getNamedItem('class')->textContent;
                        if ($class == 'no_flights_found') {
                            $flightData['depart'] = false;
                        } else {
                            $thisflight = [];
                            $thisflight['flightName'] = preg_replace('/\s+/', '', $childNodes->item(0)->textContent);
                            $thisflight['leave'] = preg_replace('/\s+/', '', $childNodes->item(2)->textContent);
                            $thisflight['route'] = preg_replace('/\s+/', '', $childNodes->item(4)->textContent);
                            $thisflight['land'] = preg_replace('/\s+/', '', $childNodes->item(6)->textContent);
                            $thisflight['discount'] = preg_replace('/\s+/', '', $childNodes->item(10)->textContent);
                            $thisflight['standard'] = preg_replace('/\s+/', '', $childNodes->item(12)->textContent);
                            $thisflight['premium'] = preg_replace('/\s+/', '', $childNodes->item(14)->textContent);
                            $thisflight['delay'] = ($class == 'flight-status-delayed') ? true : false;
                            $flightData['depart'][] = $thisflight;
                            //$td = $tbody->item(0)->childNodes->item(1)->textContent;
                        }
                    }
                }
            }
            if (!empty($arriveBody)) {
                $filter = $crawler->filter('#trip_2_date_' . $arriveBody);
                if ($filter) {
                    $tr = $filter->getNode(0)->getElementsByTagName('tr');
                    if ($tr->item(3)->attributes->getNamedItem('class')->textContent == 'no_flights_found') {
                        $flightData['arrive']['error'] = $tr->item(3)->textContent;
                    } else {
                        $tbody = $filter->getNode(0)->getElementsByTagName('tbody');
                        for ($l = 0; $l < $tbody->length; $l++) {
                            $childNodes = $tbody->item(0)->childNodes->item(0)->childNodes;
                            $delay = $tbody->item(0)->childNodes->item(0)->attributes->getNamedItem('class')->textContent;
                            $thisflight = [];
                            $thisflight['flightName'] = preg_replace('/\s+/', '', $childNodes->item(0)->textContent);
                            $thisflight['leave'] = preg_replace('/\s+/', '', $childNodes->item(2)->textContent);
                            $thisflight['route'] = preg_replace('/\s+/', '', $childNodes->item(4)->textContent);
                            $thisflight['land'] = preg_replace('/\s+/', '', $childNodes->item(6)->textContent);
                            $thisflight['discount'] = preg_replace('/\s+/', '', $childNodes->item(10)->textContent);
                            $thisflight['standard'] = preg_replace('/\s+/', '', $childNodes->item(12)->textContent);
                            $thisflight['premium'] = preg_replace('/\s+/', '', $childNodes->item(14)->textContent);
                            $thisflight['delay'] = ($delay == 'flight-status-delayed') ? true : false;
                            $flightData['arrive'][] = $thisflight;
                            //$td = $tbody->item(0)->childNodes->item(1)->textContent;
                        }
                    }
                }
            }
        }
        $this->airblue_flights = $flightData;
    }
}