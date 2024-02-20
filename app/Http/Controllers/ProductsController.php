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

            // dd('addToCart method called', $id, $product, $cart);

            if(isset($cart[$id])) {
                $cart[$id]['quantity']++;
            }  else {
                $cart[$id] = [
                    "product_name" => $product->product_name,
                    "photo" => $product->photo,
                    "price" => $product->price,
                    "quantity" => 1
                ];
            }

            session()->put('cart', $cart);

            $response = [
                'success' => true,
                'message' => 'Product added to cart successfully!',
                'cart_count' => count($cart),
            ];

            return response()->json($response);
        }

    
        public function update(Request $request)
        {
            if($request->id && $request->quantity){
                $cart = session()->get('cart');
                $cart[$request->id]["quantity"] = $request->quantity;
                session()->put('cart', $cart);
                session()->flash('success', 'Cart successfully updated!');
            }
        }
    
        public function remove(Request $request)
        {
            if($request->id) {
                $cart = session()->get('cart');
                if(isset($cart[$request->id])) {
                    unset($cart[$request->id]);
                    session()->put('cart', $cart);
                }
                session()->flash('success', 'Product successfully removed!');
            }
        }
    }