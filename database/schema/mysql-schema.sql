/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `banks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `banks` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `account_number` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `bank_code` varchar(260) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_general_ci,
  `notes` text COLLATE utf8mb4_general_ci,
  `balance` decimal(10,0) NOT NULL DEFAULT '0',
  `status` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `entery_by_user` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `credit_sales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `credit_sales` (
  `id` int NOT NULL AUTO_INCREMENT,
  `transaction_id` int NOT NULL,
  `transaction_type` int DEFAULT NULL COMMENT '1=receiving,2=payment',
  `payment_type` int DEFAULT NULL COMMENT '1=cash,2=bank payment',
  `product_id` int NOT NULL,
  `tank_id` int NOT NULL,
  `vendor_id` int NOT NULL,
  `vendor_type` int NOT NULL COMMENT ' 1=supplier,2=customer,3=product,4=expense,5=income,6=bank,7=cash,8=mp,9=employee ',
  `vehicle_id` int NOT NULL,
  `quantity` decimal(20,2) NOT NULL,
  `rate` decimal(20,2) NOT NULL,
  `amount` decimal(20,2) NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `transasction_date` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `entery_by_user` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `current_stock`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `current_stock` (
  `id` int NOT NULL AUTO_INCREMENT,
  `product_id` int NOT NULL,
  `stock` double(20,2) NOT NULL,
  `stock_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `entery_by_user` int NOT NULL DEFAULT '0',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `customers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `company_name` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `phone` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_general_ci,
  `credit_limit` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `bank_account_number` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `balance` float NOT NULL DEFAULT '0',
  `notes` text COLLATE utf8mb4_general_ci,
  `status` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `entery_by_user` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `dip_charts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `dip_charts` (
  `did` int NOT NULL AUTO_INCREMENT,
  `tank_id` varchar(15) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `mm` varchar(20) COLLATE utf8mb4_general_ci DEFAULT '0',
  `liters` varchar(20) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`did`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `dips`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `dips` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tankId` varchar(15) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `productId` varchar(15) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `dip_value` varchar(25) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '1',
  `liters` int DEFAULT '0',
  `previous_stock` float NOT NULL DEFAULT '0',
  `dip_date` date DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `entery_by_user` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `drivers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `drivers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `driver_type` varchar(25) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `driver_name` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `first_mobile_no` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `second_mobile_no` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `address` varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `cnic` varchar(25) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `city` varchar(25) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `vehicle_no` varchar(25) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `reference` varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `entery_by_user` int NOT NULL DEFAULT '0',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `expense_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `expense_transactions` (
  `expense_trans_id` int NOT NULL AUTO_INCREMENT,
  `expense_id` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `expense_type_name` varchar(25) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `expense_amount` float DEFAULT '0',
  `description` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `expense_date` date DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`expense_trans_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `expenses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `expenses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `expense_name` varchar(25) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `expense_amount` float DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `entery_by_user` int NOT NULL DEFAULT '0',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `income_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `income_transactions` (
  `income_tarns_id` int NOT NULL AUTO_INCREMENT,
  `income_id` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `income_type_name` varchar(25) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `amount` float DEFAULT NULL,
  `description` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `income_date` date DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`income_tarns_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `incomes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `incomes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `income_name` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `income_amount` float DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `entery_by_user` int NOT NULL DEFAULT '0',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `journal_new`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `journal_new` (
  `id` int NOT NULL AUTO_INCREMENT,
  `voucher_id` varchar(255) DEFAULT NULL,
  `vendor_type` int NOT NULL COMMENT '1=supplier,2=customer,3=product,4=expense,5=income,6=bank,7=cash,8=mp,9=employee',
  `vendor_id` int NOT NULL,
  `amount` double NOT NULL,
  `debit_credit` int NOT NULL COMMENT '1=credit\r\n2=debit',
  `description` text,
  `transaction_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `entery_by_user` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `journals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `journals` (
  `jid` int NOT NULL AUTO_INCREMENT,
  `to_vendor` varchar(10) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `to_vendor_type` varchar(10) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `from_vendor` varchar(10) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `from_vendor_type` varchar(10) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `journal_amount` float DEFAULT '0',
  `description` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `transaction_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`jid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ledger`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ledger` (
  `ledger_id` int NOT NULL AUTO_INCREMENT,
  `transaction_id` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tank_id` int DEFAULT NULL,
  `product_id` int DEFAULT NULL,
  `purchase_type` int DEFAULT NULL COMMENT '1=purchase,2=sale,3=bank_payment,4=journal,5=income,6=expense,7=bank_receiving,8=cash_receiving,9=cash_payment,10=journal,11=mp,12=credit_sales',
  `vendor_type` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '1=supplier,2=customer,3=product,4=expense,5=income,6=bank,7=cash,8=mp,9=employee\r\n',
  `vendor_id` varchar(15) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `transaction_type` int NOT NULL DEFAULT '0' COMMENT '1=credit,2=debit',
  `amount` decimal(20,2) DEFAULT NULL,
  `previous_balance` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tarnsaction_comment` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `transaction_date` date DEFAULT NULL,
  `ledger_transaction_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `entery_by_user` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`ledger_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `action_type` varchar(100) NOT NULL,
  `action_description` text,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `model_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `model_has_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_roles` (
  `role_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `nozzle`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `nozzle` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `opening_reading` decimal(25,0) NOT NULL,
  `product_id` int NOT NULL,
  `tank_id` int NOT NULL,
  `closing_reading` decimal(20,0) DEFAULT NULL,
  `status` int NOT NULL DEFAULT '1' COMMENT '1=active, 0= not active',
  `notes` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `entery_by_user` int NOT NULL DEFAULT '0',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `product_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_categories` (
  `pc_id` int NOT NULL AUTO_INCREMENT,
  `category_name` varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`pc_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `products` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `book_stock` int NOT NULL,
  `is_dippable` int NOT NULL DEFAULT '1' COMMENT '1 = dippable\r\n0 = not dippable',
  `physical_stock` int DEFAULT NULL,
  `price` decimal(20,2) NOT NULL DEFAULT '0.00',
  `current_purchase` decimal(20,2) NOT NULL DEFAULT '0.00',
  `current_sale` decimal(20,2) NOT NULL DEFAULT '0.00',
  `product_amount` float NOT NULL DEFAULT '0',
  `notes` text COLLATE utf8mb4_general_ci NOT NULL,
  `status` int DEFAULT '1',
  `unit` varchar(25) COLLATE utf8mb4_general_ci DEFAULT 'liter',
  `tank_id` varchar(10) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `entery_by_user` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `purchase`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `purchase` (
  `id` int NOT NULL AUTO_INCREMENT,
  `purchase_date` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `supplier_id` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'this is vendor_id not just suppliers',
  `vendor_type` int NOT NULL DEFAULT '1' COMMENT '1=supplier,2=customer,3=product,4=expense,5=income,6=bank,7=cash,8=mp,9=employee',
  `product_id` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tank_id` int NOT NULL,
  `vehicle_no` text COLLATE utf8mb4_general_ci,
  `driver_no` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `apply_carriage` int NOT NULL DEFAULT '0',
  `comments` longtext COLLATE utf8mb4_general_ci,
  `terminal_id` varchar(15) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `previous_stock` decimal(20,2) NOT NULL DEFAULT '0.00',
  `stock` decimal(20,2) DEFAULT NULL,
  `rate` decimal(20,2) NOT NULL,
  `rate_adjustment` decimal(20,2) NOT NULL DEFAULT '0.00',
  `total_amount` decimal(30,2) DEFAULT NULL,
  `status` int NOT NULL DEFAULT '0',
  `image_path` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `sold_quantity` decimal(20,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `entery_by_user` int NOT NULL DEFAULT '0',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `purchase_chambers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `purchase_chambers` (
  `pc_id` int NOT NULL AUTO_INCREMENT,
  `purchase_id` varchar(25) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `product_code` varchar(25) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `inv_dip` varchar(25) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `inv_qty` varchar(25) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `cost_price` varchar(25) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `chamber_created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `entery_by_user` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`pc_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `purchase_chambers_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `purchase_chambers_details` (
  `pcd_id` int NOT NULL AUTO_INCREMENT,
  `purchase_id` varchar(15) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `capacity` varchar(15) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `dip_value` varchar(15) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '0',
  `rec_dip_value` varchar(15) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '0',
  `gain_loss` varchar(15) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '0',
  `dip_liters` varchar(15) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '0',
  `measurements` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `purchase_date` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `lorry_id` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `entery_by_user` int DEFAULT '0',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`pcd_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `role_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `sales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sales` (
  `id` int NOT NULL AUTO_INCREMENT,
  `product_id` int NOT NULL,
  `tank_id` int NOT NULL,
  `quantity` int NOT NULL,
  `previous_stock` decimal(20,2) NOT NULL DEFAULT '0.00',
  `rate` double NOT NULL DEFAULT '0',
  `amount` decimal(20,2) NOT NULL DEFAULT '0.00',
  `nozzle_id` int DEFAULT '0',
  `opening_reading` decimal(20,2) DEFAULT '0.00' COMMENT 'also opening stock',
  `closing_reading` decimal(20,2) DEFAULT '0.00' COMMENT 'also closing stock after sales',
  `test_sales` decimal(20,2) DEFAULT '0.00',
  `customer_id` varchar(11) COLLATE utf8mb4_general_ci NOT NULL COMMENT 'this is vendor id, not only customer id',
  `vendor_type` int NOT NULL DEFAULT '2' COMMENT '1=supplier,2=customer,3=product,4=expense,5=income,6=bank,7=cash,8=mp,9=employee',
  `terminal_id` int DEFAULT '0',
  `tank_lari_id` int NOT NULL,
  `freight` int DEFAULT '0' COMMENT '0=no,1=yes',
  `freight_charges` double DEFAULT '0',
  `notes` text COLLATE utf8mb4_general_ci,
  `status` int NOT NULL DEFAULT '1',
  `profit_loss_status` int DEFAULT '0' COMMENT '0=no,1=yes',
  `sales_type` varchar(50) COLLATE utf8mb4_general_ci DEFAULT '1',
  `profit` varchar(25) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '0',
  `create_date` varchar(30) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'sale date',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `entery_by_user` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text,
  `payload` longtext NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `settings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `logo_path` varchar(255) DEFAULT NULL,
  `company_name` text,
  `short_desc` text,
  `date_lock` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `suppliers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `suppliers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `phone` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_general_ci,
  `supplier_type` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `contact_person` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `item_type` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ntn_no` varchar(25) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `fax_no` varchar(25) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `gst_no` varchar(25) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `opening_balance` float NOT NULL DEFAULT '0',
  `closing_balance` float NOT NULL DEFAULT '0',
  `terms` text COLLATE utf8mb4_general_ci,
  `status` int NOT NULL DEFAULT '0' COMMENT '0=block,1=active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `entery_by_user` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tank_lari`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tank_lari` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `larry_name` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `chambers` float DEFAULT '0',
  `lari_margin` float NOT NULL DEFAULT '0',
  `supplier_id` varchar(10) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `customer_id` int DEFAULT NULL,
  `driver_id` int DEFAULT NULL,
  `chamber_dip_one` varchar(15) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `chamber_capacity_one` varchar(15) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `chamber_dip_two` varchar(15) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `chamber_capacity_two` varchar(15) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `chamber_dip_three` varchar(15) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `chamber_capacity_three` varchar(15) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `chamber_dip_four` varchar(15) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `chamber_capacity_four` varchar(15) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `chamber_dip_five` varchar(15) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `chamber_capacity_five` varchar(15) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `chamber_dip_six` varchar(15) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `chamber_capacity_six` varchar(15) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `larry_status` int NOT NULL DEFAULT '0',
  `tank_type` varchar(10) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '1' COMMENT '1=tank_lari,2=transport, 3= customer vehicle',
  `register_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `entery_by_user` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tanks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tanks` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tank_name` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tank_limit` decimal(20,2) DEFAULT '0.00',
  `opening_stock` decimal(20,2) DEFAULT '0.00',
  `is_dippable` int NOT NULL DEFAULT '1' COMMENT '1 = dippable \r\n,0 = not dippable ',
  `cost_price` float DEFAULT '0',
  `sales_price` float DEFAULT '0',
  `ob_date` varchar(15) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `product_id` varchar(15) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `notes` longtext COLLATE utf8mb4_general_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `entery_by_user` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `terminals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `terminals` (
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_general_ci,
  `notes` text COLLATE utf8mb4_general_ci,
  `entery_by_user` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `transactions` (
  `tid` int NOT NULL AUTO_INCREMENT,
  `vendor_id` varchar(15) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `vendor_name` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `vendor_type` int DEFAULT NULL COMMENT '1=supplier,2=customer,3=product,4=expense,5=income,6=bank,7=cash,8=mp',
  `transaction_type` int DEFAULT '1' COMMENT '1=receiving,2=payment',
  `payment_type` int NOT NULL DEFAULT '1' COMMENT '1=cash,2=bank payment',
  `bank_id` varchar(10) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `bank_name` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `amount` decimal(20,2) NOT NULL DEFAULT '0.00',
  `customer_balance` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `transaction_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `entery_by_user` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`tid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_type` int NOT NULL DEFAULT '2' COMMENT '0=superAdmin,1=admin,2=employee',
  `tab_access` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_account_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1' COMMENT '1=active,0=block',
  `reset_token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `address` text COLLATE utf8mb4_unicode_ci,
  `balance` decimal(10,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `entery_by_user` int unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `entery_by_user_idx` (`entery_by_user`),
  CONSTRAINT `fk_users_entery_by_user` FOREIGN KEY (`entery_by_user`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (2,'0001_01_01_000001_create_cache_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (3,'0001_01_01_000002_create_jobs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (4,'0001_01_01_000000_create_users_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (5,'2023_07_01_000001_create_purchase_chambers_table',3);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (6,'2025_08_27_061458_create_permission_tables',4);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (7,'2025_08_27_095726_add_updated_at_column_in_transactions_table',5);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (8,'2025_09_05_111227_add_system_locked_permission',6);
