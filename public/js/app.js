let datatable = null;

let ranges = {
    'This Financial Year': [moment('2023-07-01'), moment('2024-06-30')],
    'Last Financial Year': [moment('2022-07-01'), moment('2023-06-30')],
    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
    'This Month': [moment().startOf('month'), moment().endOf('month')],
    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
}

$(document).ready(function () {
    /*
    $('.date-time-picker').datetimepicker({
        format: 'yyyy-MM-DD HH:mm:ss',
        icons: {
            time: "fa fa-clock",
            date: "fa fa-calendar",
            up: "fa fa-caret-up",
            down: "fa fa-caret-down",
            previous: "fa fa-caret-left",
            next: "fa fa-caret-right",
            today: "fa fa-today",
            clear: "fa fa-clear",
            close: "fa fa-close"
        }
    })
    */
    datePicker();
    formatNumber();
    $('.select2-search').select2({});

})

function submitAjaxForm(form, callback) {

    console.log(form);

    /*$(form).find('button[type="submit"]')
        .attr('disabled', true);*/
    //use form data instead of serialize in file upload

    const data = new FormData(form);

    //console.log({formData});

    $.ajax({
        method: 'POST',
        url: $(form).attr('action'),
        dataType: 'json',
        data: data,
        processData: false,
        contentType: false,
        success: function (response) {

            let {status, message} = response;

            if (status === 'success') {
                toastr.success(message);
                callback()
            } else {
                toastr.error(message);
            }
        },
    });

}

function parseOrReturnZeroIfNull(amount) {
    let floatAmount = parseFloat(amount);
    if (isNaN(amount)) {
        return 0
    } else {
        return floatAmount;
    }

}

function datePicker() {
    $('.date-time-picker').datetimepicker({
        format: 'yyyy-MM-DD HH:mm:ss',
        icons: {
            time: "fa fa-clock",
            date: "fa fa-calendar",
            up: "fa fa-caret-up",
            down: "fa fa-caret-down",
            previous: "fa fa-caret-left",
            next: "fa fa-caret-right",
            today: "fa fa-today",
            clear: "fa fa-clear",
            close: "fa fa-close"
        }
    })
}

function formatNumber(number) {
    let formated = new Intl.NumberFormat('en-US', {maximumSignificantDigits: 2}).format(number);
    $('.format-number').text(formated)
}

//data table default setup
jQuery.extend($.fn.dataTable.defaults, {
    //Uncomment below line to enable save state of datatable.
    //stateSave: true,
    //serverSide: true,
    sorting: [],
    aaSorting: [],
    initComplete: function () {
        datatable = this.api();
    }
});

//reload datatable
function reloadDatatable() {
    if (datatable) {
        datatable.ajax.reload();
        return;
    }
    window.location.reload();
}

$(document).on('click', '.delete-item-btn', function () {

    let url = $(this).data('href');

    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#4e90bd',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: url,
                method: 'delete',
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (res) {
                    toastr.success(res.message);
                    reloadDatatable();
                },
                error: function (er) {
                    console.log(er)
                }
            });

        }
    })
});
