<?php

// This file gets a CSV from a remote location
// and burns it to JSON file(s)

    $csv_url = 'https://docs.google.com/spreadsheet/pub?key=0AprWwp-DcA85dHRtbk1oblh6c1R4dzBQOHYtVnJ2R0E&output=csv';
    $sheets = array('EN' => 0, 'ES' => 1);
    $target_folder = 'strings/';

    if(count($sheets) > 0)
    {
        foreach($sheets AS $key => $val)
        {
            burnCopy(getCopy($csv_url.'&single=true&gid='.$val), $target_folder, $key.'.json');
        }
    }
    else
    {
        burnCopy(getCopy($csv_url), $target_folder, 'copy.json');
    }

    echo "done!";

    function getCopy($csv_file)
    {
        $s = (object) array(
            'refreshed_on' => date(DATE_RFC822, time()),
            'refreshed_by' => $_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME']
        );

        if (($handle = fopen($csv_file, "r")) !== FALSE) {

            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                if($data[0] == 'id')
                {
                    $m_ = array();
                    foreach($data AS $col)
                    {
                        array_push($m_, $col);
                    }
                }
                else
                {
                    foreach($data AS $key => $value)
                    {
                        $_[$m_[$key]] = $value;
                    }
                    $s->$data[0] = (object) $_;
                }
            }
            fclose($handle);
            return json_encode($s);
        }
        else
        {
            return false;
        }
    }

    function burnCopy($data, $folder, $file)
    {
        $handle = fopen($folder.$file, 'w');
        fwrite($handle, $data);
        return true;
    }
