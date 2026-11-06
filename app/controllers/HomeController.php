<?php
class HomeController extends Controller
{
    public function index(): void
    {
        $categories = (new Category())->all();
        $products   = (new Product())->latest(8);
        $this->view('user/home/index', compact('categories', 'products'));
    }

    public function ourStory(): void
    {
        $this->view('user/pages/our_story');
    }
}
