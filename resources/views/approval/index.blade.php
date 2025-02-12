@extends('layouts.app')

@section('main')
    <section class="content-header">
        <div class="container-fluid">
            <h4>Approval Requests</h4>
        </div>
    </section>

    <section class="content">
        @if(count($approvalRequests)>0)
        <div class="row">
            @foreach($approvalRequests as $approvalRequest)
                <div class="col-sm-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Request For {{$approvalRequest->type}}</h5>
                        </div>
                        <div class="card-body">
                            <div>
                                <div>Referred By: {{$approvalRequest->performedBy->name}}</div>
                                <div class="mt-2">Comment:</div>
                                <div>
                                    {{$approvalRequest->comment}}
                                </div>
                            </div>
                        </div>
                        <div class="card-footer d-flex justify-content-end">
                            <a href="{{route('approvals.detail', $approvalRequest->id)}}" class="btn btn-warning mx-1">View</a>

                            <button class="btn btn-primary mx-1 show-action-btn"
                                    data-href="{{route('approvals.show', $approvalRequest->id)}}">Action
                            </button>

                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        @else
        <div class="card">
            <div class="card-body">
                <h4 class="text-center">No Pending Approvals</h4>
            </div>
        </div>
        @endif
    </section>

    <!--View Modal -->
    <div class="modal fade" id="viewApprovalModal" tabindex="-1" aria-hidden="true">
    </div>

@endsection
@push('scripts')
    <script>
        $(document).ready(function () {
            $('.show-action-btn').on("click", function () {
                $('#viewApprovalModal').load($(this).data('href'), function () {
                    $(this).modal('show');
                });
            })

            $(document).on("click", '#approve_btn', function () {

                $('#approval_status').val('approved');

                $('#approval_form').submit();
            })

            $(document).on("click", '#reject_btn', function () {

                $('#approval_status').val('rejected');

                $('#approval_form').submit();
            })

        })
    </script>
@endpush
