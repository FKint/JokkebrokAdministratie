<?php
require_once(dirname(__FILE__) . "/page.php");
require_once(dirname(__FILE__) . "/dashboard/dashboard.page.php");
require_once(dirname(__FILE__) . "/aanwezigheden/aanwezigheden.page.php");
require_once(dirname(__FILE__) . "/kinderen/kinderen.page.php");
require_once(dirname(__FILE__) . "/uitstappen/uitstappen.page.php");
require_once(dirname(__FILE__) . "/instellingen/instellingen.page.php");
require_once(dirname(__FILE__) . "/../model/kinderen/kind.class.php");
require_once(dirname(__FILE__) . "/../model/voogden/voogd.class.php");
require_once(dirname(__FILE__) . "/../model/aanwezigheden/aanwezigheid.class.php");
require_once(dirname(__FILE__) . "/../model/uitstappen/uitstap.class.php");
require_once(dirname(__FILE__) . "/../helpers/log.php");
require_once(dirname(__FILE__) . "/../helpers/pdf/pdf_generator.class.php");
require_once(dirname(__FILE__) . "/uitstappen/uitstap.pdf.php");
require_once(dirname(__FILE__) . "/aanwezigheden/aanwezigheden.pdf.php");
require_once(dirname(__FILE__) . "/kinderen/kinderen.pdf.php");
require_once(dirname(__FILE__) . "/about/about.page.php");

class View
{
    protected $controller, $model;

    public function __construct($controller, $model)
    {
        $this->model = $model;
        $this->controller = $controller;
    }

