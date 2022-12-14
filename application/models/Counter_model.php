<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Counter_model extends CI_Model
{
	private $TABLE_PENJUALAN = 'penjualan';
	private $TABLE_PRODUK = 'produk';
	private $VIEW_HISTORY_PENJUALAN = 'history_penjualan';

	public function __construct ()
	{
		parent::__construct();
		$this->load->database();
	}

	public function addPenjualan (array $products)
	{
		$nextId = $this->getNextId();

		$this->db->trans_begin();
		$data = array();
		foreach ($products as $productId => $count)
		{
			$data[] = array(
				'id' => $nextId,
				'id_produk' => $productId,
				'jumlah' => $count
			);

			$result = $this->db
				->set('stok', 'stok-'.$count, false)
				->where('stok >', 0)
				->update($this->TABLE_PRODUK);

			if (!$result || ($this->db->affected_rows() == 0))
			{
				$this->db->trans_rollback();
				return false;
			}
		}

		$result = $this->db->insert_batch($this->TABLE_PENJUALAN, $data);

		$success = $result && ($this->db->trans_status() !== false);
		if ($success)
			$this->db->trans_commit();
		else
			$this->db->trans_rollback();

		return $success;
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
			->get($this->TABLE_PRODUK);
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

	public function addProduk ($nama, $harga, $stok)
	{
		$data = array(
			'nama' => $nama,
			'harga' => $harga,
			'stok' => $stok
		);
		$result = $this->db->insert($this->TABLE_PRODUK, $data);
		return $result;
	}

	public function updateStokProduk ($products)
	{
		$data = array();
		foreach ($products as $product)
		{
			$data[] = array(
				'id' => $product[0],
				'harga' => $product[1],
				'stok' => $product[2]
			);
		}
		$result = $this->db->update_batch($this->TABLE_PRODUK, $data, 'id');
		return ($result !== FALSE);
	}

	private function hasProduk ($idProduk)
	{
		$result = $this->db
			->where('id', $idProduk)
			->get($this->TABLE_PRODUK);

		return ($result->num_rows() > 0);
	}
}
