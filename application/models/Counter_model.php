<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Counter_model extends CI_Model
{
	private $TABLE_PENJUALAN = 'penjualan';
	private $VIEW_HISTORY_PENJUALAN = 'history_penjualan';

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
			->get($this->VIEW_HISTORY_PENJUALAN);
		return $query->result();
	}

	public function getDaftarProduk ()
	{
		$query = $this->db
			->order_by('nama')
			->get('produk');
		return $query->result();
	}

	public function getRekapJumlahPenjualan ($dayInterval = null)
	{
		$query = $this->db->select('nama, SUM(jumlah) AS jumlah');
		if (!is_null($dayInterval))
		{
			$dayInterval = $this->db->escape($dayInterval);
			$where = "tanggal_penjualan BETWEEN DATE_FORMAT(DATE_SUB(NOW(), interval ".$dayInterval." day), '%Y-%m-%d 00:00:00') AND NOW()";
			$query->where($where, null, false);
		}

		$result = $query
			->group_by('id_produk')
			->order_by('nama')
			->get($this->VIEW_HISTORY_PENJUALAN);

		return $result->result();
	}

	/**
	 * @param $monthInterval
	 * @return array
	 */
	public function getRekapTotalPendapatan ($monthInterval)
	{
		$rekapPendapatan = array();
		for ($interval = 0; $interval <= $monthInterval; $interval++)
		{
			if ($interval > 0)
			{
				$from = "DATE_FORMAT(DATE_SUB(NOW(), interval ".$interval." month), '%Y-%m-01 00:00:00')";
				$to = "DATE_FORMAT(DATE_SUB(NOW(), interval " . ($interval - 1) . " month), '%Y-%m-01 00:00:00')";
			}
			else
			{
				$from = "DATE_FORMAT(NOW(), '%Y-%m-01 00:00:00')";
				$to = "NOW()";
			}

			$result = $this->db
				->select('MONTH(NOW())-'.$interval.' AS month, IFNULL(SUM(total_harga), 0) AS total')
				->where('tanggal BETWEEN '.$from.' AND '.$to, null, false)
				->get('rekap_penjualan');

			$rekapPendapatan[] = $result->row();
		}

		return $rekapPendapatan;
	}
}
