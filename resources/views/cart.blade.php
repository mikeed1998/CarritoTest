@extends('layout')

@section('content')
    <table id="cart" class="table table-hover table-condensed">
        <thead>
            <tr>
                <th style="width:50%">Product</th>
                <th style="width:10%">Price</th>
                <th style="width:8%">Quantity</th>
                <th style="width:22%" class="text-center">Subtotal</th>
                <th style="width:10%"></th>
            </tr>
        </thead>
        <tbody>
            @php $total = 0 @endphp
            @if(session('cart'))
                @foreach(session('cart') as $id => $details)
                    @php $total += $details['price'] * $details['quantity'] @endphp
                    <tr data-id="{{ $id }}">
                        <td data-th="Product">
                            <div class="row">
                                <div class="col-sm-3 hidden-xs"><img src="{{ asset('img') }}/{{ $details['photo'] }}" width="100" height="100" class="img-responsive"/></div>
                                <div class="col-sm-9">
                                    <h4 class="nomargin">{{ $details['product_name'] }}</h4>
                                </div>
                            </div>
                        </td>
                        <td data-th="Price">${{ $details['price'] }}</td>
                        <td data-th="Quantity">
                            <input type="number" value="{{ $details['quantity'] }}" class="form-control quantity cart_update" data-row-id="{{ $id }}" min="1" />
                        </td>

                        <td data-th="Subtotal" class="text-center">
                            <span id="subtotal_{{ $id }}">${{ $details['price'] * $details['quantity'] }}</span>
                        </td>

                        <td class="actions" data-th="">
                            <button class="btn btn-danger btn-sm cart_remove"><i class="fa fa-trash-o"></i> Delete</button>
                        </td>
                    </tr>
                @endforeach
            @endif
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5" class="text-right"><h3><p>Total <strong id="cartTotal">${{ $total }}</strong></p></h3></td>
            </tr>
            <tr>
                <td colspan="5" class="text-right">
                    <a href="{{ url('/') }}" class="btn btn-danger"> <i class="fa fa-arrow-left"></i> Continue Shopping</a>
                    <button class="btn btn-success" id="checkoutBtn"><i class="fa fa-money"></i> Checkout</button>
                </td>
            </tr>
        </tfoot>
    </table>
@endsection

@section('scripts')
    <script type="text/javascript">
        $(".cart_update").change(function (e) {
            e.preventDefault();

            var ele = $(this);
            var rowId = ele.data("row-id");
            var quantity = ele.val();

            $.ajax({
                url: '{{ route('update_cart') }}',
                method: "patch",
                data: {
                    _token: '{{ csrf_token() }}',
                    id: rowId,
                    quantity: quantity
                },
                success: function (response) {
                    // Actualiza la subtotal directamente
                    var formattedSubtotal = '$' + parseFloat(response.subtotal).toFixed(2);
                    $('#subtotal_' + rowId).text(formattedSubtotal);

                    // Muestra notificación Toastr en éxito
                    toastr.success('Cart successfully updated!', 'Success');

                    // Llama a la función para actualizar el total
                    updateTotal(response.total);
                },
                error: function (xhr, status, error) {
                    // Muestra notificación Toastr en error de la solicitud AJAX
                    toastr.error('An error occurred while processing your request.', 'Error');
                    console.error(xhr.responseText);
                }
            });

            $(document).trigger('cartUpdated');
        });

        // Función para actualizar el total
        function updateTotal(newTotal) {
            // Actualiza el total en la vista
            $('#cartTotal').text('$' + parseFloat(newTotal).toFixed(2));
            console.log('Total updated:', newTotal);
        }

        $(".cart_remove").click(function (e) {
    e.preventDefault();

    var ele = $(this);
    var rowId = ele.parents("tr").attr("data-id");

    if (confirm("Do you really want to remove?")) {
        $.ajax({
            url: '{{ route('remove_from_cart') }}',
            method: "DELETE",
            data: {
                _token: '{{ csrf_token() }}',
                id: rowId
            },
            success: function (response) {
                // Remueve la fila de la tabla directamente
                ele.parents("tr").remove();

                // Obtiene el subtotal del producto eliminado y verifica que sea un número
                var subtotalRemoved = parseFloat($('#subtotal_' + rowId).text().replace('$', '')) || 0;

                // Verifica que el total actual sea un número
                var currentTotal = parseFloat($('#cartTotal').text().replace('$', '')) || 0;

                // Calcula el nuevo total restando el subtotal del producto eliminado
                var newTotal = currentTotal - subtotalRemoved;

                console.log('New Total:', newTotal);

                // Actualiza el total en la vista
                updateTotal(newTotal);

                // Muestra notificación Toastr en éxito
                toastr.success('El producto ha sido vaciado del carrito!', 'Success');

                // Recarga la página después de eliminar el producto
                location.reload();
            },
            error: function (xhr, status, error) {
                // Muestra notificación Toastr en error de la solicitud AJAX
                toastr.error('Hubo un problema para eliminar.', 'Error');
                console.error(xhr.responseText);
            }
        });
    }
    $(document).trigger('cartUpdated');
});


    </script>
@endsection
