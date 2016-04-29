<?php
require_once 'vendor/autoload.php';
use Symfony\Component\DomCrawler\Crawler;
use GuzzleHttp\Client;
use RuntimeException;
// crate crawler instasnce from body HTML code
class GetData {
    public function spider($header = array(), $referer = false, $url, $cookie = false,
                           $post = false) {
        if (!$cookie)
        {
            $cookie = "cookie.txt";
        }
        $cookie_text = 'PHPSESSID=4k877pjmlf3fh7qi2k56h8ht81; __utmt=1; __utma=114404165.1485780952.1461914772.1461920419.1461924549.4; __utmb=114404165.8.10.1461924549; __utmc=114404165; __utmz=114404165.1461914772.1.1.utmcsr=(direct)|utmccn=(direct)|utmcmd=(none)';

        $ch = curl_init('https://www.shaheenair.com/');
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate,sdch');
        if (isset($header) && !empty($header))
        {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 200);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
        curl_setopt($ch, CURLOPT_USERAGENT,
            "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.7) Gecko/20070914 Firefox/2.0.0.7");
        //curl_setopt($ch, CURLOPT_COOKIEJAR, realpath($cookie));
        //curl_setopt($ch, CURLOPT_COOKIEFILE, realpath($cookie));
        curl_setopt($ch, CURLOPT_COOKIE, $cookie_text);
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

    public function guzzle_post($url = "", $postData = false) {
        $client = new Client();
        $response = $client->request('POST', $url, [
            'form_params' => $postData,
            'allow_redirects' => [
                'referer'         => true,      // add a Referer header
                'protocols'       => ['https'], // only allow https URLs
                'track_redirects' => true
            ]
        ]);
        print_r($response->getBody()->getContents());
    }

    public function guzzle_get($url) {
        $client = new Client();
        $response = $client->request('GET', $url);
        print_r($response->getBody()->getContents());
    }

    public function shaheen_crawler($response) {
        $flightData = array();
        $crawler = new Crawler($response);
        $crawler->filter('ul.flightstable')->each(function(Crawler $node, $i) {
            $li = $node->getNode(0)->getElementsByTagName('li');
            for($l = 0; $l < $li->length; $l++) {
                if($li->item($l)->getAttribute('class') == 'ft2')
                    print_r($li->item($l)->childNodes->item(5)->textContent);
                echo "<br/>";
            }
        });
    }
}

$pageData = new GetData();

//$airblue_data = $pageData->spider(array(), false, "https://www.airblue.com//bookings/flight_selection.aspx?TT=RT&DC=KHI&AC=ISB&AM=2016-04&AD=28&RM=2016-04&RD=30&FL=on&CC=Y&CD=&PA=1&PC=&PI=&x=51&y=23");

//$pageData->airblue_crawler($airblue_data);

$postFields = array(
    'AvailForm' =>array(
        'FROMCITY' => 'KHI',
        'TOCITY' => 'ISB',
        'FROMDATE' => '04/30/2016',
        'TODATE' => '05/02/2016',
        'ADULTCOUNT' => '1',
        'CHILDCOUNT' => '0',
        'INFANTCOUNT' => '0',
        'CURRENCY' => 'PKR'
    )
);
$pageData->spider(false, false, 'https://www.shaheenair.com/index.php', false, http_build_query($postFields));
$shaheen_data = $pageData->spider(false, 'https://www.shaheenair.com/index.php?r=member/wait', 'https://www.shaheenair.com/index.php?r=book');

$pageData->shaheen_crawler($shaheen_data);

?>