-- One-time setup when the DB container already existed before docker/db/init was added.
-- Run: docker exec -i risistar-db-1 mariadb -u root -proot < scripts/grant_test_database.sql

CREATE DATABASE IF NOT EXISTS `2moons_test` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
GRANT ALL PRIVILEGES ON `2moons_test`.* TO '2moons'@'%';
FLUSH PRIVILEGES;
