<?php
if (!$this) {
    exit;
}
?>
<!doctype html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $this->getTitle(); ?> - Jokkebrok Administratie</title>
    <script src="libs/jquery-1.11.0.js"></script>
    <script src="libs/jquery.sortable.min.js"></script>
    <script data-main="js/main" src="./libs/require.js"></script>
    <link href="libs/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="libs/eternicode-bootstrap-datepicker/css/datepicker.css" rel="stylesheet">
    <link href="libs/bootstrap-modal/css/bootstrap-modal.css" rel="stylesheet">
    <link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
    <script>
        if (top.location != location) {
            top.location.href = document.location.href;
        }
        $(document).ready(function () {
            $("body").keypress(function (e) {
                if (!$(e.target).is("input, textarea") && !$("body").hasClass("modal-open")) {
                    switch (e.which) {
                        case 100://D: Dashboard
                            window.location = "?page=dashboard";
                            break;
                        case 97://A: Aanwezigheden
                            window.location = "?page=aanwezigheden";
                            break;
                        case 107://K: Kinderen
                            window.location = "?page=kinderen";
                            break;
                        case 117://U: Uitstappen
                            window.location = "?page=uitstappen";
                            break;
                    }
                }
            });
        });
    </script>
    <style type="text/css">
        html, body {
            height: 100%;
        }

        body {
            padding: 70px 0 0 0;
        }

        #wrap {
            min-height: 100%;
            height: auto;
            /* Negative indent footer by its height */
            margin: 0 auto -60px;
            /* Pad bottom by footer height */
            padding: 0 0 60px 0px;
        }

        /* Set the fixed height of the footer here */
        #footer {
            height: 60px;
            background-color: #f5f5f5;
        }

        #navContainer[max-width="980px"] #datumDiv {
            display: none;
        }

        a#datumNav:hover {
            color: rgb(153, 153, 153);
            cursor: default;
        }

        .datepicker {
            z-index: 2000;
        !important
        }
    </style>
</head>
<body>
<div id="wrap">
    <div class="navbar navbar-inverse navbar-fixed-top" id="navContainer" role="navigation">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="?">Jokkebrok</a>
            </div>
            <div class="collapse navbar-collapse">
                <ul class="nav navbar-nav" id="navbar">
                    <li id="dashboard">
                        <a href='?page=dashboard'>Dashboard</a>
                    </li>
                    <li id="aanwezigheden">
                        <a href='?page=aanwezigheden'>Aanwezig</a>
                    </li>
                    <li id="kinderen">
                        <a href='?page=kinderen'>Kinderen</a>
                    </li>
                    <li id="uitstappen">
                        <a href='?page=uitstappen'>Uitstappen</a>
                    </li>
                </ul>
                <ul class="nav navbar-nav pull-right">
                    <li>
                        <?php
                        $vandaag = new SpeelpleinDag();
                        $full_datum = $vandaag->getFullDatum();
                        ?>
                        <a href='#' class="text-info" id="datumNav"
                           title="<?php echo "Vandaag is " . $full_datum . "."; ?>">
                            <span class="glyphicon glyphicon-calendar"></span>
                            <?php
                            $day_of_week = $vandaag->getDayOfWeek();
                            echo $day_of_week;
                            ?>
                        </a>
                    </li>
                    <li id="instellingen" title="Instellingen">
                        <a href='?page=instellingen'><span class="glyphicon glyphicon-cog"></span></a>
                    </li>
                    <li title="Uitloggen">
                        <a href='?action=logout'><span class="glyphicon glyphicon-log-out"></span></a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="container" id="content-container">
        <?php $this->printContent(); ?>
    </div>
</div>
<div id="footer">
    <div class="container">
        <p class="text-muted text-center">
            &copy; 2013-2015 Jokkebrok Administratie by Floris Kint &amp; Roderick Demol
        </p>

        <p class="text-muted text-center">
            <a href='?page=about'>About</a>
        </p>
    </div>
</div>
<script src="libs/bootstrap/js/bootstrap.min.js"></script>
<script src="libs/eternicode-bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
<script src="libs/eternicode-bootstrap-datepicker/js/locales/bootstrap-datepicker.nl-BE.js"></script>
<script src="libs/typeahead.bundle.js"></script>
<script src="libs/bootstrap-modal/js/bootstrap-modalmanager.js"></script>
<script src="libs/bootstrap-modal/js/bootstrap-modal.js"></script>
<script>
    $.fn.datepicker.defaults.weekStart = 1;
    $('#navbar li#<?php echo $this->getCurrentTab(); ?>').addClass('active');
</script>
</body>
</html>
