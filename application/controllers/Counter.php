<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Counter extends CI_Controller
{
	public function index()
	{
		$this->load->model('counter_model', 'counterModel');
		$this->load->helper('form');

		$daftarProduk = $this->counterModel->getDaftarProduk();
		$daftarPenjualan = $this->counterModel->getHistoryPenjualan();

		$contentData = array(
			'daftarProduk' => $daftarProduk
		);
		$content = $this->load->view('counter', $contentData, true);

		$templateData = array(
			'content' => $content
		);
		$this->load->view('template', $templateData);
	}
}