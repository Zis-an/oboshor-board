<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html >
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>গত অর্থ বছরের সাথে বর্তমান অর্থ বছরের তুলনামূলক বিবরণী</title>
        <style>
            .bn-font {
                font-family: 'solaimanlipi', sans-serif;
            }
            .text-center {
                text-align: center;
            }

            .text-right {
                text-align: right;
            }

            .table-bordered, td, th {
                border: 1px solid #939393;
                padding: 7px;
            }

            .table {
                border-collapse: collapse;
                width: 100%;
            }

            .text-capitalize {
                text-transform: capitalize;
            }

            .text-right{
                text-align: right;
            }

            .font-weight-bold {
                font-weight: bold;
            }
            tr:nth-child(even) {
                background-color: #f2f2f2;
            }
            th{
                background-color: #333333;
                color: #FFF;
                font-size: 1.3em;
                text-align: center;
            }
            .head_title{
                font-size: 1.6em;
                text-align: center;
            }
            .head_txt{
                font-size: 1.5em;
                text-align: center;
            }
            .footer_name{
                font-size: 1.2em;
                text-align: center;
            }
            td{
                font-size: 1.2em;
            }
        </style>
    </head>
    <body>
        <div>
            @php
            function format($amount){
            return number_format($amount, 2);
            }
            @endphp

            @php            
            $totalAmount=0;            
            @endphp   

            @if($type == 'income')
            @php $budget_type='আয়' @endphp
            @else
            @php $budget_type='ব্যয়' @endphp
            @endif

            <div class="bn-font head_title" >বেসরকারী শিক্ষা প্রতিষ্ঠান শিক্ষক-কর্মচারী অবসর সুবিধা বোর্ড </div>
            <div class="bn-font head_txt" >শিক্ষা মন্ত্রণালয়</div>
            <div class=" head_title" ><span class="bn-font">বিগত অর্থ বছরের সাথে</span> <span class="en-font">{{$currentFinancialYear->name}}</span> <span class="bn-font"> অর্থ বছরের তুলনামূলক {{$budget_type}} বিবরণী</span></div>
            <table class="table table-bordered" style=" margin-top: 20px;">
                <thead>


                    <tr>
                        <!--<th class="bn-font" >ক্র নং</th>-->
                        <th class="bn-font" >বিবরণ</th>
                        @if(!empty($prevHeads))
                        <th><span class="en-font">{{$prevFinancialYear->name}}</span><br /><span class="bn-font">অর্থবছরের প্রস্তাবিত {{$budget_type}}</span></th>
                        @if($type == 'income')
                        <th><span class="en-font">{{$prevFinancialYear->name}}</span><br /><span class="bn-font"> অর্থবছরের প্রস্তাবিত {{$budget_type}}</span></th>
                        @endif
                        @if($type == 'expense')
                        <th><span class="en-font">{{$prevFinancialYear->name}}</span><br /><span class="bn-font">অর্থবছরের প্রকৃত {{$budget_type}}</span></th>
                        @endif
                        <th >
                            <span class="en-font">{{$currentFinancialYear->name}}</span> <br /><span class="bn-font">অর্থবছরের প্রস্তাবিত {{$budget_type}} </span>
                        </th>
                        <!--<th></th>-->
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @php $iv = 0 @endphp
                    @php $ir = 0 @endphp

                    @foreach($currentHeads as $headIndex=>$currentHead)

                    <tr>
                        {{--<td  class="text-bold" style=" text-align: center; font-weight: bold;">{{ ++$iv }}</td>--}}
                        <td class="text-bold bn-font" style=" font-weight: bold;">{{$currentHead->name}}</td>
                        @if(!empty($prevHeads))
                        <td class="text-right font-weight-bold" style="text-align: right;">{{format($prevHeads[$headIndex]->budget->amount ?? 0, 2)}}</td>
                        @if($type == 'income')
                        @php
                        $headAmount = $prevHeads[$headIndex]->budget->actual_amount ?? '0';
                        $totalAmount += $headAmount;
                        @endphp
                        <td class="text-right font-weight-bold" style="text-align: right;">{{format($headAmount, 2)}}</td>
                        @endif

                        @if($type == 'expense')
                        @php
                        $headAmount = $prevHeads[$headIndex]->budget->actual_amount ?? '0';
                        $totalAmount += $headAmount;
                        @endphp
                        <td class="text-right font-weight-bold" style="text-align: right;">{{format($headAmount, 2)}}</td>
                        @endif
                        @endif
                        <td class="text-right font-weight-bold" style="text-align: right;">{{format($currentHead->budget->amount ?? 0, 2)}}</td>
                        <!--<td>{{$headIndex}}</td>-->
                    </tr>

                    @foreach($currentHead->items as $index=>$item)
                    <tr>
                        {{--<td class="text-bold" style=" text-align: center;">{{ ++$ir }}</td>--}}
                        <td class="bn-font">{{$item->name}}</td>
                        @if(!empty($prevHeads))

                        <td class="text-right" style="text-align: right;">{{format($prevHeads[$headIndex]['items'][$index]->budget->amount ?? 0, 2)}}</td>

                        @if($type == 'income')
                        <td class="text-right" style="text-align: right;">{{format($prevHeads[$headIndex]['items'][$index]->budget->actual_amount ?? 0, 2)}}</td>
                        @endif

                        @if($type == 'expense')
                        <td class="text-right" style="text-align: right;">{{format($prevHeads[$headIndex]['items'][$index]->budget->actual_amount ?? 0, 2)}}</td>
                        @endif

                        @endif

                        <td class="text-right" style="text-align: right;">
                            {{format($item->budget->amount ?? 0, 2)}}
                        </td>
                        <!--<td>{{$index}}</td>-->
                    </tr>
                    @endforeach
                    @endforeach
                </tbody>
                <tfoot style="background-color: #eee">
                    <tr class="bg-light">
                        <!--<td></td>-->
                        <td style=" text-align: right;">
                            <strong class="bn-font">মোটঃ</strong>
                        </td>
                        @if(!empty($prevFinancialYear))
                        <td class="text-right font-weight-bold" style=" text-align: right;"><strong>{{format($prevBudget->amount, 2)}}</strong></td>
                        <td class="text-right font-weight-bold" style=" text-align: right;"><strong>{{format($totalAmount, 2)}}</strong></td>
                        @endif
                        <td class="text-right font-weight-bold" style=" text-align: right;">
                            <strong>{{format($budget->amount, 2)}}</strong>
                        </td>
                    </tr>
                </tfoot>
            </table>
            <div style="width: 100%; margin-top: 80px;" class="bn-font footer_name">
                <div style=" width: 25%; text-align: center; float: left;"><strong>(শান্তি সরকার)</strong><br />একাউন্টস অফিসার<br />অবসর সুবিধা বোর্ড</div>
                <div style=" width: 25%; text-align: center; float: left;"><strong>(মোঃ সিদ্দিকুর রহমান)</strong><br />সদস্য<br />বাজেট প্রণয়ন উপ-কমিটি</div>
                <div style=" width: 25%; text-align: center; float: left;"><strong>(মাওলানা মোঃ আবু ইউসুফ)</strong><br />সদস্য<br />বাজেট প্রণয়ন উপ-কমিটি</div>
                <div style=" width: 25%; text-align: center; float: left;"><strong>(এ কে এম মোকছেদুর রহমান)</strong><br />সদস্য<br />বাজেট প্রণয়ন উপ-কমিটি</div>
            </div>
            <div style="width: 100%; margin-top: 80px;" class="bn-font footer_name">
                <div style=" width: 33%; text-align: center; float: left;"><strong>(নুরজাহান শারমিন)</strong><br />সদস্য<br />বাজেট প্রণয়ন উপ-কমিটি</div>
                <div style=" width: 34%; text-align: center; float: left;"><strong>(অধ্যক্ষ শরীফ আহমদ সাদী)</strong><br />সচিব<br />অবসর সুবিধা বোর্ড</div>
                <div style=" width: 33%; text-align: center; float: left;"><strong>(মোঃ হাসানুল মতিন)</strong><br />আহবায়ক<br />বাজেট প্রণয়ন উপ-কমিটি<br />অতিরিক্ত সচিব, অর্থ মন্তণালয়</div>

            </div>

        </div>

    </body>
</html>

