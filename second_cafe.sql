-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Aug 22, 2026 at 12:14 PM
-- Server version: 5.7.39
-- PHP Version: 8.3.23

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `second_cafe`
--

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('second-cafe-cache-illuminate:queue:restart', 'i:1787245358;', 2102605358);

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cafe_tables`
--

CREATE TABLE `cafe_tables` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `table_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qr_token` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `capacity` smallint(5) UNSIGNED NOT NULL DEFAULT '2',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cafe_tables`
--

INSERT INTO `cafe_tables` (`id`, `table_number`, `name`, `qr_token`, `capacity`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'M-01', 'Depan Kasir', '32671f1c-a198-4300-9ee4-49a434c2ab61', 4, 1, '2026-07-19 10:33:02', '2026-07-19 10:33:02'),
(2, 'M-02', 'Dalam Ruangan', 'd4b3612b-8c1c-447e-98e8-e7b2d75de4e9', 4, 1, '2026-07-19 10:38:31', '2026-07-19 10:39:20');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Makanan', 'makanan', 'ini adalah kategori untuk Makanan', 1, '2026-07-12 10:52:11', '2026-07-12 10:52:11'),
(2, 'Minuman', 'minuman', 'ini adalah Kategori Minuman', 1, '2026-07-12 10:52:42', '2026-07-12 10:52:42');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `menus`
--

CREATE TABLE `menus` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `price` decimal(12,2) NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_available` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `menus`
--

INSERT INTO `menus` (`id`, `category_id`, `name`, `slug`, `description`, `price`, `image`, `is_available`, `created_at`, `updated_at`) VALUES
(1, 1, 'Nasi Goreng', 'nasi-goreng', 'Nasi Goreng Cabai Merah Merona', '15000.00', 'menus/01KXBRG3QVNWJ76P10Z8CB6HFH.jpeg', 1, '2026-07-12 11:12:42', '2026-07-12 11:12:42'),
(2, 1, 'Sate Kalelawar', 'sate-kalelawar', 'Sate Kalelawar Pedas Merah', '20000.00', 'menus/01KXBSHH78W0Y1WN8DPVJYKF4X.jpg', 1, '2026-07-12 11:30:57', '2026-07-12 11:30:57'),
(3, 2, 'Es Teh', 'es-teh', 'Es Teh Lemon Manis', '5000.00', 'menus/01KXBSXQKS1WXV16SH1F9G4ZTM.jpg', 1, '2026-07-12 11:37:37', '2026-07-12 11:37:37'),
(6, 2, 'Boba Coklat', 'boba-coklat', 'Es Boba Coklat Manis Melejit!', '12000.00', 'menus/01KXBTFX3YCAKN7R53TY44EZCC.jpg', 1, '2026-07-12 11:47:32', '2026-07-12 11:47:32');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_07_12_173132_create_categories_table', 2),
(5, '2026_07_12_173133_create_menus_table', 2),
(6, '2026_07_12_173134_create_cafe_tables_table', 2),
(7, '2026_07_12_173134_create_orders_table', 2),
(8, '2026_07_12_173135_create_order_items_table', 2),
(9, '2026_07_12_173136_create_payments_table', 2),
(10, '2026_07_12_174317_add_role_to_users_table', 3),
(11, '2026_07_19_190327_add_customer_checkout_fields_to_orders_table', 4),
(12, '2026_07_19_190349_add_selected_options_to_order_items_table', 4),
(13, '2026_07_20_165347_add_cashier_details_to_payments_table', 5),
(14, '2026_08_01_085717_create_notifications_table', 6),
(15, '2026_08_01_175731_add_midtrans_fields_to_payments_table', 7),
(16, '2026_08_20_235156_add_receipt_emailed_at_to_payments_table', 8);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_id` bigint(20) UNSIGNED NOT NULL,
  `data` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `type`, `notifiable_type`, `notifiable_id`, `data`, `read_at`, `created_at`, `updated_at`) VALUES
