<?php

    namespace App\Http\Controllers;

    use Illuminate\Http\Request;
    use App\Product;

    class ProductsController extends Controller
    {
        public function index()
        {
            $products = Product::all();
            return view('products', compact('products'));
        }

        public function cart()
        {
            return view('cart');
        }

        // Cambia el método addToCart en ProductsController
        public function addToCart($id)
        {
            $product = Product::findOrFail($id);

            $cart = session()->get('cart', []);

            if (isset($cart[$id])) {
                $cart[$id]['quantity']++;
            } else {
                $cart[$id] = [
                    "product_name" => $product->product_name,
                    "photo" => $product->photo,
                    "price" => $product->price,
                    "quantity" => 1
                ];
            }

            session()->put('cart', $cart);

            $totalUnits = array_sum(array_column($cart, 'quantity'));

            $response = [
                'success' => true,
                'message' => 'Product added to cart successfully!',
                'cart_count' => $totalUnits,
                'total_units' => $totalUnits,  // Agrega esta línea para pasar $totalUnits
            ];

            return response()->json($response);
        }



        public function update(Request $request)
        {
            if ($request->id && $request->quantity) {
                $cart = session()->get('cart');
                $cart[$request->id]["quantity"] = $request->quantity;
                session()->put('cart', $cart);
                session()->flash('success', 'Cart successfully updated!');

                // Recalcular el precio y la cantidad antes de enviar la respuesta
                $subtotal = $cart[$request->id]["price"] * $request->quantity;

                // Calcular el total sumando los subtotales de todos los elementos en el carrito
                $total = 0;
                foreach ($cart as $item) {
                    $total += $item["price"] * $item["quantity"];
                }

                $response = [
                    'success' => true,
                    'message' => 'Cart successfully updated!',
                    'price' => $cart[$request->id]["price"],
                    'quantity' => $request->quantity,
                    'subtotal' => $subtotal,
                    'total' => $total,
                    'cart_count' => count($cart),
                ];

                return response()->json($response);
            }
        }


        public function remove(Request $request)
        {
            if ($request->id) {
                $cart = session()->get('cart');
                if (isset($cart[$request->id])) {
                    // Restar el subtotal del producto eliminado al total
                    $total = session('cartTotal', 0) - ($cart[$request->id]['price'] * $cart[$request->id]['quantity']);

                    // Eliminar el producto del carrito
                    unset($cart[$request->id]);

                    // Actualizar el total en la sesión
                    session()->put('cartTotal', $total);

                    // Actualizar la variable de sesión 'cart'
                    session()->put('cart', $cart);
                }

                session()->flash('success', 'Product successfully removed!');

                $response = [
                    'success' => true,
                    'message' => 'Product successfully removed!',
                    'total' => $total,
                    'cart_count' => count($cart),
                ];

                return response()->json($response);
            }
        }
    }
