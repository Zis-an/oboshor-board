@extends('layouts.app')


@section('main')
    <div>
        <table class="table table-bordered" id="issue_items_table">
            <thead>
            <tr>
                <th>Date</th>
                <th>
                    Item
                </th>
                <th>Issued Quantity
                <th>Issued For</th>
            </tr>
            </thead>
        </table>
    </div>
@endsection

@push('scripts')
    <script type="text/javascript">
        $(document).ready(function () {
            $('#issue_items_table').DataTable({
                ajax: {
                    url: '/issue-inventory-items'
                },
                columns: [
                    {data: 'date'},
                    {data: 'item_name'},
                    {data: 'quantity'},
                    {data: 'issued_for'},
                ]
            })
        })
    </script>
@endpush
