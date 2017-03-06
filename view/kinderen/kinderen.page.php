<?php
require_once(dirname(__FILE__) . "/../page.php");
require_once(dirname(__FILE__) . "/../../model/werkingen/werking.class.php");

class KinderenPage extends Page
{
    public function __construct()
    {
        parent::__construct("Kinderen", "", "kinderen");
    }

    public function printContent()
    {
        ?>
        <style type="text/css">
            /*adapted from typeahead examples*/
            .twitter-typeahead .tt-query,
            .twitter-typeahead .tt-hint {
                border-radius: 8px 8px 8px 8px;
                padding: 8px 12px;
                width: 396px;
            }

            .typeahead {
                background-color: #FFFFFF;
            }

            .tt-query {
                box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset;
            }

            .tt-hint {
                color: #999999;
            }

            .tt-dropdown-menu {
                background-color: #FFFFFF;
                border: 1px solid rgba(0, 0, 0, 0.2);
                border-radius: 8px 8px 8px 8px;
                box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
                padding: 8px 0;
                width: 422px;
                overflow-y: visible;
            }

            .tt-suggestion {
                line-height: 24px;
                padding: 3px 20px;
            }

            .tt-suggestion.tt-cursor {
                background-color: #0097CF;
                color: #FFFFFF;
                cursor: pointer;
            }

            .modal-overflow .modal-body {
                overflow: visible;
            }

            .tt-suggestion p {
                margin: 0;
            }

            .modal-body {
                overflow-y: visible;
            }
        </style>
        <?php
        include(dirname(__FILE__) . "/../gezinnen/voogd.modal.php");
        include(dirname(__FILE__) . "/../gezinnen/kind.modal.php");
        ?>
        <div class="modal fade" id="uitstappenModal" tabindex="-1" role="dialog" aria-labelledby="uitstappenModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                            &times;
                        </button>
                        <h4 class="modal-title" id="uitstappenModalTitle">Uitstappen overzicht</h4>
                    </div>
                    <div class="modal-body">
                        <table id="uitstappen_tabel" class="table-bordered table-striped table">

                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">
                            Sluiten
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="verwijderKindModal" tabindex="-1" role="dialog"
             aria-labelledby="verwijderKindModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                            &times;
                        </button>
                        <h4 class="modal-title" id="verwijderKindModalTitle">Kind verwijderen</h4>
                    </div>
                    <div class="modal-body">
                        <form id="verwijderKindForm">
                            <input type="hidden" name="Id">
                        </form>
                        <p>
                            Bent u zeker dat u <span id="verwijderKindNaam"></span> wilt verwijderen?
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">
                            Annuleren
                        </button>
                        <button type="button" class="btn btn-primary" id="btnVerwijderKind">
                            Verwijderen
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="financieelModal" tabindex="-1" role="dialog" aria-labelledby="financieelModal">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                            &times;
                        </button>
                        <h4 class="modal-title" id="financieelModalTitle">Financieel overzicht</h4>
                    </div>
                    <div class="modal-body">
                        <form class="form-horizontal">
                            <div class="form-group">
                                <label class="control-label col-sm-2">Voogd: </label>

                                <div class="col-sm-10">
                                    <select class="form-control" name="financieelKindVoogd"></select>
                                </div>
                            </div>
                            <div class="form-group" id="divFinancieelTable">
                                <label class="control-label col-sm-2">Overzicht: </label>

                                <div class="col-sm-10">
                                    <table id="financieelTable" class="table table-bordered table-condensed"></table>
                                </div>
                            </div>
                            <div class="form-group" id="divFinancieelNetto">
                                <label class="control-label col-sm-2">Saldo: </label>

                                <div class="col-sm-10">€ <span id="financieelNettoBedrag"></span></div>
                            </div>
                            <div class="form-group" id="divFinancieelBetaling">
                                <label class="col-sm-2"></label>

                                <div class="col-sm-10">
                                    <button id="btnBetaling" class="btn">Betaling invoeren</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">
                            Sluiten
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <style type="text/css">
            tr.betaling {
                background-color: green;
            }

            tr.vordering {
                background-color: red;
            }
        </style>
        <div class="modal fade" id="verwijderVorderingBetalingModal" tabindex="-1" role="dialog"
             aria-labelledby="verwijderVorderingBetalingModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                            &times;
                        </button>
                        <h4 class="modal-title" id="verwijderVorderingBetalingModalTitle">Verwijderen</h4>
                    </div>
                    <div class="modal-body">
                        Bent u zeker dat u deze betaling/vordering wilt verwijderen?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">
                            Sluiten
                        </button>
                        <button type="button" class="btn btn-primary" id="btnVerwijderVorderingBetaling">Verwijderen
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="betalingModal" tabindex="-1" role="dialog" aria-labelledby="betalingModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                            &times;
                        </button>
                        <h4 class="modal-title" id="betalingModalTitle">Betaling</h4>
                    </div>
                    <div class="modal-body">
                        <form id="betalingForm" class="form-horizontal">
                            <input type="hidden" name="Id">

                            <div class="form-group">
                                <label for="Bedrag" class="control-label col-sm-2">Bedrag: </label>

                                <div class="col-sm-10">
                                    <input class="form-control" type="text" name="Bedrag">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="Datum" class="control-label col-sm-2">Datum: </label>

                                <div class="col-sm-10">
                                    <input class="form-control" type="text" name="Datum">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="Opmerking" class="control-label col-sm-2">Opmerking: </label>

                                <div class="col-sm-10">
                                    <textarea class="form-control" name="Opmerking"></textarea>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">
                            Sluiten
                        </button>
                        <button type="button" class="btn btn-primary" id="btnBetalingOpslaan">Opslaan</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="vorderingModal" tabindex="-1" role="dialog" aria-labelledby="vorderingModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                            &times;
                        </button>
                        <h4 class="modal-title" id="vorderingModalTitle">Vordering</h4>
                    </div>
                    <div class="modal-body">
                        <form id="vorderingForm" class="form-horizontal">
                            <input type="hidden" name="Id">
                            <input type="hidden" name="Aanwezigheid">

                            <div class="form-group">
                                <label for="Bedrag" class="control-label col-sm-2">Bedrag: </label>

                                <div class="col-sm-10">
                                    <input class="form-control" type="text" name="Bedrag">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="Datum" class="control-label col-sm-2">Datum: </label>

                                <div class="col-sm-10">
                                    <input class="form-control disabled" type="text" name="Datum">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="Opmerking" class="control-label col-sm-2">Opmerking: </label>

                                <div class="col-sm-10">
                                    <textarea class="form-control" name="Opmerking"></textarea>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">
                            Sluiten
                        </button>
                        <button type="button" class="btn btn-primary" id="btnVorderingOpslaan">Opslaan</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="voogdModal" tabindex="-1" role="dialog" aria-labelledby="voogdModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                            &times;
                        </button>
                        <h4 class="modal-title" id="kindToevoegenModalTitle">Voogd toevoegen</h4>
                    </div>
                    <div class="modal-body">
                        <form id="voogdForm" class="form-horizontal">
                            <input type="hidden" name="Add" value="0">
                            <div class="form-group" id="voogdIdDiv">
                                <label class="control-label col-sm-2" for="VoogdId">Voogd ID: </label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control disabled" readonly name="VoogdId" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-2" for="Voornaam">Voornaam: </label>

                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="Voornaam"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-2" for="Naam">Naam: </label>

                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="Naam" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-2" for="Telefoon">Telefoon: </label>

                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="Telefoon"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-2" for="Opmerkingen">Opmerkingen: </label>

                                <div class="col-sm-10">
                                    <textarea class="form-control" name="Opmerkingen"></textarea>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">
                            Annuleren
                        </button>
                        <button type="button" class="btn btn-primary" id="btnVoogd">
                            Opslaan
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="kindModal" tabindex="-1" role="dialog" aria-labelledby="kindModal">
            <div class="modal-dialog" style="overflow:visible;">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                            &times;
                        </button>
                        <h4 class="modal-title" id="kindModalTitle">Nieuw kind toevoegen</h4>
                    </div>
                    <div class="modal-body">
                        <form class="form-horizontal" id="kindForm">
                            <input type="hidden" name="Id" value="0">

                            <div class="form-group">
                                <label class="control-label col-sm-2" for="Voornaam">Voornaam: </label>

                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="Voornaam" value="">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="Naam" class="control-label col-sm-2">Naam: </label>

                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="Naam" value="">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-2" for="Geboortejaar">Geboortejaar: </label>

                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="Geboortejaar" value="">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-2" for="DefaultWerking">Werking*: </label>

                                <div class="col-sm-10">
                                    <select name="DefaultWerking" class="form-control"></select>
                                    <i>*Deze werking is de standaardinstelling bij de aanwezigheden</i>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-2" for="Belangrijk">Belangrijk: </label>

                                <div class="col-sm-10">
                                    <textarea class="form-control" name="Belangrijk"></textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-2" for="Voogden">Voogd(en): </label>

                                <div class="col-sm-10">
                                    <input type="text" class="form-control" style="width:100%;"
                                           placeholder="Bestaande voogd" name="VoogdQuery">
                                    <button id="btnNieuweVoogd" class="btn btn-default" data-toggle="modal"
                                            href="#voogdModal">
                                        Nieuwe voogd toevoegen
                                    </button>
                                    <ul id="lstVoogden"></ul>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">
                            Sluiten
                        </button>
                        <button type="button" class="btn btn-primary" id="submitKind">
                            Opslaan
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="pdfModal" tabindex="-1" role="dialog" aria-labelledby="pdfModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                            &times;
                        </button>
                        <h4 class="modal-title" id="pdfModalTitle">PDF genereren</h4>
                    </div>
                    <div class="modal-body">
                        Welke kolommen wilt u afdrukken?
                        <div class="row">
                            <div class="col-md-6">
                                Weergeven
                                <ul id="pdfSelectedFields" class="pdfFields"></ul>
                            </div>
                            <div class="col-md-6">
                                Verbergen
                                <ul id="pdfUnselectedFields" class="pdfFields"></ul>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">
                            Annuleren
                        </button>
                        <button type="button" class="btn btn-primary" id="btnPDF">
                            PDF genereren
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <button class="btn btn-large btn-primary" id="btnNieuwKind">
                Nieuw kind
            </button>
            <div class="pull-right">
                <button id="btnPDFModal" class="btn">
                    Pdf tonen
                </button>
            </div>
        </div>
        <br>
        <div class="row">
            <table class="table table-striped table-bordered table-condensed" id="kinderen_tabel"></table>
        </div>

        <script>
        </script>
        <script>
            require(['single_page_tabel', 'tabel/kolom', 'tabel/control', 'tabel/controls_kolom', 'tabel/filter_rij', 'tabel/filter_veld', 'tabel/row_click_listener', 'validator'], function (SinglePageTabel, Kolom, Control, ControlsKolom, FilterRij, FilterVeld, RowClickListener, Validator, require) {


                $(document).ready(function () {
                    $("body").keypress(function (e) {
                        if (!$(e.target).is("input, textarea") && !$("body").hasClass("modal-open")) {
                            if (e.keyCode == 110) {//N: Nieuw kind
                                nieuw_kind();
                            }
                        }
                    });
                });

                var uitstappen_tabel = null;

                function laad_kind_uitstappen(kind_data) {
                    var id = kind_data.Id;
                    $('#uitstappenModal').modal('show');
                    var k = new Array();
                    k.push(new Kolom('Datum', 'Datum', null, false));
                    k.push(new Kolom('Omschrijving', 'Omschrijving', null, false));

                    uitstappen_tabel = new SinglePageTabel('index.php?action=data&data=kindUitstappen&KindId=' + id, k);
                    uitstappen_tabel.setUp($('#uitstappen_tabel'));
                    uitstappen_tabel.laadTabel();
                }

                function edit_voogd(id) {
                    var data = new Object();
                    data.VoogdId = id;
                    $.get('index.php?action=data&data=voogdInfo', data, function (resp) {
                        resp = JSON.parse(resp);

                        $('#voogdForm div#voogdIdDiv').css("display", "block");
                        $('#voogdForm input[name=VoogdId]').val(resp.Id);
                        $('#voogdForm input[name=Naam]').val(resp.Naam);
                        $('#voogdForm input[name=Voornaam]').val(resp.Voornaam);
                        $('#voogdForm input[name=Telefoon]').val(resp.Telefoon);
                        $('#voogdForm textarea[name=Opmerkingen]').val(resp.Opmerkingen);
                        $('#voogdForm input[name=Add]').val('0');
                        $('#voogdModal').modal('show');
                    });
                }

                function laad_voogd(element, id) {
                    element.empty();
                    var data = new Object();
                    data.VoogdId = id;
                    $.get('index.php?action=data&data=voogdInfo', data, function (resp) {
                        resp = JSON.parse(resp);
                        element.append($('<input>').attr({
                            'type': 'hidden',
                            'name': 'Id'
                        }).val(resp.Id))
                            .append($('<span>').text(resp.Id))
                            .append($('<span>').html("&nbsp;"))
                            .append($('<span>').text(resp.Voornaam + " " + resp.Naam))
                            .attr('title', resp.Opmerkingen)
                            .append('&nbsp;')
                            .append($('<button>').addClass('btn btn-xs').text('edit').click(function () {
                                edit_voogd(id);
                                return false;
                            }))
                            .append('&nbsp;')
                            .append($('<button>').addClass('btn btn-xs').text('remove').click(function () {
                                element.remove();
                                return false;
                            }));
                    });
                }

                function voeg_voogd_toe(id) {
                    var el = $('<li>');
                    $('#lstVoogden').append(el);
                    laad_voogd(el, id);
                };

                function update_voogd(id) {
                    $('#lstVoogden li').each(function (index, value) {
                        if ($(this).find('input[name=Id]').val() == id) {
                            laad_voogd($(this), id);
                        }
                    });
                }

                function add_voogd_element(data) {
                    var el = $('<li>').append($('<input>').attr({
                        'name': 'Id',
                        'type': 'hidden'
                    }).val(-1)).append($('<input>').attr({
                        'name': 'Voornaam',
                        'placeholder': 'Voornaam',
                        'type': 'text'
                    }).css('width', '30%')).append($('<input>').attr({
                        'name': 'Naam',
                        'placeholder': 'Naam',
                        'type': 'text'
                    }).css('width', '30%')).append($('<textarea>').attr({
                        'name': 'Opmerkingen',
                        'placeholder': 'Opmerkingen'
                    }).css('width', '30%')).append($('<button>').text('x').click(function () {
                        $(this).parent().remove();
                    }));
                    if (data != null) {
                        el.find('input[name=Id]').val(data.VoogdId);
                        el.find('input[name=Naam]').val(data.Naam);
                        el.find('input[name=Voornaam]').val(data.Voornaam);
                        el.find('textarea[name=Opmerkingen]').val(data.Opmerkingen);
                    }
                    $('#lstVoogden').append(el);
                }

                $('#btnVoogd').click(function () {
                    var data = new Object();
                    data.Id = $('#voogdForm input[name=VoogdId]').val();
                    data.Naam = $('#voogdForm input[name=Naam]').val();
                    data.Voornaam = $('#voogdForm input[name=Voornaam]').val();
                    data.Opmerkingen = $('#voogdForm textarea[name=Opmerkingen]').val();
                    data.Telefoon = $('#voogdForm input[name=Telefoon]').val();
                    if ($.get('index.php?action=data&data=voogdExists', data, function (resp) {
                            //var good = true;
                            if (resp.exists) {
                                //good = window.confirm('Er bestaat al een voogd met dezelfde naam en voornaam. Bent u zeker dat het over een andere voogd gaat? Indien u denkt dat het over dezelfde voogd gaat, klik dan op "Annuleren" en voeg de voogd toe via "Bestaande voogd toevoegen".');
                                window.alert("Er bestaat al een voogd met dezelfde naam en voornaam. Gelieve een andere naam en/of voornaam in te vullen (eventueel met extra informatie tussen haakjes)");
                            }else{
                            //if (good) {
                                $.post('index.php?action=updateVoogd', data, function (resp) {
                                    try {
                                        resp = JSON.parse(resp);
                                        if ($('#voogdForm input[name=Add]').val() == '1') {
                                            voeg_voogd_toe(resp.Id);
                                        } else {
                                            update_voogd(resp.Id);
                                        }
                                    } catch (err) {
                                        console.log("error updating voogd: " + resp);
                                    }
                                    $('#voogdModal').modal('hide');
                                });
                            }
                        }, "json"));

                });
                var kinderen_tabel;
                var suggesties = new Bloodhound({
                    datumTokenizer: function (d) {
                        return Bloodhound.tokenizers.whitespace(d.value);
                    },
                    queryTokenizer: Bloodhound.tokenizers.whitespace,
                    remote: {
                        url: 'index.php?action=data&data=voogdenSuggesties',
                        replace: function (url, query) {
                            var data = new Object();
                            data.query = query;
                            data.current_voogden = new Array();
                            $('ul#lstVoogden li').each(function () {
                                data.current_voogden.push($(this).find('input[name="Id"]').val());
                            });
                            var target_url = url + "&" + $.param(data);
                            return target_url;
                        },
                        filter: function (kind) {
                            return $.map(kind.content, function (v) {
                                return {
                                    'display_value': ("<b>" + v.Voornaam + " " + v.Naam + "</b> " + v.Kinderen),
                                    'id': v.Id
                                };
                            });
                        }
                    }
                });
                suggesties.initialize();
                $('input[name="VoogdQuery"]').typeahead(null, {
                    displayKey: 'display_value',
                    source: suggesties.ttAdapter()
                }).bind('typeahead:selected', function (obj, voogd, dataset_name) {
                    $('#kindForm input[name=VoogdQuery]').typeahead('val', '');
                    voeg_voogd_toe(voogd.id);
                });
                $('#kindForm .tt-hint').addClass('form-control');
                var werking_jaren = <?php
                echo json_encode(Werking::getJSONWerkingen());
                ?>;
                $('#kindForm input[name=Geboortejaar]').change(function () {
                    if ($('#kindForm select[name=DefaultWerking]').val() == "0") {
                        var jaar = parseInt($('#kindForm input[name=Geboortejaar]').val());
                        for (var i = 0; i < werking_jaren.length; ++i) {
                            if (jaar >= werking_jaren[i].Beginjaar && jaar <= werking_jaren[i].Eindjaar) {
                                $('#kindForm select[name=DefaultWerking]').val(werking_jaren[i].Id);
                                break;
                            }
                        }
                    }
                });
                function clear_kind_form() {
                    $('#kindForm #lstVoogden').empty();
                    $('#kindForm input').val('');
                    $('#kindForm input[name=VoogdQuery]').typeahead('val', '');
                    $('#kindForm').find('input[type=text], textarea').val('');
                    $('#kindForm').find('select').val('0');
                    $('#kindForm input[name=Id]').val('0');
                }

                function wijzig_kind(data) {
                    $('#kindModalTitle').text('Kind wijzigen');
                    clear_kind_form();
                    $('.voogd_row').remove();
                    $('#kindForm input[name=Id]').val(data.Id);
                    $('#kindForm input[name=Voornaam]').val(data.Voornaam);
                    $('#kindForm input[name=Naam]').val(data.Naam);
                    $('#kindForm input[name=Geboortejaar]').val(data.Geboortejaar);
                    $('#kindForm select[name=DefaultWerking]').val(data.DefaultWerking);
                    $('#kindForm textarea[name=Belangrijk]').val(data.Belangrijk);
                    $('#kindForm #lstVoogden').empty();
                    for (var i = 0; i < data.VoogdenIds.length; ++i) {
                        voeg_voogd_toe(data.VoogdenIds[i]);
                    }
                    $('#kindModal').modal('show');
                };
                function verwijder_kind(data) {
                    $('#verwijderKindModal input[name=Id]').val(data.Id);
                    $('#verwijderKindModal span#verwijderKindNaam').text(data.Voornaam + " " + data.Naam);
                    $('#verwijderKindModal').modal('show');
                };
                $('#kindModal').on('shown', function () {
                    $('form#kindForm input[name=Voornaam]').focus();
                });
                function nieuw_kind() {
                    $('#kindModalTitle').text('Nieuw kind toevoegen');
                    clear_kind_form();
                    $('#kindModal').modal('show');
                    $('input[name=Voornaam]').focus();
                };

                $('#btnNieuwKind').click(function () {
                    nieuw_kind();
                });
                function kind_form_error(msg) {
                    alert(msg);
                };
                $('#kindForm').submit(function () {
                    var data = new Object();
                    data.Id = $('#kindForm input[name=Id]').val();
                    data.Voornaam = $('#kindForm input[name=Voornaam]').val();
                    data.Naam = $('#kindForm input[name=Naam]').val();
                    data.Geboortejaar = $('#kindForm input[name=Geboortejaar]').val();
                    data.DefaultWerking = $('#kindForm select[name=DefaultWerking]').val();
                    data.Belangrijk = $('#kindForm textarea[name=Belangrijk]').val();
                    data.VoogdIds = new Array();
                    $('#lstVoogden li').each(function () {
                        data.VoogdIds.push($(this).find("input[name=Id]").val());
                    });
                    if (!Validator.isNonZeroInteger(data.DefaultWerking)) {
                        kind_form_error("Kies een geldige werking");
                        return false;
                    }
                    if (Validator.isEmpty(data.Naam) || Validator.isEmpty(data.Voornaam)) {
                        kind_form_error("Vul de naam en voornaam in");
                        return false;
                    }
                    if (data.VoogdIds.length == 0) {
                    }
                    if (!Validator.isGoodYear(data.Geboortejaar)) {
                        kind_form_error("Vul een geldig geboortejaar in");
                        return false;
                    }
                    var save = function () {
                        $.post('index.php?action=updateKind', data, function (res) {
                            res = $.trim(res);
                            if (res == "0") {
                                alert("Kind update is niet (helemaal) gelukt. Mogelijk probeerde u een voogd te verwijderen die reeds gekoppeld was aan een aanwezigheid.");
                                console.log("kind update mislukt, error code: '" + res + "'");
                            }
                            $('#kindModal').modal('hide');
                            kinderen_tabel.laadTabel();
                        });
                    };
                    if (data.Id != 0) {
                        save();
                    } else {
                        $.get('index.php?action=data&data=kindExists', data, function (resp) {
                            var good = true;
                            if (resp.exists) {
                                good = window.confirm('Er bestaat al een kind met dezelfde naam en voornaam. Bent u zeker dat het over een ander kind gaat? Klik op "OK" om dit kind toch te registreren. Klik op "Annuleren" om dit kind niet op te slaan.');
                            }
                            if (good) {
                                save();
                            } else {

                            }
                        }, "json");
                    }
                    return false;
                });
                $('#submitKind').click(function () {
                    $('#kindForm').submit();
                });
                $('#btnVerwijderKind').click(function () {
                    $.post('index.php?action=removeKind', $('#verwijderKindForm').serialize(), function (res) {
                        if (res.Ok) {
                            $('#verwijderKindModal').modal('hide');
                            kinderen_tabel.laadTabel();
                        } else {
                            alert("Kind verwijderen mislukt. Controleer dat er geen geassocieerde Voogden of Uitstappen zijn.");
                        }
                    }, "json");
                });
                var pdf_fields = new Array('Geboortejaar', 'Belangrijk', 'Werking', 'Aanwezigheden');
                var pdf_fields_default = new Array('Voornaam', 'Naam');
                $('#btnPDFModal').click(function () {
                    $('#pdfSelectedFields').empty().unbind('sortupdate');
                    $('#pdfUnselectedFields').empty().unbind('sortupdate');
                    for (var i = 0; i < pdf_fields.length; ++i) {
                        $('#pdfUnselectedFields').append($('<li>').text(pdf_fields[i]).attr('draggable', 'true'));
                    }
                    $('#pdfSelectedFields').append($('<li>').text('Nummer').addClass('disabled'));
                    for (var i = 0; i < pdf_fields_default.length; ++i) {
                        $('#pdfSelectedFields').append($('<li>').text(pdf_fields_default[i]).attr('draggable', 'true'));
                    }
                    $('#pdfSelectedFields, #pdfUnselectedFields').sortable({
                        connectWith: '.pdfFields',
                        items: ':not(.disabled)'
                    });
                    $('#pdfModal').modal('show');
                });
                $('#btnPDF').click(function () {
                    var data = new Object();
                    data.kolommen = new Array();
                    $('#pdfSelectedFields li').each(function (index, value) {
                        data.kolommen.push($(this).text());
                    });
                    data.action = "data";
                    data.data = "kinderenPDF";
                    data.filter = kinderen_tabel.getFilter();
                    data.order = kinderen_tabel.getSort();
                    window.open('index.php?' + $.param(data));
                    $('#pdfModal').modal('hide');

                });
                $('#btnNieuweVoogd').click(function () {
                    $('#voogdForm input').val('');
                    $('#voogdForm div#voogdIdDiv').css("display", "none");
                    $('#voogdForm textarea').val('');
                    $('#voogdForm input[name=Add]').val('1');
                    $('#voogdForm input[name=Id]').val('0');
                });
                var js_array = new Array();
                $(document).ready(function () {
                    $.get('index.php?action=data&data=werkingenTabel', null, function (resp) {
                        var s = $('select[name=DefaultWerking]').empty();
                        js_array = new Array();
                        var alle_obj = new Object();
                        alle_obj.value = "";
                        alle_obj.label = "Alle";
                        js_array.push(alle_obj);
                        s.append($('<option>').val("0").text("-"));
                        for (var i = 0; i < resp.content.length; ++i) {
                            s.append($('<option>').val(resp.content[i].Id).text(resp.content[i].Afkorting + " - " + resp.content[i].Omschrijving));
                            var obj = new Object();
                            obj.value = resp.content[i].Id;
                            obj.label = resp.content[i].Afkorting;
                            js_array.push(obj);
                        }
                        laad_tabel();
                    }, "json");
                });
                function laad_tabel() {
                    var k = new Array();
                    k.push(new Kolom('Voornaam', 'Voornaam', null, true));
                    k.push(new Kolom('Naam', 'Naam', null, true));
                    k.push(new Kolom('Geboortejaar', 'Geboortejaar', null, true));
                    k.push(new Kolom('Werking', 'Werking'));
                    k.push(new Kolom('Info', 'Extra Info', function (data) {
                        var td = $('<td>');
                        if (data['Belangrijk']) {
                            td.append($('<a>').attr({
                                'data-original-title': data['Belangrijk']
                            }).append($('<span>').addClass('glyphicon glyphicon-info-sign')).tooltip()).append('&nbsp;');
                        }
                        if (data['VoogdenIds'].length > 1) {
                            var voogden_string = "Meerdere voogden";
                            td.append($('<a>').attr({
                                'data-original-title': voogden_string
                            }).append($('<span>').addClass('glyphicon glyphicon-home')).tooltip()).append('&nbsp;');
                        }
                        if (data['Schulden']) {
                            td.append($('<a>').attr({
                                'data-original-title': 'Schulden!'
                            }).append($('<span>').addClass('glyphicon glyphicon-euro')).tooltip().click(function () {
                                laad_financien_modal(data);
                                return false;
                            })).append('&nbsp;');
                        }

                        var aanwezigheden_string = "";
                        for (var i = 0; i < data['Aanwezigheden'].length; ++i) {
                            aanwezigheden_string += "Voogd '" + data['Aanwezigheden'][i]['Voogd'] + "': " + data['Aanwezigheden'][i]['Aanwezigheden'] + " dagen aanwezig.\n";
                        }
                        td.append($('<a>').attr({
                            'data-original-title': aanwezigheden_string
                        }).append($('<span>').addClass('glyphicon glyphicon-envelope')).tooltip()).append('&nbsp;');
                        return td;
                    }));
                    var controls = new Array();
                    controls.push(new Control('Uitstappen', 'btn btn-xs', laad_kind_uitstappen));
                    controls.push(new Control('Wijzigen', 'btn btn-xs', wijzig_kind));
                    controls.push(new Control('Verwijderen', 'btn btn-xs', verwijder_kind));
                    k.push(new ControlsKolom(controls));
                    kinderen_tabel = new SinglePageTabel('index.php?action=data&data=kinderenTabel', k);
                    var filter_velden = new Array();
                    filter_velden.push(new FilterVeld('VolledigeNaam', 2, 'text', null));
                    filter_velden.push(new FilterVeld('Geboortejaar', 1, 'text', null));
                    filter_velden.push(new FilterVeld('Werking', 1, 'select', {
                        options: js_array
                    }));
                    var andere_filters = new Array();
                    var alle = new Object();
                    alle.label = "Alle";
                    alle.value = "0";
                    var schulden = new Object();
                    schulden.label = "Schulden";
                    schulden.value = "Schulden";
                    var belangrijk = new Object();
                    belangrijk.label = "Belangrijke info";
                    belangrijk.value = "Belangrijk";
                    andere_filters.push(alle);
                    andere_filters.push(schulden);
                    andere_filters.push(belangrijk);
                    filter_velden.push(new FilterVeld('Andere', 1, 'select', {
                        options: andere_filters
                    }));
                    kinderen_tabel.setFilterRij(new FilterRij(filter_velden, kinderen_tabel));
                    kinderen_tabel.setRowClickListener(new RowClickListener(function (rij) {
                            var data = rij.getData();
                            laad_kind_overzicht(data.Id);
                        }
                    ));
                    kinderen_tabel.setUp($('#kinderen_tabel'));
                    kinderen_tabel.laadTabel();
                }

                function empty_vordering() {
                    $('#vorderingForm input').val('');
                    $('#vorderingForm textarea').val('');
                }

                function empty_betaling() {
                    $('#betalingForm input').val('');
                    $('#betalingForm textarea').val('');
                }
                $("#betalingModal").on('shown.bs.modal', function(){
                    $('#betalingModal input[name=Bedrag]').focus();
                });
                $("#vorderingModal").on('shown.bs.modal', function(){
                    $('#vorderingModal input[name=Bedrag]').focus();
                });
                $('#btnBetaling').show().click(function () {
                    empty_betaling();
                    $('#betalingModal').modal('show');
                    return false;
                });
                $('#btnBetalingOpslaan').click(function () {
                    $('#betalingForm').submit();
                    return false;
                });
                $('#btnVorderingOpslaan').click(function () {
                    $('#vorderingForm').submit();
                    return false;
                });
                $('#betalingForm input[name="Datum"]').datepicker({'format': 'yyyy-mm-dd', todayBtn:"linked", language:"nl-BE"}).on('changeDate', function () {
                    $('#betalingForm input[name="Datum"]').datepicker('hide');
                });
                function empty_saldo_details() {
                    $('#financieelTable').empty();
                    $('#divFinancieelTable').hide();
                    $('#divFinancieelBetaling').hide();
                    $('#divFinancieelNetto').hide();
                }

                function wijzig_vordering(vordering_data) {
                    switch (vordering_data.Type) {
                        case 'vordering':
                            empty_vordering();
                            $('#vorderingModal').modal('show');
                            $('#vorderingForm input[name=Id]').val(vordering_data.Id);
                            $('#vorderingForm input[name=Bedrag]').val(-vordering_data.Bedrag);
                            $('#vorderingForm textarea[name=Opmerking]').val(vordering_data.Opmerking);
                            $('#vorderingForm input[name=Datum]').val(vordering_data.Datum);
                            $('#vorderingForm input[name=Aanwezigheid]').val(vordering_data.Aanwezigheid);
                            break;
                        case 'betaling':
                            empty_betaling();
                            $('#betalingModal').modal('show');
                            $('#betalingForm input[name=Id]').val(vordering_data.Id);
                            $('#betalingForm input[name=Bedrag]').val(vordering_data.Bedrag);
                            $('#betalingForm textarea[name=Opmerking]').val(vordering_data.Opmerking);
                            $('#betalingForm input[name=Datum]').val(vordering_data.Datum);
                            break;
                    }

                }

                var transacties_tabel;

                function saldo_updated(kind_voogd_id) {
                    laad_saldo_details(kind_voogd_id);
                    kinderen_tabel.updateBody();
                }

                function laad_saldo_details(kind_voogd_id) {
                    function verwijder_vordering_betaling(vordering_data) {
                        $('#verwijderVorderingBetalingModal').modal('show');
                        $('#btnVerwijderVorderingBetaling').unbind('click').click(function () {
                            var data = new Object();
                            data.Id = vordering_data.Id;
                            switch (vordering_data.Type) {
                                case 'betaling':
                                    $.get('index.php?action=removeBetaling', data, function (resp) {
                                        saldo_updated(kind_voogd_id);
                                        $('#verwijderVorderingBetalingModal').modal('hide');
                                    }, "json");
                                    break;
                                case 'vordering':
                                    $.get('index.php?action=removeVordering', data, function (data) {
                                        saldo_updated(kind_voogd_id);
                                        $('#verwijderVorderingBetalingModal').modal('hide');
                                    }, "json");
                                    break;
                            }
                            return false;
                        });
                    }

                    empty_saldo_details();
                    var data = new Object();
                    data.KindVoogdId = kind_voogd_id;
                    $.get('index.php?action=data&data=kindVoogdSaldo', data, function (resp) {
                        $('#financieelNettoBedrag').text(resp.Saldo);
                    }, "json");

                    $('#divFinancieelNetto').show();
                    $('#divFinancieelBetaling').show();
                    $('#divFinancieelTable').show();
                    var saldo_kolommen = new Array();
                    saldo_kolommen.push(new Kolom('Datum', 'Datum'));
                    saldo_kolommen.push(new Kolom('Bedrag', 'Bedrag'));
                    saldo_kolommen.push(new Kolom('Opmerking', 'Opmerking'));
                    var controls = new Array();
                    controls.push(new Control('Wijzigen', 'btn btn-xs', wijzig_vordering));
                    controls.push(new Control('Verwijderen', 'btn btn-xs', verwijder_vordering_betaling));
                    saldo_kolommen.push(new ControlsKolom(controls));
                    transacties_tabel = new SinglePageTabel('index.php?action=data&data=saldoTabel&KindVoogdId=' + parseInt(kind_voogd_id), saldo_kolommen);
                    transacties_tabel.setUp($('#financieelTable'));
                    transacties_tabel.setRijStyler(function (tr, data) {
                        switch (data.Type) {
                            case 'betaling':
                                tr.addClass('betaling')
                                break;
                            case 'vordering':
                                tr.addClass('vordering');
                                break;
                        }
                    });
                    transacties_tabel.laadTabel();
                    function betaling_form_error(msg) {
                        alert(msg);
                    };
                    $('#betalingForm').unbind('submit').submit(function () {
                        var data = new Object();
                        data.Id = $('#betalingForm input[name=Id]').val();
                        data.Bedrag = $('#betalingForm input[name=Bedrag]').val();
                        data.Opmerking = $('#betalingForm textarea[name=Opmerking]').val();
                        data.Datum = $('#betalingForm input[name=Datum]').val();
                        data.KindVoogd = kind_voogd_id;
                        data.Bedrag = Validator.fixDecimalPoint(data.Bedrag);
                        if (!Validator.isPositivePayment(data.Bedrag)) {
                            betaling_form_error("Vul een geldig positief bedrag in (max 2 cijfers na decimale punt).");
                            return false;
                        }
                        if (!Validator.isGoodDate(data.Datum)) {
                            betaling_form_error("Kies een geldige datum.");
                            return false;
                        }
                        $.get('index.php?action=updateBetaling', data, function (resp) {
                            if (resp.ok) {
                                saldo_updated(kind_voogd_id);
                                $('#betalingModal').modal('hide');
                            } else {
                                betaling_form_error("Bijwerken is mislukt!");
                            }
                        }, "json");
                        return false;
                    });
                    function vordering_form_error(msg) {
                        alert(msg);
                    };
                    $('#vorderingForm').unbind('submit').submit(function () {
                        var data = new Object();
                        data.Id = $('#vorderingForm input[name=Id]').val();
                        data.Bedrag = $('#vorderingForm input[name=Bedrag]').val();
                        data.Opmerking = $('#vorderingForm textarea[name=Opmerking]').val();
                        data.Aanwezigheid = $('#vorderingForm input[name=Aanwezigheid]').val();
                        if (!Validator.isPositivePayment(data.Bedrag)) {
                            vordering_form_error("Vul een geldig positief bedrag in (max 2 cijfers na decimale punt).");
                            return false;
                        }
                        $.get('index.php?action=updateVordering', data, function (resp) {
                            if (resp.ok) {
                                saldo_updated(kind_voogd_id);
                                $('#vorderingModal').modal('hide');
                            } else {
                                vordering_form_error("Bijwerken is mislukt!");
                            }
                        }, "json");
                        return false;
                    });
                }

                function laad_financien_modal(kind) {
                    $('select[name=financieelKindVoogd]').empty().append($('<option>').val(0).text('-'));
                    empty_saldo_details();
                    var data = new Object();
                    data.KindId = kind.Id;
                    $('select[name=financieelKindVoogd]').unbind('change').change(function () {
                        if ($('select[name=financieelKindVoogd]').val() == '0') {
                            empty_saldo_details();
                        } else {
                            laad_saldo_details($('select[name=financieelKindVoogd]').val());
                        }
                    });
                    $.get('index.php?action=data&data=kindVoogden', data, function (res) {
                        for (var i = 0; i < res.content.length; ++i) {
                            $('select[name=financieelKindVoogd]').append($('<option>').val(res.content[i].Id).text(res.content[i].Voornaam + " " + res.content[i].Naam));
                        }
                        if(res.content.length == 1){
                            $('select[name=financieelKindVoogd]').val(res.content[0].Id).change();
                        }
                    }, "json");
                    $('#financieelModal').modal('show');
                }

                $('#btnNieuwKind').focus();
                $('#kindModal, #verwijderKindModal, #pdfModal, #financieelModal').on('hidden', function () {
                    $('#btnNieuwKind').focus();
                });
                $('#voogdModal input[name=Naam], #voogdModal input[name=Voornaam]').on("change keyup", function() {
                    console.log("auto-capitalizing!");
                    var current_value = $(this).val().trim();
                    if (current_value.length == 0 || current_value.charAt(0) == current_value.charAt(0).toUpperCase()) {
                        return;
                    }
                    current_value =current_value.charAt(0).toUpperCase() + current_value.substr(1);
                    $(this).val(current_value);
                })
            });
        </script>
    <?php

    }
}
