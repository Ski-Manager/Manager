<?php defined('BASEPATH') OR exit('No direct script access allowed');
/*
| -------------------------------------------------------------------
|  Google OAuth Configuration
| -------------------------------------------------------------------
|
|  google_client_id   string   Your Google OAuth 2.0 Client ID.
|
|  The client secret is intentionally not stored here.
|  ID tokens are verified server-side via Google's tokeninfo endpoint,
|  which requires only the client_id to validate the 'aud' claim.
|
|  google_signin_enabled  bool  Whether to render the Google Sign-In
|  button and load the GSI library.  Set to FALSE on localhost because
|  localhost is not a registered JavaScript origin for the OAuth client,
|  which would cause the GSI iframe request to fail with HTTP 400 and
|  the browser console message "The given origin is not allowed for the
|  given client ID."
*/
$config['google_client_id'] = '661843131336-v559t5tq8kkj5nraan2v8s4vemtednfo.apps.googleusercontent.com';

// Disable the Google Sign-In button on localhost to prevent the 400 error
// caused by the origin not being registered in the Google Cloud Console.
// HOST_TYPE is always defined in index.php ('localhost', 'subdomain', or 'site')
// before CodeIgniter loads config files.  The defined() guard is a conservative
// fallback for CLI or edge-case contexts; if it were absent GSI stays disabled,
// which is the safe default.
$config['google_signin_enabled'] = (defined('HOST_TYPE') && HOST_TYPE !== 'localhost');
