<?php
require_once(dirname(__FILE__) . "/../page.php");

class UitstappenPage extends Page
{
    public function __construct()
    {
        parent::__construct("Uitstappen", "", "uitstappen");
    }

    public function printContent()
    {
        ?>
        <div class="modal fade" id="VerwijderUitstapModal" tabindex="-1" role="dialog"
             aria-labelledby="VerwijderUitstapModal">

            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"> &times; </button>
                        <h4 class="modal-title">Uitstap verwijderen</h4>
                    </div>
                    <div class="modal-body">
                        <form class="form-inline" id="VerwijderUitstapForm">
                            <input type="hidden" name="Id" value="0">
                            Weet u zeker dat u deze uitstap wilt verwijderen?
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Sluiten</button>
                        <button type="button" class="btn btn-primary" id="btnVerwijderUitstap">Verwijderen</button>

                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="UitstapModal" tabindex="-1" role="dialog" aria-labelledby="UitstapModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"> &times; </button>
                        <h4 class="modal-title" id="UitstapModalTitle">Nieuwe uitstap toevoegen</h4>
                    </div>
                    <div class="modal-body">
                        <form class="form-horizontal" id="UitstapForm">
                            <input type="hidden" name="Id" value="0">

                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="Datum">Datum: </label>

                                <div class="col-sm-10">
                                    <input type="text" class="form-control" value="" name="Datum">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="Omschrijving" class="col-sm-2 control-label">Omschrijving: </label>

                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="Omschrijving" value="">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="Zichtbaarheid" class="control-label col-sm-2">Zichtbaarheid: </label>

                                <div class="col-sm-10">
                                    <div class="checkbox"><label><input type="checkbox" name="AanwezigheidZichtbaar"
                                                                        checked>Zichtbaar op het aanwezigheidsformulier</label>
                                    </div>
                                    <div class="checkbox"><label><input type="checkbox" name="DashboardZichtbaar"
                                                                        checked>Zichtbaar op het dashboard</label></div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Sluiten</button>
                        <button type="button" class="btn btn-primary" id="btnUitstapOpslaan">Opslaan</button>
                    </div>
                </div>
            </div>
        </div>
        <script>
            $(document).ready(function () {
                $('form#UitstapForm input[name="Datum"]').datepicker({'format': 'yyyy-mm-dd'}).on('changeDate', function () {
                    $('form#UitstapForm input[name="Datum"]').datepicker('hide');
                });
            });
        </script>
        <style type="text/css">
            table#UitstapOverzicht tbody tr :hover {
                cursor: pointer;
            }
        </style>
        <div class="row">
            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <strong>Uitstapoverzicht</strong>
                    </div>
                    <div class="panel-body">
                        <button class="btn btn-primary" id="btnNieuweUitstap"><span
                                class="glyphicon glyphicon-plus"></span>Nieuwe uitstap
                        </button>
                        <br>
                        <table class="table table-hover table-bordered" id="UitstapOverzicht">
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <strong>Uitstapdetails</strong>
                    </div>
                    <div class="panel-body" id="UitstapDetailsDiv">
                        <div id="UitstapEigenschappenDiv" style="display:none;">
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    Omschrijving: <span id="txtOmschrijving"></span><br>
                                    Datum: <span id="txtDatum"></span><br>
                                    <button type="button" class="btn btn-primary" id="btnUitstapBewerken">Uitstap
                                        Bewerken
                                    </button>
                                    &nbsp;
                                    <button type="button" class="btn btn-default" id="btnUitstapVerwijderen">Uitstap
                                        Verwijderen
                                    </button>
                                    &nbsp;
                                    <button type="button" class="btn btn-default" id="btnPDFModal">PDF Tonen</button>
                                    <br>

                                    <form class="form-horizontal">
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label" for="VolledigeNaamKind">Kind
                                                toevoegen: </label>

