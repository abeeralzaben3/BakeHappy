<?php
class ProductController extends Controller
{
    public function show($id = 0): void
    {
        $product = (new Product())->find((int) $id);
        if (!$product) {
            $this->notFound();
        }
        $this->view('user/product/show', compact('product'));
    }
}
