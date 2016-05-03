<?php

class GetData {
    private $flightData = ['airblue' => array(), 'shaheen' => array(), 'pia' => array()];
    private $bDebug;

    function __construct()
    {
        $this->bDebug = $_GET['debug'];
    }

    public function get_all_data() {
        return $this->flightData;
    }
}

$pageData = new GetData();


$pageData->airblue_crawler($airblue_data);

$postFields = array(
    'AvailForm' =>array(
        'FROMCITY' => 'KHI',
        'TOCITY' => 'ISB',
        'FROMDATE' => '05/03/2016',
        'TODATE' => '05/05/2016',
        'ADULTCOUNT' => '1',
        'CHILDCOUNT' => '0',
        'INFANTCOUNT' => '0',
        'CURRENCY' => 'PKR'
    )
);
$pageData->spider(false, false, 'https://www.shaheenair.com/index.php', false, http_build_query($postFields));
$shaheen_data = $pageData->spider(false, 'https://www.shaheenair.com/index.php?r=member/wait', 'https://www.shaheenair.com/index.php?r=book');
$pageData->shaheen_crawler($shaheen_data);


$pia_data = $pageData->spider(false, false, 'https://wl-prod.havail.sabresonicweb.com/SSW2010/PKPK/webqtrip.html?origin=KHI&destination=ISB&lang=en_GB&departureDate=2016-5-03&journeySpan=RT&returnDate=2016-05-05&alternativeLandingPage=true&numAdults=1');
$pageData->pia_crawler($pia_data);

//if($this->bDebug) {
    echo "<pre>";
    echo print_r($pageData->get_all_data());
    echo "</pre>";
//}

?>