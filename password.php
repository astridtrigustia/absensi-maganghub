
<?php
session_start(); if(!isset($_SESSION['admin'])) die("Login dulu");
$path="admin_pass.txt";
$old=trim(file_get_contents($path));

if(isset($_POST['old'])){
 if($_POST['old']!==$old) die("Password lama salah");
 file_put_contents($path,$_POST['new']);
 echo "Password berhasil diubah!";
}
?>
<link rel='stylesheet' href='css/style.css'>
<div class='container'>
<h2>Ganti Password</h2>
<form method='POST'>
<input name='old' placeholder='Password lama'>
<input name='new' placeholder='Password baru'>
<button>Ubah</button>
</form>
</div>
