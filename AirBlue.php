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
        $arrive = $data['arrive_date'];
        $adult = $data['adult'];
        $child = $data['child'];
        $infant = $data['infant'];
        $depart = str_replace('/', '-', $depart);
        $arrive = str_replace('/', '-', $arrive);
        $aDepart = explode('-', $depart);
        $aArrive = explode('-', $arrive);
        $departDate = $aDepart[2] . "-" . $aDepart[0];
        $departDay = $aDepart[1];
        $arriveDate = $aArrive[2] . "-" . $aArrive[0];
        $arriveDay = $aArrive[1];
        $departBody  = $aDepart[2] . "_" . $aDepart[0] . "_" . $aDepart[1];
        $arriveBody  = $aArrive[2] . "_" . $aArrive[0] . "_" . $aArrive[1];
        $url = 'https://www.airblue.com/bookings/flight_selection.aspx?TT='.$type.'&DC='.$from.'&AC='.$to.'&AM='.$departDate.'&AD='.$departDay.'&RM='.$arriveDate.'&RD='.$arriveDay.'&FL=on&CC=Y&CD=&PA='.$adult.'&PC='.$child.'&PI='.$infant.'&x=51&y=23';
        $airblue_data = Spider::spider_call(array(), false, $url, false, false);
        $this->airblue_crawler($airblue_data, $departBody, $arriveBody);
        return $this->airblue_flights;

    }
    public function airblue_crawler($airblue_data, $departBody, $arriveBody) {
        $flightData = array();
        $crawler = new Crawler($airblue_data);
        $emptyFilter = $crawler->filter('tr.no_flights_found');
        if(preg_replace('/\s+/', '', $emptyFilter->getNode(0)->textContent) == 'Flightsarenotavailableonthedatesselected') {
            $flightData = 'Flights are not available on the dates selected';
        } else {
            $filter = $crawler->filter('#trip_1_date_'.$departBody);
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
            $filter = $crawler->filter('#trip_2_date_'.$arriveBody);
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

        $this->airblue_flights = $flightData;
    }
}