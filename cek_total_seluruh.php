<?php session_start();

// memasukan file db.php
include 'db.php';
include 'sanitasi.php';

$session_id = session_id();
//cek ada tidaknya barang di tbs penjualan
 $querycek = $db->query("SELECT kode_barang FROM tbs_penjualan WHERE session_id = '$session_id'");
 $cek = mysqli_num_rows($querycek);

// menampilakn hasil penjumlah subtotal ALIAS total penjualan dari tabel tbs_penjualan berdasarkan data no faktur
 $query = $db->query("SELECT kode_barang,SUM(subtotal) AS total_penjualan FROM tbs_penjualan WHERE session_id = '$session_id'");
 // menyimpan data sementara yg ada pada $query
 $data = mysqli_fetch_array($query);
 if ($cek > 0) {
 	echo json_encode($data);
 }
 else{
 	echo 0;
 }

?>