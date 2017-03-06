<?php
require_once(dirname(__FILE__) . "/../page.php");
require_once(dirname(__FILE__) . "/../../model/werkingen/werking.class.php");

class InstellingenPage extends Page
{
    public function __construct()
    {
        parent::__construct("Instellingen", "", "instellingen");
    }

    public function printContent()
    {
        ?>
        <div class="row">
            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading"><strong>Werkingen</strong></div>
                    <div class="panel-body">
                        <div class="modal fade" id="WerkingModal" tabindex="-1" role="dialog"
                             aria-labelledby="WerkingModal">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal"
                                                aria-hidden="true"> &times; </button>
                                        <h4 class="modal-title">Nieuwe Werking toevoegen</h4>
                                    </div>
                                    <div class="modal-body">
                                        <form class="form-horizontal">
                                            <input type="hidden" name="Id">

                                            <div class="form-group">
                                                <label class="control-label col-sm-2"
                                                       for="Afkorting">Afkorting: </label>

                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control" value="" name="Afkorting">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="Omschrijving"
                                                       class="col-sm-2 control-label">Omschrijving: </label>

                                                <div class="col-sm-10">
                                                    <input type="text" value="" class="form-control"
                                                           name="Omschrijving">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="Beginjaar" class="col-sm-2 control-label">Beginjaar:</label>

                                                <div class="col-sm-10">
                                                    <input type="text" value="" class="form-control" name="Beginjaar">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="Eindjaar" class="col-sm-2 control-label">Eindjaar: </label>

                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control" value="" name="Eindjaar">
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Sluiten
                                        </button>
                                        <button type="button" class="btn btn-primary" id="btnWerkingOpslaan">Toevoegen
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal fade" id="VerwijderWerkingModal" tabindex="-1" role="dialog"
                             aria-labelledby="VerwijderWerkingModal">

                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="buton" class="close" data-dismiss="modal"
                                                aria-hidden="true">&times;</button>
                                        <h4 class="modal-title" id="VerwijderWerkingModalTitle">Werking verwijderen</h4>
                                    </div>
                                    <div class="modal-body">
                                        <form id="VerwijderWerkingForm">
                                            <input type="hidden" name="Id">
                                        </form>
                                        <p>Bent u zeker dat u deze werking wilt verwijderen?</p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Annuleren
                                        </button>
                                        <button type="button" class="btn btn-primary" id="btnVerwijderWerking">
                                            Verwijderen
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <table class="table table-striped table-bordered" id="tblWerkingen">
                        </table>
                        <button class="btn btn-large btn-primary" id="btnNieuweWerking">Nieuwe werking toevoegen
                        </button>
                        <script>
                            require(['single_page_tabel', 'tabel/kolom', 'tabel/control', 'tabel/controls_kolom', 'tabel/filter_rij', 'tabel/filter_veld'], function (SinglePageTabel, Kolom, Control, ControlsKolom, FilterRij, FilterVeld, require) {
                                function wijzig_werking(data) {
                                    clearWerkingForm();
                                    $('#WerkingModal input[name=Omschrijving]').val(data['Omschrijving']);
                                    $('#WerkingModal input[name=Afkorting]').val(data['Afkorting']);
                                    $('#WerkingModal input[name=Beginjaar]').val(data['Beginjaar']);
                                    $('#WerkingModal input[name=Eindjaar]').val(data['Eindjaar']);
                                    $('#WerkingModal input[name=Id]').val(data['Id']);
                                    $('#WerkingModal').modal('show');
                                };
                                function verwijder_werking(data) {
                                    $('#VerwijderWerkingModal input[name=Id]').val(data['Id']);
                                    $('#VerwijderWerkingModal').modal('show');
                                };
                                function clearWerkingForm() {
                                    $('#WerkingModal input[name=Omschrijving]').val('');
                                    $('#WerkingModal input[name=Afkorting]').val('');
                                    $('#WerkingModal input[name=Eindjaar]').val('');
                                    $('#WerkingModal input[name=Beginjaar]').val('');
                                    $('#WerkingModal input[name=Id]').val('0');
                                }

                                function nieuwe_werking() {
                                    clearWerkingForm();
                                    $('#WerkingModal').modal('show');
                                };
                                var k = new Array();
                                k.push(new Kolom('Omschrijving', 'Omschrijving'));
                                k.push(new Kolom('Afkorting', 'Afkorting'));
                                k.push(new Kolom('Beginjaar', 'Beginjaar'));
                                k.push(new Kolom('Eindjaar', 'Eindjaar'));
                                var controls = new Array();
                                controls.push(new Control('Wijzigen', 'btn btn-sm', wijzig_werking));
                                controls.push(new Control('Verwijderen', 'btn btn-sm', verwijder_werking));
                                k.push(new ControlsKolom(controls));
                                var werkingen_tabel = new SinglePageTabel('index.php?action=data&data=werkingenTabel', k);
                                werkingen_tabel.setUp($('table#tblWerkingen'));
                                $('#btnNieuweWerking').click(function () {
                                    nieuwe_werking();
                                });
                                $(document).ready(function () {
                                    werkingen_tabel.laadTabel();
                                });
                                $('#btnWerkingOpslaan').click(function () {
                                    $('#WerkingModal form').submit();
                                    return false;
                                });
                                $('#WerkingModal form').submit(function () {
                                    $.post('index.php?action=updateWerking', $('#WerkingModal form').serialize(), function (r) {
                                        r = $.trim(r);
                                        if (r == "1") {
                                            werkingen_tabel.laadTabel();
                                            $('#WerkingModal').modal('hide');
                                        } else {
                                            console.log("update Werking mislukt");
                                        }
                                    });
                                    return false;
                                });
                                $('#btnVerwijderWerking').click(function () {
                                    $.post('index.php?action=removeWerking', $('#VerwijderWerkingForm').serialize(), function (res) {
                                        if (res.Ok) {
                                            $('#VerwijderWerkingModal').modal('hide');
                                            werkingen_tabel.laadTabel();
                                        } else {
                                            alert("Werking verwijderen mislukt. Controleer dat er geen kinderen of aanwezigheden deze werking ingesteld hebben.");
                                        }
                                    }, "json");
                                });
                            });
                        </script>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading"><strong>Extraatjes</strong></div>
                    <div class="panel-body">
                        <div class="modal fade" id="ExtraatjeModal" tabindex="-1" role="dialog"
                             aria-labelledby="ExtraatjeModal">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal"
                                                aria-hidden="true"> &times; </button>
                                        <h4 class="modal-title">Extraatje</h4>
                                    </div>
                                    <div class="modal-body">
                                        <form class="form-horizontal">
                                            <input type="hidden" name="Id" value="0"/>

                                            <div class="form-group">
                                                <label class="control-label col-sm-2"
                                                       for="Omschrijving">Omschrijving: </label>

                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control" name="Omschrijving"
                                                           value="">
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Sluiten
                                        </button>
                                        <button type="button" class="btn btn-primary" id="btnExtraatjeOpslaan">Opslaan
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal fade" id="VerwijderExtraatjeModal" tabindex="-1" role="dialog"
                             aria-labelledby="VerwijderExtraatjeModal">

                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="buton" class="close" data-dismiss="modal"
                                                aria-hidden="true">&times;</button>
                                        <h4 class="modal-title" id="VerwijderExtraatjeModalTitle">Extraatje
                                            verwijderen</h4>
                                    </div>
                                    <div class="modal-body">
                                        <form id="VerwijderExtraatjeForm">
                                            <input type="hidden" name="Id">
                                        </form>
                                        <p>Bent u zeker dat u dit extraatje wilt verwijderen?</p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Annuleren
                                        </button>
                                        <button type="button" class="btn btn-primary" id="btnVerwijderExtraatje">
                                            Verwijderen
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <table class="table table-striped table-bordered" id="Extraatjes">
                        </table>
                        <button class="btn btn-large btn-primary" id="btnNieuwExtraatje">Nieuw extraatje toevoegen
                        </button>
                        <script>
                            require(['single_page_tabel', 'tabel/kolom', 'tabel/control', 'tabel/controls_kolom', 'tabel/filter_rij', 'tabel/filter_veld'], function (SinglePageTabel, Kolom, Control, ControlsKolom, FilterRij, FilterVeld, require) {
                                function wijzig_extraatje(data) {
                                    clearExtraatjeForm();
                                    $('#ExtraatjeModal input[name=Omschrijving]').val(data['Omschrijving']);
                                    $('#ExtraatjeModal input[name=Id]').val(data['Id']);
                                    $('#ExtraatjeModal').modal('show');
                                };
                                function verwijder_extraatje(data) {
                                    $('#VerwijderExtraatjeModal input[name=Id]').val(data['Id']);
                                    $('#VerwijderExtraatjeModal').modal('show');
                                };
                                function clearExtraatjeForm() {
                                    $('#ExtraatjeModal input[name=Omschrijving]').val('');
                                    $('#ExtraatjeModal input[name=Id]').val('0');
                                }

                                function nieuw_extraatje() {
                                    clearExtraatjeForm();
                                    $('#ExtraatjeModal').modal('show');
                                };
                                var k = new Array();
                                k.push(new Kolom('Omschrijving', 'Omschrijving'));
                                var controls = new Array();
                                controls.push(new Control('Wijzigen', 'btn btn-sm', wijzig_extraatje));
                                controls.push(new Control('Verwijderen', 'btn btn-sm', verwijder_extraatje));
                                k.push(new ControlsKolom(controls));
                                var extraatjes_tabel = new SinglePageTabel('index.php?action=data&data=extraatjesTabel', k);
                                extraatjes_tabel.setUp($('table#Extraatjes'));
                                $('#btnNieuwExtraatje').click(function () {
                                    nieuw_extraatje();
                                });
                                $(document).ready(function () {
                                    extraatjes_tabel.laadTabel();
                                });
                                $('#btnExtraatjeOpslaan').click(function () {
                                    $('#ExtraatjeModal form').submit();
                                });
                                $('#ExtraatjeModal form').submit(function () {
                                    $.post('index.php?action=updateExtraatje', $('#ExtraatjeModal form').serialize(), function (r) {
                                        r = $.trim(r);
                                        if (r == "1") {
                                            extraatjes_tabel.laadTabel();
                                            $('#ExtraatjeModal').modal('hide');
                                        } else {
                                            console.log("update Extraatje mislukt");
                                        }
                                    });
                                    return false;
                                });
                                $('#btnVerwijderExtraatje').click(function () {
                                    $.post('index.php?action=removeExtraatje', $('#VerwijderExtraatjeForm').serialize(), function (res) {
                                        if (res.Ok) {
                                            $('#VerwijderExtraatjeModal').modal('hide');
                                            extraatjes_tabel.laadTabel();
                                        } else {
                                            alert("Extraatje verwijderen mislukt. Controleer dat dit extraatje niet ingevuld is bij een aanwezigheid.")
                                        }
                                    }, "json");
                                });
                            });
                        </script>
                    </div>
                </div>
            </div>
        </div>
        <div class='row'>
            <a href='index.php?action=backup' download="<?php echo date("Ymd-His"); ?>.sql">Download backup</a>
        </div>
    <?php
    }
}
