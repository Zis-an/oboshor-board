@extends('layouts.app')

@section('main')
    <section class="content-header">
        <div class="container-fluid">
            <h4 class="text-capitalize">Add {{request()->query('type')}} Budget</h4>
        </div>
    </section>

    <section class="card">

        @if ($errors->any())
            @foreach ($errors->all() as $error)
                <div>{{$error}}</div>
            @endforeach
        @endif

        {!! Form::open(['url' => route('budgets.store')]) !!}
        <div class="card-header">
            <div class="row">
                <div class="col-6">
                    {{Form::label('year_id', 'Financial Years')}}
                    {{Form::select('year_id', $financialYears, '', ['class' => 'form-control', 'placeholder' => 'Select Financail Year'])}}
                </div>
            </div>
        </div>
        <input type="hidden" name="type" value="{{request()->query('type')}}"/>
        <div id="budget_form"></div>
        {!! Form::close() !!}
    </section>
@endsection

@push('scripts')
    <script>

        $(document).ready(function () {

            $(document).on('change', '.sub-total', function(){

                let amount = 0;

                $('#budget_container .sub-total').each((a, el) => {
                    amount += Number($(el).val());
                })

                $('#total-amount').val(amount)
            })

            $(document).on('change', '.td-amount', function () {

                let amount = 0;

                let subTotalRow = $(this).closest('tr').prevAll(".sub-total-row:first");
                let subTotal = 0;
                $(subTotalRow).nextUntil('tr.sub-total-row').each(function(a, el){
                    subTotal += Number(($(el).find('.td-amount')).val());
                })

                $(subTotalRow).find('.sub-total').val(subTotal)

                //let dd = $(this).closest('.sub-total');

                //console.log({dd});

                console.log('ddd');

                $('#budget_container .sub-total').each((a, el) => {
                    amount += Number($(el).val());
                })

                console.log({amount})

                $('#total-amount').val(amount)
            })

            $('#year_id').on('change', function(){
                let id = this.value;
                $.ajax({
                    url: window.location.href,
                    data: {fy: id},
                    success: function(html){
                        $("#budget_form").html(html);
                    }
                })
            })

        })

    </script>
@endpush
