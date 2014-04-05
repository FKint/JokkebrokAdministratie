<?php
require_once (dirname(__FILE__) . "/../page.php");
require_once (dirname(__FILE__) . "/../../model/werkingen/werking.class.php");
class KinderenPage extends Page {
    public function __construct() {
        parent::__construct("Kinderen","","kinderen");
        $this->buildContent();
    }

    private function getWerkingenSelect() {
        $opties = "";
        $werkingen = Werking::getWerkingen();
        foreach($werkingen as $w) {
            $opties .= "<option value=\"" . $w->getId() . "\">" . $w->getAfkorting() . " - " . $w->getOmschrijving() . "</option>";
        }
        $content = <<<HERE
<select name="DefaultWerking" class="form-control">
$opties
</select>
HERE;
        return $content;
    }
	private function getPDFModal(){
		$content = <<<HERE
<div class="modal fade" id="pdfModal" tabindex="-1" role="dialog" aria-labelledby="pdfModal">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="pdfModalTitle">PDF genereren</h4>
			</div>
			<div class="modal-body">
				Welke kolommen wilt u afdrukken?
				<div class="row">
				<div class="col-md-6">
				Weergeven
				<ul id="pdfSelectedFields" class="pdfFields">
				<li>a
				<li>b
				</ul>
				</div>
				<div class="col-md-6">
				Verbergen
				<ul id="pdfUnselectedFields" class="pdfFields">
				<li>c
				<li>d
				</ul>
				</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Annuleren</button>
				<button type="button" class="btn btn-primary" id="btnPDF">PDF genereren</button>
			</div>
		</div>
	</div>
</div>
HERE;
		return $content;
	}
    private function getVerwijderKindModal(){
        $content = <<<HERE
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
    overflow-y:auto;
}
.tt-suggestion {
    line-height: 24px;
    padding: 3px 20px;
}
.tt-suggestion.tt-cursor {
    background-color: #0097CF;
    color: #FFFFFF;
    cursor:pointer;
}
.tt-suggestion p {
    margin: 0;
}
            </style>
<div class="modal fade" id="verwijderKindModal" tabindex="-1" role="dialog" aria-labelledby="verwijderKindModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="buton" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="verwijderKindModalTitle">Kind verwijderen</h4>
            </div>
            <div class="modal-body">
                <form id="verwijderKindForm">
                    <input type="hidden" name="Id">
                </form>
                <p>Bent u zeker dat u dit kind wilt verwijderen?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Annuleren</button>
                <button type="button" class="btn btn-primary" id="btnVerwijderKind">Verwijderen</button>
            </div>
        </div>
    </div>
</div>
HERE;
        return $content;
    }
    private function getKindModal() {
        $werkingen_select = $this->getWerkingenSelect();
        $content = <<<HERE
<div class="modal fade" id="voogdModal" tabindex="-1" role="dialog" aria-labelledby="voogdModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="buton" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="verwijderKindModalTitle">Voogd Toevoegen</h4>
            </div>
            <div class="modal-body">
                <form id="voogdForm">
                <input type="hidden" name="Add" value="0">
                <input type="hidden" name="VoogdId">
                <div class="row">
                	<label>Voornaam: </label><input type="text" name="Voornaam">
                </div>
                <div class="row">
                	<label>Naam: </label><input type="text" name="Naam">
                </div>
                <div class="row">
                	<label>Opmerkingen: </label><textarea name="Opmerkingen"></textarea>
                </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Annuleren</button>
                <button type="button" class="btn btn-primary" id="btnVoogd">Opslaan</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="kindModal" tabindex="-1" role="dialog" aria-labelledby="kindModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"> &times; </button>
                <h4 class="modal-title" id="kindModalTitle">Nieuw kind toevoegen</h4>
            </div>
            <div class="modal-body">
                <form class="form-inline" id="kindForm">
                    <input type="hidden" name="Id" value="0">
                    <div class="row">
                        <label class="control-label" for="Voornaam">Voornaam: </label>
                        <input type="text" name="Voornaam" value="">
                    </div>
                    <div class="row">
                        <label for="Naam" class="control-label">Naam: </label>
                        <input type="text" name="Naam" value="">
                    </div>
                    <div class="row">
                        <label class="control-label" for="Geboortejaar">Geboortejaar: </label>
                        <input type="text" name="Geboortejaar" value="">
                    </div>
                    <div class="row">
                        <label class="control-label" for="DefaultWerking">Werking*: </label>
                        $werkingen_select
                    </div> 
                    <div class="row">
                        <i>*Deze werking is de standaardinstelling bij de aanwezigheden</i>
                    </div>
                    <div class="row">
                        <label class="control-label" for="Belangrijk">Belangrijk: </label>
                        <textarea name="Belangrijk"></textarea>
                    </div>
                    <div class="row">
                        <h3>Voogd:</h3>
                        <!--<input type="hidden" name="voogd_amount" value="0">-->
                    </div>
                    <div class="row">
                    <label class="control-label">Bestaande voogd toevoegen: </label><br>
                    <input type="text" class="form-control" name="VoogdQuery"><br>
                    </div>
                    <div class="row">
                    <label class="control-label">Nieuwe voogd toevoegen: </label><br>
                    <button id="btnNieuweVoogd" class="btn btn-default" data-toggle="modal" href="#voogdModal">Nieuwe voogd</button>
                    </div>
                    <div class="row">
                    <ul id="lstVoogden">
                    </ul>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Sluiten</button>
                <button type="button" class="btn btn-primary" id="submitKind">Opslaan</button>
            </div>
        </div>
    </div>
</div>
HERE;
        return $content;
    }

    public function buildContent() {
        $werkingen = Werking::getWerkingen();
        $werkingen_js_array = array();
        $werkingen_js_array[] = array('value'=>'', 'label'=>'Alle');
        foreach($werkingen as $w){
            $werkingen_js_array[] = array('value' => $w->getId(), 'label' => $w->getAfkorting());
        }
        $werkingen_js_array = json_encode($werkingen_js_array);
        $content = $this->getVerwijderKindModal()."\n".$this->getKindModal();
		$content .= $this->getPDFModal();
		
        $content .= <<<HERE
<div class="row">
    <button class="btn btn-large btn-primary" id="btnNieuwKind">Nieuw kind</button>
    <div class="pull-right">
        <button id="btnPDFModal" class="btn">Pdf tonen</button>
    </div>
</div>
<br>
<div class="row">
<table class="table table-striped table-bordered table-condensed" id="kinderen_tabel">
</table>
</div>
<script>
require(['tabel', 'tabel/kolom', 'tabel/control', 'tabel/controls_kolom', 'tabel/filter_rij', 'tabel/filter_veld'], function(Tabel, Kolom, Control, ControlsKolom, FilterRij, FilterVeld, require){
	   function edit_voogd(id){
	   	var data = new Object();
			data.VoogdId = id;
	   		$.get('index.php?action=data&data=voogdInfo', data, function(resp){
				resp = JSON.parse(resp);
				$('#voogdForm input[name=VoogdId]').val(resp.Id);
				$('#voogdForm input[name=Naam]').val(resp.Naam);
				$('#voogdForm input[name=Voornaam]').val(resp.Voornaam);
				$('#voogdForm textarea[name=Opmerkingen]').val(resp.Opmerkingen);
				$('#voogdForm input[name=Add]').val('0');
				$('#voogdModal').modal('show');
	   		});
	   }
	   function laad_voogd(element, id){
	   		element.empty();
	   		var data = new Object();
			data.VoogdId = id;
	   		$.get('index.php?action=data&data=voogdInfo', data, function(resp){
	   			console.log("resp = "+resp);
				resp = JSON.parse(resp);
				console.log("resp parsed");
	   			element
	   				.append($('<input>').attr({'type':'hidden', 'name': 'Id'}).val(resp.Id))
					.append($('<span>').text(resp.Voornaam+" "+resp.Naam))
					.attr('title', resp.Opmerkingen)
					.append($('<button>')
						.text('edit')
						.click(function(){
							edit_voogd(id);
							return false;
						}));
	   		});
	   }
	   function voeg_voogd_toe(id){
	   	console.log("voeg voogd toe: "+id);
	   	var el = $('<li>');
	   	$('#lstVoogden').append(el);
		laad_voogd(el, id);
	   };
	   function update_voogd(id){
	   		$('#lstVoogden li').each(function(index, value){
	   			if($(this).find('input[name=Id]').val() == id){
	   				laad_voogd($(this), id);	
	   			}
	   		});
	   }
	   $('#btnVoogd').click(function(){
	   		var data = new Object();
			data.Id = $('#voogdForm input[name=VoogdId]').val();
			data.Naam = $('#voogdForm input[name=Naam]').val();
			data.Voornaam = $('#voogdForm input[name=Voornaam]').val();
			data.Opmerkingen = $('#voogdForm textarea[name=Opmerkingen]').val();
	   		$.post('index.php?action=updateVoogd', data, function(resp){
	   			console.log(resp);
	   			try{
	   				resp = JSON.parse(resp);
	   				if($('#voogdForm input[name=Add]').val() == '1'){
	   					console.log("nieuwe");
						console.log("id = "+resp.Id);
	   					voeg_voogd_toe(resp.Id);
	   				}else{
	   					console.log("oude");
	   					update_voogd(resp.Id);
	   				}
	   			}catch(err){
	   				console.log("error updating voogd: "+resp);	
	   			}
	   			$('#voogdModal').modal('hide');
	   		});
	   });
    function add_voogd_element(data){
    	console.log("add voogd element");
    	var el = $('<li>')
			.append($('<input>').attr({
					'name':'Id',
					'type':'hidden'
				})
				.val(-1))
    		.append($('<input>').attr({
	    			'name':'Voornaam',
	    			'placeholder':'Voornaam',
	    			'type':'text',
    			})
				.css('width', '30%'))
			.append($('<input>').attr({
					'name':'Naam',
					'placeholder':'Naam',
					'type':'text', 
    			})
				.css('width', '30%'))
			.append($('<textarea>').attr({
					'name':'Opmerkingen',
					'placeholder':'Opmerkingen',
				})
				.css('width', '30%'))
			.append($('<button>')
				.text('x')
				.click(function(){
					$(this).parent().remove();
				}));
    	if(data != null){
    		el.find('input[name=Id]').val(data.VoogdId);
    		el.find('input[name=Naam]').val(data.Naam);
			el.find('input[name=Voornaam]').val(data.Voornaam);
			el.find('textarea[name=Opmerkingen]').val(data.Opmerkingen);
    	}
		$('#lstVoogden').append(el);
    }
	var suggesties = new Bloodhound({
       datumTokenizer:function(d){return Bloodhound.tokenizers.whitespace(d.value); },
       queryTokenizer: Bloodhound.tokenizers.whitespace,
       remote:{
           url:'index.php?action=data&data=voogdenSuggesties&query=%QUERY',
           filter: function(kind){
               console.log("bloodhound received this data: "+JSON.stringify(kind));
               return $.map(kind.content, function(v){
                  return { 'display_value':(v.Voornaam+" "+v.Naam+ " "+v.Kinderen), 'id':v.Id}; 
               });
           }
       }
    });
    suggesties.initialize();
    $('input[name="VoogdQuery"]').typeahead(null, {
        displayKey:'display_value',
        source: suggesties.ttAdapter()
    }).bind('typeahead:selected', function(obj, voogd, dataset_name){
		$('#kindForm input[name=VoogdQuery]').typeahead('val', '');
        voeg_voogd_toe(voogd.id);
    });
    $('#kindForm .tt-hint').addClass('form-control');
	function clear_kind_form(){
		$('#kindForm #lstVoogden').empty();
		$('#kindForm input').val('');
		$('#kindForm input[name=VoogdQuery]').typeahead('val', '');
        $('#kindForm').find('input[type=text], textarea').val('');
        $('#kindForm').find('select').val('0');
        $('#kindForm input[name=Id]').val('0');
	}
    function wijzig_kind(data){
    	clear_kind_form();
        console.log("wijzigen: "+JSON.stringify(data));
        $('.voogd_row').remove();
        $('#kindForm input[name=Id]').val(data.Id);
        $('#kindForm input[name=Voornaam]').val(data.Voornaam);
        $('#kindForm input[name=Naam]').val(data.Naam);
        $('#kindForm input[name=Geboortejaar]').val(data.Geboortejaar);
        $('#kindForm select[name=DefaultWerking]').val(data.DefaultWerking);
        $('#kindForm textarea[name=Belangrijk]').val(data.Belangrijk);
		$('#kindForm #lstVoogden').empty();
		for(var i = 0; i<  data.VoogdenIds.length; ++i){
			voeg_voogd_toe(data.VoogdenIds[i]);
		}
        $('#kindModal').modal('show');
    };
    function verwijder_kind(data){
        console.log("verwijderen: "+JSON.stringify(data));
        $('#verwijderKindModal input[name=Id]').val(data.Id);
        $('#verwijderKindModal').modal('show');
    };
    function nieuw_kind(){
    	clear_kind_form();
        $('#kindModal').modal('show');  
    };
    var k = new Array();
    k.push(new Kolom('Voornaam','Voornaam', null, true));
    k.push(new Kolom('Naam','Naam', null, true));
    k.push(new Kolom('Geboortejaar', 'Geboortejaar', null, true));
    k.push(new Kolom('Werking','Werking'));
    k.push(new Kolom('Info', 'Extra Info', function(data){
        var td = $('<td>');
        if(data['Belangrijk']){
            td.append(
                $('<a>').attr({ 
                        'data-original-title' : data['Belangrijk']
                    })
                    .append($('<span>').addClass('glyphicon glyphicon-info-sign'))
                    .tooltip());
        }
        return td;
    }));
    var controls = new Array();
    controls.push(new Control('Wijzigen', 'btn btn-xs', wijzig_kind));
    controls.push(new Control('Verwijderen', 'btn btn-xs', verwijder_kind));
    k.push(new ControlsKolom(controls));
    var t = new Tabel('index.php?action=data&data=kinderenTabel', k);
    var filter_velden = new Array();
    filter_velden.push(new FilterVeld('VolledigeNaam', 2, 'text', null));
    filter_velden.push(new FilterVeld('Geboortejaar', 1, 'text', null));
    filter_velden.push(new FilterVeld('Werking', 1, 'select', {options:$werkingen_js_array}));
    t.setFilterRij(new FilterRij(filter_velden,t));
    t.setUp($('#kinderen_tabel'));
    $(document).ready(function(){
        t.laadTabel();
        $('#btnNieuwKind').click(function(){
            nieuw_kind();
        });
        $('#kindForm').submit(function(){
			var data = new Object();
			data.Id = $('#kindForm input[name=Id]').val();
			data.Voornaam = $('#kindForm input[name=Voornaam]').val();
			data.Naam = $('#kindForm input[name=Naam]').val();
			data.Geboortejaar = $('#kindForm input[name=Geboortejaar]').val();
			data.DefaultWerking = $('#kindForm select[name=DefaultWerking]').val();
			data.Belangrijk = $('#kindForm textarea[name=Belangrijk]').val();
			data.VoogdIds = new Array();
			$('#lstVoogden li').each(function(){
				console.log("added one!");
				data.VoogdIds.push($(this).find("input[name=Id]").val());
			});
            $.post('index.php?action=updateKind', data, function(res){
               res = $.trim(res);
               if(res == "1"){
                   $('#kindModal').modal('hide');
                   t.laadTabel();
               }else{
                   console.log("kind update mislukt, error code: '"+res+"'");
               }
            });
            return false;
       });
       $('#submitKind').click(function(){
           $('#kindForm').submit();
       });
       $('#btnVerwijderKind').click(function(){
           console.log("sending delete request to server");
           console.log("data: "+$('#verwijderKindForm').serialize());
           $.post('index.php?action=removeKind', $('#verwijderKindForm').serialize(), function(res){
               res = $.trim(res);
                if(res == "1"){
                    $('#verwijderKindModal').modal('hide');
                    t.laadTabel();
                }else{
                    console.log("kind verwijderen mislukt, error code: "+res);
                }
           });
       });
	   var pdf_fields = new Array('Naam', 'Voornaam', 'Geboortejaar', 'Belangrijk', 'Werking');
	   $('#btnPDFModal').click(function(){
	   		$('#pdfSelectedFields').empty().unbind('sortupdate');
			$('#pdfUnselectedFields').empty().unbind('sortupdate');
			for(var i = 0; i < pdf_fields.length; ++i){
				$('#pdfUnselectedFields').append($('<li>').text(pdf_fields[i]).attr('draggable', 'true'));
			}
			$('#pdfSelectedFields').append($('<li>').text('Nummer').addClass('disabled'));
			$('#pdfSelectedFields, #pdfUnselectedFields').sortable({connectWith:'.pdfFields', items:':not(.disabled)'});
	   		$('#pdfModal').modal('show');
	   });
	   $('#btnPDF').click(function(){
	   		var data = new Object();
			data.kolommen = new Array();
			$('#pdfSelectedFields li').each(function(index, value){
				console.log("text = "+$(this).text());
				data.kolommen.push($(this).text());
			});
			console.log("kolommen = "+JSON.stringify(data.kolommen));
			data.action="data";
			data.data="kinderenPDF";
			data.filter = t.getFilter();
			data.order = t.getSort();
			window.open('index.php?'+$.param(data));
			$('#pdfModal').modal('hide');
	
	   });
	   $('#btnNieuweVoogd').click(function(){
	   		$('#voogdForm input,textarea').val('');
			$('#voogdForm input[name=Add]').val('1');
			$('#voogdForm input[name=Id]').val('0');
	   });
	 
    });
});
</script>
HERE;
        $this->setContent($content);
    }

}
?>

