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
		if (is_null($result) || is_null($result->id_next))
			return 1;
		else
			return ($result->id_next + 1);
	}

	public function getHistoryPenjualan ()
	{
		$query = $this->db
			->order_by('id_penjualan desc')
			->get('history_penjualan');
		return $query->result();
	}

	public function getDaftarProduk()
	{
		$query = $this->db
			->order_by('nama')
			->get('produk');
		return $query->result();
	}
}
