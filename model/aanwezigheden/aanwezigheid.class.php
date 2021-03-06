<?php
require_once(dirname(__FILE__) . "/../record.class.php");
require_once(dirname(__FILE__) . "/../kindvoogden/kindvoogd.class.php");
require_once(dirname(__FILE__) . "/../betalingen/vordering.class.php");

class Aanwezigheid extends Record
{
    protected function setLocalData($data)
    {
        $this->Datum = $data->Datum;
        $this->KindVoogdId = $data->KindVoogd;
        $this->WerkingId = $data->Werking;
        $this->Opmerkingen = $data->Opmerkingen;
        $this->MiddagNaarHuis = $data->MiddagNaarHuis;
        $this->LastChanged = $data->LastChanged;
    }

    protected function insert()
    {
        $query = Database::getPDO()->prepare("INSERT INTO Aanwezigheid (Datum, KindVoogd, Werking, Opmerkingen, MiddagNaarHuis) VALUES (:datum, :kind_voogd_id, :werking_id, :opmerkingen, :middag_naar_huis)");
        $query->bindParam(':datum', $this->Datum, PDO::PARAM_STR);
        $query->bindParam(':kind_voogd_id', $this->KindVoogdId, PDO::PARAM_INT);
        $query->bindParam(':werking_id', $this->WerkingId, PDO::PARAM_INT);
        $query->bindParam(':opmerkingen', $this->Opmerkingen, PDO::PARAM_STR);
        $query->bindParam(':middag_naar_huis', $this->MiddagNaarHuis, PDO::PARAM_INT);
        $query->execute();
        return Database::getPDO()->lastInsertId();
    }

    protected function update()
    {
        $query = Database::getPDO()->prepare("UPDATE Aanwezigheid SET Datum=:datum, KindVoogd=:kind_voogd_id, Werking=:werking_id, Opmerkingen=:opmerkingen, MiddagNaarHuis=:middag_naar_huis, LastChanged=CURRENT_TIMESTAMP WHERE Id=:id");
        $query->bindParam(':datum', $this->Datum, PDO::PARAM_STR);
        $query->bindParam(':kind_voogd_id', $this->KindVoogdId, PDO::PARAM_INT);
        $query->bindParam(':werking_id', $this->WerkingId, PDO::PARAM_INT);
        $query->bindParam(':opmerkingen', $this->Opmerkingen, PDO::PARAM_STR);
        $query->bindParam(':id', $this->Id, PDO::PARAM_INT);
        $query->bindParam(':middag_naar_huis', $this->MiddagNaarHuis, PDO::PARAM_INT);
        return $query->execute();
    }

    protected function select()
    {
        $query = Database::getPDO()->prepare("SELECT * FROM Aanwezigheid WHERE Id = :id");
        $query->bindParam(':id', $this->Id, PDO::PARAM_INT);
        $query->execute();
        return $query->fetch(PDO::FETCH_OBJ);
    }

    protected function delete()
    {
        /*if(count($this->getExtraatjes()) > 0 || count($this->getVorderingen()) > 0){
            return false;
        }*/
        $extraatjes = $this->getExtraatjeAanwezigheden();
        foreach ($extraatjes as $ea) {
            $ea->deleteFromDatabase();
        }
        $query = Database::getPDO()->prepare("DELETE FROM Aanwezigheid WHERE Id = :id");
        $query->bindParam(':id', $this->Id, PDO::PARAM_INT);
        return $query->execute();
    }

    public static function isAanwezig($kind_voogd_id, $datum)
    {
        $kv = new KindVoogd($kind_voogd_id);
        $kind = $kv->getKind();
        $kvs = $kind->getKindVoogden();
        foreach ($kvs as $k) {
            if ($k->isAanwezig($datum)) {
                return true;
            }
        }
        return false;
    }

    protected static function getFilterSQL($filter)
    {
        $sql = "";
        if (isset($filter['VolledigeNaam'])) {
            $sql .= "AND (CONCAT(K.Naam, ' ', K.Voornaam) LIKE :volledige_naam ";
            $sql .= " OR CONCAT(K.Voornaam, ' ', K.Naam) LIKE :volledige_naam2) ";
        }
        if (isset($filter['Datum'])) {
            $sql .= "AND Datum = :datum ";
        }
        if (isset($filter['Werking'])) {
            $sql .= "AND Werking = :werking_id ";
        }
        if (isset($filter['Extraatjes'])) {
            $sql .= "AND EA.Extraatje = :extraatje_id ";
        }
        if (isset($filter['KindVoogd'])) {
            $sql .= "AND A.KindVoogd = :kindvoogd_id ";
        }
        if (isset($filter['MiddagNaarHuis'])) {
            $sql .= "AND MiddagNaarHuis = :middag_naar_huis ";
        }
        if (isset($filter['BelangrijkOpmerkingen'])) {
            $sql .= "AND (K.Belangrijk <> '' OR Opmerkingen <> '') ";
        }
        return $sql;
    }

