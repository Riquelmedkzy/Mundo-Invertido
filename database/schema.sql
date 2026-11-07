CREATE DATABASE IF NOT EXISTS `projinvertido_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `projinvertido_db`;
CREATE TABLE IF NOT EXISTS `users` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `name` varchar(100) NOT NULL,
 `email` varchar(150) NOT NULL,
 `password` varchar(255) NOT NULL,
 `phone` varchar(20) NOT NULL,
 `user_type` enum('adm','usu') NOT NULL DEFAULT 'usu',
 `created_at` timestamp NULL DEFAULT current_timestamp(),
 `reset_token` varchar(255) DEFAULT NULL,
 `reset_expires` datetime DEFAULT NULL,
 PRIMARY KEY (`id`), UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS `perfil` (
 `user_id` int(11) NOT NULL,
 `foto_perfil` longblob NOT NULL,
 PRIMARY KEY (`user_id`),
 CONSTRAINT `perfil_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS `messages` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `user_id` int(11) DEFAULT NULL,
 `name` varchar(100) NOT NULL,
 `status` enum('Pendente','Lida','Respondida','Resolvida','Em Análise','Lixeira') NOT NULL DEFAULT 'Pendente',
 `email` varchar(150) NOT NULL,
 `subject` varchar(150) DEFAULT NULL,
 `message` text NOT NULL,
 `created_at` timestamp NULL DEFAULT current_timestamp(),
 PRIMARY KEY (`id`), KEY `user_id` (`user_id`),
 CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS `audit_log` (
 `log_id` int(11) NOT NULL AUTO_INCREMENT,
 `user_id` int(11) NOT NULL,
 `action_type` varchar(100) NOT NULL,
 `details` text DEFAULT NULL,
 `timestamp` datetime NOT NULL DEFAULT current_timestamp(),
 PRIMARY KEY (`log_id`), KEY `user_id` (`user_id`),
 CONSTRAINT `audit_log_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
