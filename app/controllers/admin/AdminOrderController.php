<?php
class AdminOrderController extends Controller
{
    private Order $orders;

    public function __construct()
    {
        Auth::requireAdmin();
        $this->orders = new Order();
    }

    public function index(): void
    {
        $status = $_GET['status'] ?? '';
        if (!in_array($status, Order::STATUSES, true)) {
            $status = '';
        }
        $orders = $this->orders->all($status);
        $statuses = Order::STATUSES;
        $this->view('admin/orders/index', compact('orders', 'status', 'statuses'));
    }

    public function show($id = 0): void
    {
        $order = $this->orders->find((int) $id);
        if (!$order) {
            $this->notFound();
        }
        $items    = (new OrderItem())->byOrder($order['id']);
        $statuses = Order::STATUSES;
        $payments = Order::PAYMENTS;
        $this->view('admin/orders/show', compact('order', 'items', 'statuses', 'payments'));
    }

    public function update($id = 0): void
    {
        $order = $this->orders->find((int) $id);
        if (!$order) {
            $this->notFound();
        }

        if ($this->isPost()) {
            $status  = $_POST['status'] ?? '';
            $payment = $_POST['payment_status'] ?? '';

            if (in_array($status, Order::STATUSES, true) && in_array($payment, Order::PAYMENTS, true)) {
                $this->orders->updateStatus($order['id'], $status, $payment);
                Session::flash('success', 'Order updated.');
            } else {
                Session::flash('error', 'Invalid status.');
            }
        }
        $this->redirect('/admin/order/show/' . $order['id']);
    }
}