('1312cae9-0520-4bd5-a03b-9a7a4a92097c', 'Filament\\Notifications\\DatabaseNotification', 'App\\Models\\User', 2, '{\"actions\":[{\"name\":\"openOrders\",\"alpineClickHandler\":null,\"color\":null,\"event\":null,\"eventData\":[],\"dispatchDirection\":false,\"dispatchToComponent\":null,\"extraAttributes\":[],\"icon\":null,\"iconPosition\":\"before\",\"iconSize\":null,\"isOutlined\":false,\"isDisabled\":false,\"label\":\"Buka Pesanan\",\"shouldClose\":false,\"shouldMarkAsRead\":true,\"shouldMarkAsUnread\":false,\"shouldOpenUrlInNewTab\":false,\"shouldPostToUrl\":false,\"size\":\"sm\",\"tooltip\":null,\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/orders\",\"view\":\"filament::components.button.index\"}],\"body\":\"Meja M-01 \\u2022 Kode bayar: PY4RU\",\"color\":null,\"duration\":\"persistent\",\"icon\":\"heroicon-o-bell-alert\",\"iconColor\":\"warning\",\"status\":\"warning\",\"title\":\"Pesanan baru masuk\",\"view\":null,\"viewData\":[],\"format\":\"filament\"}', NULL, '2026-08-01 08:29:37', '2026-08-01 08:29:37'),
('135a9d37-aa3a-4468-bc9f-a1cbba2144f5', 'Filament\\Notifications\\DatabaseNotification', 'App\\Models\\User', 3, '{\"actions\":[{\"name\":\"openOrders\",\"alpineClickHandler\":null,\"color\":null,\"event\":null,\"eventData\":[],\"dispatchDirection\":false,\"dispatchToComponent\":null,\"extraAttributes\":[],\"icon\":\"heroicon-o-arrow-top-right-on-square\",\"iconPosition\":\"before\",\"iconSize\":null,\"isOutlined\":false,\"isDisabled\":false,\"label\":\"Buka Pesanan\",\"shouldClose\":false,\"shouldMarkAsRead\":true,\"shouldMarkAsUnread\":false,\"shouldOpenUrlInNewTab\":false,\"shouldPostToUrl\":false,\"size\":\"sm\",\"tooltip\":null,\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/orders\",\"view\":\"filament::components.button.index\"}],\"body\":\"Pesanan ORD-20260802-R3ZR3 \\u2022 Meja M-02 \\u2022 Kode bayar: R3ZR3\",\"color\":null,\"duration\":\"persistent\",\"icon\":\"heroicon-o-bell-alert\",\"iconColor\":\"warning\",\"status\":\"warning\",\"title\":\"Pesanan baru masuk\",\"view\":null,\"viewData\":[],\"format\":\"filament\"}', NULL, '2026-08-01 18:40:47', '2026-08-01 18:40:47'),
('23f2e2c5-fff4-4f46-b1a0-5c1d02358db2', 'Filament\\Notifications\\DatabaseNotification', 'App\\Models\\User', 2, '{\"actions\":[{\"name\":\"openOrders\",\"alpineClickHandler\":null,\"color\":null,\"event\":null,\"eventData\":[],\"dispatchDirection\":false,\"dispatchToComponent\":null,\"extraAttributes\":[],\"icon\":\"heroicon-o-arrow-top-right-on-square\",\"iconPosition\":\"before\",\"iconSize\":null,\"isOutlined\":false,\"isDisabled\":false,\"label\":\"Buka Pesanan\",\"shouldClose\":false,\"shouldMarkAsRead\":true,\"shouldMarkAsUnread\":false,\"shouldOpenUrlInNewTab\":false,\"shouldPostToUrl\":false,\"size\":\"sm\",\"tooltip\":null,\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/orders\",\"view\":\"filament::components.button.index\"}],\"body\":\"Pesanan ORD-20260802-E2C2X \\u2022 Meja M-02 \\u2022 Kode bayar: E2C2X\",\"color\":null,\"duration\":\"persistent\",\"icon\":\"heroicon-o-bell-alert\",\"iconColor\":\"warning\",\"status\":\"warning\",\"title\":\"Pesanan baru masuk\",\"view\":null,\"viewData\":[],\"format\":\"filament\"}', NULL, '2026-08-01 18:37:01', '2026-08-01 18:37:01'),
('31336b65-50b4-444d-ad21-5c5e13b4728a', 'Filament\\Notifications\\DatabaseNotification', 'App\\Models\\User', 1, '{\"actions\":[{\"name\":\"openOrders\",\"alpineClickHandler\":null,\"color\":null,\"event\":null,\"eventData\":[],\"dispatchDirection\":false,\"dispatchToComponent\":null,\"extraAttributes\":[],\"icon\":null,\"iconPosition\":\"before\",\"iconSize\":null,\"isOutlined\":false,\"isDisabled\":false,\"label\":\"Buka Pesanan\",\"shouldClose\":false,\"shouldMarkAsRead\":true,\"shouldMarkAsUnread\":false,\"shouldOpenUrlInNewTab\":false,\"shouldPostToUrl\":false,\"size\":\"sm\",\"tooltip\":null,\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/orders\",\"view\":\"filament::components.button.index\"}],\"body\":\"Meja M-01 \\u2022 Kode bayar: NFGB6\",\"color\":null,\"duration\":\"persistent\",\"icon\":\"heroicon-o-bell-alert\",\"iconColor\":\"warning\",\"status\":\"warning\",\"title\":\"Pesanan baru masuk\",\"view\":null,\"viewData\":[],\"format\":\"filament\"}', '2026-08-01 08:32:40', '2026-08-01 08:29:37', '2026-08-01 08:32:40'),
('3e8624f5-332f-4ea6-a272-5a061341f6d1', 'Filament\\Notifications\\DatabaseNotification', 'App\\Models\\User', 3, '{\"actions\":[{\"name\":\"openOrders\",\"alpineClickHandler\":null,\"color\":null,\"event\":null,\"eventData\":[],\"dispatchDirection\":false,\"dispatchToComponent\":null,\"extraAttributes\":[],\"icon\":\"heroicon-o-arrow-top-right-on-square\",\"iconPosition\":\"before\",\"iconSize\":null,\"isOutlined\":false,\"isDisabled\":false,\"label\":\"Buka Pesanan\",\"shouldClose\":false,\"shouldMarkAsRead\":true,\"shouldMarkAsUnread\":false,\"shouldOpenUrlInNewTab\":false,\"shouldPostToUrl\":false,\"size\":\"sm\",\"tooltip\":null,\"url\":\"http:\\/\\/10.226.136.114:8000\\/admin\\/orders\",\"view\":\"filament::components.button.index\"}],\"body\":\"Pesanan ORD-20260816-KW482 \\u2022 Meja M-01 \\u2022 Kode bayar: KW482\",\"color\":null,\"duration\":\"persistent\",\"icon\":\"heroicon-o-bell-alert\",\"iconColor\":\"warning\",\"status\":\"warning\",\"title\":\"Pesanan baru masuk\",\"view\":null,\"viewData\":[],\"format\":\"filament\"}', NULL, '2026-08-20 17:02:32', '2026-08-20 17:02:32'),
('476eebf6-7184-406c-9dbe-a74f5f203a10', 'Filament\\Notifications\\DatabaseNotification', 'App\\Models\\User', 2, '{\"actions\":[{\"name\":\"openOrders\",\"alpineClickHandler\":null,\"color\":null,\"event\":null,\"eventData\":[],\"dispatchDirection\":false,\"dispatchToComponent\":null,\"extraAttributes\":[],\"icon\":\"heroicon-o-arrow-top-right-on-square\",\"iconPosition\":\"before\",\"iconSize\":null,\"isOutlined\":false,\"isDisabled\":false,\"label\":\"Buka Pesanan\",\"shouldClose\":false,\"shouldMarkAsRead\":true,\"shouldMarkAsUnread\":false,\"shouldOpenUrlInNewTab\":false,\"shouldPostToUrl\":false,\"size\":\"sm\",\"tooltip\":null,\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/orders\",\"view\":\"filament::components.button.index\"}],\"body\":\"Pesanan ORD-20260802-R3ZR3 \\u2022 Meja M-02 \\u2022 Kode bayar: R3ZR3\",\"color\":null,\"duration\":\"persistent\",\"icon\":\"heroicon-o-bell-alert\",\"iconColor\":\"warning\",\"status\":\"warning\",\"title\":\"Pesanan baru masuk\",\"view\":null,\"viewData\":[],\"format\":\"filament\"}', NULL, '2026-08-01 18:40:47', '2026-08-01 18:40:47'),
('7954ca59-866f-4108-8221-2c3ecba7b255', 'Filament\\Notifications\\DatabaseNotification', 'App\\Models\\User', 1, '{\"actions\":[{\"name\":\"openOrders\",\"alpineClickHandler\":null,\"color\":null,\"event\":null,\"eventData\":[],\"dispatchDirection\":false,\"dispatchToComponent\":null,\"extraAttributes\":[],\"icon\":\"heroicon-o-arrow-top-right-on-square\",\"iconPosition\":\"before\",\"iconSize\":null,\"isOutlined\":false,\"isDisabled\":false,\"label\":\"Buka Pesanan\",\"shouldClose\":false,\"shouldMarkAsRead\":true,\"shouldMarkAsUnread\":false,\"shouldOpenUrlInNewTab\":false,\"shouldPostToUrl\":false,\"size\":\"sm\",\"tooltip\":null,\"url\":\"http:\\/\\/10.226.136.114:8000\\/admin\\/orders\",\"view\":\"filament::components.button.index\"}],\"body\":\"Pesanan ORD-20260816-KW482 \\u2022 Meja M-01 \\u2022 Kode bayar: KW482\",\"color\":null,\"duration\":\"persistent\",\"icon\":\"heroicon-o-bell-alert\",\"iconColor\":\"warning\",\"status\":\"warning\",\"title\":\"Pesanan baru masuk\",\"view\":null,\"viewData\":[],\"format\":\"filament\"}', NULL, '2026-08-20 17:02:31', '2026-08-20 17:02:31'),
('7c0854e3-5434-42ed-82a5-176b2c713e50', 'Filament\\Notifications\\DatabaseNotification', 'App\\Models\\User', 2, '{\"actions\":[{\"name\":\"openOrders\",\"alpineClickHandler\":null,\"color\":null,\"event\":null,\"eventData\":[],\"dispatchDirection\":false,\"dispatchToComponent\":null,\"extraAttributes\":[],\"icon\":\"heroicon-o-arrow-top-right-on-square\",\"iconPosition\":\"before\",\"iconSize\":null,\"isOutlined\":false,\"isDisabled\":false,\"label\":\"Buka Pesanan\",\"shouldClose\":false,\"shouldMarkAsRead\":true,\"shouldMarkAsUnread\":false,\"shouldOpenUrlInNewTab\":false,\"shouldPostToUrl\":false,\"size\":\"sm\",\"tooltip\":null,\"url\":\"http:\\/\\/10.226.136.114:8000\\/admin\\/orders\",\"view\":\"filament::components.button.index\"}],\"body\":\"Pesanan ORD-20260816-KW482 \\u2022 Meja M-01 \\u2022 Kode bayar: KW482\",\"color\":null,\"duration\":\"persistent\",\"icon\":\"heroicon-o-bell-alert\",\"iconColor\":\"warning\",\"status\":\"warning\",\"title\":\"Pesanan baru masuk\",\"view\":null,\"viewData\":[],\"format\":\"filament\"}', NULL, '2026-08-20 17:02:32', '2026-08-20 17:02:32'),
('80316fd7-dbd8-4dc8-928a-f367599a3698', 'Filament\\Notifications\\DatabaseNotification', 'App\\Models\\User', 2, '{\"actions\":[{\"name\":\"openOrders\",\"alpineClickHandler\":null,\"color\":null,\"event\":null,\"eventData\":[],\"dispatchDirection\":false,\"dispatchToComponent\":null,\"extraAttributes\":[],\"icon\":null,\"iconPosition\":\"before\",\"iconSize\":null,\"isOutlined\":false,\"isDisabled\":false,\"label\":\"Buka Pesanan\",\"shouldClose\":false,\"shouldMarkAsRead\":true,\"shouldMarkAsUnread\":false,\"shouldOpenUrlInNewTab\":false,\"shouldPostToUrl\":false,\"size\":\"sm\",\"tooltip\":null,\"url\":\"http:\\/\\/10.226.136.114:8000\\/admin\\/orders\",\"view\":\"filament::components.button.index\"}],\"body\":\"Meja M-02 \\u2022 Kode bayar: M6EUF\",\"color\":null,\"duration\":\"persistent\",\"icon\":\"heroicon-o-bell-alert\",\"iconColor\":\"warning\",\"status\":\"warning\",\"title\":\"Pesanan baru masuk\",\"view\":null,\"viewData\":[],\"format\":\"filament\"}', NULL, '2026-08-01 02:29:11', '2026-08-01 02:29:11'),
('810fdea1-fed4-471a-8ac4-4c44af2796a9', 'Filament\\Notifications\\DatabaseNotification', 'App\\Models\\User', 3, '{\"actions\":[{\"name\":\"openOrders\",\"alpineClickHandler\":null,\"color\":null,\"event\":null,\"eventData\":[],\"dispatchDirection\":false,\"dispatchToComponent\":null,\"extraAttributes\":[],\"icon\":\"heroicon-o-arrow-top-right-on-square\",\"iconPosition\":\"before\",\"iconSize\":null,\"isOutlined\":false,\"isDisabled\":false,\"label\":\"Buka Pesanan\",\"shouldClose\":false,\"shouldMarkAsRead\":true,\"shouldMarkAsUnread\":false,\"shouldOpenUrlInNewTab\":false,\"shouldPostToUrl\":false,\"size\":\"sm\",\"tooltip\":null,\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/orders\",\"view\":\"filament::components.button.index\"}],\"body\":\"Pesanan ORD-20260801-88L9B \\u2022 Meja M-02 \\u2022 Kode bayar: 88L9B\",\"color\":null,\"duration\":\"persistent\",\"icon\":\"heroicon-o-bell-alert\",\"iconColor\":\"warning\",\"status\":\"warning\",\"title\":\"Pesanan baru masuk\",\"view\":null,\"viewData\":[],\"format\":\"filament\"}', NULL, '2026-08-01 11:27:52', '2026-08-01 11:27:52'),
('9ba0bd42-529f-42a7-8c30-6afa9f369d89', 'Filament\\Notifications\\DatabaseNotification', 'App\\Models\\User', 1, '{\"actions\":[{\"name\":\"openOrders\",\"alpineClickHandler\":null,\"color\":null,\"event\":null,\"eventData\":[],\"dispatchDirection\":false,\"dispatchToComponent\":null,\"extraAttributes\":[],\"icon\":null,\"iconPosition\":\"before\",\"iconSize\":null,\"isOutlined\":false,\"isDisabled\":false,\"label\":\"Buka Pesanan\",\"shouldClose\":false,\"shouldMarkAsRead\":true,\"shouldMarkAsUnread\":false,\"shouldOpenUrlInNewTab\":false,\"shouldPostToUrl\":false,\"size\":\"sm\",\"tooltip\":null,\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/orders\",\"view\":\"filament::components.button.index\"}],\"body\":\"Meja M-01 \\u2022 Kode bayar: M8CVZ\",\"color\":null,\"duration\":\"persistent\",\"icon\":\"heroicon-o-bell-alert\",\"iconColor\":\"warning\",\"status\":\"warning\",\"title\":\"Pesanan baru masuk\",\"view\":null,\"viewData\":[],\"format\":\"filament\"}', NULL, '2026-08-01 08:29:37', '2026-08-01 08:29:37'),
('9bae1908-1bb8-445c-bb74-ef87bc30d951', 'Filament\\Notifications\\DatabaseNotification', 'App\\Models\\User', 1, '{\"actions\":[{\"name\":\"openOrders\",\"alpineClickHandler\":null,\"color\":null,\"event\":null,\"eventData\":[],\"dispatchDirection\":false,\"dispatchToComponent\":null,\"extraAttributes\":[],\"icon\":\"heroicon-o-arrow-top-right-on-square\",\"iconPosition\":\"before\",\"iconSize\":null,\"isOutlined\":false,\"isDisabled\":false,\"label\":\"Buka Pesanan\",\"shouldClose\":false,\"shouldMarkAsRead\":true,\"shouldMarkAsUnread\":false,\"shouldOpenUrlInNewTab\":false,\"shouldPostToUrl\":false,\"size\":\"sm\",\"tooltip\":null,\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/orders\",\"view\":\"filament::components.button.index\"}],\"body\":\"Pesanan ORD-20260802-E2C2X \\u2022 Meja M-02 \\u2022 Kode bayar: E2C2X\",\"color\":null,\"duration\":\"persistent\",\"icon\":\"heroicon-o-bell-alert\",\"iconColor\":\"warning\",\"status\":\"warning\",\"title\":\"Pesanan baru masuk\",\"view\":null,\"viewData\":[],\"format\":\"filament\"}', '2026-08-15 18:33:28', '2026-08-01 18:37:01', '2026-08-15 18:33:28'),
('ac2373bd-3936-45d4-9723-0cf499cb04e5', 'Filament\\Notifications\\DatabaseNotification', 'App\\Models\\User', 1, '{\"actions\":[{\"name\":\"openOrders\",\"alpineClickHandler\":null,\"color\":null,\"event\":null,\"eventData\":[],\"dispatchDirection\":false,\"dispatchToComponent\":null,\"extraAttributes\":[],\"icon\":null,\"iconPosition\":\"before\",\"iconSize\":null,\"isOutlined\":false,\"isDisabled\":false,\"label\":\"Buka Pesanan\",\"shouldClose\":false,\"shouldMarkAsRead\":true,\"shouldMarkAsUnread\":false,\"shouldOpenUrlInNewTab\":false,\"shouldPostToUrl\":false,\"size\":\"sm\",\"tooltip\":null,\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/orders\",\"view\":\"filament::components.button.index\"}],\"body\":\"Meja M-01 \\u2022 Kode bayar: PY4RU\",\"color\":null,\"duration\":\"persistent\",\"icon\":\"heroicon-o-bell-alert\",\"iconColor\":\"warning\",\"status\":\"warning\",\"title\":\"Pesanan baru masuk\",\"view\":null,\"viewData\":[],\"format\":\"filament\"}', NULL, '2026-08-01 08:29:37', '2026-08-01 08:29:37'),
('b4101eab-0d8b-4246-806f-11c6fa92b1e1', 'Filament\\Notifications\\DatabaseNotification', 'App\\Models\\User', 2, '{\"actions\":[{\"name\":\"openOrders\",\"alpineClickHandler\":null,\"color\":null,\"event\":null,\"eventData\":[],\"dispatchDirection\":false,\"dispatchToComponent\":null,\"extraAttributes\":[],\"icon\":null,\"iconPosition\":\"before\",\"iconSize\":null,\"isOutlined\":false,\"isDisabled\":false,\"label\":\"Buka Pesanan\",\"shouldClose\":false,\"shouldMarkAsRead\":true,\"shouldMarkAsUnread\":false,\"shouldOpenUrlInNewTab\":false,\"shouldPostToUrl\":false,\"size\":\"sm\",\"tooltip\":null,\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/orders\",\"view\":\"filament::components.button.index\"}],\"body\":\"Meja M-01 \\u2022 Kode bayar: M8CVZ\",\"color\":null,\"duration\":\"persistent\",\"icon\":\"heroicon-o-bell-alert\",\"iconColor\":\"warning\",\"status\":\"warning\",\"title\":\"Pesanan baru masuk\",\"view\":null,\"viewData\":[],\"format\":\"filament\"}', NULL, '2026-08-01 08:29:37', '2026-08-01 08:29:37'),
('bff518af-26cb-4d34-a604-c1a2ccac0093', 'Filament\\Notifications\\DatabaseNotification', 'App\\Models\\User', 3, '{\"actions\":[{\"name\":\"openOrders\",\"alpineClickHandler\":null,\"color\":null,\"event\":null,\"eventData\":[],\"dispatchDirection\":false,\"dispatchToComponent\":null,\"extraAttributes\":[],\"icon\":\"heroicon-o-arrow-top-right-on-square\",\"iconPosition\":\"before\",\"iconSize\":null,\"isOutlined\":false,\"isDisabled\":false,\"label\":\"Buka Pesanan\",\"shouldClose\":false,\"shouldMarkAsRead\":true,\"shouldMarkAsUnread\":false,\"shouldOpenUrlInNewTab\":false,\"shouldPostToUrl\":false,\"size\":\"sm\",\"tooltip\":null,\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/orders\",\"view\":\"filament::components.button.index\"}],\"body\":\"Pesanan ORD-20260802-E2C2X \\u2022 Meja M-02 \\u2022 Kode bayar: E2C2X\",\"color\":null,\"duration\":\"persistent\",\"icon\":\"heroicon-o-bell-alert\",\"iconColor\":\"warning\",\"status\":\"warning\",\"title\":\"Pesanan baru masuk\",\"view\":null,\"viewData\":[],\"format\":\"filament\"}', NULL, '2026-08-01 18:37:01', '2026-08-01 18:37:01'),
('d4145947-063d-46a5-b71a-f70840715d68', 'Filament\\Notifications\\DatabaseNotification', 'App\\Models\\User', 2, '{\"actions\":[{\"name\":\"openOrders\",\"alpineClickHandler\":null,\"color\":null,\"event\":null,\"eventData\":[],\"dispatchDirection\":false,\"dispatchToComponent\":null,\"extraAttributes\":[],\"icon\":\"heroicon-o-arrow-top-right-on-square\",\"iconPosition\":\"before\",\"iconSize\":null,\"isOutlined\":false,\"isDisabled\":false,\"label\":\"Buka Pesanan\",\"shouldClose\":false,\"shouldMarkAsRead\":true,\"shouldMarkAsUnread\":false,\"shouldOpenUrlInNewTab\":false,\"shouldPostToUrl\":false,\"size\":\"sm\",\"tooltip\":null,\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/orders\",\"view\":\"filament::components.button.index\"}],\"body\":\"Pesanan ORD-20260801-88L9B \\u2022 Meja M-02 \\u2022 Kode bayar: 88L9B\",\"color\":null,\"duration\":\"persistent\",\"icon\":\"heroicon-o-bell-alert\",\"iconColor\":\"warning\",\"status\":\"warning\",\"title\":\"Pesanan baru masuk\",\"view\":null,\"viewData\":[],\"format\":\"filament\"}', NULL, '2026-08-01 11:27:52', '2026-08-01 11:27:52'),
('dff46dd6-a42a-4848-b88a-8d48a39c4e1d', 'Filament\\Notifications\\DatabaseNotification', 'App\\Models\\User', 2, '{\"actions\":[{\"name\":\"openOrders\",\"alpineClickHandler\":null,\"color\":null,\"event\":null,\"eventData\":[],\"dispatchDirection\":false,\"dispatchToComponent\":null,\"extraAttributes\":[],\"icon\":null,\"iconPosition\":\"before\",\"iconSize\":null,\"isOutlined\":false,\"isDisabled\":false,\"label\":\"Buka Pesanan\",\"shouldClose\":false,\"shouldMarkAsRead\":true,\"shouldMarkAsUnread\":false,\"shouldOpenUrlInNewTab\":false,\"shouldPostToUrl\":false,\"size\":\"sm\",\"tooltip\":null,\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/orders\",\"view\":\"filament::components.button.index\"}],\"body\":\"Meja M-01 \\u2022 Kode bayar: NFGB6\",\"color\":null,\"duration\":\"persistent\",\"icon\":\"heroicon-o-bell-alert\",\"iconColor\":\"warning\",\"status\":\"warning\",\"title\":\"Pesanan baru masuk\",\"view\":null,\"viewData\":[],\"format\":\"filament\"}', NULL, '2026-08-01 08:29:37', '2026-08-01 08:29:37'),
('ec48f3cb-7833-4b69-b80b-a8f1fb3b2ab7', 'Filament\\Notifications\\DatabaseNotification', 'App\\Models\\User', 1, '{\"actions\":[{\"name\":\"openOrders\",\"alpineClickHandler\":null,\"color\":null,\"event\":null,\"eventData\":[],\"dispatchDirection\":false,\"dispatchToComponent\":null,\"extraAttributes\":[],\"icon\":\"heroicon-o-arrow-top-right-on-square\",\"iconPosition\":\"before\",\"iconSize\":null,\"isOutlined\":false,\"isDisabled\":false,\"label\":\"Buka Pesanan\",\"shouldClose\":false,\"shouldMarkAsRead\":true,\"shouldMarkAsUnread\":false,\"shouldOpenUrlInNewTab\":false,\"shouldPostToUrl\":false,\"size\":\"sm\",\"tooltip\":null,\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/orders\",\"view\":\"filament::components.button.index\"}],\"body\":\"Pesanan ORD-20260801-88L9B \\u2022 Meja M-02 \\u2022 Kode bayar: 88L9B\",\"color\":null,\"duration\":\"persistent\",\"icon\":\"heroicon-o-bell-alert\",\"iconColor\":\"warning\",\"status\":\"warning\",\"title\":\"Pesanan baru masuk\",\"view\":null,\"viewData\":[],\"format\":\"filament\"}', NULL, '2026-08-01 11:27:52', '2026-08-01 11:27:52');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `cafe_table_id` bigint(20) UNSIGNED NOT NULL,
  `order_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `public_token` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_method` enum('cashier','online') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_status` enum('unpaid','pending','paid','failed','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'unpaid',
  `status` enum('menunggu_pembayaran','menunggu_verifikasi','diterima','diproses','siap','selesai','dibatalkan') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'menunggu_pembayaran',
  `subtotal` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `ordered_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `cafe_table_id`, `order_code`, `public_token`, `customer_name`, `customer_phone`, `customer_email`, `payment_method`, `payment_status`, `status`, `subtotal`, `total_amount`, `notes`, `ordered_at`, `created_at`, `updated_at`) VALUES
