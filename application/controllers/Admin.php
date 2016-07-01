<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller
{
	public function __construct ()
	{
		parent::__construct();
		$this->load->library('session');
	}

	public function index()
	{
		$this->load->model('counter_model', 'counterModel');
		$messages = $this->session->flashdata('messages');

		$daftarProduk = $this->counterModel->getDaftarProduk();
		$daftarPenjualan = $this->counterModel->getHistoryPenjualan();

		$this->load->helper('form');
		$contentData = array(
			'daftarProduk' => $daftarProduk,
			'daftarPenjualan' => $daftarPenjualan,
			'messages' => $messages
		);
		$content = $this->load->view('admin', $contentData, true);

		$templateData = array(
			'title' => 'Counter Warung BTI',
			'content' => $content
		);
		$this->load->view('template', $templateData);
	}

	public function addProduk()
	{
		$this->load->model('counter_model', 'counterModel');

		$messages = array();

		$this->load->library('form_validation');
		$this->form_validation->set_rules('product', 'Produk', 'required');
		$this->form_validation->set_rules('quantity', 'Jumlah', 'required');
		if ($this->form_validation->run())
		{
			$product = $this->input->post('product');
			$price = $this->input->post('price');
			$quantity = $this->input->post('quantity');

			$success = $this->counterModel->addProduk($product, $price, $quantity);

			$message = array('error' => !$success);
			if ($success)
				$message['text'] = 'Sukses menambah produk!';
			else
				$message['text'] = 'Gagal menambah produk: database error.';

			$messages[] = $message;
		}

		$this->showIndex($messages);
	}

	public function updateStokProduk ()
	{
		$this->load->model('counter_model', 'counterModel');

		$messages = array();

		$this->load->library('form_validation');
		$this->form_validation->set_rules('productId[]', 'Produk', 'required');
		$this->form_validation->set_rules('price[]', 'Harga', 'required');
		$this->form_validation->set_rules('quantity[]', 'Stok', 'required');
		if ($this->form_validation->run())
		{
			$productIds = $this->input->post('productId');
			$prices = $this->input->post('price');
			$quantities = $this->input->post('quantity');

			$products = array();
			for ($i = 0; $i < count($productIds); $i++)
			{
				$productId = $productIds[$i];
				$price = intval($prices[$i]);
				$quantity = intval($quantities[$i]);

				$products[] = array($productId, $price, $quantity);
			}

			$success = $this->counterModel->updateStokProduk($products);

			$message = array('error' => !$success);
			if ($success)
				$message['text'] = 'Sukses merubah stok!';
			else
				$message['text'] = 'Gagal merubah stok: database error.';

			$messages[] = $message;
		}

		$this->showIndex($messages);
	}

	private function showIndex ($messages = array())
	{
		$this->session->set_flashdata('messages', $messages);
		redirect('admin');
	}
}
