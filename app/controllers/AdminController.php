<?php
require_once APP_ROOT . '/app/services/AiCategorizer.php';

class AdminController extends Controller
{
    public function __construct()
    {
        $this->requireAdmin();
    }

    public function index()
    {
        $this->dashboard();
    }

    public function dashboard()
    {
        $analytics = $this->model('Analytics');
        $this->view('admin/dashboard', array(
            'title'          => 'Tableau de bord admin',
            'totalProducts'  => $this->model('Product')->count(),
            'totalUsers'     => $this->model('User')->count(),
            'totalOrders'    => $this->model('Order')->count(),
            'revenue'        => $this->model('Order')->revenue(),
            'totalVisits'    => $analytics->totalVisits(),
            'uniqueVisitors' => $analytics->uniqueVisitors(),
            'visits7d'       => $analytics->visitsLast7Days(),
            'topPages'       => $analytics->topPages(),
            'topProducts'    => $analytics->topProducts(),
            'countries'      => $analytics->visitorsByCountry(),
        ));
    }

    // ---------- Product management ----------
    public function products()
    {
        $this->view('admin/products', array(
            'title'    => 'Gérer les produits',
            'products' => $this->model('Product')->all(),
        ));
    }

    public function productNew()
    {
        $this->productForm();
    }

    public function productEdit($id = null)
    {
        $this->productForm((int)$id);
    }

    private function productForm($id = null)
    {
        $productModel  = $this->model('Product');
        $categoryModel = $this->model('Category');
        $product = null;
        if ($id) {
            $product = $productModel->find($id);
        }
        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Security::verifyCsrf();
            $data = array(
                'category_id' => isset($_POST['category_id']) ? $_POST['category_id'] : null,
                'name'        => isset($_POST['name'])        ? trim($_POST['name'])        : '',
                'slug'        => isset($_POST['slug'])        ? trim($_POST['slug'])        : '',
                'brand'       => isset($_POST['brand'])       ? trim($_POST['brand'])       : '',
                'color'       => isset($_POST['color'])       ? trim($_POST['color'])       : '',
                'description' => isset($_POST['description']) ? trim($_POST['description']) : '',
                'price'       => isset($_POST['price'])       ? $_POST['price']             : 0,
                'stock'       => isset($_POST['stock'])       ? $_POST['stock']             : 0,
                'image'       => null,
                'is_new'      => isset($_POST['is_new']) ? 1 : 0,
            );

            // Optional file upload (extension-based check).
            if (isset($_FILES['image']) && !empty($_FILES['image']['tmp_name'])) {
                $data['image'] = $this->handleUpload($_FILES['image']);
            } elseif ($product) {
                $data['image'] = $product['image'];
            }

            // AI auto-categorization (sets ai_tags + may override category).
            $ai = AiCategorizer::categorize($data['name'], $data['description'], $data['image']);
            $data['ai_tags'] = $ai['tags'];
            if (empty($data['category_id']) && !empty($ai['category_id'])) {
                $data['category_id'] = $ai['category_id'];
            }

            if (!$data['category_id'] || $data['name'] === '' || (float)$data['price'] <= 0) {
                $error = 'La catégorie, le nom et le prix sont requis.';
            } else {
                if ($product) {
                    $productModel->update($product['id'], $data);
                } else {
                    $productModel->create($data);
                }
                $this->redirect('admin/products');
            }
        }

        if ($product) {
            $title = 'Modifier le produit';
        } else {
            $title = 'Nouveau produit';
        }

        $this->view('admin/product_form', array(
            'title'      => $title,
            'product'    => $product,
            'categories' => $categoryModel->all(),
            'error'      => $error,
        ));
    }

    public function productDelete($id = null)
    {
        $this->model('Product')->delete((int)$id);
        $this->redirect('admin/products');
    }

    // Extension-based upload validation (no mime_content_type).
    private function handleUpload($file)
    {
        $name_parts = explode(".", $file['name']);
        $ext = strtolower(end($name_parts));
        $allowed = array('jpg', 'jpeg', 'png', 'webp');
        if (!in_array($ext, $allowed)) {
            return null;
        }

        if (!is_dir(UPLOADS)) {
            mkdir(UPLOADS, 0775, true);
        }
        $newName = bin2hex(random_bytes(8)) . '.' . $ext;
        $dest = UPLOADS . '/' . $newName;
        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            return null;
        }
        return $newName;
    }

    // ---------- User management ----------
    public function users()
    {
        $this->view('admin/users', array(
            'title' => 'Gérer les utilisateurs',
            'users' => $this->model('User')->all(),
        ));
    }

    public function userRole()
    {
        Security::verifyCsrf();
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $rawRole = isset($_POST['role']) ? $_POST['role'] : 'client';
        if ($rawRole === 'admin') {
            $role = 'admin';
        } else {
            $role = 'client';
        }
        if ($id && $id !== Auth::id()) {
            $this->model('User')->updateRole($id, $role);
        }
        $this->redirect('admin/users');
    }

    public function userDelete($id = null)
    {
        $id = (int)$id;
        if ($id && $id !== Auth::id()) {
            $this->model('User')->delete($id);
        }
        $this->redirect('admin/users');
    }

    // ---------- Order supervision ----------
    public function orders()
    {
        $this->view('admin/orders', array(
            'title'  => 'Toutes les commandes',
            'orders' => $this->model('Order')->all(),
        ));
    }

    public function orderStatus()
    {
        Security::verifyCsrf();
        $id     = isset($_POST['id'])     ? (int)$_POST['id'] : 0;
        $status = isset($_POST['status']) ? $_POST['status']  : 'pending';
        $allowed = array('pending', 'paid', 'shipped', 'delivered', 'cancelled');
        if ($id && in_array($status, $allowed)) {
            $this->model('Order')->updateStatus($id, $status);
        }
        $this->redirect('admin/orders');
    }
}
