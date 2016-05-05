<!DOCTYPE html>
<html>


<!-- Mirrored from preview.byaviators.com/template/superlist/ by HTTrack Website Copier/3.x [XR&CO'2013], Tue, 19 Apr 2016 05:42:44 GMT -->
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
    <?php include_once 'styles.php'; ?>
    <title>RoadGuide</title>
</head>


<body>
<?php include_once 'header.php';
include_once 'search.php';
include_once 'cities.php'; ?>
<div class="page-wrapper">

    <div class="main">
        <div class="main-inner">
            <div class="content">
                <div class="mt-150">
                    <div class="hero-image">
                        <div class="hero-image-inner" style="background-image: url('assets/img/home-background.jpg');">

                            <div class="hero-image-form-wrapper">
                                <div class="container">
                                    <div class="row">
                                        <div class="col-sm-4 col-sm-offset-8 col-lg-6 col-lg-offset-3">
                                            <form method="POST" action="index.php">
                                                <h2>Start Searching</h2>
                                                <?php if(!empty($sError)) { ?>
                                                <div role="alert" class="alert alert-icon alert-danger">
                                                    <strong>Oh snap!</strong> <?php echo $sError; ?>
                                                </div>
                                                <?php } ?>
                                                <div class="form-group">
                                                    <div class="radio-inline">
                                                        <input type="radio" <?php if($_SESSION['type'] == 'RT') echo "checked"; ?>  name="type" value="RT" id="rt"><label onclick="ArriveDiv('show')" for="rt">Round Trip</label>

                                                    </div>
                                                <div class="radio-inline">
                                                    <input type="radio"  <?php if($_SESSION['type'] == 'OW') echo "checked"; ?>  name="type" value="OW"  id="ow"><label onclick="ArriveDiv('hide')" for="ow">One Way</label>
                                                </div>
                                                    </div>
                                                <div class="hero-image-location form-group">
                                                    <select class="form-control" title="From" name="from" id="from">
                                                        <option value="">Departure City</option>
                                                        <?php
                                                            foreach($aCity as $thisCity) {
                                                        ?>
                                                        <option value="<?php echo $thisCity['code']; ?>" <?php if($_SESSION['from'] == $thisCity['code']) echo "selected" ?>><?php echo $thisCity['name']; ?></option>
                                                        <?php
                                                            }
                                                        ?>
                                                    </select>
                                                </div><!-- /.form-group -->

                                                <div class="hero-image-location form-group">
                                                    <select class="form-control" title="To" name="to" id="to">
                                                        <option value="">Arrival City</option>
                                                        <?php
                                                        foreach($aCity as $thisCity) {
                                                            ?>
                                                            <option value="<?php echo $thisCity['code']; ?>" <?php if($_SESSION['to'] == $thisCity['code']) echo "selected" ?>><?php echo $thisCity['name']; ?></option>
                                                            <?php
                                                        }
                                                        ?>
                                                    </select>
                                                </div><!-- /.form-group -->

                                                <div class="hero-image-date form-group">
                                                    <input type="text" class="form-control" value="<?php echo $_SESSION['depart'];?>" placeholder="Departing On" name="depart_date" id="depart_date">
                                                </div><!-- /.form-group -->
                                                <?php if($_SESSION['type'] != 'OW') { ?>
                                                <div id="arriveDiv" class="hero-image-date form-group">
                                                    <input type="text" class="form-control" value="<?php echo $_SESSION['arrive'];?>" placeholder="Returning On" name="arrive_date" id="arrive_date">
                                                </div><!-- /.form-group -->
                                                <?php } ?>
                                                <button type="submit" onclick="loader()" id="search" name="search" class="btn btn-primary btn-block">Search</button>
                                                <div class="loading loading--double" id="loader"></div>
                                            </form>
                                        </div><!-- /.col-* -->
                                    </div><!-- /.row -->
                                </div><!-- /.container -->
                            </div><!-- /.hero-image-form-wrapper -->
                        </div><!-- /.hero-image-inner -->
                    </div><!-- /.hero-image -->

                </div>

                <div class="container">
                    <div class="block background-white fullwidth pt0 pb0">
                        <div class="partners">
                            <a href="#">
                                <img src="assets/img/airblue.png" alt="">
                            </a>

                            <a href="#">
                                <img src="assets/img/shaheen_air.png" alt="">
                            </a>

                            <a href="#">
                                <img src="assets/img/pia.png" alt="">
                            </a>
                        </div><!-- /.partners -->

                    </div>
                    <div class="page-header" style="display:none">
                        <h1>Fair Pricing</h1>
                        <p>Our company offers best pricing options for field agents and companies. If you are interested <br>in special plan don't hesitate and contact our <a href="#">sales support</a>.</p>
                    </div><!-- /.page-header -->
                    <?php if(isset($_POST['search']) && empty($sError)) { ?>
                    <div class="pricings" style="">
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="pricing">
                                    <div class="pricing-title">AirBlue</div>
                                    <div class="pricing-subtitle-note">Dates are flexible</div>
                                    <?php if (isset($flights['airblue']['error'])) { ?>
                                        <div class="pricing-price"><span
                                                class="pricing-currency"></span><?php echo $flights['airblue']['error']; ?>
                                        </div>
                                    <?php } else { ?>
                                        <div class="pricing-subtitle">Departure Flights</div>
                                        <?php if (isset($flights['airblue']['depart']['error'])) { ?>
                                            <strong><?php echo $flights['airblue']['depart']['error']; ?></strong>
                                        <?php } else { ?>
                                            <ul class="pricing-list">
                                            <?php foreach ($flights['airblue']['depart'] as $depart) { ?>
                                                <li><span>Flight | Date | Leave | Land | Fare</span><strong>
                                                        <?php echo $depart['flightName']; ?> |
                                                        <?php echo $_SESSION['depart']; ?> |
                                                        <?php echo $depart['leave']; ?> |
                                                        <?php echo $depart['land']; ?> |
                                                        <?php echo $depart['standard']; ?>
                                                    </strong>
                                                </li>
                                            <?php }
                                        } ?>
                                        </ul>
                                        <?php if ($_SESSION['type'] != 'OW') { ?>
                                            <div class="pricing-subtitle">Return Flights</div>
                                            <?php if (isset($flights['airblue']['arrive']['error'])) { ?>
                                                <strong><?php echo $flights['airblue']['arrive']['error']; ?></strong>
                                            <?php } else { ?>
                                                <ul class="pricing-list">
                                                <?php foreach ($flights['airblue']['arrive'] as $arrive) { ?>
                                                    <li><span>Flight | Date | Leave | Land | Fare</span><strong>
                                                            <?php echo $arrive['flightName']; ?> |
                                                            <?php echo $_SESSION['arrive']; ?> |
                                                            <?php echo $arrive['leave']; ?> |
                                                            <?php echo $arrive['land']; ?> |
                                                            <?php echo $arrive['standard']; ?>
                                                        </strong>
                                                    </li>
                                                <?php }
                                            } ?>
                                            </ul>
                                        <?php }
                                    }
                                    ?>
                                </div><!-- /.pricing -->
                            </div><!-- /.col-* -->

                            <div class="col-sm-4">
                                <div class="pricing">
                                    <div class="pricing-title">Shaheen Air</div>
                                    <div class="pricing-subtitle-note">Dates are flexible</div>
                                    <?php if (isset($flights['shaheen']['error'])) { ?>
                                        <div class="pricing-price"><span
                                                class="pricing-currency"></span><?php echo $flights['shaheen']['error']; ?>
                                        </div>
                                    <?php } else { ?>
                                        <div class="pricing-subtitle">Departure Flights</div>
                                        <?php if (isset($flights['shaheen']['depart']['error'])) { ?>
                                            <strong><?php echo $flights['shaheen']['depart']['error']; ?></strong>
                                        <?php } else { ?>
                                            <ul class="pricing-list">
                                                <?php $departCount = 0;
                                                foreach ($flights['shaheen']['depart'] as $depart) {
                                                    if ($departCount < 3) { ?>
                                                        <li><span>Flight | Date | Leave | Land | Fare</span><strong>
                                                                <?php echo $depart[0]['flightName']; ?> |
                                                                <?php echo $depart[0]['leaveDate']; ?> |
                                                                <?php echo $depart[0]['leaveTime']; ?> |
                                                                <?php echo $depart[0]['landTime']; ?> |
                                                                <?php echo "PKR" . (intval($depart[1]['fare']) + intval($depart[1]['fee_tax'])); ?>
                                                            </strong>
                                                        </li>
                                                    <?php }
                                                    $departCount++;
                                                } ?>
                                            </ul>
                                        <?php } ?>
                                        <?php if ($_SESSION['type'] != 'OW') { ?>
                                            <div class="pricing-subtitle">Return Flights</div>
                                            <?php if (isset($flights['shaheen']['arrive']['error'])) { ?>
                                                <strong><?php echo $flights['shaheen']['arrive']['error']; ?></strong>
                                            <?php } else { ?>
                                                <ul class="pricing-list">
                                                <?php $arriveCount = 0;
                                                foreach ($flights['shaheen']['arrive'] as $arrive) {
                                                    if ($arriveCount < 3) { ?>
                                                        <li><span>Flight | Date | Leave | Land | Fare</span><strong>
                                                                <?php echo $arrive[0]['flightName']; ?> |
                                                                <?php echo $arrive[0]['leaveDate']; ?> |
                                                                <?php echo $arrive[0]['leaveTime']; ?> |
                                                                <?php echo $arrive[0]['landTime']; ?> |
                                                                <?php echo "PKR" . (intval($arrive[1]['fare']) + intval($arrive[1]['fee_tax'])); ?>
                                                            </strong>
                                                        </li>
                                                    <?php }
                                                    $arriveCount++;
                                                }
                                            }
                                            ?>
                                            </ul>
                                        <?php }
                                    } ?>
                                </div><!-- /.pricing -->
                            </div><!-- /.col-* -->

                            <div class="col-sm-4">
                                <div class="pricing">
                                    <div class="pricing-title">PIA</div>
                                    <div class="pricing-subtitle-note">Dates are flexible</div>
                                    <?php if (isset($flights['pia']['error'])) { ?>
                                        <div class="pricing-price"><span
                                                class="pricing-currency"></span><?php echo $flights['pia']['error']; ?>
                                        </div>
                                    <?php } else { ?>
                                        <div class="pricing-subtitle">Departure Flights</div>
                                        <?php if (isset($flights['pia']['depart']['error'])) { ?>
                                            <strong><?php echo $flights['pia']['depart']['error']; ?></strong>
                                        <?php } else { ?>
                                            <ul class="pricing-list">
                                                <?php $departCount = 0;
                                                foreach ($flights['pia']['depart'] as $depart) {
                                                    if ($departCount < 3) { ?>
                                                        <li><span>Flight | Date | Leave | Land | Fare</span><strong>
                                                                <?php echo $depart['flightName']; ?> |
                                                                <?php echo $_SESSION['depart']; ?> |
                                                                <?php echo $depart['leave']; ?> |
                                                                <?php echo $depart['land']; ?> |
                                                                <?php echo $depart['economy']; ?>
                                                            </strong>
                                                        </li>
                                                    <?php }
                                                    $departCount++;
                                                } ?>
                                            </ul>
                                        <?php } ?>
                                        <?php if ($_SESSION['type'] != 'OW') { ?>
                                            <div class="pricing-subtitle">Return Flights</div>
                                            <?php if (isset($flights['pia']['arrive']['error'])) { ?>
                                                <strong><?php echo $flights['pia']['arrive']['error']; ?></strong>
                                            <?php } else { ?>
                                                <ul class="pricing-list">
                                                <?php $arriveCount = 0;
                                                foreach ($flights['pia']['arrive'] as $arrive) {
                                                    if ($arriveCount < 3) { ?>
                                                        <li><span>Flight | Date | Leave | Land | Fare</span><strong>
                                                                <?php echo $arrive['flightName']; ?> |
                                                                <?php echo $_SESSION['arrive']; ?> |
                                                                <?php echo $arrive['leave']; ?> |
                                                                <?php echo $arrive['land']; ?> |
                                                                <?php echo $arrive['economy']; ?>
                                                            </strong>
                                                        </li>
                                                    <?php }
                                                    $arriveCount++;
                                                }
                                            }
                                            ?>
                                            </ul>
                                        <?php }
                                    } ?>
                                </div><!-- /.pricing -->
                            </div><!-- /.col-* -->
                        </div><!-- /.pricings -->
                        <?php } ?>


                    <?php include_once 'footer.php'; ?>
                </div><!-- /.container -->

            </div><!-- /.content -->
        </div><!-- /.main-inner -->
    </div><!-- /.main -->

</div><!-- /.page-wrapper -->

</body>
<?php include_once 'scripts.php'; ?>
<!-- Mirrored from preview.byaviators.com/template/superlist/ by HTTrack Website Copier/3.x [XR&CO'2013], Tue, 19 Apr 2016 05:43:47 GMT -->
</html>