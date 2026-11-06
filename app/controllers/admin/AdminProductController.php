<?php
class AdminProductController extends Controller
{
    private Product $products;
    private Category $categories;

    public function __construct()
    {
        Auth::requireAdmin();
        $this->products   = new Product();
        $this->categories = new Category();
    }

    public function index(): void
    {
        $products = $this->products->search();
        $this->view('admin/products/index', compact('products'));
    }

    public function create(): void
    {
        $errors = [];
        $old = [];

        if ($this->isPost()) {
            [$old, $errors] = $this->validate();
            $image = null;

            if (!$errors) {
                [$image, $imageError] = ImageUploader::upload($_FILES['image'] ?? [], 'products');
                if ($imageError) {
                    $errors[] = $imageError;
                }
            }
            if (!$errors) {
                $this->products->create($old, $image);
                Session::flash('success', 'Product created.');
                $this->redirect('/admin/product');
            }
        }

        $categories = $this->categories->all();
        $this->view('admin/products/create', compact('errors', 'old', 'categories'));
    }

    public function edit($id = 0): void
    {
        $product = $this->products->find((int) $id);
        if (!$product) {
            $this->notFound();
        }

        $errors = [];
        $old = $product;

        if ($this->isPost()) {
            [$old, $errors] = $this->validate();
            $image = $product['image'];

            if (!$errors) {
                [$newImage, $imageError] = ImageUploader::upload($_FILES['image'] ?? [], 'products');
                if ($imageError) {
                    $errors[] = $imageError;
                } elseif ($newImage) {
                    $image = $newImage;
                }
            }
            if (!$errors) {
                $this->products->update($product['id'], $old, $image);
                if ($image !== $product['image']) {
                    ImageUploader::delete($product['image']);
                }
                Session::flash('success', 'Product updated.');
                $this->redirect('/admin/product');
            }
            $old['id'] = $product['id'];
            $old['image'] = $product['image'];
        }

        $categories = $this->categories->all();
        $this->view('admin/products/edit', compact('errors', 'old', 'categories'));
    }

    public function delete($id = 0): void
    {
        if ($this->isPost()) {
            $product = $this->products->find((int) $id);
            if ($product) {
                $this->products->delete($product['id']);
                ImageUploader::delete($product['image']);
                Session::flash('success', 'Product deleted.');
            }
        }
        $this->redirect('/admin/product');
    }

    // Returns [cleanData, errors]
    private function validate(): array
    {
        $d = [
            'name'        => Validator::clean($_POST['name'] ?? ''),
            'description' => Validator::clean($_POST['description'] ?? ''),
            'price'       => $_POST['price'] ?? '',
            'stock'       => $_POST['stock'] ?? '',
            'category_id' => (int) ($_POST['category_id'] ?? 0),
        ];
        $e = [];

        if ($d['name'] === '') {
            $e[] = 'Name is required.';
        }
        if (!is_numeric($d['price']) || $d['price'] <= 0) {
            $e[] = 'Price must be a positive number.';
        }
        if (filter_var($d['stock'], FILTER_VALIDATE_INT) === false || $d['stock'] < 0) {
            $e[] = 'Stock must be a whole number (0 or more).';
        }
        if (!$this->categories->find($d['category_id'])) {
            $e[] = 'Choose a valid category.';
        }
        return [$d, $e];
    }
}
