-- phpMyAdmin SQL Dump
-- Sistem Persediaan Pisang Wijen Satria
-- Versi sistem: 5.1.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `persediaan_pisang_wijen_satria`
--

-- --------------------------------------------------------
-- Tabel `kategori`
-- --------------------------------------------------------

CREATE TABLE `kategori` (
  `id_kategori` int(11) NOT NULL,
  `nama_kategori` varchar(125) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `kategori` (`id_kategori`, `nama_kategori`) VALUES
(1, 'Pisang Wijen Original'),
(2, 'Pisang Wijen Coklat'),
(3, 'Pisang Wijen Keju'),
(4, 'Pisang Wijen Greentea'),
(5, 'Pisang Wijen Strawberry'),
(6, 'Minuman Pendamping'),
(7, 'Bahan Baku');

-- --------------------------------------------------------
-- Tabel `produk`
-- --------------------------------------------------------

CREATE TABLE `produk` (
  `id_produk` int(11) NOT NULL,
  `id_kategori` int(11) NOT NULL,
  `id_supplier` int(11) NOT NULL,
  `kode_produk` varchar(10) NOT NULL,
  `stok_min` int(11) NOT NULL,
  `nama_produk` varchar(50) NOT NULL,
  `harga_produk` varchar(25) NOT NULL,
  `satuan` varchar(10) NOT NULL,
  `stok_supp` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `produk` (`id_produk`, `id_kategori`, `id_supplier`, `kode_produk`, `stok_min`, `nama_produk`, `harga_produk`, `satuan`, `stok_supp`) VALUES
(1, 1, 1, 'PWS-001', 20, 'Pisang Wijen Satria Original', '10000', 'pcs', 150),
(2, 2, 1, 'PWS-002', 20, 'Pisang Wijen Satria Coklat', '12000', 'pcs', 120),
(3, 3, 1, 'PWS-003', 20, 'Pisang Wijen Satria Keju', '12000', 'pcs', 130),
(4, 4, 2, 'PWS-004', 15, 'Pisang Wijen Satria Greentea', '13000', 'pcs', 100),
(5, 5, 2, 'PWS-005', 15, 'Pisang Wijen Satria Strawberry', '13000', 'pcs', 110),
(6, 1, 1, 'PWS-006', 10, 'Pisang Wijen Satria Box Isi 5', '45000', 'box', 80),
(7, 1, 1, 'PWS-007', 10, 'Pisang Wijen Satria Box Isi 10', '85000', 'box', 60),
(8, 6, 3, 'PWS-008', 30, 'Es Teh Manis', '5000', 'cup', 200),
(9, 6, 3, 'PWS-009', 30, 'Es Jeruk Segar', '7000', 'cup', 180),
(10, 7, 1, 'PWS-010', 50, 'Pisang Kepok (Bahan Baku)', '3000', 'kg', 500),
(11, 7, 2, 'PWS-011', 30, 'Wijen Putih (Bahan Baku)', '25000', 'kg', 200),
(12, 7, 2, 'PWS-012', 20, 'Tepung Terigu (Bahan Baku)', '12000', 'kg', 300);

-- --------------------------------------------------------
-- Tabel `produk_keluar`
-- --------------------------------------------------------

CREATE TABLE `produk_keluar` (
  `id_produk_keluar` int(11) NOT NULL,
  `id_produk_masuk` int(11) NOT NULL,
  `id_transaksi` varchar(20) NOT NULL,
  `qty_kel` varchar(20) NOT NULL,
  `tgl_keluar` varchar(20) NOT NULL,
  `time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `produk_keluar` (`id_produk_keluar`, `id_produk_masuk`, `id_transaksi`, `qty_kel`, `tgl_keluar`, `time`) VALUES
(1, 1, '20231113PWS00001', '5', '2023-11-13', '2023-11-13 08:00:00'),
(2, 2, '20231113PWS00001', '3', '2023-11-13', '2023-11-13 08:00:00'),
(3, 3, '20231113PWS00002', '4', '2023-11-13', '2023-11-13 09:30:00'),
(4, 4, '20231113PWS00003', '2', '2023-11-13', '2023-11-13 10:15:00'),
(5, 5, '20231113PWS00004', '6', '2023-11-13', '2023-11-13 11:00:00'),
(6, 6, '20231113PWS00005', '1', '2023-11-13', '2023-11-13 12:30:00'),
(7, 1, '20231113PWS00006', '10', '2023-11-13', '2023-11-13 14:00:00'),
(8, 8, '20231113PWS00007', '5', '2023-11-13', '2023-11-13 15:00:00');

-- --------------------------------------------------------
-- Tabel `produk_masuk`
-- --------------------------------------------------------

CREATE TABLE `produk_masuk` (
  `id_produk_masuk` int(11) NOT NULL,
  `id_produk` int(11) NOT NULL,
  `id_tran_supp` int(11) NOT NULL,
  `qty` varchar(20) NOT NULL,
  `sisa` int(11) NOT NULL,
  `create_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `tgl_masuk` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `produk_masuk` (`id_produk_masuk`, `id_produk`, `id_tran_supp`, `qty`, `sisa`, `create_time`, `tgl_masuk`) VALUES
(1, 1, 1, '100', 90, '2023-11-01 07:00:00', '2023-11-01'),
(2, 2, 1, '100', 97, '2023-11-01 07:00:00', '2023-11-01'),
(3, 3, 1, '100', 96, '2023-11-01 07:00:00', '2023-11-01'),
(4, 4, 2, '80', 78, '2023-11-05 07:00:00', '2023-11-05'),
(5, 5, 2, '80', 74, '2023-11-05 07:00:00', '2023-11-05'),
(6, 6, 1, '50', 49, '2023-11-10 07:00:00', '2023-11-10'),
(7, 7, 1, '30', 30, '2023-11-10 07:00:00', '2023-11-10'),
(8, 8, 3, '200', 195, '2023-11-13 06:00:00', '2023-11-13'),
(9, 9, 3, '150', 150, '2023-11-13 06:00:00', '2023-11-13'),
(10, 10, 1, '300', 300, '2023-11-01 06:00:00', '2023-11-01'),
(11, 11, 2, '100', 100, '2023-11-01 06:00:00', '2023-11-01'),
(12, 12, 2, '150', 150, '2023-11-01 06:00:00', '2023-11-01');

-- --------------------------------------------------------
-- Tabel `supplier`
-- --------------------------------------------------------

CREATE TABLE `supplier` (
  `id_supplier` int(11) NOT NULL,
  `nama_supplier` varchar(50) NOT NULL,
  `nama_toko` varchar(50) NOT NULL,
  `alamat` varchar(125) NOT NULL,
  `no_hp` varchar(15) NOT NULL,
  `username_supp` varchar(50) NOT NULL,
  `pass_supp` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `supplier` (`id_supplier`, `nama_supplier`, `nama_toko`, `alamat`, `no_hp`, `username_supp`, `pass_supp`) VALUES
(1, 'Bapak Suryo Wibowo', 'UD Pisang Makmur', 'Malang, Jawa Timur', '081333000111', 'supp_suryo', 'suryo123'),
(2, 'Ibu Siti Rahayu', 'Toko Bahan Kue Barokah', 'Surabaya, Jawa Timur', '081555000222', 'supp_siti', 'siti456'),
(3, 'Bapak Hendra Jaya', 'CV Minuman Segar Nusantara', 'Malang, Jawa Timur', '082111000333', 'supp_hendra', 'hendra789');

-- --------------------------------------------------------
-- Tabel `transaksi`
-- --------------------------------------------------------

CREATE TABLE `transaksi` (
  `id_transaksi` varchar(20) NOT NULL,
  `id_user` int(11) NOT NULL,
  `tgl_transaksi` varchar(15) NOT NULL,
  `total_bayar` varchar(15) NOT NULL,
  `pembayaran` varchar(15) NOT NULL,
  `kembali` varchar(15) NOT NULL,
  `time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `transaksi` (`id_transaksi`, `id_user`, `tgl_transaksi`, `total_bayar`, `pembayaran`, `kembali`, `time`) VALUES
('20231113PWS00001', 3, '2023-11-13', '86000', '100000', '14000', '2023-11-13 08:00:00'),
('20231113PWS00002', 3, '2023-11-13', '48000', '50000', '2000', '2023-11-13 09:30:00'),
('20231113PWS00003', 3, '2023-11-13', '26000', '30000', '4000', '2023-11-13 10:15:00'),
('20231113PWS00004', 3, '2023-11-13', '78000', '80000', '2000', '2023-11-13 11:00:00'),
('20231113PWS00005', 3, '2023-11-13', '45000', '50000', '5000', '2023-11-13 12:30:00'),
('20231113PWS00006', 3, '2023-11-13', '100000', '100000', '0', '2023-11-13 14:00:00'),
('20231113PWS00007', 3, '2023-11-13', '25000', '30000', '5000', '2023-11-13 15:00:00');

-- --------------------------------------------------------
-- Tabel `transaksi_supp`
-- --------------------------------------------------------

CREATE TABLE `transaksi_supp` (
  `id_tran_supp` int(11) NOT NULL,
  `id_supplier` int(11) NOT NULL,
  `tgl_tran_supp` varchar(10) NOT NULL,
  `tot_bayar` varchar(15) NOT NULL,
  `status_transaksi` int(11) NOT NULL,
  `time_supp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `transaksi_supp` (`id_tran_supp`, `id_supplier`, `tgl_tran_supp`, `tot_bayar`, `status_transaksi`, `time_supp`) VALUES
(1, 1, '2023-11-01', '3000000', 1, '2023-11-01 07:00:00'),
(2, 2, '2023-11-05', '2600000', 1, '2023-11-05 07:00:00'),
(3, 3, '2023-11-13', '1750000', 0, '2023-11-13 06:00:00'),
(4, 1, '2023-11-10', '2550000', 1, '2023-11-10 07:00:00');

-- --------------------------------------------------------
-- Tabel `user`
-- --------------------------------------------------------

CREATE TABLE `user` (
  `id_user` int(11) NOT NULL,
  `nama_user` varchar(50) NOT NULL,
  `alamat` text NOT NULL,
  `no_hp` varchar(15) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `level_user` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `user` (`id_user`, `nama_user`, `alamat`, `no_hp`, `username`, `password`, `level_user`) VALUES
(1, 'Satria Nugraha', 'Malang, Jawa Timur', '081200001111', 'pemilik', 'pemilik', 2),
(2, 'Andi Prasetyo', 'Malang, Jawa Timur', '081200002222', 'admin', 'admin', 1),
(3, 'Dewi Kasir', 'Malang, Jawa Timur', '081200003333', 'kasir', 'kasir', 3);

-- --------------------------------------------------------
-- Index & Constraints
-- --------------------------------------------------------

ALTER TABLE `kategori` ADD PRIMARY KEY (`id_kategori`);
ALTER TABLE `produk` ADD PRIMARY KEY (`id_produk`);
ALTER TABLE `produk_keluar` ADD PRIMARY KEY (`id_produk_keluar`);
ALTER TABLE `produk_masuk` ADD PRIMARY KEY (`id_produk_masuk`);
ALTER TABLE `supplier` ADD PRIMARY KEY (`id_supplier`);
ALTER TABLE `transaksi` ADD PRIMARY KEY (`id_transaksi`);
ALTER TABLE `transaksi_supp` ADD PRIMARY KEY (`id_tran_supp`);
ALTER TABLE `user` ADD PRIMARY KEY (`id_user`);

ALTER TABLE `kategori` MODIFY `id_kategori` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
ALTER TABLE `produk` MODIFY `id_produk` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
ALTER TABLE `produk_keluar` MODIFY `id_produk_keluar` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
ALTER TABLE `produk_masuk` MODIFY `id_produk_masuk` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
ALTER TABLE `supplier` MODIFY `id_supplier` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
ALTER TABLE `transaksi_supp` MODIFY `id_tran_supp` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
ALTER TABLE `user` MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
