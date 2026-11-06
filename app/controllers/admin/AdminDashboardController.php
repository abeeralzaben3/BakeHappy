<?php
class AdminDashboardController extends Controller
{
    public function __construct()
    {
        Auth::requireAdmin();
    }

    public function index(): void
    {
        $orders = new Order();
        $stats = [
            'revenue'   => $orders->revenue(),
            'orders'    => $orders->count(),
            'products'  => (new Product())->count(),
            'customers' => (new User())->countCustomers(),
        ];
        $recent = $orders->recent(5);
        $this->view('admin/dashboard/index', compact('stats', 'recent'));
    }
}
