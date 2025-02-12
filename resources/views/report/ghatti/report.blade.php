@extends('layouts.app')

@section('main')

    <section class="content-header">
        <h3>Ghatti Report</h3>
    </section>

    <div class="card">
        <div class="card-header">
            <div>
                <button id="export_btn_pdf" class="btn btn-success mx-1">PDF</button>
                <button id="export_btn_excel" class="btn btn-info mx">Excel</button>
            </div>
        </div>
        <div class="card-body">

            <table class="table table-bordered">
                <thead>
                <tr>
                    <td colspan="2" class="text-center"> বেসরকারী শিক্ষা প্রতিষ্ঠান শিক্ষক-কর্মচারী অবসর সুবিধা
                        বোর্ড,শিক্ষা মন্ত্রণালয়
                    </td>
                </tr>
                <tr>
                    <td colspan="2" class="text-center">শিক্ষক-কর্মচারীদের অবসর সুবিধা ভাতা প্রদানে সম্ভাব্য ঘাটতি</td>
                </tr>
                <tr>
                    <td colspan="2" class="text-center">২০২০-২০২১ অর্থ বছরে স্কুল নভেম্বর'১৭-সেপ্টেম্বর’১৮,কলেজ
                        অক্টোবর'১৭-ডিসেম্বর’১৮ ও মাদ্রাসা জানুয়ারী'১৮-আগষ্ট’১৮ পর্যন্ত ৭৯০৩ টি আবেদন নিষ্পত্তি করে
                        ৭৯৭,৪৫,৩৭,৩২৯ টাকা প্রদান করা হয়েছে।
                    </td>
                </tr>
                <tr>
                    <th>
                        নিষ্পত্তির জন্য অপেক্ষমান
                    </th>
                    <th style="width: 20%">আবেদন সংখ্যা</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>স্কুল ক্যাটাগরী : অক্টোবর’১৮ হতে জুন’২২ সাল পর্যন্ত =45 মাস ´ প্রতিমাসে ৩৯৫টি</td>
                    <td> 17,775</td>
                </tr>
                <tr>
                    <td>কলেজ ক্যাটাগরী : জানু’১৯ হতে জুন’২২ সাল পর্যন্ত =৪২ মাস × প্রতিমাসে ১৬১টি</td>
                    <td> 6,762</td>
                </tr>
                <tr>
                    <td>মাদ্রাসা ক্যাটাগরী : সেপ্টেম্বর’১৮ হতে জুন’২২ সাল পর্যন্ত =4৬ মাস ´প্রতিমাসে ২৮০টি</td>
                    <td> 12,880</td>
                </tr>
                <tr>
                    <td>কারিগরী ক্যাটাগরী : সম্ভাব্য মোট সংখ্যা ধরা হয়েছে</td>
                    <td> 80</td>
                </tr>
                <tr>
                    <td>মোট আবেদন সংখ্যা</td>
                    <td> 37,497</td>
                </tr>
                <tr>
                    <th>বিবরণ</th>
                    <th>টাকার পরিমাণ</th>
                </tr>
                <tr>
                    <td>অবসরকালীন শিক্ষকগণের আর্থিক ,মানসিক ও শারিরীক অবস্থা বিবেচনা করে চলমান ২০২১-২০২২ অর্থ বছরের ৩০শে
                        জুন পর্যন্ত হিসাব করে প্রাপ্ত ৩৭,৪৯৭ টি আবেদন নিষ্পত্তি করা প্রয়োজন।
                        স্কুল/কলেজ/মাদ্রাসা/কারিগরি ক্যাটাগরীর সকল শিক্ষা প্রতিষ্ঠানের অবসর সুবিধা প্রদানের গড় অনুযায়ী
                        প্রতিটি আবেদন ১১,৭১,৯২২ (৫% হারে ৩টি ইনক্রিমেন্টসহ) টাকা হিসেবে ৩৭,৪৯৭ টি আবেদন নিষ্পত্তির জন্য
                        প্রয়োজন
                    </td>
                    <td> 43,943,559,234.00</td>
                </tr>
                <tr>
                    <td>বোর্ডের অফিস পরিচালনা ব্যয়ের পর জুন ২০২২ পর্যন্ত শিক্ষক কর্মচারীদের অবসর সুবিধা ভাতা প্রদান করা
                        যাবে (1119,39,96,32০-সম্ভাব্য অনুদান ১০০,০০,০০,০০০)
                    </td>
                    <td> 10,193,996,320.00</td>
                </tr>
                <tr>
                    <td>সুতরাং হালনাগাদ অবসর সুবিধা ভাতা পরিশোধের জন্য অর্থের প্রয়োজন</td>
                    <td> 33,749,562,914.00</td>
                </tr>
                </tbody>
            </table>

        </div>
    </div>
@endsection

@push('scripts')
    <script>

        $(document).on('click', '#export_btn_pdf', function () {

            let url = window.location.pathname+`?export=true&type=pdf`

            window.open(url, '_blank');

        })

        $(document).on('click', '#export_btn_excel', function () {

            let url = window.location.pathname+`?export=true&type=excel`

            window.open(url, '_blank');

        })

    </script>
@endpush
