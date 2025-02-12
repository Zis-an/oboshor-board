@extends('layouts.app')

@section('main')

    @include('partials.error-alert', ['errors' => $errors])

    <h4>Issue Item</h4>

    {!! Form::open(['url' => route('issue-inventory-items.store')]) !!}

    <div class="row">
        <div class="col-12">
            {!! Form::label('user_id', 'Select User', ['class' => 'control-label']) !!}
            {!! Form::select('user_id', $users, '', ['class' => 'form-control']) !!}
        </div>
        <div class="col-12">
            <table class="table table-bordered" id="issue_items_table">
                <thead>
                <tr>
                    <th>Item</th>
                    <th>Quantity</th>
                </tr>
                </thead>
                <tbody>
                @include('issue-item.row', ['items' => $items, 'index' =>0])
                </tbody>
                <tfoot>
                <tr>
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
            <button>Submit</button>
        </div>
    </div>

    {!! Form::close() !!}

@endsection

@push('scripts')
    <script type="text/javascript">
        $(document).ready(function () {
            $('#add_more').click(function () {
                console.log('add more');
                let index = $('#issue_items_table tbody').find('tr:last>input[name="index"]').val();
                console.log({index});
                $.ajax(
                    {
                        method: 'post',
                        url: `/add-issue-item-row`,
                        data: {index},
                        success: function (html) {
                            $('#issue_items_table tbody').append(html);
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
