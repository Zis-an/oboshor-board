@extends('layouts.app')

@section('main')

    <div>
        @include('partials.error-alert', ['errors' => $errors])
    </div>


    <div class="card">
        <div class="card-body">
            <div>
                {!! Form::open(['url' => route('item-requests.store')]) !!}
                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            {!! Form::label('title', 'Title', ['class' => 'control-label']) !!}
                            {!! Form::text('title', '', ['class' => 'form-control', 'rows' => '3']) !!}
                        </div>
                    </div>
                    <div class="col-12">
                        <table class="table table-bordered" id="request_items_table">
                            <thead>
                            <tr>
                                <th>Item</th>
                                <th>Quantity</th>
                                <th>Priority</th>
                            </tr>
                            </thead>
                            <tbody>
                            @include('item.item-request.row', ['items' => $items, 'index' =>0])
                            </tbody>
                            <tfoot>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>
                                    <button id="add_more" class="btn btn-primary" type="button">
                                        <i class="fa fa-plus"></i>
                                    </button>
                                </td>
                            </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="col-12">
                        <button class="btn btn-primary">
                            Submit
                        </button>
                    </div>

                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script type="text/javascript">
        $(document).ready(function () {
            $('#add_more').click(function () {
                console.log('add more');
                let index = $('#request_items_table tbody').find('tr:last>input[name="index"]').val();
                console.log({index});
                $.ajax(
                    {
                        method: 'post',
                        url: `/add-request-item-row`,
                        data: {index},
                        success: function (html) {
                            $('#request_items_table tbody').append(html);
                        }
                    }
                );
            })

            $(document).on('click', '.remove-item-btn', function () {
                $(this).closest('tr').remove();
            })
        })
    </script>

@endpush
