<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Counter extends CI_Controller
{
	public function index()
	{
		$this->load->model('counter_model', 'counterModel');

		$messages = array();

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

			$success = $this->counterModel->addPenjualan($products);

			$message = array('error' => !$success);
			if ($success)
				$message['text'] = 'Sukses melakukan pembelian!';
			else
				$message['text'] = 'Gagal melakukan pembelian: stok kosong atau database error.';

			$messages[] = $message;
		}

		$this->load->helper('form');

		$daftarProduk = $this->counterModel->getDaftarProduk();
		$daftarPenjualan = $this->counterModel->getHistoryPenjualan();

		$contentData = array(
			'daftarProduk' => $daftarProduk,
			'daftarPenjualan' => $daftarPenjualan,
			'messages' => $messages
		);
		$content = $this->load->view('counter', $contentData, true);

		$templateData = array(
			'title' => 'Counter Warung BTI',
			'content' => $content
		);
		$this->load->view('template', $templateData);
	}
	
	public function getRekapJumlahPenjualan()
	{
		$dayInterval = $this->input->get('interval');
		if (is_null($dayInterval) || $dayInterval == '' || !is_numeric($dayInterval))
			$dayInterval = null;
		else if ($dayInterval < 0)
			$dayInterval = 0;

		$this->load->model('counter_model', 'counterModel');
		$rekapJumlahPenjualan = $this->counterModel->getRekapJumlahPenjualan($dayInterval);

		$response = array(
			'error' => '',
			'data' => $rekapJumlahPenjualan
		);

		if (isset($_SERVER['HTTP_ORIGIN']))
		{
			header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
		}
		header('Content-type: application/json');
		header('Cache-control: max-age=60');
		echo json_encode($response);
	}

	public function getRekapTotalPendapatan()
	{
		$monthInterval = $this->input->get('interval');
		if (empty($monthInterval) || !is_numeric($monthInterval) || $monthInterval == 0)
		{
			$rekapTotalPendapatan = array();
			$error = 'Interval harus numerik';
		}
		else
		{
			$this->load->model('counter_model', 'counterModel');
			$rekapTotalPendapatan = $this->counterModel->getRekapTotalPendapatan($monthInterval);
			$error = '';
		}

		$response = array(
			'error' => $error,
			'data' => $rekapTotalPendapatan
		);

		if (isset($_SERVER['HTTP_ORIGIN']))
		{
			header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
		}
		header('Content-type: application/json');
		header('Cache-control: max-age=60');
		echo json_encode($response);
	}
}
