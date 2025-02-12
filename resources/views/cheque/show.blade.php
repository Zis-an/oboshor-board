@extends('layouts.app')

@section('main')
    <section class="content-header">
        <div class="container-fluid">
            <div class="d-flex align-items-center justify-content-between">
                <h4>Cheque Details</h4>
            </div>
        </div>
    </section>

    @include('partials.error-alert')

    <div class="card">
        <div class="card-body">
            <table class="table table-bordered">
                <tr>
                    <th>Cheque Number</th>
                    <td>{{$cheque->number}}</td>
                </tr>
                <tr>
                    <th>Amount</th>
                    <td>{{$cheque->amount}}</td>
                </tr>
                <tr>
                    <th>{{$cheque->type == 'received' ? 'Received Date': "Issue Date"}}</th>
                    <td>{{\Carbon\Carbon::parse($cheque->issue_date)->format('d-m-Y h:i A')}}</td>
                </tr>
                <tr>
                    <th>Deposited To</th>
                    <td>
                        {{$cheque->transaction->account->account_no ?? ''}}
                    </td>
                </tr>
                @if (!empty($cheque->transaction) && $cheque->transaction->status == 'final')
                    <tr>
                        <th>Transaction</th>
                        <th>{{$cheque->transaction->date}}</th>
                    </tr>
                    <tr>
                        <th>Transaction Id</th>
                        <td>
                            <a href="{{route('expenses.show',$cheque->transaction->id)}}">#{{$cheque->transaction->id}}</a>
                        </td>
                    </tr>
                @endif

                @if($cheque->type == 'received')
                    <tr>
                        <th>Head</th>
                        <td>{{$cheque->head->name ?? ''}}</td>
                    </tr>
                    <tr>
                        <th>Head Item</th>
                        <td>{{$cheque->headItem->name}}</td>
                    </tr>
                    <tr>
                        <th>Received From</th>
                        <td>{{$cheque->receivedFrom->name ?? ''}}</td>
                    </tr>
                @endif

                <tr>
                    <th>File</th>
                    <td>
                        @if($cheque->file)
                            <a href="{{asset($cheque->file)}}" class="btn btn-info" target="_blank">View File</a>
                        @else
                            No File
                            <button id="attach_file" class="btn btn-primary">Attach File</button>
                        @endif
                    </td>
                </tr>
            </table>

            <div class="d-none" id="file_upload_panel">
                <form action="{{route('cheques.attach-file', $cheque->id)}}" method="post" enctype="multipart/form-data">
                    @csrf
                    <h4>Add File</h4>
                    <hr/>
                    <div class="row">
                        <div class="col-sm-6">
                            <input type="file" name="file" id="file" class="form-control"
                                   accept="application/pdf, image/jpg, image/png, image/jpeg"
                            >
                        </div>
                        <div>
                            <button class="btn btn-primary" type="submit">Upload</button>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>

@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            $('#attach_file').click(function () {
                $('#file_upload_panel').removeClass('d-none')
            })
        })
    </script>
@endpush
