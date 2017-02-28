class dateTimeController{

    public function presenteerdatetime($datetime,$type="1")
    {
        global $cfg;
       
        $this_lang = $cfg["lang"];
       
        $jaar = substr($datetime,0,4);
        $maand = substr($datetime,4,2);
        $dag = substr($datetime,6,2);
        $uur = substr($datetime,8,2);
        $min = substr($datetime,10,2);
        $sec = substr($datetime,12,2);
       
        if ($type == 1)
        {
            $echo = $dag . "-" . $maand . "-" . $jaar;   
        }
        elseif ($type == 2)
        {
            $echo = $dag . "-" . $maand . "-" . $jaar . " | " . $uur . ":" . $min . "uur";   
        }
       
        elseif ($type == 5)
        {
            $dag = round($dag);
            $maand = round($maand);
            $maandnaam = strtolower($cfg["maandenArray"][$this_lang][$maand]);
           
            $echo = $dag . " " . $maandnaam . " " . $jaar;   
        }
        elseif ($type == 51)
        {
            $dag = round($dag);
            $maand = round($maand);
            $maandnaam = strtolower($cfg["maandenArray"][$this_lang][$maand]);
           
            $echo = $dag . " " . $maandnaam;   
        }
        elseif ($type == 6)
        {
            $dag = ($dag);
            $maand = round($maand);
            $maandnaam = strtolower($cfg["maanden3Array"][$maand]);
            //$jaar = substr($jaar,-2);
           
            $echo = $dag . " " . $maandnaam . " " . $jaar;   
        }
        elseif ($type == 7)
        {
            $echo = $jaar . "-" . $maand . "-" . $dag;   
        }
        elseif ($type == 8)
        {
            $echo = $dag . "/" . $maand;   
        }
        elseif ($type == 9)
        {
            $maand = round($maand);
            $maandnaam = strtolower($cfg["maandenArray"][$this_lang][$maand]);
           
            $echo = $dag . " " . $maandnaam . " " . $jaar;   
        }
        elseif ($type == 10)
        {
            //lokaal
           
            $echo = $dag . "-" . $maand . "-" . $jaar;   
        }
       
        if ($type == 11)
        {
            $echo = $dag . "-" . $maand . "-" . $jaar . ", " . $uur . ":" . $min;   
        }
        elseif ($type == 19)
        {
            $maand = round($maand);
            $maandnaam = strtolower($cfg["maandenArray"][$this_lang][$maand]);
           
            $echo = $dag . " " . $maandnaam . " " . $jaar . ", " . $uur . ":" . $min;   
        }
        elseif ($type == 20)
        {
            $maand = round($maand);
            $maandnaam = strtolower($cfg["maandenArray"][$this_lang][$maand]);
           
            $dagnr = date("N",datetime2time($datetime));
           
            $echo = $cfg["dagenArray"]["nl"][$dagnr]  . " " . $dag . " " . $maandnaam . " " . $jaar;   
        }
        elseif ($type == 21) //taal
        {
            $maand = round($maand);
            $maandnaam = strtolower($cfg["maandenArray"][$this_lang][$maand]);
           
            $echo = $dag . " " . $maandnaam . " " . $jaar;   
        }
        elseif ($type == 22)
        {
            $maand = round($maand);
            $maandnaam = strtolower($cfg["maandenArray"][$this_lang][$maand]);
           
            $echo = $maandnaam . " " . $jaar;   
        }
        elseif ($type == 23)
        {
            $this_time = datetime2time($datetime);
           
            $this_minuten = floor(aantalminutenverschil(date("YmdHis"),$datetime));
            $this_uren = floor(aantalurenverschil(date("YmdHis"),$datetime));
            $this_dagen = floor(aantaldagenverschil(date("YmdHis"),$datetime));
           
            $datum = substr($datetime,0,8);
            $vandaag = date("Ymd");
            $gisteren = myplusdagen($datum,-1);
            $eergisteren = myplusdagen($datum,-2);
           
            $echo = "";
           
            if ($this_minuten < 2)
            {
                $echo = "1 minuut geleden";
            }
            elseif ($this_uren < 1)
            {
                $echo = "$this_minuten minuten geleden";
            }
            elseif ($this_uren <= 3)
            {
                $echo = "$this_uren uur geleden";
            }
            elseif ($datum == $vandaag)
            {
                $echo = "vandaag";
            }
            elseif ($datum == $gisteren)
            {
                $echo = "gisteren";
            }
            elseif ($this_dagen <= 10)
            {
                $echo = "$this_dagen dagen geleden";
            }
            else
            {
                $echo = presenteerdatetime($datetime,6);
            }       
        }
        elseif ($type == 24)
        {
            $echo = $dag . "." . $maand . "." . $jaar;   
        }
       
        if ($datetime == "19000000000000" or $datetime == "0")
        {
            $echo = "";
        }
       
        return $echo;
    }
}