<?php
class CartController extends Controller
{
    public function index(): void
    {
        $items = Cart::items();
        $total = Cart::total($items);
        $this->view('user/cart/index', compact('items', 'total'));
    }

    public function add($id = 0): void
    {
        if (!$this->isPost()) {
            $this->redirect('/shop');
        }

        $product = (new Product())->find((int) $id);
        if (!$product || $product['stock'] < 1) {
            Session::flash('error', 'This product is not available.');
            $this->redirect('/shop');
        }

        $qty = max(1, (int) ($_POST['qty'] ?? 1));
        Cart::add((int) $id, $qty);
        Session::flash('success', 'Added to cart.');
        $this->redirect('/cart');
    }

    // The cart form sends qty[product_id] = number
    public function update(): void
    {
        if ($this->isPost()) {
            foreach ($_POST['qty'] ?? [] as $id => $qty) {
                Cart::update((int) $id, (int) $qty);
            }
        }
        $this->redirect('/cart');
    }

    public function remove($id = 0): void
    {
        if ($this->isPost()) {
            Cart::remove((int) $id);
        }
        $this->redirect('/cart');
    }
}
