@extends('layouts.app')

@section('main')

    <section class="content-header d-flex justify-content-between">
        <h4>Sent Requests</h4>
        <a class="btn btn-primary" href="{{route('item-requests.create')}}">Create</a>
    </section>

    <section class="content">
        <div class="card">
            <div class="card-body">
                <table class="table table-bordered" id="requests_table">
                    <thead>
                    <tr>
                        <th>Date</th>
                        <th>Title</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script type="text/javascript">
        $(document).ready(function () {
            $('#requests_table').DataTable({
                ajax: {
                    url: "/item-requests",
                },
                columns: [
                    {data: 'title'},
                    {data: "date"},
                    {data: 'action'}
                ]
            });
        })
    </script>
@endpush
