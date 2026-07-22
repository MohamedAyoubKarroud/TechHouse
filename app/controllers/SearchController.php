<?php

class SearchController extends Controller
{
    public function index()
    {
        $keyword = isset($_GET['q']) ? trim($_GET['q']) : '';

        $filters = array(
            'category_id' => isset($_GET['category_id']) ? $_GET['category_id'] : null,
            'brand'       => isset($_GET['brand'])       ? $_GET['brand']       : null,
            'min_price'   => isset($_GET['min_price'])   ? $_GET['min_price']   : '',
            'max_price'   => isset($_GET['max_price'])   ? $_GET['max_price']   : '',
        );

        $productModel  = $this->model('Product');
        $categoryModel = $this->model('Category');

        if ($keyword != '') {
            $results = $productModel->search($keyword, $filters);
        } else {
            $results = array();
        }

        if ($keyword != '') {
            $titleKw = $keyword;
        } else {
            $titleKw = '...';
        }

        $this->view('search/results', array(
            'title'      => 'Recherche : ' . $titleKw,
            'keyword'    => $keyword,
            'filters'    => $filters,
            'results'    => $results,
            'categories' => $categoryModel->all(),
            'brands'     => $productModel->distinctBrands(),
        ));
    }
}
