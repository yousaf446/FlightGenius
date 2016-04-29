<?php
require_once 'vendor/autoload.php';
use Symfony\Component\DomCrawler\Crawler;
use RuntimeException;
// crate crawler instasnce from body HTML code
class GetData {
    public function spider($header = array(), $referer = false, $url, $cookie = false,
                           $post = false) {
        if (!$cookie)
        {
            $cookie = "cookie.txt";
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate,sdch');
        if (isset($header) && !empty($header))
        {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 200);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
        curl_setopt($ch, CURLOPT_USERAGENT,
            "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.7) Gecko/20070914 Firefox/2.0.0.7");
        curl_setopt($ch, CURLOPT_COOKIEJAR, realpath($cookie));
        curl_setopt($ch, CURLOPT_COOKIEFILE, realpath($cookie));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        if (isset($referer) && $referer != false)
        {
            curl_setopt($ch, CURLOPT_REFERER, $referer);
        } else
        {
            curl_setopt($ch, CURLOPT_REFERER, $url);
        }
        //if have to post data on the server
        if (isset($post) && !empty($post) && $post)
        {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        } //endif
        $data = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);
        return ($data);
    }

    public function airblue_crawler($response) {
        $flightData = array();
        $crawler = new Crawler($response);
        $filter = $crawler->filter('#trip_1_date_2016_04_28');
        $tbody = $filter->getNode(0)->getElementsByTagName('tbody');
        for($l = 0 ; $l < $tbody->length; $l++) {
            $childNodes = $tbody->item(0)->childNodes->item(0)->childNodes;
            $class = $tbody->item(0)->childNodes->item(0)->attributes->getNamedItem('class')->textContent;
            if($class == 'no_flights_found') {
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
        $filter = $crawler->filter('#trip_1_date_2016_04_30');
        $tbody = $filter->getNode(0)->getElementsByTagName('tbody');
        for($l = 0 ; $l < $tbody->length; $l++) {
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
            $thisflight['delay'] = ($delay == 'flight-status-delayed') ? true :false;
            $flightData['arrive'][] = $thisflight;
            //$td = $tbody->item(0)->childNodes->item(1)->textContent;
        }
        echo "<pre>";
        echo print_r($flightData);
        echo "</pre>";
    }
}
$pageData = new GetData();
$data = $pageData->spider(array(), false, "https://www.airblue.com//bookings/flight_selection.aspx?TT=RT&DC=KHI&AC=ISB&AM=2016-04&AD=28&RM=2016-04&RD=30&FL=on&CC=Y&CD=&PA=1&PC=&PI=&x=51&y=23");

$pageData->airblue_crawler($data);


?>