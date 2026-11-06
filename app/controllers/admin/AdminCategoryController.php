?php
class AdminCategoryController extends Controller
{
    private Category $categories;

    public function __construct()
    {
        Auth::requireAdmin();
        $this->categories = new Category();
    }

    public function index(): void
    {
        $categories = $this->categories->all();
        $this->view('admin/categories/index', compact('categories'));
    }

    public function create(): void
    {
        $errors = [];
        $old = [];

        if ($this->isPost()) {
            $old = $this->input();
            if ($old['name'] === '') {
                $errors[] = 'Name is required.';
            }
            $image = null;
            if (!$errors) {
                [$image, $imageError] = ImageUploader::upload($_FILES['image'] ?? [], 'categories');
                if ($imageError) {
                    $errors[] = $imageError;
                }
            }
            if (!$errors) {
                $this->categories->create($old['name'], $old['description'], $image);
                Session::flash('success', 'Category created.');
                $this->redirect('/admin/category');
            }
        }

        $this->view('admin/categories/create', compact('errors', 'old'));
    }

    public function edit($id = 0): void
    {
        $category = $this->categories->find((int) $id);
        if (!$category) {
            $this->notFound();
        }

        $errors = [];
        $old = $category;

        if ($this->isPost()) {
            $old = $this->input();
            if ($old['name'] === '') {
                $errors[] = 'Name is required.';
            }
            $image = $category['image'];
            if (!$errors) {
                [$newImage, $imageError] = ImageUploader::upload($_FILES['image'] ?? [], 'categories');
                if ($imageError) {
                    $errors[] = $imageError;
                } elseif ($newImage) {
                    $image = $newImage;
                }
            }
            if (!$errors) {
                $this->categories->update($category['id'], $old['name'], $old['description'], $image);
                if ($image !== $category['image']) {
                    ImageUploader::delete($category['image']);
                }
                Session::flash('success', 'Category updated.');
                $this->redirect('/admin/category');
            }
            $old['id'] = $category['id'];
            $old['image'] = $category['image'];
        }

        $this->view('admin/categories/edit', compact('errors', 'old'));
    }

    public function delete($id = 0): void
    {
        if ($this->isPost()) {
            $category = $this->categories->find((int) $id);
            if ($category) {
                if ($this->categories->hasProducts($category['id'])) {
                    Session::flash('error', 'Cannot delete a category that has products.');
                } else {
                    $this->categories->delete($category['id']);
                    ImageUploader::delete($category['image']);
                    Session::flash('success', 'Category deleted.');
                }
            }
        }
        $this->redirect('/admin/category');
    }

    private function input(): array
    {
        return [
            'name'        => Validator::clean($_POST['name'] ?? ''),
            'description' => Validator::clean($_POST['description'] ?? ''),
        ];
    }
}
