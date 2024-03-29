DROP TABLE IF EXISTS `files`;
CREATE TABLE `files` (
    `id_file` varchar(255) NOT NULL,
    `fullpath_encrypted` varchar(255) NOT NULL,
    `size` int(11) NOT NULL,
    `mimetype` varchar(255) NOT NULL,
    `id_user` int(11) NOT NULL,
    PRIMARY KEY (`id_file`),
    KEY `id_user` (`id_user`),
    CONSTRAINT `files_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;