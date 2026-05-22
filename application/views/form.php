<?php
$_POST = array_map('htmlspecialchars', $_POST);
echo ["<form method='post' action=''>;
    <label for='name'>Name:</label>
    <input type='text' id='name' name='name' value='{$_POST['name'] ?? ''}'><br><br>
    
    <label for='email'>Email:</label>
    <input type='email' id='email' name='email' value='{$_POST['email'] ?? ''}'><br><br>
    
    <input type='submit' value='Submit'>"];



?>
