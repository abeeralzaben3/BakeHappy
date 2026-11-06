<?php
class OrderController extends Controller
{
    public function __construct()
    {
        Auth::requireLogin();
    }

    public function index(): void
    {
        $orders = (new Order())->byUser(Auth::id());
        $this->view('user/orders/index', compact('orders'));
    }

    public function receipt($id = 0): void
    {
        $order = (new Order())->find((int) $id);

        // Only the owner (or an admin) can see an order
        if (!$order || ($order['user_id'] != Auth::id() && !Auth::isAdmin())) {
            $this->notFound();
        }

        $items = (new OrderItem())->byOrder($order['id']);
        $this->view('user/orders/receipt', compact('order', 'items'));
    }
}
