<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Counter extends CI_Controller
{
	public function index()
	{
		$this->load->model('counter_model', 'counterModel');

		$this->load->library('form_validation');
		$this->form_validation->set_rules('product[]', 'Produk', 'required');
		$this->form_validation->set_rules('quantity[]', 'Jumlah', 'required');
		if ($this->form_validation->run())
		{
			$productIds = $this->input->post('product');
			$quantities = $this->input->post('quantity');

			$products = array();
			for ($i = 0; $i < count($productIds); $i++)
			{
				$productId = $productIds[$i];
				$quantity = intval($quantities[$i]);

				if (isset($products[$productId]))
					$products[$productId] += $quantity;
				else
					$products[$productId] = $quantity;
			}

			$this->counterModel->add($products);
		}

		$this->load->helper('form');

		$daftarProduk = $this->counterModel->getDaftarProduk();
		$daftarPenjualan = $this->counterModel->getHistoryPenjualan();

		$contentData = array(
			'daftarProduk' => $daftarProduk,
			'daftarPenjualan' => $daftarPenjualan
		);
		$content = $this->load->view('counter', $contentData, true);

		$templateData = array(
			'title' => 'Counter Warung BTI',
			'content' => $content
		);
		$this->load->view('template', $templateData);
	}
}
