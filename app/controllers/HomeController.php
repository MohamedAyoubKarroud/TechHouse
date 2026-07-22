<?php

class HomeController extends Controller
{
    public function index()
    {
        $product  = $this->model('Product');
        $category = $this->model('Category');

        $this->view('home/index', array(
            'title'      => APP_NAME . ' — Music & Audio Equipment',
            'categories' => $category->all(),
            'featured'   => $product->newest(8),
        ));
    }
}
