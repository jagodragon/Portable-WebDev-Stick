-- ============================================================================
--  Portable WebDev Stick - first-run account setup (SHARED TEMPLATE)
--  Installs to:  SharedSettings/setup.sql
--
--  This file is a template. The 1stRun.Setup scripts replace the three tokens
--  below with the values typed at their prompts (safely escaped by
--  setup-generate.php), run the result ONCE against a MariaDB session started
--  with --skip-grant-tables, and delete the filled-in copy.
--  Never fill this file in by hand, and never commit a filled-in copy.
--
--  Tokens:  __ROOT_PW__   __WEBDEV_PW__   __EDITOR_DB__
-- ============================================================================

-- FLUSH PRIVILEGES must come first: after a --skip-grant-tables start, MariaDB
-- refuses account-management statements until the privilege tables are loaded.
FLUSH PRIVILEGES;

-- Root: make sure every local host form exists, then set the same password on
-- each. (Socket connections arrive as 'localhost', TCP as '127.0.0.1'.)
CREATE USER IF NOT EXISTS 'root'@'localhost' IDENTIFIED BY '__ROOT_PW__';
ALTER USER 'root'@'localhost' IDENTIFIED BY '__ROOT_PW__';
CREATE USER IF NOT EXISTS 'root'@'127.0.0.1' IDENTIFIED BY '__ROOT_PW__';
ALTER USER 'root'@'127.0.0.1' IDENTIFIED BY '__ROOT_PW__';
CREATE USER IF NOT EXISTS 'root'@'::1' IDENTIFIED BY '__ROOT_PW__';
ALTER USER 'root'@'::1' IDENTIFIED BY '__ROOT_PW__';

-- webdev: the everyday account your projects and the database editor connect
-- with. Both host forms, for the same reason as root.
CREATE USER IF NOT EXISTS 'webdev'@'localhost' IDENTIFIED BY '__WEBDEV_PW__';
ALTER USER 'webdev'@'localhost' IDENTIFIED BY '__WEBDEV_PW__';
CREATE USER IF NOT EXISTS 'webdev'@'127.0.0.1' IDENTIFIED BY '__WEBDEV_PW__';
ALTER USER 'webdev'@'127.0.0.1' IDENTIFIED BY '__WEBDEV_PW__';

-- A development-sandbox account: full rights on THIS local server, but it
-- cannot hand those rights to anyone else (no GRANT OPTION), and none of
-- these accounts accept connections from other machines.
GRANT ALL PRIVILEGES ON *.* TO 'webdev'@'localhost';
GRANT ALL PRIVILEGES ON *.* TO 'webdev'@'127.0.0.1';

-- The database the editor opens. Created here so the editor always lands on
-- a database that exists. (Your projects may create more, any time.)
CREATE DATABASE IF NOT EXISTS `__EDITOR_DB__` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

FLUSH PRIVILEGES;
