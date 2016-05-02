<?php
require_once(dirname(__FILE__) . "/../page.php");
require_once(dirname(__FILE__) . "/../../model/werkingen/werking.class.php");
require_once(dirname(__FILE__) . "/../../model/extraatjes/extraatje.class.php");
require_once(dirname(__FILE__) . "/../../model/speelpleindag/speelpleindag.class.php");
require_once(dirname(__FILE__) . "/../../model/extraatjes/extraatje_aanwezigheid.class.php");
require_once(dirname(__FILE__) . "/../../model/uitstappen/uitstap.class.php");

class DashboardPage extends Page
{
    public function __construct()
    {
        parent::__construct("Dashboard", "", "dashboard");
    }

    private function printAanwezighedenVandaagContent()
    {
        $vandaag = new SpeelpleinDag();
        $werkingen = Werking::getWerkingen();
        $werkingen_amount = count($werkingen);
        $extraatjes = Extraatje::getExtraatjes();
        $werkingen_ths = "";
        foreach ($werkingen as $w) {
            $werkingen_ths .= "<th>" . $w->getOmschrijving() . "</th>";
        }
        $werkingen_extraatjes_tbody = "";
        $extraatje_index = 0;
        $datum_str = "&filter%5BDatum%5D=" . $vandaag->getDatum();
        foreach ($extraatjes as $e) {
            ++$extraatje_index;
            $current_line = "<tr>";
            if ($extraatje_index == 1) {
                $current_line .= "<td rowspan=\"" . count($extraatjes) . "\">Extra's</td>";
            }
            $current_line .= "<td>" . $e->getOmschrijving() . "</td>";
            foreach ($werkingen as $w) {
                $filter = array();
                $filter['Datum'] = $vandaag->getDatumForDatabase();
                $extraatje_id = $e->getId();
                $werking_id = $w->getId();
                $filter['Werking'] = $werking_id;
                $filter['Extraatje'] = $extraatje_id;
                $amount = ExtraatjeAanwezigheid::countExtraatjeAanwezigheden($filter);
                $current_line .= "<td><a href='index.php?page=aanwezigheden&filter%5BWerking%5D=$werking_id&filter%5BExtraatje%5D=$extraatje_id$datum_str'>$amount</a></td>";
            }
            $filter = array();
            $filter['Datum'] = $vandaag->getDatumForDatabase();
            $extraatje_id = $e->getId();
            $filter['Extraatje'] = $extraatje_id;
            $amount = ExtraatjeAanwezigheid::countExtraatjeAanwezigheden($filter);
            $current_line .= "<td><a href='index.php?page=aanwezigheden&filter%5BExtraatje%5D=$extraatje_id$datum_str'>$amount</a></td>";
            $current_line .= "</tr>";
            $werkingen_extraatjes_tbody .= $current_line;
        }
        $werkingen_footer = "<tr><th colspan='2'>Aanwezige kinderen";
        $sum = 0;
        foreach ($werkingen as $w) {
            $filter = array();
            $filter['Datum'] = $vandaag->getDatumForDatabase();
            $filter['Werking'] = $w->getId();
            $amount = Aanwezigheid::countAanwezigheden($filter);
            $werkingen_footer .= "<th><a href='index.php?page=aanwezigheden&filter%5BWerking%5D=" . $w->getId() . "$datum_str'>" . $amount . "</a>";
            $sum += $amount;
        }
        $werkingen_footer .= "<th><a href='index.php?page=aanwezigheden$datum_str'>$sum</tr>";
        $content = <<<HERE
<table class="table table-bordered">
<thead>
	<tr>
		<th colspan="2" rowspan="2">
		<th colspan="$werkingen_amount" class="text-center">Werking
		<th rowspan="2">Totaal
	</tr>
	<tr>
		$werkingen_ths
	</tr>
</thead>
<tbody>
$werkingen_extraatjes_tbody
</tbody>
$werkingen_footer
</table>
HERE;
        echo $content;
    }

