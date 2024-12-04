--
-- MySQL 5.5.5
-- Wed, 04 Dec 2024 23:26:14 +0000
--


CREATE DATABASE IF NOT EXISTS digital_wallet;
USE digital_wallet;

CREATE TABLE `bank_details` (
   `id` int(11) not null auto_increment,
   `user_id` int(11) not null,
   `account_name` varchar(255) not null,
   `account_number` varchar(20) not null,
   `bank_name` varchar(255) not null,
   PRIMARY KEY (`id`),
   KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=2;


CREATE TABLE `feedback` (
   `id` int(11) not null auto_increment,
   `user_name` varchar(255) not null,
   `email` varchar(255) not null,
   `message` text not null,
   `image_path` varchar(255),
   `created_at` timestamp not null default CURRENT_TIMESTAMP,
   PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=3;


CREATE TABLE `transactions` (
   `id` int(11) not null auto_increment,
   `user_id` int(11) not null,
   `amount` decimal(10,2) not null,
   `type` enum('deposit','withdrawal') not null,
   `status` enum('pending','completed','failed') default 'pending',
   `paystack_reference` varchar(50),
   `created_at` timestamp not null default CURRENT_TIMESTAMP,
   PRIMARY KEY (`id`),
   UNIQUE KEY (`paystack_reference`),
   KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=29;


CREATE TABLE `users` (
   `id` int(11) not null auto_increment,
   `name` varchar(181) not null,
   `email` varchar(181) not null,
   `password` varchar(181) not null,
   `created_at` timestamp not null default CURRENT_TIMESTAMP,
   `bank_account_number` varchar(20),
   `bank_name` varchar(255),
   `account_type` varchar(20),
   `recipient_code` varchar(255),
   PRIMARY KEY (`id`),
   UNIQUE KEY (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=12;


CREATE TABLE `wallets` (
   `id` int(11) not null auto_increment,
   `user_id` int(11) not null,
   `balance` decimal(10,2) default '0.00',
   `virtual_account` varchar(20),
   `created_at` timestamp not null default CURRENT_TIMESTAMP,
   PRIMARY KEY (`id`),
   UNIQUE KEY (`user_id`),
   UNIQUE KEY (`virtual_account`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=12;


CREATE TABLE `withdraw_requests` (
   `id` int(11) not null auto_increment,
   `user_id` int(11) not null,
   `amount` decimal(10,2) not null,
   `account_number` varchar(20) not null,
   `bank_name` varchar(100) not null,
   `account_type` varchar(50),
   `status` enum('pending','approved','rejected') default 'pending',
   `created_at` timestamp not null default CURRENT_TIMESTAMP,
   PRIMARY KEY (`id`),
   KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=8;


CREATE TABLE `withdrawals` (
   `id` int(11) not null auto_increment,
   `user_id` int(11) not null,
   `amount` decimal(10,2) not null,
   `account_number` varchar(20) not null,
   `bank_name` varchar(100) not null,
   `account_type` varchar(20) not null,
   `status` varchar(20) default 'pending',
   `name` varchar(255) not null,
   `created_at` timestamp not null default CURRENT_TIMESTAMP,
   `updated_at` timestamp not null default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
   PRIMARY KEY (`id`),
   KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=3
