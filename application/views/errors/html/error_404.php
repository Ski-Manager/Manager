<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>404 Page Not Found</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<!-- Google Fonts: use display=swap for non-blocking font rendering -->
<link rel="preload" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'">
<noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap"></noscript>
<!-- Font Awesome loaded asynchronously to avoid render-blocking -->
<link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
<noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"></noscript>
<style>

body {
	font-family: 'Inter', 'Helvetica Neue', Helvetica, Arial, sans-serif;
	background: linear-gradient(to bottom right, #e0f2f7, #bbdefb);
	display: flex;
	justify-content: center;
	align-items: center;
	min-height: 100vh;
	margin: 0;
	color: #333;
	overflow: hidden;
}

@keyframes fadeInUp {
	from { opacity: 0; transform: translateY(20px); }
	to   { opacity: 1; transform: translateY(0); }
}

.error-container {
	background-color: #fff;
	padding: 40px;
	border-radius: 25px;
	box-shadow: 0 15px 30px rgba(0, 0, 0, 0.25);
	text-align: center;
	max-width: 500px;
	width: 90%;
	animation: fadeInUp 0.6s ease both;
}

.icon {
	font-size: 3em;
	color: #2196f3;
	margin-bottom: 15px;
}

.error-code {
	font-size: 5em;
	font-weight: 600;
	color: #2196f3;
	line-height: 1;
	margin-bottom: 10px;
}

h1 {
	color: #444;
	font-size: 1.5em;
	font-weight: 600;
	margin: 0 0 15px 0;
}

p {
	line-height: 1.8;
	font-size: 1em;
	color: #555;
	margin-bottom: 15px;
}

.error-message {
	background-color: #f5f5f5;
	border-left: 4px solid #2196f3;
	border-radius: 6px;
	padding: 12px 15px;
	text-align: left;
	font-size: 0.9em;
	color: #555;
	margin-bottom: 25px;
}

.btn-home {
	display: inline-block;
	background-color: #2196f3;
	color: #fff;
	text-decoration: none;
	padding: 12px 30px;
	border-radius: 50px;
	font-weight: 600;
	font-size: 1em;
	transition: background-color 0.2s ease, transform 0.2s ease;
}

.btn-home:hover {
	background-color: #1976d2;
	transform: translateY(-2px);
}

.btn-home:focus {
	outline: 2px solid #2196f3;
	outline-offset: 3px;
}

.contact-info {
	color: #666;
	font-size: 0.9em;
	margin-top: 25px;
}

.contact-info a {
	color: #2196f3;
	text-decoration: none;
	font-weight: 600;
	transition: color 0.2s ease;
}

.contact-info a:hover {
	color: #1976d2;
	text-decoration: underline;
}

@media (max-width: 550px) {
	.error-container {
		padding: 30px 20px;
	}
	.error-code {
		font-size: 4em;
	}
	h1 {
		font-size: 1.3em;
	}
}
</style>
<script src="https://pl28525965.profitablecpmratenetwork.com/ce/37/c2/ce37c287ef68836c85b2c1d396361fd6.js"></script>
</head>
<body>
	<div class="error-container" role="main">
		<div class="icon" aria-hidden="true"><i class="fas fa-map-signs"></i></div>
		<div class="error-code" aria-label="Error 404">404</div>
		<h1 class="h1"><?php echo $heading; ?></h1>
		<div class="error-message"><?php echo $message; ?></div>
		<a href="/" class="btn-home"><i class="fas fa-home" aria-hidden="true"></i>&nbsp; Go to Homepage</a>
		<div class="contact-info">
			<p>Need help? Contact us at <a href="mailto:contact@ski-manager.net">contact@ski-manager.net</a></p>
		</div>
	</div>
</body>
</html>