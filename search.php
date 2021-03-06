<?php

require_once "AirBlue.php";
require_once "ShaheenAir.php";
require_once "PIA.php";

$sError = "";
$flights = array('airblue', 'shaheen', 'pia');
if(isset($_POST['search'])) {
    session_start();
    $vType = (isset($_POST['type']) && !empty($_POST['type'])) ? $_POST['type'] : "";
    $vFrom = (isset($_POST['from']) && !empty($_POST['from'])) ? $_POST['from'] : "";
    $vTo = (isset($_POST['to']) && !empty($_POST['to'])) ? $_POST['to'] : "";
    $vDepart = (isset($_POST['depart_date']) && !empty($_POST['depart_date'])) ? $_POST['depart_date'] : date('m/d/Y');
    $vArrive = (isset($_POST['arrive_date']) && !empty($_POST['arrive_date'])) ? $_POST['arrive_date'] : "";
    if(empty($vType)) {
        $sError = "Please Select One From RoundTrip / One Way";
    } elseif(empty($vFrom) || empty($vTo)) {
        $sError = "Please Select Both From & To Locations";
    } elseif((empty($vDepart) || empty($vArrive)) && $vType == 'RT') {
        $sError = "Please Select Both Depart & Return Date";
    } elseif(strtotime($vArrive) < strtotime($vDepart) && $vType == 'RT') {
        $sError = "Return Date is earlier than Depart Date. Please Change.";
    }
    $_SESSION['type']   = $vType;
    $_SESSION['from']   = $vFrom;
    $_SESSION['to']     = $vTo;
    $_SESSION['depart'] = $vDepart;
    $_SESSION['arrive'] = $vArrive;


    if(empty($sError)) {
        $data = [
            'type' => $vType,
            'from' => $vFrom,
            'to' => $vTo,
            'depart_date' => $vDepart,
            'arrive_date' => $vArrive
        ];
        $airblue = new AirBlue();
        $flights['airblue'] = $airblue->airblue_url($data);
        //print_r($flights['airblue']);
        $shaheen = new ShaheenAir();
        $flights['shaheen'] = $shaheen->shaheenair_url($data);
        //print_r($flights['shaheen']);
        $pia = new PIA();
        $flights['pia'] = $pia->pia_url($data);
        //print_r($flights['pia']);
    }

}