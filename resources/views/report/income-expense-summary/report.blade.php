@extends('layouts.app')

@section('main')

    <section class="content-header">
        <h3>Income Expense Summary</h3>
    </section>

    <div class="card">
        <div class="card-header">
            <button id="export_btn_pdf" class="btn btn-success mx-1">PDF</button>
            <button id="export_btn_excel" class="btn btn-info mx">Excel</button>
        </div>
        <div class="card-body">

            <table class="table table-bordered">
                <thead>
                <tr>
                    <td colspan="4" class="text-center">
                        বেসরকারী শিক্ষা প্রতিষ্ঠান শিক্ষক-কর্মচারী অবসর সুবিধা বোর্ড
                    </td>
                </tr>
                <tr>
                    <td colspan="4" class="text-center">শিক্ষা মন্ত্রণালয়</td>
                </tr>
                <tr>
                    <td colspan="4" class="text-center">২০২2-২০২3 অর্থ বছরের বাজেট</td>
                </tr>
                <tr>
                    <th colspan="2">আয়</th>
                    <th colspan="2">ব্যয়</th>
                </tr>
                <tr>
                    <th>বিবরণ</th>
                    <th>টাকার পরিমাণ</th>
                    <th>বিবরণ</th>
                    <th>টাকার পরিমাণ</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>৩টি ব্যাংকে রক্ষিত 37টি FDR হিসাব হতে প্রাপ্ত নীট লভ্যাংশ 25,03,26,359 টাকার ৭৫% হিসেবে আয়
                        (পরিশিষ্ট-৩)
                        ***
                    </td>
                    <td> 187,744,769.25</td>
                    <td>১। শিক্ষক-কর্মচারীদের অবসর সুবিধা ভাতা প্রদান (সম্ভাব্যসহ)</td>
                    <td> 12,231,233,068.07</td>
                </tr>
                <tr>
                    <td>২। বিগত ২০২1-২০২2 অর্থ বছরের এসটিডি হিসেবে অব্যয়িত অর্থ (পরিশিষ্ট-২)</td>
                    <td> 1,507,674,420.57</td>
                    <td>২। বোর্ডে কর্মরত কর্মকর্তা/কর্মচারীদের সম্মানী,বেতন ও অন্যান্য ভাতাদি খাতে ব্যয়</td>
                    <td> 9,275,324.00</td>
                </tr>
                <tr>
                    <td>৩। ৫টি ব্যাংকে রক্ষিত ৯টি STD হিসাব হতে বাৎসরিক লভ্যাংশ (পরিশিষ্ট-৩)</td>
                    <td> 28,786,037.25</td>
                    <td>৩। বোর্ডের অফিস পরিচালনার ক্ষেত্রে সরবরাহ ও সেবা খাতে ব্যয়</td>
                    <td> 6,392,284.00</td>
                </tr>
                <tr>
                    <td>৪। শিক্ষক-কর্মচারীদের মাসিক বেতনের ৬% হারে প্রাপ্ত চাঁদা (67,70,82,778×৫%
                        ইনক্রিমেন্ট=71,09,36,917×১২মাস,পরি-৪)
                    </td>
                    <td> 8,531,243,003.00</td>
                    <td>৪। বোর্ডের অফিস পরিচালনার ক্ষেত্রে এপিএ,প্রশিক্ষণ ও প্রনোদনা খাতে ব্যয়</td>
                    <td> 380,000.00</td>
                </tr>
                <tr>
                    <td>৫। সরকার কর্তৃক প্রদত্ত সম্ভাব্য অনুদান (শিক্ষক কর্মচারীদের অবসর সুবিধা ভাতা প্রদানে)</td>
                    <td> 2,000,000,000.00</td>
                    <td>৫। বোর্ডের অফিস পরিচালনার ক্ষেত্রে উন্নয়ন খাতে ব্যয়</td>
                    <td> 8,450,000.00</td>
                </tr>
                <tr>
                    <th>৬। অন্যান্য উৎস হতে প্রাপ্তি</th>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td> (ক) নিয়োগ সংক্রান্ত ব্যাংক ড্রাফট হতে প্রাপ্তি (১০০০টি×২৫০/- হারে)</td>
                    <td> 250,000.00</td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>(খ) পুরাতন জিনিসপত্র বিক্রি হতে প্রাপ্তি</td>
                    <td> 31,800.00</td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td> (গ) প্রারম্ভিক তহবিল (হাতে নগদ,পরিশিষ্ট-২)</td>
                    <td> 646.00</td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>মোট টাকার পরিমাণ</td>
                    <td> 12,255,730,676.07</td>
                    <td>মোট টাকার পরিমাণ</td>
                    <td> 12,255,730,676.07</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script>

        $(document).on('click', '#export_btn_pdf', function () {

            let url = window.location.pathname + `?export=true&type=pdf`

            window.open(url, '_blank');

        })

        $(document).on('click', '#export_btn_excel', function () {

            let url = window.location.pathname + `?export=true&type=excel`

            window.open(url, '_blank');

        })

    </script>
@endpush
