<?php
// ============================================================================
//  Portable WebDev Stick - database editor entry point (WRAPPER)
//  Installs to:  www/AdminerEditor/index.php
//  (First rename the index.php already there to editor-6.1.2.php -
//   this wrapper hands control to that file at the bottom.)
//
//  This file is NOT the editor. Adminer and its Editor are configured by
//  code, not by a settings file: a wrapper defines adminer_object() - the
//  functions the editor asks for its connection, its database, its sign-in
//  check and its permanent-login key - and then includes the stock file.
//
//  All values come from SharedSettings/db-credentials.php, the file
//  1stRun.Setup generated. Upgrading the editor later means replacing
//  editor-6.1.2.php only; this wrapper and your credentials stay untouched.
// ============================================================================

$stickRoot = dirname(__DIR__, 2);
$credsFile = $stickRoot . '/SharedSettings/db-credentials.php';

if (!file_exists($credsFile)) {
    header('Content-Type: text/plain; charset=utf-8');
    echo "The database editor is not set up yet.\n\n"
    . "Run 1stRun.Setup (the setup script at the root of this stick) once,\n"
    . "then reload this page.\n";
    exit;
}

$stickCreds = require $credsFile;

function adminer_object() {
    class WebDevEditor extends \Adminer\Adminer {
        // How the EDITOR connects to MariaDB (with the webdev account).
        // Editor's users never see these credentials - see login() below.
        public function credentials() {
            global $stickCreds;
            $db = $stickCreds['db'];
            return [$db['host'] . ':' . $db['port'], $db['user'], $db['pass']];
        }
        // The one database this editor instance opens.
        public function database() {
            global $stickCreds;
            return $stickCreds['editor']['database'];
        }
        // The editor's own sign-in gate: a username and password chosen at
        // setup time, checked against the stored one-way hash. These are
        // NOT database credentials.
        public function login($login, $password) {
            global $stickCreds;
            return $login === $stickCreds['editor']['user']
            && password_verify($password, $stickCreds['editor']['pass_hash']);
        }
        // Key that encrypts the "permanent login" cookie. Generated at setup.
        // The parameter matches the stock class's signature in Editor 6.x -
        // it must be declared even though this wrapper ignores it, or PHP
        // refuses the class outright (a child may not accept FEWER arguments
        // than the method it overrides).
        public function permanentLogin($create = false) {
            global $stickCreds;
            return $stickCreds['editor']['permanent_key'];
        }
        public function name() {
            return 'WebDev Stick Editor';
        }
    }
    return new WebDevEditor;
}

include __DIR__ . '/editor-6.1.2.php';
