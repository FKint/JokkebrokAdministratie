<?php

class SpeelpleinDag
{
    private $datum;

    public function __construct($data)
    {
        if ($data == NULL) {
            $datum = date('Y-m-d');
        } 
        else {
            $datum = $data->Datum;
        }
        $this->datum = $datum;
    }

    public function getDatum()
    {
        return $this->datum;
    }

    public function getDatumForDatabase()
    {
        return strftime('%Y-%m-%d', strtotime($this->datum));
    }

    public function getFullDatum()
    {
        setlocale(LC_TIME, "nl_BE.UTF8");
        $d = strtotime($this->datum);
        return strftime("%A%e %B %Y", $d);
    }

    public function getDayOfWeek()
    {
        setlocale(LC_TIME, "nl_BE.UTF8");
        $d = strtotime($this->datum);
        return strftime("%A", $d);
    }
    
    public static function getSpeelpleindagen()
    {
        $sql = "SELECT DISTINCT A.Datum as Datum FROM Aanwezigheid A ORDER BY A.Datum ";
        $query = Database::getPDO()->prepare($sql);
        $query->execute();
        $speelpleindagen = array();
        while ($rs = $query->fetch(PDO::FETCH_OBJ)) {
            $speelpleindagen[] = new SpeelpleinDag($rs);
        }
        return $speelpleindagen;
    }    
    
}

?>