    private function printAanwezighedenZomerContent()
    {
        $werkingen = Werking::getWerkingen();
        $werkingen_amount = count($werkingen);
        $speelpleindagen = SpeelpleinDag::getSpeelpleindagen();
        $werkingen_ths = "";
        foreach ($werkingen as $w) {
            $werkingen_ths .= "<th>" . $w->getOmschrijving() . "</th>";
        }
        $werkingen_datums_tbody = "";
        $speelpleindag_index = 0;
        foreach ($speelpleindagen as $e) {
            $datum_str = "&filter%5BDatum%5D=" . $e->getDatum();
            ++$speelpleindag_index;
            $current_line = "<tr>";
            //if ($speelpleindag_index == 1) {
            //    $current_line .= "<td rowspan=\"" . count($speelpleindagen) . "\">Datum</td>";
            //}
            $current_line .= "<td>" . $e->getDatum() . "</td>";
            foreach ($werkingen as $w) {
                $filter = array();
                $filter['Datum'] = $e->getDatumForDatabase();
                $werking_id = $w->getId();
                $filter['Werking'] = $werking_id;
                $amount = Aanwezigheid::countAanwezigheden($filter);
                $current_line .= "<td><a href='index.php?page=aanwezigheden&filter%5BWerking%5D=$werking_id$datum_str'>$amount</a></td>";
            }
            $filter = array();
            $filter['Datum'] = $e->getDatumForDatabase();
            $amount = Aanwezigheid::countAanwezigheden($filter);
            $current_line .= "<td><a href='index.php?page=aanwezigheden$datum_str'>$amount</a></td>";
            $current_line .= "</tr>";
            $werkingen_datums_tbody .= $current_line;
        }
        $werkingen_footer = "<tr><th>Aanwezige kinderen";
        $sum = 0;
        foreach ($werkingen as $w) {
            $filter = array();
            $filter['Werking'] = $w->getId();
            $amount = Aanwezigheid::countAanwezigheden($filter);
            $werkingen_footer .= "<th><a href='index.php?page=aanwezigheden&filter%5BWerking%5D=" . $w->getId() . "&filter%5BDatum%5D='>" . $amount . "</a>";
            $sum += $amount;
        }
        $werkingen_footer .= "<th><a href='index.php?page=aanwezigheden'>$sum</tr>";
        $content = <<<HERE
<table class="table table-bordered">
<thead>
	<tr>
		<th rowspan="2">
		<th colspan="$werkingen_amount" class="text-center">Werking
		<th rowspan="2">Totaal
	</tr>
	<tr>
		$werkingen_ths
	</tr>
$werkingen_footer
</thead>
<tbody>
$werkingen_datums_tbody
</tbody>
</table>
HERE;
        echo $content;
    }

    public function printContent()
    {
        ?>

        <div class="row">
            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading"><strong>Aanwezigheden Vandaag</strong></div>
                    <div class="panel-body">
                        <?php $this->printAanwezighedenVandaagContent(); ?>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading"><strong>Uitstappen</strong></div>
                    <div class="panel-body">
                        <style type="text/css">
                            table#UitstapOverzicht tr :hover {
                                cursor: pointer;
                            }
                        </style>
                        <table class="table table-bordered table-hover" id="UitstapOverzicht">
                            <thead>
                            <tr>
                                <th>Datum</th>
                                <th>Beschrijving</th>
                                <th>#</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $filter = array();
                            $filter['DashboardZichtbaar'] = true;
                            $order = array();
                            $order['Datum'] = "asc";
                            $uitstappen = Uitstap::getUitstappen($filter, $order);
                            foreach ($uitstappen as $u) {
                                ?>
                                <tr>
                                    <td>
                                        <input name="Id" type="hidden" value="<?php echo $u->getId(); ?>">
                                        <?php echo $u->getDatum(); ?>
                                    </td>
                                    <td>
                                        <?php echo $u->getOmschrijving(); ?>
                                    </td>
                                    <td>
                                        <?php echo $u->getAantalDeelnemers(); ?>
                                    </td>
                                </tr>
                                <?php
                            }
                            ?>
                            </tbody>
                        </table>
                        <script>
                            function uitstap_clicked(id) {
                                window.location.href = '?page=uitstappen&UitstapId=' + id;
                            }

                            $('table#UitstapOverzicht').on("click", "tr", function () {
                                uitstap_clicked($(this).find('input[name=Id]').val());
                            });
                        </script>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading"><strong>Aanwezigheden Zomer</strong></div>
                    <div class="panel-body">
                        <?php $this->printAanwezighedenZomerContent(); ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}

?>
