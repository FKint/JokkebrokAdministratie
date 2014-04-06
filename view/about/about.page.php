<?php
require_once (dirname(__FILE__) . "/../page.php");
require_once (dirname(__FILE__) . "/../../model/werkingen/werking.class.php");
class AboutPage extends Page {
    public function __construct() {
        parent::__construct("Over","","about");
        $this->buildContent();
    }

	public function buildContent(){
		$content = <<<HERE
		<p class="text-center">
Concept: Roderick Demol & Floris Kint<br>
Realisatie: Floris Kint
<br>
<br>
<a href='https://github.com/fkint/JokkebrokAdministratie' target="_blank">Deze applicatie op Github</a>
<br>
<br>
Gebruikte technologieën (onvolledig, niet in een bepaalde volgorde):
<ul class="list-inline text-center">
<li><a href='http://twitter.github.io/typeahead.js/' target="_blank">Typeahead</a>
<li><a href='https://github.com/jschr/bootstrap-modal/' target="_blank">Bootstrap-modal</a>
<li><a href='http://glyphicons.com/' target="_blank">Glyphicon</a>
<li><a href='http://getbootstrap.com/' target="_blank">Bootstrap</a>
<li><a href='http://jquery.com/' target="_blank">jQuery</a>
<li><a href='http://farhadi.ir/projects/html5sortable/' target="_blank">HTM5-sortable</a>
<li><a href='http://www.mpdf1.com/mpdf/' target="_blank">mPDF</a>
</ul>
</p>

HERE;
		$this->setContent($content);
	}
}
?>