<?php

class ProductController extends Controller
{
    public function index($categorySlug = null)
    {
        $productModel  = $this->model('Product');
        $categoryModel = $this->model('Category');

        $isNew = 0;
        if (isset($_GET['is_new'])) {
            $isNew = 1;
        }

        $filters = array(
            'category_id' => null,
            'brand'       => isset($_GET['brand'])     ? $_GET['brand']     : null,
            'color'       => isset($_GET['color'])     ? $_GET['color']     : null,
            'min_price'   => isset($_GET['min_price']) ? $_GET['min_price'] : '',
            'max_price'   => isset($_GET['max_price']) ? $_GET['max_price'] : '',
            'is_new'      => $isNew,
            'sort'        => isset($_GET['sort'])      ? $_GET['sort']      : 'newest',
        );

        $category = null;
        if ($categorySlug) {
            $category = $categoryModel->findBySlug($categorySlug);
            if ($category) {
                $filters['category_id'] = (int)$category['id'];
            }
        }

        if ($category) {
            $title = $category['name'];
        } else {
            $title = 'Tous les produits';
        }

        $this->view('products/index', array(
            'title'       => $title,
            'category'    => $category,
            'categories'  => $categoryModel->all(),
            'products'    => $productModel->filter($filters),
            'brands'      => $productModel->distinctBrands($filters['category_id']),
            'colors'      => $productModel->distinctColors($filters['category_id']),
            'priceBounds' => $productModel->priceBounds($filters['category_id']),
            'filters'     => $filters,
        ));
    }

    public function show($id)
    {
        $productModel = $this->model('Product');
        $product = $productModel->find((int)$id);
        if (!$product) {
            http_response_code(404);
            echo 'Produit introuvable.';
            return;
        }

        $this->model('Analytics')->recordProductView((int)$id, Auth::id());

        $this->view('products/show', array(
            'title'   => $product['name'],
            'product' => $product,
        ));
    }
}
