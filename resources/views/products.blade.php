@extends('layout')
    
@section('content')
     
<div class="row">
    @foreach($products as $product)
        <div class="col-xs-18 col-sm-6 col-md-4" style="margin-top:10px;">
            <div class="img_thumbnail productlist">
                <img src="{{ asset('img/'.$product->photo) }}" class="img-fluid">
                <div class="caption">
                    <h4>{{ $product->product_name }}</h4>
                    <p>{{ $product->product_description }}</p>
                    <p><strong>Price: </strong> ${{ $product->price }}</p>
                    <p class="btn-holder">
                        <a href="#" class="btn btn-primary btn-block text-center btn-add-to-cart" role="button" data-product-id="{{ $product->id }}">
                            Add to cart
                        </a>
                    </p>
                </div>
            </div>
        </div>
    @endforeach
</div>
     
@endsection

@section('scripts')
<script>
    // Mueve la función updateCartCount fuera del $(document).ready
    function updateCartCount(count) {
        console.log('Updating cart count to:', count);
        $('.cart-count').text(count);
    }

    // Mantén el resto del script como está
    $(".btn-add-to-cart").click(function (e) {
        e.preventDefault();

        var productId = $(this).data('product-id');

        $.ajax({
            url: 'add-to-cart/' + productId,
            method: "GET",
            success: function (response) {
                if (response.success) {
                    // Actualizar el contador del carrito
                    updateCartCount(response.cart_count);

                    // Actualizar el contador del carrito en el menú (agregando esto)
                    $('.badge-danger').text(response.cart_count);

                    // Mostrar notificación Toastr de éxito
                    toastr.success(response.message);
                } else {
                    // Mostrar notificación Toastr de error
                    toastr.error('Error adding product to cart.');
                }
            },
            error: function (xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    });
</script>
@endsection

