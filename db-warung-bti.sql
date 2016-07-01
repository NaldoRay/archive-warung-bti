-- phpMyAdmin SQL Dump
-- version 4.4.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Jul 01, 2016 at 10:44 AM
-- Server version: 5.5.42
-- PHP Version: 5.5.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `warung_bti`
--

-- --------------------------------------------------------

--
-- Stand-in structure for view `history_penjualan`
--
CREATE TABLE IF NOT EXISTS `history_penjualan` (
`id_penjualan` int(10) unsigned
,`id_produk` int(4) unsigned
,`nama` varchar(100)
,`harga` int(5) unsigned
,`jumlah` int(2)
,`tanggal_penjualan` timestamp
);

-- --------------------------------------------------------

--
-- Table structure for table `penjualan`
--

CREATE TABLE IF NOT EXISTS `penjualan` (
  `id` int(10) unsigned NOT NULL,
  `id_produk` int(4) unsigned NOT NULL,
  `jumlah` int(2) NOT NULL,
  `tanggal` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `penjualan`
--

INSERT INTO `penjualan` (`id`, `id_produk`, `jumlah`, `tanggal`) VALUES
(1, 2, 5, '2016-06-21 07:35:18'),
(1, 1, 2, '2016-06-21 07:35:18'),
(2, 2, 1, '2016-06-21 08:19:29'),
(2, 1, 1, '2016-06-21 08:19:29'),
(3, 3, 1, '2016-06-22 05:05:38'),
(3, 2, 1, '2016-06-22 05:05:38'),
(4, 3, 1, '2016-06-22 05:05:49'),
(4, 2, 1, '2016-06-22 05:05:49'),
(5, 3, 1, '2016-06-22 05:07:33'),
(6, 3, 1, '2016-06-23 03:40:24'),
(6, 2, 3, '2016-06-23 03:40:24'),
(0, 3, 1, '2016-05-23 01:12:25'),
(5, 3, 1, '2016-05-23 01:12:25');

-- --------------------------------------------------------

--
-- Table structure for table `produk`
--

CREATE TABLE IF NOT EXISTS `produk` (
  `id` int(4) unsigned NOT NULL,
  `nama` varchar(100) NOT NULL,
  `harga` int(5) unsigned NOT NULL,
  `stok` int(3) NOT NULL DEFAULT '0',
  `tanggal_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `produk`
--

INSERT INTO `produk` (`id`, `nama`, `harga`, `stok`, `tanggal_update`) VALUES
(1, 'Beng Beng', 2000, 6, '2016-06-21 01:31:00'),
(2, 'Basreng', 3000, 1, '2016-06-21 07:26:23'),
(3, 'Yoghurt', 10000, 3, '2016-06-22 05:05:16'),
(4, 'Pangsit', 3000, 2, '2016-07-01 07:46:55');

-- --------------------------------------------------------

--
-- Stand-in structure for view `rekap_penjualan`
--
CREATE TABLE IF NOT EXISTS `rekap_penjualan` (
`id_penjualan` int(10) unsigned
,`total_harga` decimal(42,0)
,`tanggal` timestamp
);

-- --------------------------------------------------------

--
-- Structure for view `history_penjualan`
--
DROP TABLE IF EXISTS `history_penjualan`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `history_penjualan` AS select `penjualan`.`id` AS `id_penjualan`,`produk`.`id` AS `id_produk`,`produk`.`nama` AS `nama`,`produk`.`harga` AS `harga`,`penjualan`.`jumlah` AS `jumlah`,`penjualan`.`tanggal` AS `tanggal_penjualan` from (`penjualan` join `produk`) where (`penjualan`.`id_produk` = `produk`.`id`) order by `penjualan`.`id` desc;

-- --------------------------------------------------------

--
-- Structure for view `rekap_penjualan`
--
DROP TABLE IF EXISTS `rekap_penjualan`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `rekap_penjualan` AS select `penjualan`.`id` AS `id_penjualan`,(sum(`produk`.`harga`) * `penjualan`.`jumlah`) AS `total_harga`,`penjualan`.`tanggal` AS `tanggal` from (`penjualan` join `produk`) where (`penjualan`.`id_produk` = `produk`.`id`) group by `penjualan`.`id`;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `penjualan`
--
ALTER TABLE `penjualan`
  ADD KEY `id_penjualan` (`id`),
  ADD KEY `id_produk` (`id_produk`);

--
-- Indexes for table `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `produk`
--
ALTER TABLE `produk`
  MODIFY `id` int(4) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `penjualan`
--
ALTER TABLE `penjualan`
  ADD CONSTRAINT `fk_produk_id` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