    protected static function getFilterJoinsSQL($filter)
    {
        $sql = "";
        if (isset($filter['Extraatjes'])) {
            $sql .= "LEFT JOIN ExtraatjeAanwezigheid EA ON EA.Aanwezigheid = A.Id ";
        }
        return $sql;
    }

    protected static function applyFilterParameters($query, $filter)
    {
        if (isset($filter['VolledigeNaam'])) {
            $tmp = '%' . $filter['VolledigeNaam'] . '%';
            $query->bindParam(':volledige_naam', $tmp, PDO::PARAM_STR);
            $query->bindParam(':volledige_naam2', $tmp, PDO::PARAM_STR);
        }
        if (isset($filter['Datum'])) {
            $query->bindParam(':datum', $filter['Datum'], PDO::PARAM_STR);
        }
        if (isset($filter['Werking'])) {
            $query->bindParam(':werking_id', $filter['Werking'], PDO::PARAM_INT);
        }
        if (isset($filter['Extraatjes'])) {
            $query->bindParam(':extraatje_id', $filter['Extraatjes'], PDO::PARAM_INT);
        }
        if (isset($filter['KindVoogd'])) {
            $query->bindParam(':kindvoogd_id', $filter['KindVoogd'], PDO::PARAM_INT);
        }
        if (isset($filter['MiddagNaarHuis'])) {
            $query->bindParam(':middag_naar_huis', $filter['MiddagNaarHuis'], PDO::PARAM_INT);
        }
    }

    protected static function getOrderSQL($order)
    {
        if (count($order) == 0)
            return " ORDER BY LastChanged DESC ";
        $first = true;
        $sql = "ORDER BY";
        foreach ($order as $o) {
            $curr_sql = " ";
            if (!$first) {
                $curr_sql = ", ";
            }
            if (!isset($o['Veld']) || !isset($o['Order'])) {
                continue;
            }
            $stop = false;
            switch ($o['Veld']) {
                case 'Naam':
                    $curr_sql .= " K.Naam";
                    break;
                case 'Voornaam':
                    $curr_sql .= " K.Voornaam";
                    break;
                case 'Geboortejaar':
                    $curr_sql .= " K.Geboortejaar";
                    break;
                case 'Datum':
                    $curr_sql .= " Datum";
                    break;
                default:
                    $stop = true;
                    break;
            }
            switch (strtolower($o['Order'])) {
                case 'asc':
                    $curr_sql .= " ASC";
                    break;
                case 'desc':
                    $curr_sql .= " DESC";
                    break;
                default:
                    $stop = true;
                    break;
            }
            if ($stop)
                continue;
            $sql .= $curr_sql;
            $first = false;
        }
        if ($first) {
            return "";
        }
        return $sql;
    }

    public static function countAanwezigheden($filter)
    {
        $sql = "SELECT COUNT(*) as Amount FROM Aanwezigheid WHERE 1 ";
        $sql .= static::getFilterSQL($filter);
        $query = Database::getPDO()->prepare($sql);
        static::applyFilterParameters($query, $filter);
        $query->execute();
        $res = $query->fetch();
        return $res['Amount'];
    }

    public static function getAanwezigheden($filter, $order = array())
    {
        $sql = "SELECT A.Id as Id, A.Datum as Datum, A.KindVoogd as KindVoogd, A.Werking as Werking, A.Opmerkingen as Opmerkingen, A.MiddagNaarHuis as MiddagNaarHuis, A.LastChanged as LastChanged FROM Aanwezigheid A LEFT JOIN KindVoogd KV on KV.Id=A.KindVoogd LEFT JOIN Kind K ON K.Id=KV.Kind ";
        $sql .= static::getFilterJoinsSQL($filter);
        $sql .= " WHERE 1 ";
        $sql .= static::getFilterSQL($filter);
        $sql .= static::getOrderSQL($order);
        $query = Database::getPDO()->prepare($sql);
        static::applyFilterParameters($query, $filter);
        $query->execute();
        $aanwezigheden = array();
        while ($rs = $query->fetch(PDO::FETCH_OBJ)) {
            $a = new Aanwezigheid($rs);
            if (isset($filter['Schulden']) && $filter['Schulden'] && !$a->getKindVoogd()->getHeeftSchulden()) {
                continue;
            }
            $aanwezigheden[] = $a;
        }
        return $aanwezigheden;
    }

