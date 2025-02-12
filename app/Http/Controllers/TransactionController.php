<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index($start_date, $end_date){
        $data = json_decode('[{"$id":"1","lot_no":"267","index_no":"416272","routing_no":"13515619","acc_no":"010008476692","acc_name":"MD. ASHIQUL AMIN","amount":"1339725","eft_date":"09/01/2024","return_flag":true,"return_reason":"No Account/Unable to Locate Account"},{"$id":"2","lot_no":"245","index_no":"014943","routing_no":"13513091","acc_no":"100022338514","acc_name":"MD JASHIM UDDIN","amount":"370185","eft_date":"09/01/2024","return_flag":true,"return_reason":"No Account/Unable to Locate Account"},{"$id":"3","lot_no":"286","index_no":"418442","routing_no":"32027051","acc_no":"4817334011841","acc_name":"MD SADRATUL SHAH","amount":"1640245","eft_date":"09/01/2024","return_flag":false,"return_reason":null},{"$id":"4","lot_no":"328","index_no":"351324","routing_no":"32027051","acc_no":"3335011006863","acc_name":"Md Abu Yousuf","amount":"779385","eft_date":"09/01/2024","return_flag":false,"return_reason":null},{"$id":"5","lot_no":"361","index_no":"B351270","routing_no":"32027051","acc_no":"3327011004111","acc_name":"BHABA RANJAN DAS","amount":"779385","eft_date":"09/01/2024","return_flag":false,"return_reason":null},{"$id":"6","lot_no":"245","index_no":"011662","routing_no":"32027051","acc_no":"4616002002779","acc_name":"Md. Siddiqur Rahman","amount":"69225","eft_date":"09/01/2024","return_flag":false,"return_reason":null}]', true);
       return view('transection.index', compact('data', 'start_date', 'end_date'));
        dd($data);
    }
}
