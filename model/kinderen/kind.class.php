<?php
require_once(dirname(__FILE__)."/../record.class.php");
class Kind extends Record{
    protected function setLocalData($data){
        $this->Voornaam = $data->Voornaam;
        $this->Naam = $data->Naam;
        $this->Geboortejaar = $data->Geboortejaar;
        $this->DefaultWerkingId = $data->DefaultWerkingId;
        $this->Belangrijk = $data->Belangrijk;
    }
    protected function insert(){
        $query = Database::getPDO()->prepare("INSERT INTO Kind (Voornaam, Naam, Geboortejaar, DefaultWerkingId, Belangrijk) VALUES (:voornaam, :naam, :geboortejaar, :default_werking_id, :belangrijk)");
        $query->bindParam(':voornaam', $this->Voornaam, PDO::PARAM_STR);
        $query->bindParam(':naam', $this->Naam, PDO::PARAM_STR);
        $query->bindParam(':geboortejaar', $this->Geboortejaar, PDO::PARAM_STR);
        $query->bindParam(':default_werking_id', $this->DefaultWerkingId, PDO::PARAM_INT);
        $query->bindParam(':belangrijk', $this->Belangrijk, PDO::PARAM_STR);
        $query->execute();
        return Database::getPDO()->lastInsertId();
    }
    protected function update(){
        $query = Database::getPDO()->prepare('UPDATE Kind SET Naam=:naam, Voornaam=:voornaam, Geboortejaar=:geboorte_jaar, DefaultWerkingId=:default_werking_id, Belangrijk=:belangrijk WHERE Id=:id');
        $query->bindParam(':naam', $this->Naam, PDO::PARAM_STR);
        $query->bindParam(':voornaam', $this->Voornaam, PDO::PARAM_STR);
        $query->bindParam(':geboorte_jaar', $this->Geboortejaar, PDO::PARAM_STR);
        $query->bindParam(':default_werking_id', $this->DefaultWerkingId, PDO::PARAM_STR);
        $query->bindParam(':belangrijk', $this->Belangrijk, PDO::PARAM_STR);
        $query->bindParam(':id', $this->Id, PDO::PARAM_INT);
        $res = $query->execute();
        return $res;
    }
    protected function select(){
        $query = Database::getPDO()->prepare("SELECT * FROM Kind WHERE Id = :id");
        $query->bindParam(':id', $this->Id, PDO::PARAM_INT);
        $query->execute();
        return $query->fetch(PDO::FETCH_OBJ);
    }
    protected function delete(){
        $query = Database::getPDO()->prepare("DELETE FROM Kind WHERE Id = :id");
        $query->bindParam(':id', $this->Id, PDO::PARAM_INT);
        return $query->execute();
    }
    protected static function getFilterJoinsSQL($filter){
        $sql = "";
        
        return $sql;
    }
    protected static function getFilterSQL($filter){
        $sql = "";
        if(isset($filter['VolledigeNaam'])){
            $sql .= "AND (CONCAT(Naam, ' ', Voornaam) LIKE :volledige_naam ";
            $sql .= " OR CONCAT(Voornaam, ' ', Naam) LIKE :volledige_naam2) ";
        }
        if(isset($filter['WerkingId'])){
            $sql .= "AND DefaultWerkingId = :werking_id ";
        }
        return $sql;
    }
    protected static function applyFilterParameters($query, $filter){
        if(isset($filter['VolledigeNaam'])){
            $query->bindParam(':volledige_naam', $tmp = '%'.$filter['VolledigeNaam'].'%', PDO::PARAM_STR);
            $query->bindParam(':volledige_naam2', $tmp = '%'.$filter['VolledigeNaam'].'%', PDO::PARAM_STR);
        }
        if(isset($filter['WerkingId'])){
            $query->bindParam(':werking_id', $filter['WerkingId'], PDO::PARAM_INT);
        }
    }
    public static function getKinderen($filter){
        $sql = "SELECT * FROM Kind WHERE 1 ";
        $sql .= static::getFilterSQL($filter);
        $query = Database::getPDO()->prepare($sql);
        static::applyFilterParameters($query, $filter);
        $query->execute();
        $kinderen = array();
        while($rs = $query->fetch(PDO::FETCH_OBJ)){
            $kinderen[] = new Kind($rs);
        }
        return $kinderen;
    }
    public function getJSONData(){
        $query = Database::getPDO()->prepare("SELECT K.Id as Id, K.Voornaam as Voornaam, K.Naam as Naam, K.Geboortejaar as Geboortejaar, K.Belangrijk as Belangrijk, W.Afkorting as Werking, K.DefaultWerkingId as DefaultWerkingId FROM Kind K LEFT JOIN Werking W ON K.DefaultWerkingId=W.Id WHERE K.Id=:id");
        $query->bindParam(':id', $this->getId(), PDO::PARAM_INT);
        $query->execute();
        return $query->fetch(PDO::FETCH_OBJ); 
    }
}
?>