    public function output()
    {
        if ($this->model->getSession()->getLoggedIn()) {
            if (isset($_GET['action']) && $_GET['action'] == 'backup') {
                echo Database::getDatabaseDump();
                exit;
            }
            if (isset($_GET['action']) && $_GET['action'] == 'data') {
                if (!isset($_GET['data']))
                    return;
                switch ($_GET['data']) {
                    case 'kind':
                        $kindId = $_REQUEST['KindId'];
                        $kind = new Kind($kindId);
                        echo json_encode($kind->getJSONData());
                        break;
                    case 'voogd':
                        $voogdId = $_REQUEST['VoogdId'];
                        $voogd = new Voogd($voogdId);
                        echo json_encode($voogd->getJSONData());
                        break;
                    case 'kindExists':
                        $filter = array();
                        if (isset($_REQUEST['Naam'])) {
                            $filter['Naam'] = $_REQUEST['Naam'];
                        }
                        if (isset($_REQUEST['Voornaam'])) {
                            $filter['Voornaam'] = $_REQUEST['Voornaam'];
                        }
                        $result = array();
                        $result['exists'] = count(Kind::getKinderen($filter)) > 0;
                        echo json_encode($result);
                        break;
                    case 'voogdExists':
                        $filter = array();
                        if (isset($_REQUEST['Naam'])) {
                            $filter['Naam'] = $_REQUEST['Naam'];
                        }
                        if (isset($_REQUEST['Voornaam'])) {
                            $filter['Voornaam'] = $_REQUEST['Voornaam'];
                        }
                        if (isset($_REQUEST['Id'])) {
                            $filter['Id'] = $_REQUEST['Id'];
                        }
                        $exists = false;
                        $id = -1;
                        $naam = "";
                        $voornaam = "";
                        if(isset($_REQUEST['Naam'])){
                            $naam = $_REQUEST['Naam'];
                        }
                        if(isset($_REQUEST['Voornaam'])){
                            $voornaam = $_REQUEST['Voornaam'];
                        }
                        if(isset($_REQUEST['Id'])){
                            $id = $_REQUEST['Id'];
                        }
                        if(Voogd::otherVoogdExists($id, $naam, $voornaam)){
                            $exists = true;
                        }
                        $result = array();
                        $result['exists'] = $exists;
                        echo json_encode($result);
                        break;
                    case 'voogdKinderenTabel':
                        $voogd_id = $_REQUEST['VoogdId'];
                        $voogd = new Voogd($voogd_id);
                        $kinderen = $voogd->getKinderen();
                        $result = array();
                        $result['content'] = array();
                        foreach ($kinderen as $k) {
                            $result['content'][] = $k->getJSONData();
                        }
                        echo json_encode($result);
                        break;
                    case 'kindVoogdenTabel':
                        $kind_id = $_REQUEST['KindId'];
                        $kind = new Kind($kind_id);
                        $kindvoogden = $kind->getKindVoogden();
                        $result = array();
                        $result['content'] = array();
                        foreach ($kindvoogden as $k) {
                            $result['content'][] = $k->getVoogd()->getJSONData();
                        }
                        echo json_encode($result);
                        break;
                    case 'kinderenTabel':
                        $filter = null;
                        if (isset($_REQUEST['filter'])) {
                            $filter = $_REQUEST['filter'];
                        }
                        $order = array();
                        if (isset($_REQUEST['order'])) {
                            $order = $_REQUEST['order'];
                        }
                        if ($filter['Andere']) {
                            if ($filter['Andere'] == 'Schulden') {
                                $filter['Schulden'] = true;
                            } else if ($filter['Andere'] == 'Belangrijk') {
                                $filter['Belangrijk'] = true;
                            }
                        }
                        $result = array();
                        $result['content'] = array();
                        $kinderen = Kind::getKinderen($filter, 0, $order);
                        foreach ($kinderen as $k) {
                            $result['content'][] = $k->getJSONData();
                        }
                        echo json_encode($result);
                        break;
                    case 'kinderenSuggesties':
                        $query = $_GET['query'];
                        $result = array();
                        $result['content'] = array();
                        $filter = array();
                        $filter['VolledigeNaam'] = $query;
                        $kinderen = Kind::getKinderen($filter, 10);
                        foreach ($kinderen as $k) {
                            $voogden = $k->getKindVoogden();
                            $kid = $k->getId();
                            $voogden_namen_ids = array();
                            foreach ($voogden as $v) {
                                $voogd = $v->getVoogd();
                                $voogden_namen_ids[] = array('KindVoogdId' => $v->getId(), 'Voogd'=>$v->getVoogdId(), 'VolledigeNaam' => ($voogd->getVoornaam() . " " . $voogd->getNaam()));
                            }
                            $uitstapdeelnames = $k->getUitstapDeelnames();
                            $uitstappen = array();
                            foreach ($uitstapdeelnames as $ud) {
                                $uitstappen[] = $ud->getUitstapId();
                            }
                            $result['content'][] = array('Id' => $k->getId(), 'Naam' => $k->getNaam(), 'Voornaam' => $k->getVoornaam(), 'DefaultWerkingId' => $k->getDefaultWerkingId(), 'Voogden' => $voogden_namen_ids, 'Uitstappen' => $uitstappen);
                        }
                        echo json_encode($result);
                        break;
                    case 'kinderenPDF':
                        $order = array();
                        $filter = null;
                        if (isset($_REQUEST['filter'])) {
                            $filter = $_REQUEST['filter'];
                            if ($filter['Andere']) {
                                if ($filter['Andere'] == 'Schulden') {
                                    $filter['Schulden'] = true;
                                } else if ($filter['Andere'] == 'Belangrijk') {
                                    $filter['Belangrijk'] = true;
                                }
                            }
                        }
                        if (isset($_REQUEST['order'])) {
                            $order = $_REQUEST['order'];
                        }
                        $kolommen = array();
                        if (isset($_REQUEST['kolommen'])) {
                            $kolommen = $_REQUEST['kolommen'];
                        }
                        $kpdf = new KinderenPDF($filter, $order, $kolommen);
                        $kpdf->outputPDF();
                        break;
                    case 'uitstapPDF':
                        $u = new Uitstap($_REQUEST['Id']);
                        $updf = new UitstapPDF($u);
                        $updf->outputPDF();
                        break;
                    case 'uitstapDetails':
                        $u = new Uitstap($_POST['Id']);
                        $result['content'] = $u->getJSONData();
                        echo json_encode($result);
                        break;
                    case 'uitstappenTabel':
                        $result = array();
                        $result['content'] = array();
                        $filter = array();
                        if (isset($_REQUEST['filter'])) {
                            $filter = $_REQUEST['filter'];
                        }
                        $order = array('Datum' => 'desc');

                        $uitstappen = Uitstap::getUitstappen($filter, $order);
                        foreach ($uitstappen as $u) {
                            $result['content'][] = $u->getJSONData();
                        }
                        echo json_encode($result);
                        break;
                    case 'uitstapDeelnamesTabel':
                        $id = $_GET['uitstap_id'];
                        $uitstap = new Uitstap($id);
                        $deelnemers = $uitstap->getDeelnemers();
                        $result = array();
                        $result['content'] = array();
                        foreach ($deelnemers as $d) {
                            $result['content'][] = $d->getJSONData();
                        }
                        echo json_encode($result);
                        break;
                    case 'aanwezighedenPDF':
                        $order = array();
                        $filter = null;
                        if (isset($_REQUEST['filter'])) {
                            $filter = $_REQUEST['filter'];
                            if (isset($filter['Andere'])) {
                                if ($filter['Andere'] == 'Schulden') {
                                    $filter['Schulden'] = true;
                                } else if ($filter['Andere'] == 'BelangrijkOpmerkingen') {
                                    $filter['BelangrijkOpmerkingen'] = true;
                                }
                            }
                        }
                        if (isset($_REQUEST['order'])) {
                            $order = $_REQUEST['order'];
                        }
                        $kolommen = array();
                        if (isset($_REQUEST['kolommen'])) {
                            $kolommen = $_REQUEST['kolommen'];
                        }
                        $title = $_REQUEST['title'];
                        $apdf = new AanwezighedenPDF($filter, $order, $kolommen, $title);
                        $apdf->outputPDF();
                        break;
                    case 'aanwezighedenTabel':
                        $filter = null;
                        if (isset($_POST['filter'])) {
                            $filter = $_POST['filter'];
                        }
                        $order = array();
                        if (isset($_POST['order'])) {
                            $order = $_POST['order'];
                        }
                        if (isset($filter['Andere'])) {
                            if ($filter['Andere'] == 'Schulden') {
                                $filter['Schulden'] = true;
                            } else if ($filter['Andere'] == 'BelangrijkOpmerkingen') {
                                $filter['BelangrijkOpmerkingen'] = true;
                            }
                        }
                        $result = array();
                        $result['content'] = array();
                        $aanwezigheden = Aanwezigheid::getAanwezigheden($filter, $order);
                        foreach ($aanwezigheden as $a) {
                            $result['content'][] = $a->getJSONData();
                        }
                        echo json_encode($result);
                        break;
                    case 'aanwezigheidDetails':
                        $id = $_GET['id'];
                        $aanwezigheid = new Aanwezigheid($id);
                        $kindvoogd = $aanwezigheid->getKindVoogd();
                        $kind = $kindvoogd->getKind();
                        $voogden = $kind->getKindVoogden();
                        $result = array();
                        $result['Id'] = $aanwezigheid->getId();
                        $result['KindId'] = $kind->getId();
                        $result['Geboortejaar'] = $kind->getGeboortejaar();
                        $result['KindVolledigeNaam'] = $kind->getVoornaam() . " " . $kind->getNaam();
                        $result['Opmerkingen'] = $aanwezigheid->getOpmerkingen();
                        $result['MiddagNaarHuis'] = $aanwezigheid->getMiddagNaarHuis();
                        $result['Datum'] = $aanwezigheid->getDatum();
                        $result['KindVoogdId'] = $kindvoogd->getId();
                        $result['KindVoogden'] = array();
                        foreach ($voogden as $v) {
                            $voogd = $v->getVoogd();
                            $result['KindVoogden'][] = array('Id' => $v->getId(), 'VolledigeNaam' => ($voogd->getVoornaam() . " " . $voogd->getNaam()));
                        }
                        $result['Werking'] = $aanwezigheid->getWerkingId();
                        $result['Extraatjes'] = array();
                        $extraatjes = $aanwezigheid->getExtraatjes();
                        foreach ($extraatjes as $e) {
                            $result['Extraatjes'][] = $e->getId();
                        }
                        $vorderingen = $aanwezigheid->getVorderingen();
                        if (count($vorderingen) > 0) {
                            $result['Vorderingen'] = array();
                            foreach ($vorderingen as $v) {
                                $result['Vorderingen'][] = $v->getJSONData();
                            }
                        }

                        echo json_encode($result);
                        break;
                    case 'werkingenTabel':
                        $werkingen = Werking::getWerkingen();
                        $result = array();
                        $result['content'] = array();
                        foreach ($werkingen as $w) {
                            $result['content'][] = $w->getJSONData();
                        }
                        echo json_encode($result);
                        break;
                    case 'extraatjesTabel':
                        $extraatjes = Extraatje::getExtraatjes();
                        $result = array();
                        $result['content'] = array();
                        foreach ($extraatjes as $e) {
                            $result['content'][] = array('Id' => $e->getId(), 'Omschrijving' => $e->getOmschrijving());
                        }
                        echo json_encode($result);
                        break;
                    case 'voogdInfo':
                        $voogd = null;
                        if (isset($_REQUEST['VoogdId'])) {
                            $voogd = new Voogd($_REQUEST['VoogdId']);
                        } else {
                            $kvoogd = new KindVoogd($_GET['kind_voogd_id']);
                            $voogd = $kvoogd->getVoogd();
                        }
                        echo json_encode($voogd->getJSONData());
                        break;
                    case 'voogdenSuggesties':
                        $query = $_REQUEST['query'];
                        $result = array();
                        $result['content'] = array();
                        $filter = array();
                        $filter['VolledigeNaam'] = $query;
                        $voogden = Voogd::getVoogden($filter, 10);
                        if (count($voogden) < 10) {
                            $kinderen = Kind::getKinderen($filter, 10 - count($voogden));
                            foreach ($kinderen as $k) {
                                $kind_voogden = $k->getKindVoogden();
                                foreach ($kind_voogden as $kv) {
                                    if (count($voogden) == 10)
                                        break;
                                    $voogden[] = $kv->getVoogd();
                                }
                            }
                        }
                        $resulting_voogden = array();
                        foreach ($voogden as $v) {
                            $good = true;
                            foreach ($resulting_voogden as $rv) {
                                if ($rv->getId() == $v->getId()) {
                                    $good = false;
                                    break;
                                }
                            }
                            if (isset($_REQUEST['current_voogden'])) {
                                foreach ($_REQUEST['current_voogden'] as $vid) {
                                    if (intval($vid) == $v->getId()) {
                                        $good = false;
                                        break;
                                    }
                                }
                            }
                            if (!$good) {
                                continue;
                            }
                            $resulting_voogden[] = $v;
                        }
                        $voogden = $resulting_voogden;
                        foreach ($voogden as $v) {
                            $kinderen_string = "";
                            $kinderen = $v->getKinderen();
                            if (count($kinderen) > 0) {
                                $kinderen_string .= "(Voogd van ";
                                $first = true;
                                foreach ($kinderen as $k) {
                                    if (!$first) {
                                        $kinderen_string .= ", ";
                                    }
                                    $kinderen_string .= $k->getVoornaam() . " " . $k->getNaam();
                                    $first = false;
                                }
                                $kinderen_string .= ")";
                            }
                            $result['content'][] = array('Id' => $v->getId(), 'Naam' => $v->getNaam(), 'Voornaam' => $v->getVoornaam(), 'Kinderen' => $kinderen_string);
                        }
                        echo json_encode($result);
                        break;
                    case 'kindVoogden':
                        $kind_id = $_REQUEST['KindId'];
                        $kind = new Kind($kind_id);
                        $kindvoogden = $kind->getKindVoogden();
                        $result = array();
                        $result['content'] = array();
                        foreach ($kindvoogden as $kv) {
                            $obj = new stdClass();
                            $obj->Id = $kv->getId();
                            $voogd = $kv->getVoogd();
                            $obj->Naam = $voogd->getNaam();
                            $obj->Voornaam = $voogd->getVoornaam();
                            $result['content'][] = $obj;
                        }
                        echo json_encode($result);
                        exit;
                    case 'kindVoogdSaldo':
                        $kindvoogd = new KindVoogd($_REQUEST['KindVoogdId']);
                        //$kindvoogd->updateSaldo();
                        $result = array();
                        $result['Saldo'] = $kindvoogd->getSaldo();
                        echo json_encode($result);
                        exit;
                    case 'saldoTabel':
                        $result = array();
                        $result['content'] = array();
                        $kindvoogd = new KindVoogd($_REQUEST['KindVoogdId']);
                        $vorderingen = $kindvoogd->getVorderingen();
                        foreach ($vorderingen as $v) {
                            $obj = new stdClass();
                            $obj->Type = "vordering";
                            $obj->Id = $v->getId();
                            $obj->Bedrag = -$v->getBedrag();
                            $obj->Datum = $v->getAanwezigheid()->getDatum();
                            $obj->Aanwezigheid = $v->getAanwezigheidId();
                            $obj->Opmerking = $v->getOpmerking();
                            $result['content'][] = $obj;
                        }
                        $betalingen = $kindvoogd->getBetalingen();
                        foreach ($betalingen as $b) {
                            $obj = new stdClass();
                            $obj->Type = "betaling";
                            $obj->Id = $b->getId();
                            $obj->Bedrag = $b->getBedrag();
                            $obj->Datum = $b->getDatum();
                            $obj->Opmerking = $b->getOpmerking();
                            $result['content'][] = $obj;
                        }
                        echo json_encode($result);
                        exit;
                    case 'kindUitstappen':
                        $result = array();
                        $result['content'] = array();
                        $kind = new Kind($_REQUEST['KindId']);
                        $uitstap_deelnames = $kind->getUitstapDeelnames();
                        foreach ($uitstap_deelnames as $uk) {
                            $o = new stdClass();
                            $o = $uk->getUitstap()->getJSONData();
                            $result['content'][] = $o;
                        }
                        echo json_encode($result);
                        exit;
                    case 'aanwezigheidUitstappen':
                        $kind_id = null;
                        if (isset($_REQUEST['KindId'])) {
                            $kind_id = $_REQUEST['KindId'];
                        }

                        $filter = array();
                        $filter['AanwezigheidZichtbaar'] = true;
                        $uitstappen = Uitstap::getUitstappen($filter, array("Datum"=>"asc"));
                        $result = array();
                        $result['content'] = array();
                        foreach ($uitstappen as $u) {
                            $o = $u->getJSONData();
                            $o->Ingeschreven = false;
                            if ($kind_id) {
                                if ($u->isIngeschreven($kind_id)) {
                                    $o->Ingeschreven = true;
                                }
                            }
                            $result['content'][] = $o;
                        }
                        echo json_encode($result);
                        exit;
                }
            } else {
                $page = "dashboard";
                if (isset($_GET['page'])) {
                    $page = $_GET['page'];
                }
                $p = NULL;
                switch ($page) {
                    case "dashboard":
                        $p = new DashboardPage();
                        break;
                    case "aanwezigheden":
                        $p = new AanwezighedenPage();
                        break;
                    case "kinderen":
                        $p = new KinderenPage();
                        break;
                    case "instellingen":
                        $p = new InstellingenPage();
                        break;
                    case "uitstappen":
                        $p = new UitstappenPage();
                        break;
                    case "about":
                        $p = new AboutPage();
                        break;
                    default:
                        $p = new Page("Not found", "Page not found!", "");
                        break;
                }
                $p->output();
            }
        } else {
            require(dirname(__FILE__) . "/../public_html/login.php");
        }
    }

}

?>