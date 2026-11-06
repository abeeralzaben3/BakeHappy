<?php
class CheckoutController extends Controller
{
    public function __construct()
    {
        Auth::requireLogin();
    }

    public function index(): void
    {
        if (Cart::isEmpty()) {
            $this->redirect('/cart');
        }
        $user = (new User())->find(Auth::id());
        $old = ['full_name' => $user['name'], 'phone' => $user['phone']];
        $this->showForm([], $old);
    }

    public function place(): void
    {
        if (!$this->isPost() || Cart::isEmpty()) {
            $this->redirect('/cart');
        }

        $data = [
            'full_name' => Validator::clean($_POST['full_name'] ?? ''),
            'phone'     => Validator::clean($_POST['phone'] ?? ''),
            'city'      => Validator::clean($_POST['city'] ?? ''),
            'address'   => Validator::clean($_POST['address'] ?? ''),
            'notes'     => Validator::clean($_POST['notes'] ?? ''),
        ];

        $errors = [];
        foreach (['full_name', 'phone', 'city', 'address'] as $field) {
            if ($data[$field] === '') {
                $errors[] = ucfirst(str_replace('_', ' ', $field)) . ' is required.';
            }
        }
        if (($_POST['payment_method'] ?? '') !== 'COD') {
            $errors[] = 'Please choose Cash on Delivery.';
        }
        if ($errors) {
            $this->showForm($errors, $data);
            return;
        }

        $items = Cart::items();
        try {
            $orderId = (new Order())->place(Auth::id(), $data, $items, Cart::total($items));
        } catch (Exception $e) {
            Session::flash('error', $e->getMessage());
            $this->redirect('/cart');
        }

        Cart::clear();
        $this->redirect('/order/receipt/' . $orderId);
    }

    private function showForm(array $errors, array $old): void
    {
        $items = Cart::items();
        $total = Cart::total($items);
        $this->view('user/checkout/index', compact('items', 'total', 'errors', 'old'));
    }
}
