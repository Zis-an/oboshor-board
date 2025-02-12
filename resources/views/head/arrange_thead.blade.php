<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Reorder Table</title>
        <!-- Include SortableJS -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.14.0/Sortable.min.js"></script>
        <style>
            /* Optional: Add some basic styles */
            .sortable-row {
                margin: 10px;
                padding: 10px;
                background: #f9f9f9;
                border: 1px solid #ddd;
            }
        </style>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
              integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    </head>

    <body>
        <h3 class="text-center my-4">Reorder Table Rows</h3>

        <ul id="sortable-table">
            @foreach ($heads as $row)
            <li class="sortable-row" data-id="{{ $row->id }}">
                {{ $row->name ?? '' }}
            </li>
            @endforeach
        </ul>

        <button id="save-order" class="btn btn-info ml-5">Save Order</button>

        <script>
    // Initialize SortableJS to make the list draggable
    var el = document.getElementById('sortable-table');
    var sortable = new Sortable(el, {
        animation: 150,
        ghostClass: 'sortable-ghost',
    });

    // Save button click event to send the new order to the server
    document.getElementById('save-order').addEventListener('click', function () {
        var order = [];
        // Get the IDs of the reordered rows
        document.querySelectorAll('#sortable-table .sortable-row').forEach(function (item) {
            order.push(item.getAttribute('data-id'));
        });

        // Send the reordered IDs to the backend via AJAX
        fetch('{{ route('save-order') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                order: order
            })
        })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Order saved successfully!');
                        window.location.href = '/expense-heads';
                    } else {
                        alert('Failed to save order.');
                    }
                });
    });
        </script>
    </body>

</html>
