<?php
class ShopController extends Controller
{
    public function index(): void
    {
        $search     = Validator::clean($_GET['q'] ?? '');
        $categoryId = (int) ($_GET['category'] ?? 0);

        $products   = (new Product())->search($search, $categoryId);
        $categories = (new Category())->all();

        $this->view('user/shop/index', compact('products', 'categories', 'search', 'categoryId'));
    }
}
