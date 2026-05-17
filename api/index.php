<?php
/**
 * Vercel serverless entry point for CodeIgniter 3.
 *
 * All dynamic requests are routed here by vercel.json.
 * We change the working directory to the project root so CI3
 * finds its system/, application/, and vendor/ directories,
 * then delegate to the real index.php.
 */

// Move working directory to the project root (one level up from api/)
chdir(dirname(__DIR__));

// Boot CodeIgniter — __FILE__ inside index.php will resolve to the root,
// so FCPATH, BASEPATH, and APPPATH are all set correctly.
require dirname(__DIR__) . '/index.php';
