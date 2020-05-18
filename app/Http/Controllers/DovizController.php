<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use SimpleXMLElement;

class DovizController extends Controller
{
    public function xml($date)
    {
        $JSONString = array(
            "success" => false,
            "tarih"=>"",
            "bulten_no"=>"",
            "results" => array()
          );

        $date= (!empty($date) ? $date : 'today');
        if ($date=='today')
        {
            $url ='https://www.tcmb.gov.tr/kurlar/today.xml';
        }
        else
        {

                $yil=substr($date,4,4);
                $ay=substr($date,2,2);
                $url = 'https://www.tcmb.gov.tr/kurlar/'.$yil.$ay.'/'.$date.'.xml';
            
        }
        
        try {
            $sxe=true;
            $xml = simplexml_load_file($url);
        } catch (\Exception $e) 
        {
            $sxe=false;
        }
        if (false === $sxe) 
        {
            $JSONString['results'][] =  array(
                'message'=>'Tarih Hatalı'
                );
        }
        else
        {
            $count = 1;
            $Tarih_Date =(string) $xml->attributes()->Tarih;
            $Bulten_No =(string) $xml->attributes()->Bulten_No;
            $JSONString['tarih']=$Tarih_Date;
            $JSONString['bulten_no']=$Bulten_No;
                foreach ($xml->children() as $children) {
                    $Unit=(string) $children->Unit;
                    $CurrencyCode=(string) $children->attributes()->CurrencyCode;
                    $CurrencyName=(string) $children->CurrencyName;
                    $ForexBuying=(string) $children->ForexBuying;
                    $ForexSelling=(string) $children->ForexSelling;
                    $BanknoteBuying=(string) $children->BanknoteBuying;
                    $BanknoteSelling=(string) $children->BanknoteSelling;
                    $JSONString['results'][] =  array(
                    'Row'=>$count,
                    'Unit'=>$Unit,

                    'Code' => $CurrencyCode,
                    'CurrencyName' => $CurrencyName,
                    'ForexBuying'=>$ForexBuying,
                    'ForexSelling'=>$ForexSelling,
                    'BanknoteBuying' => $BanknoteBuying,
                    'BanknoteSelling' => $BanknoteSelling
                );
                    $count=$count+1;
                }
                $JSONString['success'] = true;
        }       
            $json = json_encode($JSONString);
            $array = json_decode($json,TRUE);
         return response()->json($array,
         200,
         ['Content-Type' => 'application/json;charset=UTF-8', 'Charset' => 'utf-8'], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT
                                 );
    }
}