    public function getDatum()
    {
        return $this->Datum;
    }

    public function getKindVoogd()
    {
        return new KindVoogd($this->KindVoogdId);
    }

    public function getKindVoogdId()
    {
        return $this->KindVoogdId;
    }

    public function getWerkingId()
    {
        return $this->WerkingId;
    }

    public function getOpmerkingen()
    {
        return $this->Opmerkingen;
    }

    public function getMiddagNaarHuis()
    {
        return $this->MiddagNaarHuis;
    }

    public function getExtraatjeAanwezigheden()
    {
        $filter = array('AanwezigheidId' => $this->getId());
        $extraatje_aanwezigheden = ExtraatjeAanwezigheid::getExtraatjeAanwezigheden($filter);
        return $extraatje_aanwezigheden;
    }

    public function getExtraatjes()
    {
        $extraatje_aanwezigheden = $this->getExtraatjeAanwezigheden();
        $extraatjes = array();
        foreach ($extraatje_aanwezigheden as $ea) {
            $extraatjes[] = $ea->getExtraatje();
        }
        return $extraatjes;
    }

    public function getJSONData()
    {
        //TODO: collect from local data
        $query = Database::getPDO()->prepare("SELECT A.Id as Id, A.MiddagNaarHuis as MiddagNaarHuis, K.Voornaam as Voornaam, K.Naam as Naam, K.Belangrijk as Belangrijk, W.Afkorting as Werking, A.Opmerkingen as Opmerkingen, A.Datum as Datum, CONCAT(V.Voornaam, ' ', V.Naam) as VoogdVolledigeNaam, A.LastChanged as LastChanged, KV.Kind as KindId FROM Aanwezigheid A LEFT JOIN KindVoogd KV ON A.KindVoogd=KV.Id LEFT JOIN Kind K ON K.Id=KV.Kind LEFT JOIN Werking W ON A.Werking=W.Id LEFT JOIN Voogd V ON V.Id=KV.Voogd WHERE A.Id= :id ");
        $id = $this->getId();
        $query->bindParam(':id', $id, PDO::PARAM_INT);
        $query->execute();
        $aanwezigheid = $query->fetch(PDO::FETCH_OBJ);
        $aanwezigheid->VoogdId = $this->getKindVoogd()->getVoogdId();
        $aanwezigheid->Extraatjes = array();
        $extraatjes = $this->getExtraatjes();
        $aanwezigheid->Schulden = $this->getKindVoogd()->getSaldo();
        foreach ($extraatjes as $e) {
            $aanwezigheid->Extraatjes[] = array('Id' => $e->getId(), 'Omschrijving' => $e->getOmschrijving());
        }
        return $aanwezigheid;
    }

    public function setExtraatjes($extraatjes)
    {
        $current_extraatjes = $this->getExtraatjeAanwezigheden();
        foreach ($current_extraatjes as $ce) {
            $ce->deleteFromDatabase();
        }
        foreach ($extraatjes as $e) {
            $this->addExtraatje($e);
        }
    }

    public function addExtraatje($e)
    {
        $obj = new stdClass();
        $obj->Id = 0;
        $obj->Aanwezigheid = $this->getId();
        $obj->Extraatje = $e;
        $ea = new ExtraatjeAanwezigheid($obj);
        $ea->updateDatabase();
    }

    public function getVorderingen()
    {
        $filter = array();
        $filter['Aanwezigheid'] = $this->getId();
        return Vordering::getVorderingen($filter);
    }

    public function setVorderingen($vorderingen)
    {
        $original_vorderingen = $this->getVorderingen();
        foreach ($original_vorderingen as $v) {
            $good = false;
            foreach ($vorderingen as $v2) {
                if ($v->getId() == $v2->getId()) {
                    $good = true;
                    break;
                }
            }
            if (!$good) {
                $v->deleteFromDatabase();
            }
        }
        foreach ($vorderingen as $v) {
            $v->updateDatabase();
        }
    }
}

?>