                                            <div class="col-sm-10">
                                                <input type="text" value="" class="typeahead form-control"
                                                       name="VolledigeNaamKind">
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <style type="text/css">
                                /*adapted from typeahead examples*/
                                typeahead, .tt-query, .tt-hint {
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

                                .tt-suggestion p {
                                    margin: 0;
                                }
                            </style>
                        </div>
                        <div id="UitstapDeelnamesDiv">
                            <div class="text-center" width="100%">
                                <em>details van de uitstap</em>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script>
            var laad_uitstap;
            var laad_uitstap_details_placeholder;
            var init_function = function () {
            };
            require(['single_page_tabel', 'navigation_tabel', 'tabel/kolom', 'tabel/control', 'tabel/controls_kolom', 'tabel/filter_rij', 'tabel/filter_veld', 'tabel/row_click_listener', 'validator'], function (SinglePageTabel, NavigationTabel, Kolom, Control, ControlsKolom, FilterRij, FilterVeld, RowClickListener, Validator, require) {
                function voeg_kind_toe(kind_id, uitstap_id) {
                    var d = new Object();
                    d.Id = 0;
                    d.KindId = kind_id;
                    d.UitstapId = uitstap_id;
                    $.get('index.php?action=updateDeelname', d, function (res) {
                        $('input[name=VolledigeNaamKind]').val('');
                        uitstap_deelnemers_tabel.laadTabel();
                    });
                };
                function wijzig_uitstap(data) {
                    clearUitstapForm();
                    $('#UitstapModalTitle').text('Uitstap wijzigen');
                    $('#UitstapModal input[name=Datum]').val(data['Datum']).datepicker('update');
                    $('#UitstapModal input[name=Omschrijving]').val(data['Omschrijving']);
                    $('#UitstapModal input[name=Id]').val(data['Id']);
                    $('#UitstapModal input[name=DashboardZichtbaar]').prop('checked', data['DashboardZichtbaar'] == '1');
                    $('#UitstapModal input[name=AanwezigheidZichtbaar]').prop('checked', data['AanwezigheidZichtbaar'] == '1');
                    $('#UitstapModal').modal('show');
                };
                function verwijder_uitstap(data) {
                    $('#VerwijderUitstapModal input[name=Id]').val(data['Id']);
                    $('#VerwijderUitstapModal').modal('show');
                };
                function clearUitstapForm() {
                    $('#UitstapModal input[name=Omschrijving]').val('');
                    $('#UitstapModal input[name=Id]').val('0');
                }

                function nieuwe_uitstap() {
                    clearUitstapForm();
                    $('#UitstapModalTitle').text('Nieuwe uitstap');
                    $('#UitstapModal').modal('show');
                };
                var uitstap_deelnemers_tabel = null;
                laad_uitstap_details_placeholder = function () {
                    $('#UitstapEigenschappenDiv').css('display', 'none');
                    $('#UitstapDeelnamesDiv').empty()
                        .append($('<div>').addClass('text-center').css('width', '100%').html('<em>details van de uitstap</em>'));
                }
                laad_uitstap = function (data) {
                    var eigenschappen_div = $('#UitstapEigenschappenDiv').css('display', 'inline');
                    eigenschappen_div.find('#txtDatum').text(data['Datum']);
                    eigenschappen_div.find('#txtOmschrijving').text(data['Omschrijving']);
                    eigenschappen_div.find('input[name=VolledigeNaamKind]').val('');
                    eigenschappen_div.find('#btnUitstapBewerken').unbind('click').click(function () {
                        wijzig_uitstap(data);
                        return false;
                    });
                    eigenschappen_div.find('#btnUitstapVerwijderen').unbind('click').click(function () {
                        verwijder_uitstap(data);
                        return false;
                    });
                    eigenschappen_div.find('#btnPDFModal').unbind('click').click(function () {
                        var senddata = new Object();
                        senddata.Id = data['Id'];
                        window.open('index.php?action=data&data=uitstapPDF&' + $.param(senddata));
                    });
                    var suggesties = new Bloodhound({
                        datumTokenizer: function (d) {
                            return Bloodhound.tokenizers.whitespace(d.value);
                        },
                        queryTokenizer: Bloodhound.tokenizers.whitespace,
                        remote: {
                            url: 'index.php?action=data&data=kinderenSuggesties&query=%QUERY',
                            filter: function (kind) {
                                return $.map(kind.content, function (k) {
                                    return {
                                        'display_value': (k.Voornaam + " " + k.Naam),
                                        'Id': k.Id/*, 'Voogden':k.Voogden*/,
                                        'DefaultWerkingId': k.DefaultWerkingId
                                    };
                                });
                            }
                        }
                    });
                    suggesties.initialize();
                    $('input[name="VolledigeNaamKind"]').typeahead(null, {
                        displayKey: 'display_value',
                        source: suggesties.ttAdapter()
                    }).unbind('typeahead:selected').bind('typeahead:selected', function (obj, kind, dataset_name) {
                        voeg_kind_toe(kind['Id'], data['Id']);
                        $('input[name=VolledigeNaamKind]').typeahead('val', '');
                    });
                    var div = $('#UitstapDeelnamesDiv').empty();
                    var tabel_div = $('<div>');
                    div.append(tabel_div);
                    var tabel = $('<table>').addClass('table table-hover table-bordered table-condensed');
                    tabel_div.append(tabel);
                    var uitstap_deelnemers_kolommen = new Array();
                    uitstap_deelnemers_kolommen.push(new Kolom('Naam', 'Naam'));
                    uitstap_deelnemers_kolommen.push(new Kolom('Voornaam', 'Voornaam'));

                    var controls = new Array();

                    function verwijder_deelname(data) {
                        var d = new Object();
                        d.Id = data['Id'];
                        $.get('index.php?action=removeDeelname', d, function (res) {
                            uitstap_deelnemers_tabel.laadTabel();
                        });
                    };
                    controls.push(new Control('Verwijderen', 'btn btn-xs', verwijder_deelname));
                    uitstap_deelnemers_kolommen.push(new ControlsKolom(controls));
                    var id = parseInt(data['Id']);
                    uitstap_deelnemers_tabel = new SinglePageTabel('index.php?action=data&data=uitstapDeelnamesTabel&uitstap_id=' + id, uitstap_deelnemers_kolommen);
                    uitstap_deelnemers_tabel.setUp(tabel);
                    uitstap_deelnemers_tabel.laadTabel();
                };
                function uitstap_clicked(row) {
                    laad_uitstap(row.getData());
                };
                var k = new Array();
                function update_uitstap(id, datum, omschrijving, aanwezigheid_zichtbaar, dashboard_zichtbaar){
                    var data = new Object();
                    data.Id = id;
                    data.Datum = datum;
                    data.Omschrijving = omschrijving;
                    if(aanwezigheid_zichtbaar)
                        data.AanwezigheidZichtbaar = true;
                    if(dashboard_zichtbaar)
                        data.DashboardZichtbaar = true;
                    $.post('index.php?action=updateUitstap', data, function(data){
                        uitstappen_tabel.laadTabel();
                    });
                }
                k.push(new Kolom('Datum', 'Datum'));
                k.push(new Kolom('Omschrijving', 'Omschrijving'));
                k.push(new Kolom("AanwezigheidZichtbaar", "Aanwezigheid", function(data){
                    var td = $('<td>');
                    var checkbox = $('<input type="checkbox">');
                    if(data['AanwezigheidZichtbaar'] == "1"){
                        checkbox.prop("checked", true);
                    }
                    checkbox.change(function(e){
                        update_uitstap(data['Id'], data['Datum'], data['Omschrijving'], $(this).prop("checked"), data['DashboardZichtbaar']=="1");
                    });
                    td.append(checkbox);
                    return td;
                }));
                k.push(new Kolom("DashboardZichtbaar", "Dashboard", function(data){
                    var td = $('<td>');
                    var checkbox = $('<input type="checkbox">');
                    if(data['DashboardZichtbaar'] == "1"){
                        checkbox.prop("checked", true);
                    }
                    checkbox.change(function(e){
                        update_uitstap(data['Id'], data['Datum'], data['Omschrijving'], data["AanwezigheidZichtbaar"] == "1", $(this).prop("checked"));
                    });
                    td.append(checkbox);
                    return td;
                }));
                var uitstappen_tabel = new NavigationTabel('index.php?action=data&data=uitstappenTabel', k, 6);
                uitstappen_tabel.setRowClickListener(new RowClickListener(uitstap_clicked));
                uitstappen_tabel.setUp($('table#UitstapOverzicht'));
                $('#btnNieuweUitstap').click(function () {
                    nieuwe_uitstap();
                });
                $(document).ready(function () {
                    uitstappen_tabel.laadTabel();
                    init_function();
                });
                $('#btnUitstapOpslaan').click(function () {
                    $('#UitstapModal form').submit();
                });
                function uitstap_form_error(msg) {
                    alert(msg);
                };
                $('#UitstapModal form').submit(function () {
                    var id = $('#UitstapModal form input[name=Id]').val();
                    var data = new Object();
                    data.Id = id;
                    data.Datum = $('#UitstapModal form input[name=Datum]').val();
                    data.Omschrijving = $('#UitstapModal form input[name=Omschrijving]').val();
                    if ($('#UitstapModal form input[name=AanwezigheidZichtbaar]').prop('checked')) {
                        data.AanwezigheidZichtbaar = true;
                    }
                    if ($('#UitstapModal form input[name=DashboardZichtbaar]').prop('checked')) {
                        data.DashboardZichtbaar = true;
                    }
                    if (!Validator.isGoodDate(data.Datum)) {
                        uitstap_form_error("Selecteer een geldige datum");
                        return false;
                    }
                    if (Validator.isEmpty(data.Omschrijving)) {
                        uitstap_form_error("Vul een omschrijving in");
                        return false;
                    }
                    console.log("data = "+JSON.stringify(data));
                    $.post('index.php?action=updateUitstap', data, function (r) {
                        if (r.Ok == "1") {
                            var d = new Object();
                            d.Id = r.Id;
                            uitstappen_tabel.laadTabel();
                            $.post('index.php?action=data&data=uitstapDetails', d, function (res) {
                                laad_uitstap(JSON.parse(res).content);
                            });
                            $('#UitstapModal').modal('hide');
                        } else {
                            console.log("update Uitstap mislukt");
                        }
                    }, "json");
                    return false;
                });
                $('#btnVerwijderUitstap').click(function () {
                    $.post('index.php?action=removeUitstap', $('#VerwijderUitstapForm').serialize(), function (res) {
                        if (res.Ok) {
                            $('#VerwijderUitstapModal').modal('hide');
                            uitstappen_tabel.laadTabel();
                            laad_uitstap_details_placeholder();
                        } else {
                            alert("Uitstap verwijderen mislukt.");
                        }
                    }, "json");
                });
            });
        </script>
        <?php
        $content = "";
        if (isset($_REQUEST['UitstapId'])) {
            $id = $_REQUEST['UitstapId'];
            $content .= <<<HERE
<script>
init_function = function(){
	var data = new Object();
	data.Id = $id;
	$.post('?action=data&data=uitstapDetails', data, function(resp){
		laad_uitstap(JSON.parse(resp).content);
	});
};
</script>
HERE;
        } else {
            $content .= <<<HERE
<script>
init_function = function(){
		laad_uitstap_details_placeholder();};
</script>
HERE;
        }
        echo $content;
    }
}

?>
