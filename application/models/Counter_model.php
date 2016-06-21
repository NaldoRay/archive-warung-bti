<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Counter_model extends CI_Model
{
	private $TABLE_PENJUALAN = 'penjualan';

	public function __construct ()
	{
		parent::__construct();
		$this->load->database();
	}

	public function add (array $products)
	{
		$nextId = $this->getNextId();
		$data = array();
		foreach ($products as $productId => $count)
		{
			$data[] = array(
				'id' => $nextId,
				'id_produk' => $productId,
				'jumlah' => $count
			);
		}

		$result = $this->db->insert_batch($this->TABLE_PENJUALAN, $data);
		return ($result !== false);
	}

	private function getNextId()
	{
		$query = $this->db
			->select_max('id', 'id_next')
			->get($this->TABLE_PENJUALAN);

		$result = $query->row();
		if (is_null($result))
			return 1;
		else
			return $result->id_next;
	}

	public function getHistoryPenjualan ()
	{
		$query = $this->db
			->order_by('tanggal_penjualan')
			->get('history_penjualan');
		return $query->result();
	}

	public function getDaftarProduk()
	{
		$query = $this->db->get('produk');
		return $query->result();
	}
}