(1, 1, 'ORD-20260720-DLSTX', 'a7146848-b91a-417e-9a1e-d22f5e00cff0', 'Yogi Sepdu Dehiya', '082252664455', 'yogi@gmail.com', 'cashier', 'unpaid', 'menunggu_pembayaran', '52000.00', '52000.00', NULL, '2026-07-20 09:43:54', '2026-07-20 09:43:54', '2026-07-20 09:43:54'),
(2, 1, 'ORD-20260721-BFCX0', 'd380ba8d-2e82-46bc-8b5d-28e1a5223eab', 'Yogi Sepdu Dehiya', '082252664455', 'yogi@gmail.com', 'cashier', 'paid', 'diterima', '35000.00', '35000.00', NULL, '2026-07-21 08:02:54', '2026-07-21 08:02:54', '2026-07-21 08:03:50'),
(3, 1, 'ORD-20260727-KA3AM', '78048ed1-9428-4a28-9ef4-05dec65d9c6f', 'Yogi Sepdu Dehiya', '082252664455', 'yogi@gmail.com', 'cashier', 'unpaid', 'menunggu_pembayaran', '27000.00', '27000.00', 'pesanan di antar bersamaan', '2026-07-27 07:47:48', '2026-07-27 07:47:48', '2026-07-27 07:47:48'),
(4, 1, 'ORD-20260727-C22NH', '4b3669c6-596a-4557-866e-49f9429f5a42', 'Yogi Sepdu Dehiya', '082252664455', 'yogi@gmail.com', 'cashier', 'unpaid', 'diterima', '27000.00', '27000.00', NULL, '2026-07-27 08:16:04', '2026-07-27 08:16:04', '2026-08-01 10:27:56'),
(5, 1, 'ORD-20260727-QZY3Y', '3e86234d-e14e-4e21-83b5-e3b25f19fd07', 'Anishah Kartari', '+6281268450490', 'anishah.kartari@gmail.com', 'cashier', 'paid', 'diterima', '15000.00', '15000.00', NULL, '2026-07-27 09:08:52', '2026-07-27 09:08:52', '2026-07-28 10:15:17'),
(6, 1, 'ORD-20260727-YBT7V', '34d509b4-d658-4003-b647-55d512e07ad3', 'yogi', '084574547896', 'yogi@admin.com', 'cashier', 'paid', 'diterima', '27000.00', '27000.00', NULL, '2026-07-27 09:10:31', '2026-07-27 09:10:31', '2026-07-27 09:56:10'),
(7, 1, 'ORD-20260727-N934Z', 'b72008ed-8573-4695-9257-ae1e46c3e45e', 'yogi', '084574547896', 'yogi@admin.com', 'cashier', 'paid', 'diterima', '20000.00', '20000.00', NULL, '2026-07-27 09:45:48', '2026-07-27 09:45:48', '2026-07-27 09:55:59'),
(8, 1, 'ORD-20260727-F998S', 'ed92fdc6-35de-4f8b-9a1f-4a3f6dafd695', 'yogi', '084574547896', 'yogi@admin.com', 'cashier', 'paid', 'diterima', '20000.00', '20000.00', NULL, '2026-07-27 09:57:20', '2026-07-27 09:57:20', '2026-07-28 10:11:37'),
(9, 1, 'ORD-20260727-EVXNK', '5f71d97d-d3fe-4f31-8164-3bb3c35454e1', 'yogi', '084574547896', 'yogi@admin.com', 'cashier', 'paid', 'diterima', '12000.00', '12000.00', NULL, '2026-07-27 09:57:45', '2026-07-27 09:57:45', '2026-07-27 10:23:21'),
(10, 2, 'ORD-20260801-M6EUF', '219cdc89-2909-49b2-8159-059b659fbcf7', 'Yogi', '081234567845', 'yogi@gmail.com', 'cashier', 'paid', 'diterima', '35000.00', '35000.00', 'Tolong di percepat', '2026-08-01 02:29:07', '2026-08-01 02:29:07', '2026-08-01 02:31:16'),
(11, 1, 'ORD-20260801-M8CVZ', 'd692efa9-a10c-4ff3-8c34-c7b81a7ec235', 'yogi', '082252331122', 'yogi@gmail.com', 'cashier', 'unpaid', 'diterima', '15000.00', '15000.00', NULL, '2026-08-01 07:59:55', '2026-08-01 07:59:55', '2026-08-01 10:27:45'),
(12, 1, 'ORD-20260801-PY4RU', 'e902a284-0f9c-4a26-8267-90b42c2cb8be', 'retno', '081234567897', 'retno@gmail.com', 'cashier', 'paid', 'diterima', '20000.00', '20000.00', NULL, '2026-08-01 08:09:21', '2026-08-01 08:09:21', '2026-08-01 09:45:09'),
(13, 1, 'ORD-20260801-NFGB6', '70944ca3-1165-4238-8408-2a357947d1b3', 'sepdu', '085522334455', 'sepdu@gmail.com', 'cashier', 'paid', 'diterima', '12000.00', '12000.00', NULL, '2026-08-01 08:23:40', '2026-08-01 08:23:40', '2026-08-01 09:23:09'),
(14, 2, 'ORD-20260801-88L9B', '451088b5-2ad9-4f15-bd0a-50ef7cf83e28', 'retno', '085236961245', 'retno@gmail.com', 'online', 'pending', 'menunggu_pembayaran', '15000.00', '15000.00', NULL, '2026-08-01 11:27:50', '2026-08-01 11:27:50', '2026-08-01 11:27:50'),
(15, 2, 'ORD-20260802-E2C2X', '8ce378a9-2482-4d9a-b2c4-a6b0f0046e5e', 'retno', '082256452312', 'retno@gmail.com', 'online', 'paid', 'diterima', '15000.00', '15000.00', NULL, '2026-08-01 18:36:58', '2026-08-01 18:36:58', '2026-08-01 18:39:16'),
(16, 2, 'ORD-20260802-R3ZR3', '0e3e9dfa-7382-4c48-89ca-dc0a2bcae8aa', 'yogi', '012345678987', 'yogi@gmail.com', 'online', 'paid', 'diterima', '12000.00', '12000.00', NULL, '2026-08-01 18:40:47', '2026-08-01 18:40:47', '2026-08-01 18:41:09'),
(17, 1, 'ORD-20260816-KW482', '5a208e68-ef8a-43a1-86e4-02082e7191df', 'M nazori', '081312123434', 'productiondusun@gmail.com', 'online', 'paid', 'diterima', '15000.00', '15000.00', NULL, '2026-08-15 19:17:47', '2026-08-15 19:17:47', '2026-08-15 19:22:26');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `menu_id` bigint(20) UNSIGNED DEFAULT NULL,
  `menu_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `unit_price` decimal(12,2) NOT NULL,
  `quantity` int(10) UNSIGNED NOT NULL,
  `selected_options` json DEFAULT NULL,
  `subtotal` decimal(12,2) NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `menu_id`, `menu_name`, `unit_price`, `quantity`, `selected_options`, `subtotal`, `notes`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'Nasi Goreng', '15000.00', 1, NULL, '15000.00', 'pakai cabe sedikit', '2026-07-20 09:43:54', '2026-07-20 09:43:54'),
(2, 1, 2, 'Sate Kalelawar', '20000.00', 1, NULL, '20000.00', 'yang manis', '2026-07-20 09:43:54', '2026-07-20 09:43:54'),
(3, 1, 6, 'Boba Coklat', '12000.00', 1, NULL, '12000.00', 'tanpa boba', '2026-07-20 09:43:54', '2026-07-20 09:43:54'),
(4, 1, 3, 'Es Teh', '5000.00', 1, NULL, '5000.00', NULL, '2026-07-20 09:43:54', '2026-07-20 09:43:54'),
(5, 2, 1, 'Nasi Goreng', '15000.00', 1, NULL, '15000.00', NULL, '2026-07-21 08:02:54', '2026-07-21 08:02:54'),
(6, 2, 2, 'Sate Kalelawar', '20000.00', 1, NULL, '20000.00', 'okelah oke', '2026-07-21 08:02:54', '2026-07-21 08:02:54'),
(7, 3, 1, 'Nasi Goreng', '15000.00', 1, NULL, '15000.00', 'cabe nya sedikit', '2026-07-27 07:47:48', '2026-07-27 07:47:48'),
(8, 3, 6, 'Boba Coklat', '12000.00', 1, NULL, '12000.00', 'es nya banyak', '2026-07-27 07:47:48', '2026-07-27 07:47:48'),
(9, 4, 1, 'Nasi Goreng', '15000.00', 1, NULL, '15000.00', 'tambah cabai', '2026-07-27 08:16:04', '2026-07-27 08:16:04'),
(10, 4, 6, 'Boba Coklat', '12000.00', 1, NULL, '12000.00', 'tambah es', '2026-07-27 08:16:04', '2026-07-27 08:16:04'),
(11, 5, 1, 'Nasi Goreng', '15000.00', 1, NULL, '15000.00', NULL, '2026-07-27 09:08:52', '2026-07-27 09:08:52'),
(12, 6, 1, 'Nasi Goreng', '15000.00', 1, NULL, '15000.00', NULL, '2026-07-27 09:10:31', '2026-07-27 09:10:31'),
(13, 6, 6, 'Boba Coklat', '12000.00', 1, NULL, '12000.00', NULL, '2026-07-27 09:10:31', '2026-07-27 09:10:31'),
(14, 7, 2, 'Sate Kalelawar', '20000.00', 1, NULL, '20000.00', NULL, '2026-07-27 09:45:48', '2026-07-27 09:45:48'),
(15, 8, 2, 'Sate Kalelawar', '20000.00', 1, NULL, '20000.00', NULL, '2026-07-27 09:57:20', '2026-07-27 09:57:20'),
(16, 9, 6, 'Boba Coklat', '12000.00', 1, NULL, '12000.00', NULL, '2026-07-27 09:57:45', '2026-07-27 09:57:45'),
(17, 10, 1, 'Nasi Goreng', '15000.00', 1, NULL, '15000.00', NULL, '2026-08-01 02:29:07', '2026-08-01 02:29:07'),
(18, 10, 2, 'Sate Kalelawar', '20000.00', 1, NULL, '20000.00', 'Cabe sedikit saja', '2026-08-01 02:29:07', '2026-08-01 02:29:07'),
(19, 11, 1, 'Nasi Goreng', '15000.00', 1, NULL, '15000.00', NULL, '2026-08-01 07:59:55', '2026-08-01 07:59:55'),
(20, 12, 2, 'Sate Kalelawar', '20000.00', 1, NULL, '20000.00', NULL, '2026-08-01 08:09:21', '2026-08-01 08:09:21'),
(21, 13, 6, 'Boba Coklat', '12000.00', 1, NULL, '12000.00', NULL, '2026-08-01 08:23:40', '2026-08-01 08:23:40'),
(22, 14, 1, 'Nasi Goreng', '15000.00', 1, NULL, '15000.00', NULL, '2026-08-01 11:27:50', '2026-08-01 11:27:50'),
(23, 15, 1, 'Nasi Goreng', '15000.00', 1, NULL, '15000.00', NULL, '2026-08-01 18:36:58', '2026-08-01 18:36:58'),
(24, 16, 6, 'Boba Coklat', '12000.00', 1, NULL, '12000.00', NULL, '2026-08-01 18:40:47', '2026-08-01 18:40:47'),
(25, 17, 1, 'Nasi Goreng', '15000.00', 1, NULL, '15000.00', NULL, '2026-08-15 19:17:47', '2026-08-15 19:17:47');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `payment_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `method` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `gateway` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gateway_order_id` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gateway_transaction_id` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount` decimal(12,2) NOT NULL,
  `amount_received` decimal(12,2) DEFAULT NULL,
  `change_amount` decimal(12,2) DEFAULT NULL,
  `status` enum('menunggu_verifikasi','berhasil','ditolak') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'menunggu_verifikasi',
  `proof_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `verified_by` bigint(20) UNSIGNED DEFAULT NULL,
  `rejection_reason` text COLLATE utf8mb4_unicode_ci,
  `paid_at` timestamp NULL DEFAULT NULL,
  `verified_at` timestamp NULL DEFAULT NULL,
  `receipt_emailed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `qr_code_url` text COLLATE utf8mb4_unicode_ci,
  `qr_string` longtext COLLATE utf8mb4_unicode_ci,
  `expires_at` timestamp NULL DEFAULT NULL,
  `gateway_payload` json DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `order_id`, `payment_code`, `method`, `gateway`, `gateway_order_id`, `gateway_transaction_id`, `amount`, `amount_received`, `change_amount`, `status`, `proof_image`, `verified_by`, `rejection_reason`, `paid_at`, `verified_at`, `receipt_emailed_at`, `created_at`, `updated_at`, `qr_code_url`, `qr_string`, `expires_at`, `gateway_payload`) VALUES
(1, 2, 'PAY-20260721-MV3TP', 'cashier', NULL, NULL, NULL, '35000.00', '50000.00', '15000.00', 'berhasil', NULL, 1, NULL, '2026-07-21 08:03:50', '2026-07-21 08:03:50', NULL, '2026-07-21 08:03:50', '2026-07-21 08:03:50', NULL, NULL, NULL, NULL),
(2, 7, 'PAY-20260727-QQTTW', 'cashier', NULL, NULL, NULL, '20000.00', '25000.00', '5000.00', 'berhasil', NULL, 1, NULL, '2026-07-27 09:55:59', '2026-07-27 09:55:59', NULL, '2026-07-27 09:55:59', '2026-07-27 09:55:59', NULL, NULL, NULL, NULL),
(3, 6, 'PAY-20260727-UNUPK', 'cashier', NULL, NULL, NULL, '27000.00', '100000.00', '73000.00', 'berhasil', NULL, 1, NULL, '2026-07-27 09:56:10', '2026-07-27 09:56:10', NULL, '2026-07-27 09:56:10', '2026-07-27 09:56:10', NULL, NULL, NULL, NULL),
(4, 9, 'PAY-20260727-DTTQJ', 'cashier', NULL, NULL, NULL, '12000.00', '15000.00', '3000.00', 'berhasil', NULL, 1, NULL, '2026-07-27 10:23:21', '2026-07-27 10:23:21', NULL, '2026-07-27 10:23:21', '2026-07-27 10:23:21', NULL, NULL, NULL, NULL),
(5, 8, 'PAY-20260728-2K44U', 'cashier', NULL, NULL, NULL, '20000.00', '25000.00', '5000.00', 'berhasil', NULL, 1, NULL, '2026-07-28 10:11:37', '2026-07-28 10:11:37', NULL, '2026-07-28 10:11:37', '2026-07-28 10:11:37', NULL, NULL, NULL, NULL),
(6, 5, 'PAY-20260728-C0UZC', 'cashier', NULL, NULL, NULL, '15000.00', '20000.00', '5000.00', 'berhasil', NULL, 1, NULL, '2026-07-28 10:15:17', '2026-07-28 10:15:17', NULL, '2026-07-28 10:15:17', '2026-07-28 10:15:17', NULL, NULL, NULL, NULL),
(7, 10, 'PAY-20260801-5LBJT', 'cashier', NULL, NULL, NULL, '35000.00', '40000.00', '5000.00', 'berhasil', NULL, 1, NULL, '2026-08-01 02:31:16', '2026-08-01 02:31:16', NULL, '2026-08-01 02:31:16', '2026-08-01 02:31:16', NULL, NULL, NULL, NULL),
(8, 13, 'PAY-20260801-1H6RS', 'cashier', NULL, NULL, NULL, '12000.00', '50000.00', '38000.00', 'berhasil', NULL, 1, NULL, '2026-08-01 09:23:09', '2026-08-01 09:23:09', NULL, '2026-08-01 09:23:09', '2026-08-01 09:23:09', NULL, NULL, NULL, NULL),
(9, 12, 'PAY-20260801-ZBELC', 'cashier', NULL, NULL, NULL, '20000.00', '25000.00', '5000.00', 'berhasil', NULL, 1, NULL, '2026-08-01 09:45:09', '2026-08-01 09:45:09', NULL, '2026-08-01 09:45:09', '2026-08-01 09:45:09', NULL, NULL, NULL, NULL),
(10, 14, 'PAY-20260801-ZM1HR', 'qris', 'midtrans', 'QRIS-14-20260801182751-5YQFKJ', '4869d122-ce68-4899-9673-7a3d9b88ebbb', '15000.00', NULL, NULL, 'menunggu_verifikasi', NULL, NULL, NULL, NULL, NULL, NULL, '2026-08-01 11:27:51', '2026-08-01 18:36:31', 'https://api.sandbox.midtrans.com/v2/qris/4869d122-ce68-4899-9673-7a3d9b88ebbb/qr-code', '00020101021226620014COM.GO-JEK.WWW011993600914337867951480210M3786795140303UKE51440014ID.CO.QRIS.WWW0215AID3126925099070303UKE5204362653033605405150005802ID5911tunasBimbel6006BANJAR61054631362395028A120260801182752YqLuErMYxOID0703A016304EF6B', '2026-08-01 18:42:51', '{\"currency\": \"IDR\", \"metadata\": {\"local_order_id\": \"14\", \"local_payment_id\": \"10\"}, \"order_id\": \"QRIS-14-20260801182751-5YQFKJ\", \"expiry_time\": \"2026-08-02 01:42:51\", \"merchant_id\": \"M378679514\", \"status_code\": \"201\", \"fraud_status\": \"accept\", \"gross_amount\": \"15000.00\", \"payment_type\": \"qris\", \"signature_key\": \"6d711b8465961f23c38890e3f02725a4ff58e23389e75b3a6823d643cabf6927374ce9142172f3b3503632fd145b9aeabefbd9649f2ee63022182d788db993b1\", \"status_message\": \"Success, transaction is found\", \"transaction_id\": \"4869d122-ce68-4899-9673-7a3d9b88ebbb\", \"transaction_time\": \"2026-08-02 01:27:52\", \"transaction_status\": \"pending\"}'),
(11, 15, 'PAY-20260802-1WKVU', 'qris', 'midtrans', 'QRIS-15-20260802013659-KBISAS', 'cf4ad85b-2576-4bd8-bf02-b06f1f4da753', '15000.00', NULL, NULL, 'berhasil', NULL, NULL, NULL, '2026-08-01 18:39:15', '2026-08-01 18:39:16', NULL, '2026-08-01 18:36:59', '2026-08-01 18:39:16', 'https://api.sandbox.midtrans.com/v2/qris/cf4ad85b-2576-4bd8-bf02-b06f1f4da753/qr-code', '00020101021226620014COM.GO-JEK.WWW011993600914337867951480210M3786795140303UKE51440014ID.CO.QRIS.WWW0215AID3126925099070303UKE5204362653033605405150005802ID5911tunasBimbel6006BANJAR61054631362395028A1202608011837001Hgc5Mna1IID0703A016304B6BF', '2026-08-01 18:51:59', '{\"issuer\": \"dana\", \"acquirer\": \"gopay\", \"currency\": \"IDR\", \"metadata\": {\"local_order_id\": \"15\", \"local_payment_id\": \"11\"}, \"order_id\": \"QRIS-15-20260802013659-KBISAS\", \"expiry_time\": \"2026-08-02 01:51:59\", \"merchant_id\": \"M378679514\", \"status_code\": \"200\", \"fraud_status\": \"accept\", \"gross_amount\": \"15000.00\", \"payment_type\": \"qris\", \"signature_key\": \"9f75271cc4c8e43ffc966c3c3cb12205119a51f36fd6331eb1321ebdefeea374fd9bbcfc1208ba474a2bfea460d33fc81b49017ccc3736ce6e8ef8ac43757d0d\", \"status_message\": \"Success, transaction is found\", \"transaction_id\": \"cf4ad85b-2576-4bd8-bf02-b06f1f4da753\", \"settlement_time\": \"2026-08-02 01:39:15\", \"transaction_time\": \"2026-08-02 01:37:00\", \"transaction_type\": \"off-us\", \"transaction_status\": \"settlement\"}'),
(12, 16, 'PAY-20260802-GXWYF', 'qris', 'midtrans', 'QRIS-16-20260802014047-YT0NFW', '24412d3f-f5a4-462f-943c-8c66fe2185e5', '12000.00', NULL, NULL, 'berhasil', NULL, NULL, NULL, '2026-08-01 18:41:07', '2026-08-01 18:41:09', NULL, '2026-08-01 18:40:47', '2026-08-01 18:41:09', 'https://api.sandbox.midtrans.com/v2/qris/24412d3f-f5a4-462f-943c-8c66fe2185e5/qr-code', '00020101021226620014COM.GO-JEK.WWW011993600914337867951480210M3786795140303UKE51440014ID.CO.QRIS.WWW0215AID3126925099070303UKE5204362653033605405120005802ID5911tunasBimbel6006BANJAR61054631362395028A120260801184048NSTJAZE9J8ID0703A0163044806', '2026-08-01 18:55:47', '{\"issuer\": \"gopay\", \"acquirer\": \"gopay\", \"currency\": \"IDR\", \"metadata\": {\"local_order_id\": \"16\", \"local_payment_id\": \"12\"}, \"order_id\": \"QRIS-16-20260802014047-YT0NFW\", \"expiry_time\": \"2026-08-02 01:55:47\", \"merchant_id\": \"M378679514\", \"status_code\": \"200\", \"fraud_status\": \"accept\", \"gross_amount\": \"12000.00\", \"payment_type\": \"qris\", \"signature_key\": \"596d7a16a35ff6c3dbc59a0efdd75f37d5f90c8323b289ad55612d0c924e05597d083687b714862cac3c5fe3ec311647c9867d5e2ffe50fc1a1be726f84b0387\", \"status_message\": \"Success, transaction is found\", \"transaction_id\": \"24412d3f-f5a4-462f-943c-8c66fe2185e5\", \"settlement_time\": \"2026-08-02 01:41:07\", \"transaction_time\": \"2026-08-02 01:40:48\", \"transaction_type\": \"on-us\", \"transaction_status\": \"settlement\", \"merchant_cross_reference_id\": \"8acdb300-dcbb-421e-8101-f23aea2894fe\"}'),
(13, 17, 'PAY-20260816-HIXSK', 'qris', 'midtrans', 'QRIS-17-20260816021749-K6UNBQ', 'b386615e-7b42-43a8-bd81-03664e1760ed', '15000.00', NULL, NULL, 'berhasil', NULL, NULL, NULL, '2026-08-15 19:22:23', '2026-08-15 19:22:26', NULL, '2026-08-15 19:17:49', '2026-08-15 19:22:26', 'https://api.sandbox.midtrans.com/v2/qris/b386615e-7b42-43a8-bd81-03664e1760ed/qr-code', '00020101021226620014COM.GO-JEK.WWW011993600914337867951480210M3786795140303UKE51440014ID.CO.QRIS.WWW0215AID3126925099070303UKE5204362653033605405150005802ID5911tunasBimbel6006BANJAR61054631362395028A120260815191750fYS41MmiI2ID0703A016304411C', '2026-08-15 19:32:49', '{\"issuer\": \"gopay\", \"acquirer\": \"gopay\", \"currency\": \"IDR\", \"metadata\": {\"local_order_id\": \"17\", \"local_payment_id\": \"13\"}, \"order_id\": \"QRIS-17-20260816021749-K6UNBQ\", \"expiry_time\": \"2026-08-16 02:32:49\", \"merchant_id\": \"M378679514\", \"status_code\": \"200\", \"fraud_status\": \"accept\", \"gross_amount\": \"15000.00\", \"payment_type\": \"qris\", \"signature_key\": \"e54deb0cb6cdef61dfd576d3ee82674ee5c48f6c018b5e90f0b9ba52767354d4b9fe213494feaf4eb92b088607c6601d73244354e1226aaaf4784a9fe9744d27\", \"status_message\": \"Success, transaction is found\", \"transaction_id\": \"b386615e-7b42-43a8-bd81-03664e1760ed\", \"settlement_time\": \"2026-08-16 02:22:23\", \"transaction_time\": \"2026-08-16 02:17:50\", \"transaction_type\": \"on-us\", \"transaction_status\": \"settlement\", \"merchant_cross_reference_id\": \"8acdb300-dcbb-421e-8101-f23aea2894fe\"}');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('fhWYmXVYflMlx3Pp1KLqtCJoLVByVRUXY3oZ0wR9', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 Edg/151.0.0.0', 'YTo2OntzOjY6Il90b2tlbiI7czo0MDoidU82QXNwZXg3MWNpMldwWGtDNWNLVVNIVjZ1MlRsVlRQdTNmZVJlcSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzY6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9wYXltZW50cyI7czo1OiJyb3V0ZSI7czozOToiZmlsYW1lbnQuYWRtaW4ucmVzb3VyY2VzLnBheW1lbnRzLmluZGV4Ijt9czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MjtzOjE3OiJwYXNzd29yZF9oYXNoX3dlYiI7czo2NDoiMmViMDIyMGJiZWJlNzFhZjA1NGY1ZGE5ZTdhZjEyZmZjNzJjNTEwMGJiNjIwNGI4N2ExYTI3NzA1MGU4OTgwNSI7czo2OiJ0YWJsZXMiO2E6Mzp7czo0MDoiZTc5M2EyNzlkNTZlNDUwNjA5NzU0MDIwZDYyN2JlZWNfY29sdW1ucyI7YTo4OntpOjA7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6OToiYXR0ZW50aW9uIjtzOjU6ImxhYmVsIjtzOjA6IiI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjE7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTI6ImNhc2hpZXJfY29kZSI7czo1OiJsYWJlbCI7czoxMDoiS29kZSBCYXlhciI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjI7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MjI6ImNhZmVUYWJsZS50YWJsZV9udW1iZXIiO3M6NToibGFiZWwiO3M6MTY6Ik1lamEgLyBQZWxhbmdnYW4iO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTozO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjEzOiJpdGVtc19zdW1tYXJ5IjtzOjU6ImxhYmVsIjtzOjExOiJJc2kgUGVzYW5hbiI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjQ7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTI6InRvdGFsX2Ftb3VudCI7czo1OiJsYWJlbCI7czo1OiJUb3RhbCI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjU7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTQ6InBheW1lbnRfc3RhdHVzIjtzOjU6ImxhYmVsIjtzOjEwOiJQZW1iYXlhcmFuIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6NjthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo2OiJzdGF0dXMiO3M6NToibGFiZWwiO3M6MTQ6IlN0YXR1cyBQZXNhbmFuIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6NzthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxMDoib3JkZXJlZF9hdCI7czo1OiJsYWJlbCI7czo1OiJNYXN1ayI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO319czozNzoiZTc5M2EyNzlkNTZlNDUwNjA5NzU0MDIwZDYyN2JlZWNfc29ydCI7TjtzOjQwOiI5OTg3ZDUyNDBlYjMwNGQ4Y2YxZGU5YjgwZjRlOWY0N19jb2x1bW5zIjthOjExOntpOjA7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTI6InBheW1lbnRfY29kZSI7czo1OiJsYWJlbCI7czoxNToiS29kZSBQZW1iYXlhcmFuIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MTthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxNjoib3JkZXIub3JkZXJfY29kZSI7czo1OiJsYWJlbCI7czoxNDoiS29kZSBQZWxhbmdnYW4iO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aToyO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjE5OiJvcmRlci5jdXN0b21lcl9uYW1lIjtzOjU6ImxhYmVsIjtzOjE0OiJOYW1hIFBlbGFuZ2dhbiI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjM7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6Mjg6Im9yZGVyLmNhZmVUYWJsZS50YWJsZV9udW1iZXIiO3M6NToibGFiZWwiO3M6MTA6Ik5vbW9yIE1lamEiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTo0O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjY6Im1ldGhvZCI7czo1OiJsYWJlbCI7czoxNzoiTWV0b2RlIFBlbWJheWFyYW4iO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTo1O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjY6ImFtb3VudCI7czo1OiJsYWJlbCI7czoxMzoiVG90YWwgVGFnaWhhbiI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjY7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTU6ImFtb3VudF9yZWNlaXZlZCI7czo1OiJsYWJlbCI7czoxMzoiVWFuZyBEaXRlcmltYSI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjc7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTM6ImNoYW5nZV9hbW91bnQiO3M6NToibGFiZWwiO3M6OToiS2VtYmFsaWFuIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6ODthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo2OiJzdGF0dXMiO3M6NToibGFiZWwiO3M6NjoiU3RhdHVzIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6OTthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxMzoidmVyaWZpZXIubmFtZSI7czo1OiJsYWJlbCI7czo1OiJLYXNpciI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjEwO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjc6InBhaWRfYXQiO3M6NToibGFiZWwiO3M6MTY6Ildha3R1IFBlbWJheWFyYW4iO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9fX19', 1786823521),
('GFmf8ueMvrVP6S9TVPzKBn3qkM1Wxrm3WKIXT0BG', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36 Edg/151.0.0.0', 'YToyOntzOjY6Il90b2tlbiI7czo0MDoiTGJIWjhadkFLQlNPZ3BmRDNkY2JZbE9HWmZab0JwNURRQkc2TTkwZiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1786819312),
('T1k72OfigxBTir4NrrCIlvLaXGtBK91Fih5YJdsl', NULL, '10.226.136.103', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Mobile Safari/537.36', 'YTo2OntzOjY6Il90b2tlbiI7czo0MDoidDNJZlBXVGlHMjR0UjZ4eG5rSlkxUWNMVVpEZnFaNkF4ODdJenoxciI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Nzg6Imh0dHA6Ly8xMC4yMjYuMTM2LjExNDo4MDAwL21lamEvMzI2NzFmMWMtYTE5OC00MzAwLTllZTQtNDlhNDM0YzJhYjYxL2tlcmFuamFuZyI7czo1OiJyb3V0ZSI7czoxOToiY3VzdG9tZXIuY2FydC5pbmRleCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NjoiX2NhY2hlIjthOjA6e31zOjI0OiJjdXN0b21lcl9jaGVja291dF90b2tlbnMiO2E6MDp7fXM6Mjc6InNlY29uZF9jYWZlX2N1c3RvbWVyX29yZGVycyI7YToxOntpOjE7YToxOntpOjA7aToxNzt9fX0=', 1786821755);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('admin','cashier') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'cashier',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `role`, `is_active`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Admin Second Cafe', 'admin@secondcafe.com', NULL, '$2y$12$ZWNITt3mf0Hz3ngQA7WvJuQ1w80ypF.5ItzjqXbI3P61IqpOhz2J.', 'admin', 1, NULL, '2026-07-12 10:28:02', '2026-07-12 10:28:02'),
(2, 'Kasir Second Cafe', 'kasir@secondcafe.com', NULL, '$2y$12$P6uf.Lf6Ua9TprzHISG0au4xETMFcf6AQmb/jLFSG5CLBpWzlIree', 'cashier', 1, NULL, '2026-07-12 10:46:39', '2026-07-12 10:46:39'),
(3, 'Kasir 2', 'Kasir@gmail.com', NULL, '$2y$12$Eh5SB.oZRzSmh0la8yYvI.eq.0aQyzm8Ydr78df/RB0amwcqva6jO', 'cashier', 1, NULL, '2026-08-01 08:44:09', '2026-08-01 08:44:09');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `cafe_tables`
--
ALTER TABLE `cafe_tables`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cafe_tables_table_number_unique` (`table_number`),
  ADD UNIQUE KEY `cafe_tables_qr_token_unique` (`qr_token`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `categories_slug_unique` (`slug`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `menus`
--
ALTER TABLE `menus`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `menus_slug_unique` (`slug`),
  ADD KEY `menus_category_id_is_available_index` (`category_id`,`is_available`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `orders_order_code_unique` (`order_code`),
  ADD UNIQUE KEY `orders_public_token_unique` (`public_token`),
  ADD KEY `orders_cafe_table_id_foreign` (`cafe_table_id`),
  ADD KEY `orders_status_ordered_at_index` (`status`,`ordered_at`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_items_menu_id_foreign` (`menu_id`),
  ADD KEY `order_items_order_id_menu_id_index` (`order_id`,`menu_id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `payments_payment_code_unique` (`payment_code`),
  ADD UNIQUE KEY `payments_gateway_order_id_unique` (`gateway_order_id`),
  ADD KEY `payments_verified_by_foreign` (`verified_by`),
  ADD KEY `payments_order_id_status_index` (`order_id`,`status`),
  ADD KEY `payments_gateway_transaction_id_index` (`gateway_transaction_id`),
  ADD KEY `payments_expires_at_index` (`expires_at`),
  ADD KEY `payments_receipt_emailed_at_index` (`receipt_emailed_at`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cafe_tables`
--
ALTER TABLE `cafe_tables`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `menus`
--
ALTER TABLE `menus`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `menus`
--
ALTER TABLE `menus`
  ADD CONSTRAINT `menus_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_cafe_table_id_foreign` FOREIGN KEY (`cafe_table_id`) REFERENCES `cafe_tables` (`id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_menu_id_foreign` FOREIGN KEY (`menu_id`) REFERENCES `menus` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payments_verified_by_foreign` FOREIGN KEY (`verified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
