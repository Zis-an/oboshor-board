@component('components.bootstrap-modal',  ['title' => 'Edit Transaction', 'size' => 'xl',])
    <div>
        {!! Form::open(['url' => route('lot-item-transactions.update', [$lotItemId, $transaction->id]), 'method' => 'put', 'id' => 'transaction_edit_form']) !!}
        <div class="row">
            <div class="col-12">
            <div class="form-group">
                    <label class="control-label">Date*</label>
                    <input type="text"
                           class="form-control date-time-picker"
                           name="date"
                           value="{{ $transaction->date }}"
                           required
                    />
                </div>
                <div class="col-12 mt-4">
                    <button class="btn btn-primary float-right">Submit</button>
                </div>
            </div>
        </div>
        {!! Form::close() !!}
    </div>
@endcomponent
