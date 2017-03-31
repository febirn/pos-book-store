<?php

namespace App\Controllers;


use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Models\ProductModel;
use App\Models\CategoryModel;

class ProductController extends AbstractController
{
    public function index($request, $response)
	{
		return $this->view->render($response, 'products/list.twig');
	}

    public function getAdd(Request $request, Response $response)
    {
        return $this->view->render($response, 'products/add.twig');
    }

    public function postAdd(Request $request, Response $response)
	{
        $storage = new \Upload\Storage\FileSystem('assets/image');
        $image = new \Upload\File('image',$storage);
        $image->setName(uniqid());
        $image->addValidations(array(
            new \Upload\Validation\Mimetype(array('image/png', 'image/gif',
            'image/jpg', 'image/jpeg')),
            new \Upload\Validation\Size('5M')
        ));

        $data = array(
          'name'       => $image->getNameWithExtension(),
          'extension'  => $image->getExtension(),
          'mime'       => $image->getMimetype(),
          'size'       => $image->getSize(),
          'md5'        => $image->getMd5(),
          'dimensions' => $image->getDimensions()
      );


		$product = new ProductModel($this->db);

		$this->validation->rule('required', ['title', 'short_desc', 'price', 'category_id']);

        $this->validation->labels([
            'title'	       => 'Judul',
			'short_desc'   => 'Deskripsi',
			'price'		   => 'Harga',
			'category_id'  => 'Kategori',
			'image'  	   => 'Gambar',
		]);

		if ($this->validation->validate()) {
            $image->upload();
			$product->add($request->getParams(), $data['name']);

			return $response->withRedirect($this->router
							->pathFor('product.add'));

			$this->flash->addMessage('succes', 'Data successfully added');

			return $response->withRedirect($this->router
							->pathFor('product.add'));
		} else {
			$_SESSION['old'] = $request->getParams();
			$_SESSION['errors'] = $this->validation->errors();
			return $response->withRedirect($this->router
							->pathFor('product.add'));
		}
    }

    public function getActiveProduct(Request $request, Response $response, $arg)
    {
        $product = new ProductModel($this->db);
        $data['product'] = $product->getAll();
        return $this->view->render($response, 'products/list-active.twig', $data);
    }

    public function getInactiveProduct(Request $request, Response $response, $arg)
    {
        $product = new ProductModel($this->db);
        $product_list = $product->getInactive();
        $data['product'] = $product_list;
        return $this->view->render($response, 'products/list-inactive.twig',
        	$data);
    }

    public function setActive(Request $request, Response $response, $args)
	{
		$product = new ProductModel($this->db);
		$product_restore = $product->restoreData($args['id']);
		return $response->withRedirect($this->router
						->pathFor('product.inactive'));
	}

    public function getEdit(Request $request, Response $response, $args)
    {
        $product = new ProductModel($this->db);
        $data['product'] = $product->find('id', $args['id']);

        $category = new CategoryModel($this->db);
		$categoryAll = $category->getAll();
		$data['category'] = $categoryAll;

        return $this->view->render($response, 'products/edit.twig', $data);
    }

    public function setUpdate(Request $request, Response $response, $args)
	{
		$product = new ProductModel($this->db);

        $this->validation->rule('required', ['title', 'short_desc', 'price', 'category_id', 'image']);

        $this->validation->labels([
            'title'	       => 'Judul',
			'short_desc'   => 'Deskripsi',
			'price'		   => 'Harga',
			'category_id'  => 'Kategori',
			'image'  	   => 'Gambar',
		]);

		if ($this->validation->validate()) {
			$product->updateData($request->getParams(), $args['id']);
			return $response->withRedirect($this->router->pathFor('product.active'));
		} else {
			$_SESSION['old'] = $request->getParams();
			$_SESSION['errors'] = $this->validation->errors();
			return $response->withRedirect($this->router->pathFor('product.edit', ['id' => $args['id']]));
		}
    }

    public function setDelete(Request $request, Response $response)
	{
         foreach ($request->getParam('product') as $key => $value) {
            $product = new ProductModel($this->db);
            $product_del = $product->hardDelete($value);
        }

		return $response->withRedirect($this->router
						->pathFor('product.inactive'));
	}

    public function setInactive(Request $request, Response $response)
	{
         foreach ($request->getParam('inactive') as $key => $value) {
            $product = new ProductModel($this->db);
            $product_del = $product->softDelete($value);
        }

		return $response->withRedirect($this->router
						->pathFor('product.active'));
	}
}
