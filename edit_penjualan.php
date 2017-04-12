<?php include 'session_login.php';

// memasukan file session login,  header, navbar, db.php,
include 'header.php';
include 'navbar.php';
include 'db.php';
include 'sanitasi.php';

 
 $nomor_faktur = stringdoang($_GET['no_faktur']);
 $id_pelanggan = stringdoang($_GET['kode_pelanggan']);
 $nama_gudang = stringdoang($_GET['nama_gudang']);
 $kode_gudang = stringdoang($_GET['kode_gudang']);


    $select_pel = $db->query("SELECT kode_pelanggan,nama_pelanggan FROM pelanggan WHERE id = '$id_pelanggan'");
    $nma_pelanggan = mysqli_fetch_array($select_pel);
    $kode_pelanggan = $nma_pelanggan['kode_pelanggan'];

//perhitungan jumlah bayar lama (jumlah_bayar + potongan)
    $jumlah_bayar_piutang = $db->query("SELECT SUM(jumlah_bayar) AS jumlah_bayar, SUM(potongan) AS potongan FROM detail_pembayaran_piutang WHERE no_faktur_penjualan = '$nomor_faktur'");
    $ambil_jumlah = mysqli_fetch_array($jumlah_bayar_piutang);
    $jumlah_bayar = $ambil_jumlah['jumlah_bayar'];
    $potongan_piutang = $ambil_jumlah['potongan'];
    $jumlah_bayar_lama = $jumlah_bayar + $potongan_piutang;
//perhitungan jumlah bayar (jumlah_bayar + potongan)

//ambil data no_faktur yang di edit dari penjualan
    $data_potongan = $db->query("SELECT biaya_admin,potongan,tax,ppn,total,tanggal FROM penjualan WHERE no_faktur = '$nomor_faktur'");
    $ambil_potongan = mysqli_fetch_array($data_potongan);
    $potongan = $ambil_potongan['potongan'];
    $ppn = $ambil_potongan['ppn'];
    $tax = $ambil_potongan['tax'];
    $biaya_admin = $ambil_potongan['biaya_admin'];
    $tanggal_ganti = $ambil_potongan['tanggal'];
    $total_akhir = $ambil_potongan['total'];
//ambil data no_faktur yang di edit dari penjualan

//ambil data untuk form pembayaran yang akan di edit diambil dari perhitunga berikut
    $data_potongan_persen = $db->query("SELECT SUM(subtotal) AS subtotal FROM detail_penjualan WHERE no_faktur = '$nomor_faktur'");
    $ambil_potongan_persen = mysqli_fetch_array($data_potongan_persen);
    $subtotal_persen = $ambil_potongan_persen['subtotal'];
   
    $potongan_persen = $potongan / $subtotal_persen * 100;
    $hasil_persen = intval($potongan_persen);

    $subtotal_tax = $subtotal_persen - $potongan;
    $hasil_sub = intval($subtotal_tax);

    $biaya_adm_persen = $biaya_admin / $subtotal_persen * 100;
    $hasil_adm = intval($biaya_adm_persen);

    $potongan_tax = $tax / $hasil_sub * 100;
    $hasil_tax = intval($potongan_tax);
//ambil data untuk form pembayaran yang akan di edit diambil dari perhitunga berikut

 ?>



 <style type="text/css">
  .disabled {
    opacity: 0.6;
    cursor: not-allowed;
    disabled: true;
}
</style>


<script>
  $(function() {
    $( ".tanggal" ).datepicker({dateFormat: "yy-mm-dd"});
  });
  </script>

<!--untuk membuat agar tampilan form terlihat rapih dalam satu tempat -->
 <div style="padding-left: 5%; padding-right: 5%">


  <!--membuat teks dengan ukuran h3-->      
  <h3>EDIT PENJUALAN : <?php echo $nomor_faktur;?></h3><br>


<!--membuat agar tabel berada dalam baris tertentu-->
 <div class="row">
<div class="col-sm-8">
  

<!-- membuat form menjadi beberpa bagian -->
<form enctype="multipart/form-data" role="form" action="formpenjualan.php" method="post ">
        

<div class="row">

<div class="form-group col-sm-4">
  <label> Kode Pelanggan </label>
  <select type="text" name="kode_pelanggan" id="kd_pelanggan" class="form-control chosen"  required="" autofocus="">
  <option value="<?php echo $kode_pelanggan; ?>"><?php echo $kode_pelanggan; ?> - <?php echo $nma_pelanggan['nama_pelanggan']; ?></option>   
  <?php 
    
    //untuk menampilkan semua data pada tabel pelanggan dalam DB
    $query = $db->query("SELECT kode_pelanggan,nama_pelanggan FROM pelanggan");

    //untuk menyimpan data sementara yang ada pada $query
    while($data = mysqli_fetch_array($query))
    {
    
    echo "<option value='".$data['kode_pelanggan'] ."' class='opt-pelanggan-".$data['kode_pelanggan']."' data-level='".$data['level_harga'] ."'>".$data['kode_pelanggan'] ." - ".$data['nama_pelanggan'] ."</option>";
    }
    
    
    ?>
  </select>
</div>

<div class="form-group  col-sm-2">
    <label> Gudang </label><br>
       <select name="kode_gudang" id="kode_gudang" class="form-control chosen" required="" >
          <option value="<?php echo $kode_gudang; ?>"><?php echo $nama_gudang; ?></option>
          <?php 
          
          // menampilkan seluruh data yang ada pada tabel suplier
          $query = $db->query("SELECT kode_gudang,nama_gudang FROM gudang");
          
          // menyimpan data sementara yang ada pada $query
          while($data = mysqli_fetch_array($query))
          {
          
          echo "<option value='".$data['kode_gudang'] ."'>".$data['nama_gudang'] ."</option>";
          }
          
          
          ?>
    </select>
</div>

<div class="form-group  col-sm-2">
    <label>Sales</label>
      <select name="sales" id="sales" class="form-control chosen" required="">
      <?php 
    
      //untuk menampilkan semua data pada tabel pelanggan dalam DB
      $query01 = $db->query("SELECT id,nama FROM user WHERE status_sales = 'Iya'");

      //untuk menyimpan data sementara yang ada pada $query
      while($data01 = mysqli_fetch_array($query01))
      {
      echo "<option value='".$data01['id'] ."'>".$data01['nama'] ."</option>";
      }
      ?>
  </select>
</div>

<div class="form-group col-sm-2">
  <label> Level Harga </label><br>
  <select type="text" name="level_harga" id="level_harga" class="form-control chosen" required="">
  <option value="harga_1">Level 1</option>
  <option value="harga_2">Level 2</option>
  <option value="harga_3">Level 3</option>
  <option value="harga_4">Level 4</option>
  <option value="harga_5">Level 5</option>
  <option value="harga_6">Level 6</option>
  <option value="harga_7">Level 7</option>
  </select>
</div>



<div class="form-group  col-sm-2">
      <label>PPN</label>
          <select name="ppn" id="ppn" class="form-control chosen">
            <option value="<?php echo $ppn; ?>"><?php echo $ppn; ?></option>  
            <option >Include</option>  
            <option >Exclude</option>
            <option >Non</option>          
     </select>
</div>

</div><!--end div row data informasi pelanggan -->


<div class="row">


    <input type="hidden"  style="height:15px;font-size:15px" name="no_faktur" id="nomor_faktur_penjualan" class="form-control" readonly="" value="<?php echo $nomor_faktur; ?>" required="" >



<div class="form-group  col-sm-2">
      <label> Tanggal </label>
      <input type="text" style="height:15px;font-size:15px" name="tanggal" id="tanggal"  value="<?php echo $tanggal_ganti; ?>" class="form-control tanggal" autocomplete="off" >
</div>

<input type="hidden" name="ppn_input" id="ppn_input" value="<?php echo $ppn; ?>" class="form-control" placeholder="ppn input">  
<input type="hidden" name="tipe_produk" id="tipe_produk" class="form-control" >  

</div>

</form><!--tag penutup form-->


<button type="button" id="cari_produk_penjualan" class="btn btn-info" data-toggle="modal" data-target="#myModal"><i class='fa  fa-search'></i>  Cari (F1) </button>
<button type="button" id="daftar_order" class="btn btn-success" data-toggle="modal" data-target="#modal_order"><i class='fa  fa-search'></i> Cari Order (F6) </button>

<?php 
$hud = $db->query("SELECT setting_tampil FROM setting_antrian");
$my = mysqli_fetch_array($hud);

if ($my['setting_tampil'] == 'Tampil')
{
?>



<button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample"><i class='fa fa-list-ol'> </i>
Antrian  </button>

<button class="btn btn-warning" type="button" data-toggle="collapse" data-target="#sss" aria-expanded="false" aria-controls="collapseExample"><i class='fa fa-list-ol'> </i>
Order </button>
</p>  
</form>

<style>


tr:nth-child(even){background-color: #f2f2f2}


</style>

<div class="collapse" id="collapseExample">
 <table id="tableuser" class="table-border table-sm">
   <thead>
  <th style='background-color: #4CAF50; color:white'>No Faktur </th>   
  <th style='background-color: #4CAF50; color:white'>Kode Pelanggan</th>   
  <th style='background-color: #4CAF50; color:white'>Nama Pelanggan</th>
  <th style='background-color: #4CAF50; color:white'>Subtotal</th>
  <th style='background-color: #4CAF50; color:white' > Bayar</th>
   </thead>
<tbody>

  <?php
                
                //menampilkan semua data yang ada pada tabel tbs penjualan dalam DB
                $perintah = $db->query("SELECT p.id,p.no_faktur,p.total,p.kode_pelanggan,p.tanggal,p.tanggal_jt,p.jam,p.user,p.sales,p.kode_meja,p.status,p.potongan,p.tax,p.sisa,p.kredit,g.nama_gudang,p.kode_gudang,pl.nama_pelanggan FROM penjualan p INNER JOIN gudang g ON p.kode_gudang = g.kode_gudang INNER JOIN pelanggan pl ON p.kode_pelanggan = pl.id WHERE p.status = 'Simpan Sementara' ORDER BY p.id DESC ");
                
                //menyimpan data sementara yang ada pada $perintah
                
                while ($data1 = mysqli_fetch_array($perintah))
                {
                //menampilkan data
                echo "<tr>
                <td style='font-size:15px'>". $data1['no_faktur'] ."</td>
                <td style='font-size:15px'>". $data1['kode_pelanggan'] ."</td>
                <td style='font-size:15px;'>". $data1['nama_pelanggan'] ."</td>
                <td style='font-size:15px'>". $data1['total'] ."</td>
                <td style='font-size:15px'>
                <a href='proses_pesanan_barang.php?no_faktur=".$data1['no_faktur']."&kode_pelanggan=".$data1['kode_pelanggan']."&nama_pelanggan=".$data1['nama_pelanggan']."&nama_gudang=".$data1['nama_gudang']."&kode_gudang=".$data1['kode_gudang']."' class='btn btn-warning' > Rp</a> 
                 </td>
                </tr>";
                }

                ?>


</tbody>
 </table>
</div>
<?php
}
?>

<!--tampilan modal-->
<div id="modal_order" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- isi modal-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Data Order</h4>
      </div>
        <div class="modal-body">
            <div class="table-resposive">
                <table id="table_order" align="center" class="table">
                <thead>
                <th> No Faktur Order  </th>
                <th >Kode Pelanggan</th>
                <th> Tanggal </th>
                <th> Jam </th>
                <th> Total </th>
                <th> Keterangan </th>
                <th> Petugas Kasir </th>             
                </thead>
          </table>
        </div>
    </div> <!-- tag penutup modal-body-->
      <div class="modal-footer">
        <button type="button" order="" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
  </div><!--  <div class="modal-content"> -->

  </div>
</div><!-- end of modal data barang  -->



<!--tampilan modal-->
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">

    <!-- isi modal-->
    <div class="modal-content">

      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <center><h4 class="modal-title"><b>Data Barang</b></h4></center>
      </div>
      <div class="modal-body">
        <div class="table-responsive">
          <table id="tabel_cari" class="table table-bordered table-sm">
            <thead> <!-- untuk memberikan nama pada kolom tabel -->
            <th> Kode Barang </th>
            <th> Nama Barang </th>
            <th> Harga Jual Level 1</th>
            <th> Harga Jual Level 2</th>
            <th> Harga Jual Level 3</th>
            <th> Harga Jual Level 4 </th>
            <th> Harga Jual Level 5</th>
            <th> Harga Jual Level 6</th>
            <th> Harga Jual Level 7</th>
            <th> Jumlah Barang </th>
            <th> Satuan </th>
            <th> Kategori </th>
            <th> Suplier </th>
        </thead> <!-- tag penutup tabel -->
      </table>
    </div>
</div> <!-- tag penutup modal-body-->
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
  </div><!--     <div class="modal-content"> -->
  </div>
</div><!-- end of modal data barang  -->


<!-- Modal Hapus data -->
<div id="modal_hapus" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Konfirmsi Hapus Data Edit Pembelian</h4>
      </div>
      <div class="modal-body">
   <p>Apakah Anda yakin Ingin Menghapus Data ini ?</p>
   <form >
    <div class="form-group">
     <input type="text" id="nama-barang" class="form-control" readonly=""> 
     <input type="text" id="kode-barang" class="form-control" readonly=""> 
     <input type="hidden" id="id_hapus" class="form-control" >
    </div>
   </form>
   
  <div class="alert alert-success" style="display:none">
   <strong>Berhasil!</strong> Data berhasil Di Hapus
  </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-info" id="btn_jadi_hapus">Ya</button>
        <button type="button" class="btn btn-warning" data-dismiss="modal">Batal</button>
      </div>
    </div>
  </div>
</div><!-- end of modal hapus data  -->               


<!-- Modal Untuk Confirm PESAN alert-->
<div id="modal_promo_alert" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>       
    </div>
    <div class="modal-body">
      <span id="tampil_alert">
      </span>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Closed</button>
    </div>
    </div>
  </div>
</div>
<!--modal end pesan alert-->



<div id="modal_alert" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h3 style="color:orange" class="modal-title"><span class="glyphicon glyphicon-info-sign">Info!</span></h3>
        <h4>Maaf No Transaksi <strong><?php echo $nomor_faktur; ?></strong> tidak dapat dihapus atau di edit, karena telah terdapat Transaksi Pembayaran Piutang atau Retur Penjualan. Dengan daftar sebagai berikut :</h4>
      </div>

      <div class="modal-body">
      <span id="modal-alert">
       </span>


     </div>

      <div class="modal-footer">
        <h6 style="text-align: left"><i> * jika ingin menghapus atau mengedit data,<br>
        silahkan hapus terlebih dahulu Transaksi Pembayaran Piutang atau Retur Penjualan</i></h6>
        <button type="button" class="btn btn-warning btn-close" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>


<!--form barcode barang -->
<form id="form_barcode" class="form-inline">
  <br>
    <div class="form-group">
        <input type="text" style="height:15px" name="kode_barcode" id="kode_barcode" class="form-control" placeholder="Kode Barcode">
    </div>     
    <button type="submit" id="submit_barcode" class="btn btn-primary" style="font-size:15px" ><i class="fa fa-barcode"></i> Submit Barcode</button>
             
</form>
<!--end form barcode barang -->



<!-- membuat form prosestbspenjual -->
<form class="form" action="proses_tambah_edit_penjualan.php" role="form" id="formtambahproduk">

<div class="row 1">

<div class="col-sm-3">
    <select style="font-size:15px; height:20px" type="text" name="kode_barang" id="kode_barang" class="form-control chosen" data-placeholder="SILAKAN PILIH...">
    <option value="">SILAKAN PILIH...</option>
       <?php 

        include 'cache.class.php';
          $c = new Cache();
          $c->setCache('produk');
          $data_c = $c->retrieveAll();

          foreach ($data_c as $key) {
            echo '<option id="opt-produk-'.$key['kode_barang'].'" value="'.$key['kode_barang'].'" data-kode="'.$key['kode_barang'].'" nama-barang="'.$key['nama_barang'].'" harga="'.$key['harga_jual'].'" harga_jual_2="'.$key['harga_jual2'].'" harga_jual_3="'.$key['harga_jual3'].'" harga_jual_4="'.$key['harga_jual4'].'" harga_jual_5="'.$key['harga_jual5'].'" harga_jual_6="'.$key['harga_jual6'].'" harga_jual_7="'.$key['harga_jual7'].'" satuan="'.$key['satuan'].'" kategori="'.$key['kategori'].'" status="'.$key['status'].'" suplier="'.$key['suplier'].'" limit_stok="'.$key['limit_stok'].'" ber-stok="'.$key['berkaitan_dgn_stok'].'" tipe_barang="'.$key['tipe_barang'].'" id-barang="'.$key['id'].'" > '. $key['kode_barang'].' ( '.$key['nama_barang'].' ) </option>';
          }

        ?>
    </select>
</div>

<input type="hidden" class="form-control" name="nama_barang" id="nama_barang" placeholder="Nama Barang" readonly="">

<div class="col-sm-2">
     <input type="text" style="height:15px;" class="form-control" name="jumlah_barang" autocomplete="off" id="jumlah_barang" placeholder="Jumlah Barang" required="">
 </div>

<div class="col-sm-2">
    <select type="text" style="font-size:15px; height:35px"  name="satuan_konversi" id="satuan_konversi" class="form-control"  required="" style="height:50px;font-size:15px; width: 100px" >    
          <?php 
          
          
          $query = $db->query("SELECT id, nama  FROM satuan");
          while($data = mysqli_fetch_array($query))
          {
          
          echo "<option value='".$data['id']."'>".$data['nama'] ."</option>";
          }
                      
          ?>        
    </select>
</div>
  
<div class="form-group col-sm-2">
    <input style="height:15px;" type="text" class="form-control" name="potongan" ata-toggle="tooltip" data-placement="top" title="Jika Ingin Potongan Dalam Bentuk Persen (%), input : 10%" autocomplete="off" id="potongan1" placeholder="Potongan " >
</div>


<div class="form-group col-sm-1">
    <input style="height:15px;font-size:15px;" type="text" class="form-control" name="tax" autocomplete="off" id="tax1"  placeholder="Tax(%)" >
</div>

<button type="submit" id="submit_produk" class="btn btn-success" style="font-size:15px" >Submit (F3) </button>



</div><!--end div class="row 1"-->


  <input type="hidden" class="form-control" name="jumlah_barang_tbs" id="jumlah_barang_tbs">
  <input type="hidden" class="form-control" name="real_id" id="real_id" value="<?php echo $id_pelanggan ?>">
  <input type="hidden" class="form-control" name="limit_stok" id="limit_stok">
  <input type="hidden" placeholder="Stok" class="form-control" name="jumlahbarang" id="jumlahbarang">
  <input type="hidden" class="form-control" name="ber_stok" id="ber_stok" placeholder="Ber Stok" >
  <input type="hidden" class="form-control" name="harga_lama" id="harga_lama">
  <input type="hidden" class="form-control" name="harga_baru" id="harga_baru">
  <input type="hidden" id="satuan_produk" name="satuan_produk" class="form-control" value="" >
  <input type="hidden" id="harga_produk" placeholder="Harga / Level" name="harga" class="form-control" value="" >
  <input type="hidden" id="id_produk" name="id_produk" placeholder="id_produk" class="form-control" value="" >  
  <input type="hidden" name="no_faktur" id="no_faktur0" class="form-control" value="<?php echo $nomor_faktur; ?>" >

</form> <!-- tag penutup form -->          
                
  <div class="table-responsive"> <!--tag untuk membuat garis pada tabel-->  
        <span id="span_tbs">  
            <table id="tabel_tbs_penjualan" class="table table-sm">
                <thead>
                    <th> Kode</th>
                    <th style="width:1000%"> Nama</th>
                    <th> Jumlah</th>
                    <th> Satuan </th>
                    <th> Harga </th>
                    <th> Potongan </th>
                    <th> Pajak </th>
                    <th> Subtotal </th>
                    <th> Hapus </th>
                    </thead>
                <tbody id="tbody">
                </tbody>              
            </table>
      </span>
</div>

                <h6 style="text-align: left ; color: red" ><i> * Klik 2x pada kolom jumlah barang jika ingin mengedit.</i></h6>
                <h6 style="text-align: left ;"><i>* Short Key (F2) untuk mencari Kode Produk atau Nama Produk.</i></h6>
                <h6 style="text-align: left ;"><i><b> * Data Order yang lama (Sudah tergabung). Silakan di Edit table di atas </b></i></h6>


<div class="collapse" id="sss">

<div class="card card-block">

<div class="row">
    <div class="col-sm-4">
        <span id="select_order">
            <select style="font-size:15px; height:35px" name="hapus_order" id="hapus_order" class="form-control gg" required="" >
          <?php 
          
          // menampilkan seluruh data yang ada pada tabel suplier
          $query = $db->query("SELECT no_faktur_order FROM tbs_penjualan WHERE no_faktur = '$nomor_faktur' AND no_faktur_order != '' ");
          
          // menyimpan data sementara yang ada pada $query
          while($data = mysqli_fetch_array($query))
          {
        
                echo "<option value='".$data['no_faktur_order'] ."'>".$data['no_faktur_order'] ."</option>";
          
          }
          
          
          ?>
          </select>
    <input type="hidden" class="form-control" name="total_perorder" id="total_perorder">
     </span>
</div>

   <div class="col-sm-4"> 
     <button type="submit" id="btn-hps-order" class="btn btn-danger" style="font-size:15px" > Hapus </button>
   </div> 

</div>

  <h5><b>Data Order</b></h5> 
        <div class="table-responsive"> <!--tag untuk membuat garis pada tabel-->
            <span id="order_data">  
                <table id="table_tbs_order" class="table table-sm">
                    <thead>
                    <th style="width:500%"> No Faktur Order  </th>
                    <th> Kode  </th>
                    <th style="width:1000%"> Nama </th>
                    <th> Jumlah </th>
                    <th> Satuan </th>
                    <th> Harga </th>
                    <th> Subtotal </th>
                    <th> Potongan </th>
                    <th> Pajak </th>
                  </thead> 
                </table>
          </span>
      </div>  

</div><!-- end <div class="card card-block"> -->
</div><!-- end <div class="colapse"> -->




</div><!--DIV END cOL SM 6 1-->

<div class="col-sm-4">
 <form action="proses_bayar_edit_jual.php" id="form_jual" method="POST" >
    
    <style type="text/css">
    .disabled {
    opacity: 0.6;
    cursor: not-allowed;
    disabled: false;
    }
    </style>

    <div class="card card-block">


<div class="row">

<div class="form-group  col-sm-6">
        <label> Subtotal </label><br>
        <input type="text" name="total" id="total2" class="form-control" style="height:15px;font-size:15px" placeholder="Total" readonly="" >
</div>

<div class="form-group col-sm-6">
       <label>Biaya Admin </label><br>
        <select class="form-control chosen" id="biaya_admin_select" name="biaya_admin_select" >
              <option value="0" selected=""> Silahkan Pilih </option>
                <?php 
                $get_biaya_admin = $db->query("SELECT persentase,nama FROM biaya_admin");
                while ( $take_admin = mysqli_fetch_array($get_biaya_admin))
                {
                echo "<option value='".$take_admin['persentase']."'>".$take_admin['nama']." ".$take_admin['persentase']."%</option>";
                }
                ?>
       </select>  
</div>

</div><!--end <div class="row">-->

<div class="row">

  <div class="col-xs-6">          
        <label>Biaya Admin (Rp)</label>
              <input type="text" name="biaya_admin_rupiah" value="<?php echo $biaya_admin ?>" style="height:15px;font-size:15px" id="biaya_adm" class="form-control" placeholder="Biaya Admin Rp" autocomplete="off" onkeydown="return numbersonly(this, event);" onkeyup="javascript:tandaPemisahTitik(this);">
  </div>

   <div class="col-xs-6">
      <label>Biaya Admin (%)</label>
            <input type="text" name="biaya_admin_persen" style="height:15px;font-size:15px" id="biaya_admin_persen" value="<?php echo intval($hasil_adm); ?>" class="form-control" placeholder="Biaya Admin %" autocomplete="off" >
   </div>

</div> <!--end <div class="row">-->

<div class="row">
    
    <div class="form-group col-sm-6">  
          <label> Diskon ( Rp )</label><br>
          <input type="text" name="potongan" id="potongan_penjualan" value="<?php echo $potongan ?>" style="height:15px;font-size:15px" class="form-control" placeholder="" autocomplete="off"  onkeydown="return numbersonly(this, event);" onkeyup="javascript:tandaPemisahTitik(this);">    
    </div>


    <div class="form-group col-sm-6">
        <label> Diskon ( % )</label><br>
        <input type="text" name="potongan_persen" id="potongan_persen" value="<?php echo intval($hasil_persen); ?>" style="height:15px;font-size:15px" class="form-control" placeholder="" autocomplete="off" >
   </div>

   <input type="text" style="display: none" name="tax" id="tax" value="<?php echo $hasil_tax; ?>" style="height:15px;font-size:15px" class="form-control"  autocomplete="off" >  

</div> <!--end <div class="row">-->
          
          
<div class="row">

  <div class="form-group col-sm-6">
      <label> Tanggal </label><br>
      <input type="text" name="tanggal_jt" id="tanggal_jt" style="height:15px;font-size:15px" placeholder="Tanggal JT" class="form-control tanggal" autocomplete="off">
  </div>


<div class="form-group  col-sm-6">
    <label> Cara Bayar </label><br>
        <select type="text" name="cara_bayar" id="carabayar1" class="form-control" required=""  >
          <option value=""> Silahkan Pilih </option>
             <?php 
             $sett_akun = $db->query("SELECT sa.kas, da.nama_daftar_akun FROM setting_akun sa INNER JOIN daftar_akun da ON sa.kas = da.kode_daftar_akun");
             $data_sett = mysqli_fetch_array($sett_akun);

             echo "<option selected value='".$data_sett['kas']."'>".$data_sett['nama_daftar_akun'] ."</option>";
             
             $query = $db->query("SELECT nama_daftar_akun, kode_daftar_akun FROM daftar_akun WHERE tipe_akun = 'Kas & Bank'");
             while($data = mysqli_fetch_array($query))
             {
              echo "<option value='".$data['kode_daftar_akun']."'>".$data['nama_daftar_akun'] ."</option>";
              }
             ?>
    </select>
</div>

</div> <!--end <div class="row">-->

          <input type="hidden" name="tax_rp" id="tax_rp" class="form-control"  autocomplete="off" >
           <label style="display: none"> Adm Bank  (%)</label>
          <input type="hidden" name="adm_bank" id="adm_bank"  value="" class="form-control" >
          
          
<div class="row">

    <div class="form-group col-sm-6">
          <label style="font-size:15px"> Total Akhir</label><br>
          <b><input type="text" name="total" id="total1" class="form-control" value="" style="height: 20px; width:90%; font-size:20px;"font-size:25px;" placeholder="Total" readonly="" ></b>
    </div>

    <div class="form-group col-sm-6">
        <label> Pembayaran </label><br>
        <b><input type="text" name="pembayaran" id="pembayaran_penjualan" style="height: 20px; width:90%; font-size:20px;" autocomplete="off" class="form-control"   style="font-size: 20px"  onkeydown="return numbersonly(this, event);" onkeyup="javascript:tandaPemisahTitik(this);"></b>
    </div>

</div> <!--end <div class="row">-->


<div class="row">

      <div class="col-sm-6">
          <label> Kembalian </label><br>
          <b><input type="text" name="sisa_pembayaran" id="sisa_pembayaran_penjualan" style="height:15px;font-size:15px" class="form-control"  readonly="" required=""  style="font-size: 20px" ></b>
      </div>
          
      <div class="col-sm-6">
          <label> Kredit </label><br>
          <b><input type="text" name="kredit" id="kredit" class="form-control" style="height:15px;font-size:15px"  readonly="" required="" ></b>
      </div>

</div> <!--end <div class="row">-->
          
      <label> Keterangan </label><br>
        <textarea type="text" name="keterangan" id="keterangan" class="form-control"> </textarea>

          <b><input type="hidden" name="zxzx" id="zxzx" class="form-control" style="height: 50px; width:90%; font-size:25px;"  readonly="" required="" ></b>
          <b><input type="hidden" name="jumlah_bayar_lama" id="jumlah_bayar_lama" value="<?php echo $jumlah_bayar_lama; ?>" class="form-control" style="height: 50px; width:90%; font-size:25px;"  readonly=""></b>

<?php 

if ($_SESSION['otoritas'] == 'Pimpinan') {
 echo '<label style="display:none"> Total Hpp </label><br>
          <input type="hidden" name="total_hpp" id="total_hpp" style="height: 50px; width:90%; font-size:25px;" class="form-control" placeholder="" readonly="" required="">';
}

mysqli_close($db); 
 ?>

          <input type="hidden" name="jumlah" id="jumlah1" class="form-control" placeholder="jumlah">   <br> 
          <!-- memasukan teks pada kolom kode pelanggan, dan nomor faktur penjualan namun disembunyikan -->
          <input type="hidden" name="no_faktur" id="nofaktur" class="form-control" value="<?php echo $nomor_faktur; ?>" required="" >
          <input type="hidden" name="kode_pelanggan" id="k_pelanggan" class="form-control" required="" >

</div>


    <button type="submit" id="penjualan" class="btn btn-info" data-faktur='<?php echo $nomor_faktur ?>' style="font-size:15px">Bayar (F8)</button>
    <button type="submit" id="piutang" class="btn btn-warning" data-faktur='<?php echo $nomor_faktur; ?>' style="font-size:15px">Piutang (F9)</button>


          
  <div class="row">
      <div class="col-sm-3">
      <a href="penjualan.php?status=semua" id="transaksi_baru" class="btn btn-primary"  style="display: none;">Kembali Ke Laporan</a>
  </div>
    
  <div class="col-sm-3">
      <a href='cetak_penjualan_tunai.php?no_faktur=<?php echo $nomor_faktur; ?>' id="cetak_tunai"  style="display: none;" class="btn btn-success" target="blank">Cetak Tunai </a>
  </div>

  <div class="col-sm-3">
      <a href='cetak_penjualan_tunai_besar.php?no_faktur=<?php echo $nomor_faktur; ?>' id="cetak_tunai_besar" style="display: none;"  class="btn btn-info" target="blank">Cetak Tunai Besar</a>
  </div>
    
  <div class="col-sm-3">
    <a href='cetak_penjualan_piutang.php?no_faktur=<?php echo $nomor_faktur ?>' id="cetak_piutang"  style="display: none;" class="btn btn-warning" target="blank"> <span class="  glyphicon glyphicon-print"> </span> Cetak Piutang </a>
  </div>

        </div>      
    </div>

</form>

</div>

</div><!-- end of row -->   
          
        <br>
          <div class="alert alert-success" id="alert_berhasil" style="display:none">
          <strong>Success!</strong> Pembayaran Berhasil
      </div>

</div><!-- end of container -->


    
<script>
//untuk menampilkan data tabel
$(document).ready(function(){
    $("#kd_pelanggan").focus();
});

</script>

<!-- SHORTCUT -->
<!-- js untuk tombol shortcut -->
 <script src="shortcut.js"></script>
<!-- js untuk tombol shortcut -->


<script type="text/javascript">
  //SELECT CHOSSESN    
$(".chosen").chosen({no_results_text: "Maaf, Data Tidak Ada!",search_contains:true});    
</script>


<script> 
    shortcut.add("f2", function() {
        // Do something

        $("#kode_barang").focus();

    });

    
    shortcut.add("f1", function() {
        // Do something

        $("#cari_produk_penjualan").click();

    }); 

    shortcut.add("f6", function() {
        // Do something

        $("#daftar_order").click();

    }); 
    
    shortcut.add("f3", function() {
        // Do something

        $("#submit_produk").click();

    }); 

    
    shortcut.add("f4", function() {
        // Do something

        $("#carabayar1").focus();

    }); 

    
    shortcut.add("f7", function() {
        // Do something

        $("#pembayaran_penjualan").focus();

    }); 

    
    shortcut.add("f8", function() {
        // Do something

        $("#penjualan").click();

    }); 

    
    shortcut.add("f9", function() {
        // Do something

        $("#piutang").click();

    }); 

    
    shortcut.add("f10", function() {
        // Do something

        $("#simpan_sementara").click();

    }); 

    
    shortcut.add("ctrl+b", function() {
        // Do something

    var session_id = $("#session_id").val()

        window.location.href="batal_penjualan.php?session_id="+session_id+"";


    }); 

     shortcut.add("ctrl+k", function() {
        // Do something

        $("#cetak_langsung").click();


    }); 
</script>
<!-- SHORTCUT -->


<!--DATA TABLE MENGGUNAKAN AJAX-->
<script type="text/javascript" language="javascript" >
      $(document).ready(function() {

  $(document).on('click', '#daftar_order', function (e) {

            $('#table_order').DataTable().destroy();

          var dataTable = $('#table_order').DataTable( {
          "processing": true,
          "serverSide": true,
          "ajax":{
            url :"datatable_daftar_order.php", // json datasource
           
            type: "post",  // method  , by default get
            error: function(){  // error handling
              $(".employee-grid-error").html("");
              $("#table_order").append('<tbody class="employee-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
              $("#employee-grid_processing").css("display","none");
            }
        },
            
            "fnCreatedRow": function( nRow, aData, iDataIndex ) {

              $(nRow).attr('class', "pilih_order");
              $(nRow).attr('data-order', aData[0]);
              $(nRow).attr('data-total', aData[4]);

          },
        });

        $("#form").submit(function(){
        return false;
        });
        

      } );
    } );
    </script>
<!--/DATA TABLE MENGGUNAKAN AJAX-->


<!--untuk memasukkan perintah java script-->
<script type="text/javascript">
      $(document).ready(function() {
// jika dipilih, nim akan masuk ke input dan modal di tutup
  $(document).on('click', '.pilih_order', function (e) {

    var no_faktur = $("#nomor_faktur_penjualan").val();


$.post("ambil_edit_order_penjualan.php",{no_faktur_order:$(this).attr('data-order'),no_faktur:no_faktur},function(data){

      $("#modal_order").modal('hide');


$.post("ambil_select_order_edit.php",{no_faktur:no_faktur},function(data){
  $("#select_order").html(data);
  });

});


var total_perorder = bersihPemisah(bersihPemisah(bersihPemisah(bersihPemisah($(this).attr('data-total')))));
          if (total_perorder == '') 
          {
          total_perorder = 0;
          }
       
 var subtotal = bersihPemisah(bersihPemisah(bersihPemisah(bersihPemisah($("#total2").val()))));
          if (subtotal == '') 
          {
          subtotal = 0;
          }

var total_akhir1 = parseInt(subtotal,10) + parseInt(total_perorder,10);


var biaya_adm = bersihPemisah(bersihPemisah(bersihPemisah(bersihPemisah($("#biaya_adm").val()))));
    if (biaya_adm == ''  || biaya_adm == 0,00 )
    {
      biaya_adm = 0;
    }


    var pot_fakt_per = bersihPemisah(bersihPemisah(bersihPemisah(bersihPemisah($("#potongan_persen").val()))));
   if (pot_fakt_per == "") {
        pot_fakt_per = 0;
      }

    var pot_fakt_rp = bersihPemisah(bersihPemisah(bersihPemisah(bersihPemisah($("#potongan_penjualan").val()))));
 if (pot_fakt_rp == "") {
        pot_fakt_rp = 0;
      }
   var tax_faktur = bersihPemisah(bersihPemisah(bersihPemisah(bersihPemisah($("#tax").val()))));


    if (pot_fakt_per == 0) {
      var potongaaan = pot_fakt_rp;

      var pot_fakt_per = parseInt(potongaaan,10) / parseInt(total_akhir1,10) * 100;

    var total_akhier = parseInt(total_akhir1,10) - parseInt(pot_fakt_rp,10);


         //Hitung pajak
        if (tax_faktur != 0 ) {
        var hasil_tax = parseInt(total_akhier,10) * parseInt(tax_faktur,10) / 100;

        }
        else
        {
        var hasil_tax = 0;
        }
    //end hitung pajak
    var total_akhir = parseInt(total_akhier,10) + parseInt(biaya_adm,10);


    }
    else if(pot_fakt_rp == 0)
    {
      var potongaaan = pot_fakt_per;
      var pos = potongaaan.search("%");
      var potongan_persen = bersihPemisah(bersihPemisah(bersihPemisah(bersihPemisah(potongaaan))));
          potongan_persen = potongan_persen.replace("%","");
      potongaaan = parseInt(total_akhir1,10) * parseInt(potongan_persen,10) / 100;

        var total_akhier = parseInt(total_akhir1,10) - parseInt(potongaaan,10);


         //Hitung pajak
        if (tax_faktur != 0) {
        var hasil_tax = parseInt(total_akhier,10) * parseInt(tax_faktur,10) / 100;

        }
        else
        {
        var hasil_tax = 0;
        }
    //end hitung pajak
   var total_akhir = parseInt(total_akhier,10) + parseInt(biaya_adm,10);

    }
     else if(pot_fakt_rp != 0 && pot_fakt_per != 0)
    {
      var potongaaan = pot_fakt_per;
      var pos = potongaaan.search("%");
      var potongan_persen = bersihPemisah(bersihPemisah(bersihPemisah(bersihPemisah(potongaaan))));
          potongan_persen = potongan_persen.replace("%","");
      potongaaan = parseInt(total_akhir1,10) * parseInt(potongan_persen,10) / 100;

     
       var total_akhier = parseInt(total_akhir1,10) - parseInt(potongaaan,10);


         //Hitung pajak
        if (tax_faktur != 0) {
        var hasil_tax = parseInt(total_akhier,10) * parseInt(tax_faktur,10) / 100;

        }
        else
        {
        var hasil_tax = 0;
        }
    //end hitung pajak

    var total_akhir = parseInt(total_akhier,10) + parseInt(biaya_adm,10);


    }


      $("#potongan_persen").val(Math.round(pot_fakt_per));
      $("#total1").val(tandaPemisahTitik(total_akhir));
      $("#potongan_penjualan").val(Math.round(potongaaan));
      $("#tax_rp").val(Math.round(hasil_tax));
      $("#total2").val(tandaPemisahTitik(total_akhir1));

// ambil datatable yang terbaru
            $('#table_tbs_order').DataTable().destroy();
          var dataTable = $('#table_tbs_order').DataTable( {
          "processing": true,
          "serverSide": true,
          "ajax":{
            url :"datatable_edit_tbs_order.php", // json datasource
            "data": function ( d ) {
              d.no_faktur = $("#nomor_faktur_penjualan").val();
                                // d.custom = $('#myInput').val();
                                // etc
            },
            type: "post",  // method  , by default get
            error: function(){  // error handling
              $(".employee-grid-error").html("");
              $("#table_tbs_order").append('<tbody class="employee-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
              $("#employee-grid_processing").css("display","none");
            }
        },      
    });
// ambil datatable yang terbaru


});


});
</script>
<!--end javascript order all-->





<!--java scrip order all-->
<script type="text/javascript" language="javascript" >
      $(document).ready(function() {
  $(document).on('click', '#btn-hps-order', function (e) {

var hapus_order = $("#hapus_order").val();
var no_faktur = $("#nomor_faktur_penjualan").val();

$.post("hapus_order_tbs_edit.php",{hapus_order:hapus_order,no_faktur:no_faktur},function(data){


$.get("ambil_select_order.php",function(info){
  $("#select_order").html(info);
});
    
}); 


// ambil datatable yang terbaru
            $('#table_tbs_order').DataTable().destroy();
          var dataTable = $('#table_tbs_order').DataTable( {
          "processing": true,
          "serverSide": true,
          "ajax":{
            url :"datatable_edit_tbs_order.php", // json datasource
            "data": function ( d ) {
                    d.no_faktur = $("#nomor_faktur_penjualan").val();
                                // d.custom = $('#myInput').val();
                                // etc
            },
            type: "post",  // method  , by default get
            error: function(){  // error handling
              $(".employee-grid-error").html("");
              $("#table_tbs_order").append('<tbody class="employee-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
              $("#employee-grid_processing").css("display","none");
            }
        },      
    });
// ambil datatable yang terbaru


var biaya_adm = bersihPemisah(bersihPemisah(bersihPemisah(bersihPemisah($("#biaya_adm").val()))));
    if (biaya_adm == ''  || biaya_adm == 0,00 )
    {
      biaya_adm = 0;
    }


 var total_perorder = bersihPemisah(bersihPemisah(bersihPemisah(bersihPemisah($("#total_perorder").val()))));
          if (total_perorder == '') 
          {
          total_perorder = 0;
          }
       
 var subtotal = bersihPemisah(bersihPemisah(bersihPemisah(bersihPemisah($("#total2").val()))));
          if (subtotal == '') 
          {
          subtotal = 0;
          }

var total_akhir1 = parseInt(subtotal,10) - parseInt(total_perorder,10);


var pot_fakt_per = bersihPemisah(bersihPemisah(bersihPemisah(bersihPemisah($("#potongan_persen").val()))));
   if (pot_fakt_per == "") 
      {
        pot_fakt_per = 0;
      }

var pot_fakt_rp = bersihPemisah(bersihPemisah(bersihPemisah(bersihPemisah($("#potongan_penjualan").val()))));
 if (pot_fakt_rp == "") 
      {
        pot_fakt_rp = 0;
      }

   var tax_faktur = bersihPemisah(bersihPemisah(bersihPemisah(bersihPemisah($("#tax").val()))));


    if (pot_fakt_per == 0) {
      var potongaaan = pot_fakt_rp;

      var pot_fakt_per = parseInt(potongaaan,10) / parseInt(total_akhir1,10) * 100;

    var total_akhier = parseInt(total_akhir1,10) - parseInt(pot_fakt_rp,10);


         //Hitung pajak
        if (tax_faktur != 0 ) {
        var hasil_tax = parseInt(total_akhier,10) * parseInt(tax_faktur,10) / 100;

        }
        else
        {
        var hasil_tax = 0;
        }
    //end hitung pajak
    var total_akhir = parseInt(total_akhier,10) + parseInt(biaya_adm,10);


    }
    else if(pot_fakt_rp == 0)
    {
      var potongaaan = pot_fakt_per;
      var pos = potongaaan.search("%");
      var potongan_persen = bersihPemisah(bersihPemisah(bersihPemisah(bersihPemisah(potongaaan))));
          potongan_persen = potongan_persen.replace("%","");
      potongaaan = parseInt(total_akhir1,10) * parseInt(potongan_persen,10) / 100;

        var total_akhier = parseInt(total_akhir1,10) - parseInt(potongaaan,10);


         //Hitung pajak
        if (tax_faktur != 0) {
        var hasil_tax = parseInt(total_akhier,10) * parseInt(tax_faktur,10) / 100;

        }
        else
        {
        var hasil_tax = 0;
        }
    //end hitung pajak
   var total_akhir = parseInt(total_akhier,10) + parseInt(biaya_adm,10);

    }
     else if(pot_fakt_rp != 0 && pot_fakt_per != 0)
    {
      var potongaaan = pot_fakt_per;
      var pos = potongaaan.search("%");
      var potongan_persen = bersihPemisah(bersihPemisah(bersihPemisah(bersihPemisah(potongaaan))));
          potongan_persen = potongan_persen.replace("%","");
      potongaaan = parseInt(total_akhir1,10) * parseInt(potongan_persen,10) / 100;

     
       var total_akhier = parseInt(total_akhir1,10) - parseInt(potongaaan,10);


         //Hitung pajak
        if (tax_faktur != 0) {
        var hasil_tax = parseInt(total_akhier,10) * parseInt(tax_faktur,10) / 100;

        }
        else
        {
        var hasil_tax = 0;
        }
    //end hitung pajak

    var total_akhir = parseInt(total_akhier,10) + parseInt(biaya_adm,10);


    }


      $("#potongan_persen").val(Math.round(pot_fakt_per));
      $("#total1").val(tandaPemisahTitik(total_akhir));
      $("#potongan_penjualan").val(Math.round(potongaaan));
      $("#tax_rp").val(Math.round(hasil_tax));
      $("#total2").val(tandaPemisahTitik(total_akhir1));



});








});


</script>

<script type="text/javascript">
   $(document).on('ready', function (e) {                
// START DATATABLE AJAX START TBS PENJUALAN
      $('#tabel_tbs_penjualan').DataTable().destroy();
      $('#table_tbs_order').DataTable().destroy();


            var dataTable = $('#tabel_tbs_penjualan').DataTable( {
            "processing": true,
            "serverSide": true,
            "info":     false,
            "language": { "emptyTable":     "My Custom Message On Empty Table" },
            "ajax":{
              url :"data_tbs_edit_penjualan.php", // json datasource
             "data": function ( d ) {
                    d.no_faktur = $("#nomor_faktur_penjualan").val();
                                // d.custom = $('#myInput').val();
                                // etc
            },
                  type: "post",  // method  , by default get
              error: function(){  // error handling
                $(".tbody").html("");
                $("#tabel_tbs_penjualan").append('<tbody class="tbody"><tr><th colspan="3"></th></tr></tbody>');
                $("#tableuser_processing").css("display","none");
                
              }
            }   

      });

// ambil datatable order yang terbaru
            $('#table_tbs_order').DataTable().destroy();

          var dataTable = $('#table_tbs_order').DataTable( {
          "processing": true,
          "serverSide": true,
          "ajax":{
            url :"datatable_edit_tbs_order.php", // json datasource
            "data": function ( d ) {
                    d.no_faktur = $("#nomor_faktur_penjualan").val();
                                // d.custom = $('#myInput').val();
                                // etc
            },

            type: "post",  // method  , by default get
            error: function(){  // error handling
              $(".employee-grid-error").html("");
              $("#table_tbs_order").append('<tbody class="employee-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
              $("#employee-grid_processing").css("display","none");
            }
        },
        });
// ambil datatable order yang terbaru

        
        $("#span_tbs").show()
        $("#btnRujukLab").show()
        $('#pembayaran_penjualan').val('');
        $('#potongan_penjualan').val('');
        $('#potongan_persen').val('');

// END DATATABLE AJAX END DATATABLE AJAX TBS PENJUALAN
});
 </script>


<!--untuk memasukkan perintah java script-->
<script type="text/javascript">

// jika dipilih, nim akan masuk ke input dan modal di tutup
  $(document).on('click', '.pilih', function (e) {
  document.getElementById("kode_barang").value = $(this).attr('data-kode');
  $("#kode_barang").trigger('chosen:updated');

  document.getElementById("nama_barang").value = $(this).attr('nama-barang');
  document.getElementById("limit_stok").value = $(this).attr('limit_stok');
  document.getElementById("satuan_produk").value = $(this).attr('satuan');
  document.getElementById("ber_stok").value = $(this).attr('ber-stok');
  document.getElementById("harga_lama").value = $(this).attr('harga');
  document.getElementById("harga_baru").value = $(this).attr('harga');
  document.getElementById("satuan_konversi").value = $(this).attr('satuan');
  document.getElementById("id_produk").value = $(this).attr('id-barang');
  
  var kode_barang = $("#kode_barang").val();
  var no_faktur = $("#nomor_faktur_penjualan").val();

$.post('cek_kode_barang_tbs_edit_penjualan.php',{kode_barang:kode_barang,no_faktur:no_faktur}, function(data){
  if(data == 1){
    alert("Anda Tidak Bisa Menambahkan Barang Yang Sudah Ada, Silakan Edit atau Pilih Barang Yang Lain !");

    $("#kode_barang").val('');
    $("#kode_barang").trigger('chosen:updated');
    $("#kode_barang").trigger('chosen:open');
    $("#nama_barang").val('');

   }//penutup if

    });////penutup function(cek_kode_barang_tbs_penjualan)

var level_harga = $("#level_harga").val();

var harga_level_1 = $(this).attr('harga');
var harga_level_2 = $(this).attr('harga_level_2');  
var harga_level_3 = $(this).attr('harga_level_3');
var harga_level_4 = $(this).attr('harga_level_4');
var harga_level_5 = $(this).attr('harga_level_5');  
var harga_level_6 = $(this).attr('harga_level_6');
var harga_level_7 = $(this).attr('harga_level_7');

if (level_harga == "harga_1") {
  $("#harga_produk").val(harga_level_1);
  $("#harga_lama").val(harga_level_1);
  $("#harga_baru").val(harga_level_1);
  $('#kolom_cek_harga').val('1');
}

else if (level_harga == "harga_2") {
  $("#harga_produk").val(harga_level_2);
  $("#harga_baru").val(harga_level_2);
  $("#harga_lama").val(harga_level_2);
  $('#kolom_cek_harga').val('1');
}

else if (level_harga == "harga_3") {
  $("#harga_produk").val(harga_level_3);
  $("#harga_lama").val(harga_level_3);
  $("#harga_baru").val(harga_level_3);
  $('#kolom_cek_harga').val('1');
}

else if (level_harga == "harga_4") {
  $("#harga_produk").val(harga_level_4);
  $("#harga_lama").val(harga_level_4);
  $("#harga_baru").val(harga_level_4);
  $('#kolom_cek_harga').val('1');
}

else if (level_harga == "harga_5") {
  $("#harga_produk").val(harga_level_5);
  $("#harga_lama").val(harga_level_5);
  $("#harga_baru").val(harga_level_5);
  $('#kolom_cek_harga').val('1');
}

else if (level_harga == "harga_6") {
  $("#harga_produk").val(harga_level_6);
  $("#harga_lama").val(harga_level_6);
  $("#harga_baru").val(harga_level_6);
  $('#kolom_cek_harga').val('1');
}

else if (level_harga == "harga_7") {
  $("#harga_produk").val(harga_level_7);
  $("#harga_lama").val(harga_level_7);
  $("#harga_baru").val(harga_level_7);
  $('#kolom_cek_harga').val('1');
}
  document.getElementById("jumlahbarang").value = $(this).attr('jumlah-barang');


$.post("lihat_promo_alert.php",{id:$(this).attr('id-barang')},function(data){

    if (data == '')
    {

    }
    else{
      $("#modal_promo_alert").modal('show');
      $("#tampil_alert").html(data);
    }

});


  $('#myModal').modal('hide'); 

  $("#jumlah_barang").focus();


});

  </script>


  <script type="text/javascript">
$(document).ready(function(){
  //end cek level harga
  $("#level_harga").change(function(){
  
  var level_harga = $("#level_harga").val();
  var kode_barang = $("#kode_barang").val();
  var satuan_konversi = $("#satuan_konversi").val();
  var jumlah_barang = $("#jumlah_barang").val();
  var id_produk = $("#id_produk").val();

$.post("cek_level_harga_barang.php",
        {level_harga:level_harga, kode_barang:kode_barang,jumlah_barang:jumlah_barang,id_produk:id_produk,satuan_konversi:satuan_konversi},function(data){

          $("#harga_produk").val(data);
          $("#harga_baru").val(data);
        });
    });
});
//end cek level harga
</script>





<!-- cek stok satuan konversi keyup-->
<script type="text/javascript">
  $(document).ready(function(){
    $("#jumlah_barang").keyup(function(){
      var jumlah_barang = $("#jumlah_barang").val();
      var satuan_konversi = $("#satuan_konversi").val();
      var kode_barang = $("#kode_barang").val();
      var id_produk = $("#id_produk").val();
      var no_faktur = $("#no_faktur0").val();
      var prev = $("#satuan_produk").val();

      $.post("cek_stok_konversi_edit_penjualan.php",
        {jumlah_barang:jumlah_barang,satuan_konversi:satuan_konversi,kode_barang:kode_barang,id_produk:id_produk,no_faktur:no_faktur},function(data){

          if (data < 0) {
            alert("Jumlah Melebihi Stok");
            $("#jumlah_barang").val('');
          $("#satuan_konversi").val(prev);
          }

      });
    });
  });
</script>
<!-- cek stok satuan konversi keyup-->

<!-- cek stok satuan konversi change-->
<script type="text/javascript">
  $(document).ready(function(){
    $("#satuan_konversi").change(function(){
      var jumlah_barang = $("#jumlah_barang").val();
      var satuan_konversi = $("#satuan_konversi").val();
      var kode_barang = $("#kode_barang").val();
      var no_faktur = $("#no_faktur0").val();
      var id_produk = $("#id_produk").val();
      var prev = $("#satuan_produk").val();

      $.post("cek_stok_konversi_edit_penjualan.php",
        {jumlah_barang:jumlah_barang,satuan_konversi:satuan_konversi,kode_barang:kode_barang,id_produk:id_produk,no_faktur:no_faktur},function(data){

          if (data < 0) {
            alert("Jumlah Melebihi Stok");
            $("#jumlah_barang").val('');
          $("#satuan_konversi").val(prev);

          }

      });
    });
  });
</script>
<!-- end cek stok satuan konversi change-->


<script>
$(document).ready(function(){
    $("#satuan_konversi").change(function(){

      var prev = $("#satuan_produk").val();
      var harga_lama = $("#harga_lama").val();
      var satuan_konversi = $("#satuan_konversi").val();
      var id_produk = $("#id_produk").val();
      var harga_produk = $("#harga_lama").val();
      var jumlah_barang = $("#jumlah_barang").val();
      var kode_barang = $("#kode_barang").val();
      

      $.getJSON("cek_konversi_penjualan.php",{kode_barang:kode_barang,satuan_konversi:satuan_konversi, id_produk:id_produk,harga_produk:harga_produk,jumlah_barang:jumlah_barang},function(info){

          if (satuan_konversi == prev) {
      
          $("#harga_produk").val(harga_lama);
          $("#harga_baru").val(harga_lama);

        }

        else if (info.jumlah_total == 0) {
          alert('Satuan Yang Anda Pilih Tidak Tersedia Untuk Produk Ini !');
          $("#satuan_konversi").val(prev);
          $("#harga_produk").val(harga_lama);
          $("#harga_baru").val(harga_lama);

        }

        else{
          $("#harga_produk").val(info.harga_pokok);
          $("#harga_baru").val(info.harga_pokok);
        }

      });

        
    });

});
</script>


<script>
//untuk menampilkan sisa penjualan secara otomatis
  $(document).ready(function(){

  $("#jumlah_barang").keyup(function(){
     var jumlah_barang = $("#jumlah_barang").val();
     var jumlahbarang = $("#jumlahbarang").val();
     var limit_stok = $("#limit_stok").val();
     var ber_stok = $("#ber_stok").val();
     var stok = jumlahbarang - jumlah_barang;


if (stok < 0 )

      {

       if (ber_stok == 'Jasa') 
       {
       
       }
       
       else{
       alert ("Jumlah Melebihi Stok!");
       $("#jumlah_barang").val('');
       }

    }

    else if( limit_stok > stok  ){

      alert ("Persediaan Barang Ini Sudah Mencapai Batas Limit Stok, Segera Lakukan Pembelian !");
    }
  });
})

</script>



<script>

//untuk menampilkan data yang diambil pada form tbs penjualan berdasarkan id=formtambahproduk
  $("#submit_barcode").click(function(){

    var kode_barang = $("#kode_barcode").val();
    var level_harga = $("#level_harga").val();
    var sales = $("#sales").val();
    var no_faktur = $("#nomor_faktur_penjualan").val();

$.get("cek_barang.php",{kode_barang:kode_barang},function(data){
if (data != 1) {

alert("Barang Yang Anda Pesan Tidak Tersedia !!")

}

else{

/// JAVASCRIPT BARCODE
$.post("barcode_edit.php",{kode_barang:kode_barang,sales:sales,level_harga:level_harga,no_faktur:no_faktur},function(data){


        $(".tr-kode-"+kode_barang+"").remove();

        $("#ppn").attr("disabled", true);
        $("#tbody").prepend(data);
        $("#nama_barang").val('');
        $("#nama_barang").val('');
        $("#jumlah_barang").val('');
        $("#potongan1").val('');

        
});
/// JAVASCRIPT BARCODE


/// JAVASCRIPT ALERT PROMO
$.getJSON('lihat_nama_barang.php',{kode_barang:kode_barang}, function(json){

$.post("lihat_promo_alert.php",{id:json.id},function(info){

    if (info == '')
    {

    }
    else{
      $("#modal_promo_alert").modal('show');
      $("#tampil_alert").html(info);
    }

});

});
///END  JAVASCRIPT ALERT PROMO


}

});


     $("#form_barcode").submit(function(){
    return false;
    
    });
});
 </script>  




<script>

   //untuk menampilkan data yang diambil pada form tbs penjualan berdasarkan id=formtambahproduk
  $("#submit_produk").click(function(){

    var no_faktur = $("#nomor_faktur_penjualan").val();
    var kode_pelanggan = $("#kd_pelanggan").val();
    var kode_barang = $("#kode_barang").val();
    var n = kode_barang.indexOf("(");
    if (n > 0)
    {
    var kode_barang = kode_barang.substr(0, kode_barang.indexOf('('));
    }
    var nama_barang = $("#nama_barang").val();
    var jumlah_barang = $("#jumlah_barang").val();
    var harga = $("#harga_produk").val();
    var harga_baru = $("harga_baru").val();
    var potongan = $("#potongan1").val();
    var tax = $("#tax1").val();
    if (potongan == '')
    {
      potongan = 0;
    }
    var tax = bersihPemisah(bersihPemisah(bersihPemisah(bersihPemisah($("#tax1").val()))));
     if (tax == '')
    {
      tax = 0;
    }
    var jumlahbarang = $("#jumlahbarang").val();
    var satuan = $("#satuan_konversi").val();
    var sales = $("#sales").val();
    var a = $(".tr-kode-"+kode_barang+"").attr("data-kode-barang");
    var ber_stok = $("#ber_stok").val();
    var ppn = $("#ppn").val();
    var stok = parseInt(jumlahbarang,10) - parseInt(jumlah_barang,10);
    
var subtotal = bersihPemisah(bersihPemisah(bersihPemisah(bersihPemisah($("#total2").val()))));
          if (subtotal == '') 
          {
          subtotal = 0;
          }

   var tax_faktur = bersihPemisah(bersihPemisah(bersihPemisah(bersihPemisah($("#tax").val()))));

    var pot_fakt_per = bersihPemisah(bersihPemisah(bersihPemisah(bersihPemisah($("#potongan_persen").val()))));
   if (pot_fakt_per == "") {
        pot_fakt_per = 0;
      }

    var pot_fakt_rp = bersihPemisah(bersihPemisah(bersihPemisah(bersihPemisah($("#potongan_penjualan").val()))));
    if (pot_fakt_rp == "") {
        pot_fakt_rp = 0;
      }

  var biaya_adm = bersihPemisah(bersihPemisah(bersihPemisah(bersihPemisah($("#biaya_adm").val()))));
        if (biaya_adm == '')
        {
          biaya_adm = 0;
        }

  //PPN
  if (ppn == 'Exclude') {
  
      var total1 = parseInt(jumlah_barang,10) * parseInt(harga,10) - parseInt(potongan,10);
      var total_tax_exclude = parseInt(total1,10) * parseInt(tax,10) / 100;  
      var total = parseInt(total1,10) + parseInt(Math.round(total_tax_exclude,10));

    }
    else
    {
      var total = parseInt(jumlah_barang,10) * parseInt(harga,10) - parseInt(potongan,10);
    }
  //PPN


    var total_akhir1 = parseInt(subtotal,10) + parseInt(total,10);


    if (pot_fakt_per == 0) {
      var potongaaan = pot_fakt_rp;

      var pot_fakt_per = parseInt(potongaaan,10) / parseInt(total_akhir1,10) * 100;

    var total_akhier = parseInt(total_akhir1,10) - parseInt(pot_fakt_rp,10);


         //Hitung pajak
        if (tax_faktur != 0 ) {
        var hasil_tax = parseInt(total_akhier,10) * parseInt(tax_faktur,10) / 100;

        }
        else
        {
        var hasil_tax = 0;
        }
    //end hitung pajak
    var total_akhir = parseInt(total_akhier,10) + parseInt(biaya_adm,10) + parseInt(Math.round(hasil_tax),10);


    }
    else if(pot_fakt_rp == 0)
    {
      var potongaaan = pot_fakt_per;
      var pos = potongaaan.search("%");
      var potongan_persen = bersihPemisah(bersihPemisah(bersihPemisah(bersihPemisah(potongaaan))));
          potongan_persen = potongan_persen.replace("%","");
      potongaaan = parseInt(total_akhir1,10) * parseInt(potongan_persen,10) / 100;

        var total_akhier = parseInt(total_akhir1,10) - parseInt(potongaaan,10);


         //Hitung pajak
        if (tax_faktur != 0) {
        var hasil_tax = parseInt(total_akhier,10) * parseInt(tax_faktur,10) / 100;

        }
        else
        {
        var hasil_tax = 0;
        }

    //end hitung pajak
   var total_akhir = parseInt(total_akhier,10) + parseInt(biaya_adm,10) + parseInt(Math.round(hasil_tax),10);

    }
     else if(pot_fakt_rp != 0 && pot_fakt_per != 0)
    {
      var potongaaan = pot_fakt_per;
      var pos = potongaaan.search("%");
      var potongan_persen = bersihPemisah(bersihPemisah(bersihPemisah(bersihPemisah(potongaaan))));
          potongan_persen = potongan_persen.replace("%","");
      potongaaan = parseInt(total_akhir1,10) * parseInt(potongan_persen,10) / 100;
     
       var total_akhier = parseInt(total_akhir1,10) - parseInt(potongaaan,10);


         //Hitung pajak
        if (tax_faktur != 0) {
        var hasil_tax = parseInt(total_akhier,10) * parseInt(tax_faktur,10) / 100;

        }
        else
        {
        var hasil_tax = 0;
        }
    //end hitung pajak

    var total_akhir = parseInt(total_akhier,10) + parseInt(biaya_adm,10) + parseInt(Math.round(hasil_tax),10);


    }


  if (jumlah_barang == ''){
  alert("Jumlah Barang Harus Diisi");
  }
  else if (kode_pelanggan == ''){
  alert("Kode Pelanggan Harus Dipilih");
  }
  else if (stok < 0 ){
  alert("Jumlah Barang Melebihi Stok");
  }
  
 else if (ber_stok == 'Jasa' ){

    $(".tr-kode-"+kode_barang+"").remove();


 $.post("proses_tambah_edit_penjualan.php",{ppn:ppn,no_faktur:no_faktur,kode_barang:kode_barang,nama_barang:nama_barang,jumlah_barang:jumlah_barang,harga:harga,harga_baru:harga_baru,potongan:potongan,tax:tax,satuan:satuan,sales:sales},function(data){
     
     $("#kode_barang").focus();
      $("#ppn").attr("disabled", true);
     $("#tbody").prepend(data);
      $("#tabel_tbs_penjualan").show();

    $("#kode_barang").val('');
      $("#kode_barang").val('').trigger("chosen:updated");
      $("#kode_barang").trigger("chosen:open");

     $("#nama_barang").val('');
     $("#jumlah_barang").val('');
     $("#potongan1").val('');
     $("#tax1").val('');
     $("#pembayaran_penjualan").val('');
    
//Pembaruan datatable baru 
     $('#tabel_tbs_penjualan').DataTable().destroy();

                        var dataTable = $('#tabel_tbs_penjualan').DataTable( {
                          "processing": true,
                          "serverSide": true,
                          "ajax":{
                            url :"data_tbs_edit_penjualan.php", // json datasource
                              "data": function ( d ) {
                                d.no_faktur = $("#nomor_faktur_penjualan").val();
                                // d.custom = $('#myInput').val();
                                // etc
                            },
                             
                              type: "post",  // method  , by default get
                            error: function(){  // error handling
                              $(".employee-grid-error").html("");
                              $("#tabel_tbs_penjualan").append('<tbody class="employee-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
                              $("#employee-grid_processing").css("display","none");
                              }
                          },
                             "fnCreatedRow": function( nRow, aData, iDataIndex ) {

                              $(nRow).attr('class','tr-id-'+aData[11]+'');         

                          }
                        });
//Pembaruan datatable baru 


     });

 }

  else if (stok < 0) {

    alert ("Jumlah Melebihi Stok Barang !");

  }

  else{

       $("#potongan_persen").val(Math.round(pot_fakt_per));
      $("#total1").val(tandaPemisahTitik(total_akhir));
      $("#potongan_penjualan").val(Math.round(potongaaan));
      $("#total2").val(tandaPemisahTitik(total_akhir1));
      $("#tax_rp").val(Math.round(hasil_tax));

    $("#kode_barang").focus();

    $(".tr-kode-"+kode_barang+"").remove();

    $.post("proses_tambah_edit_penjualan.php",{ppn:ppn,no_faktur:no_faktur,kode_barang:kode_barang,nama_barang:nama_barang,jumlah_barang:jumlah_barang,harga:harga,harga_baru:harga_baru,potongan:potongan,tax:tax,satuan:satuan,sales:sales},function(data){
     
     $("#kode_barang").focus();
      $("#ppn").attr("disabled", true);
     $("#tbody").prepend(data);
    $("#kode_barang").val('');
      $("#kode_barang").val('').trigger("chosen:updated");
      $("#kode_barang").trigger("chosen:open");
           $("#nama_barang").val('');
     $("#jumlah_barang").val('');
     $("#potongan1").val('');
     $("#tax1").val('');
     $("#pembayaran_penjualan").val('');
     

//Pembaruan datatable baru 
     $('#tabel_tbs_penjualan').DataTable().destroy();

                        var dataTable = $('#tabel_tbs_penjualan').DataTable( {
                          "processing": true,
                          "serverSide": true,
                          "ajax":{
                            url :"data_tbs_edit_penjualan.php", // json datasource
                              "data": function ( d ) {
                                d.no_faktur = $("#nomor_faktur_penjualan").val();
                                // d.custom = $('#myInput').val();
                                // etc
                            },
                             
                              type: "post",  // method  , by default get
                            error: function(){  // error handling
                              $(".employee-grid-error").html("");
                              $("#tabel_tbs_penjualan").append('<tbody class="employee-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
                              $("#employee-grid_processing").css("display","none");
                              }
                          },
                             "fnCreatedRow": function( nRow, aData, iDataIndex ) {

                              $(nRow).attr('class','tr-id-'+aData[11]+'');         

                          }
                        });
//Pembaruan datatable baru 
     
     });
}
    

      
});

  $("#formtambahproduk").submit(function(){
    return false;
});
</script>

<!--<script type="text/javascript">
//menampilkan no urut faktur setelah tombol click di pilih
      $("#cari_produk_penjualan").click(function() {

      $("#alert_berhasil").hide();
      
      var no_faktur = $("#no_faktur0").val();
      //coding update jumlah barang baru "rabu,(9-3-2016)"
      $.post('modal_edit_penjualan.php',{no_faktur:no_faktur},function(data) {
      
      $(".modal_baru").html(data);
      $("#cetak_tunai").hide('');
      $("#cetak_tunai_besar").hide('');
      $("#cetak_piutang").hide('');
      });
      /* Act on the event */
      });

</script>-->


<script>
  $("#penjualan").click(function(){

        var no_faktur = $("#nomor_faktur_penjualan").val();
        var sisa_pembayaran = bersihPemisah(bersihPemisah(bersihPemisah(bersihPemisah( $("#sisa_pembayaran_penjualan").val() ))));
        var kredit = bersihPemisah(bersihPemisah(bersihPemisah(bersihPemisah( $("#kredit").val() )))); 
        var kode_pelanggan = $("#kd_pelanggan").val();
        var tanggal_jt = $("#tanggal_jt").val();
                var real_id = $("#real_id").val();

        var total = bersihPemisah(bersihPemisah(bersihPemisah(bersihPemisah( $("#total1").val() )))); 
        var potongan =  bersihPemisah(bersihPemisah(bersihPemisah(bersihPemisah( $("#potongan_penjualan").val() ))));

var biaya_adm =  bersihPemisah(bersihPemisah(bersihPemisah(bersihPemisah($("#biaya_adm").val()))));

        var potongan_persen = $("#potongan_persen").val();
        var tax = $("#tax_rp").val();
        var cara_bayar = $("#carabayar1").val();
        var pembayaran = bersihPemisah(bersihPemisah(bersihPemisah(bersihPemisah( $("#pembayaran_penjualan").val() ))));
        var total_hpp = $("#total_hpp").val();
        var harga = $("#harga_produk").val();
        var sales = $("#sales").val();
        var tanggal = $("#tanggal").val();
        var kode_gudang = $("#kode_gudang").val();
        var keterangan = $("#keterangan").val();
        var jumlah_bayar_lama = $("#jumlah_bayar_lama").val();
        var ppn_input = $("#ppn_input").val();
        var ppn = $("#ppn").val();
        var total2 = $("#total2").val();
        var sisa = pembayaran - total;
        var sisa_kredit = total - pembayaran;

        var jumlah_kredit_baru = parseInt(kredit,10) - parseInt(jumlah_bayar_lama,10);
       var x = parseInt(jumlah_bayar_lama,10) + parseInt(pembayaran,10);
       $("#zxzx").val(x);

  

 
 if (sisa_pembayaran < 0)
 {

  alert("Jumlah Pembayaran Tidak Mencukupi");

 }

else if (sisa < 0) 
 {

alert("Silakan Bayar Piutang");
 }

 else if (kode_pelanggan == "") 
 {

alert("Kode Pelanggan Harus Di Isi");

 }
else if (pembayaran == "") 
 {

alert("Pembayaran Harus Di Isi");

  $("#pembayaran_penjualan").focus();

 }

 else if (jumlah_bayar_lama == 0)

 {



  $("#penjualan").hide();
  $("#piutang").hide();
  $("#transaksi_baru").show();  

 $.post("proses_bayar_edit_jual.php",{real_id:real_id,biaya_adm:biaya_adm,total2:total2,kode_gudang:kode_gudang,tanggal:tanggal,no_faktur:no_faktur,sisa_pembayaran:sisa_pembayaran,kredit:kredit,kode_pelanggan:kode_pelanggan,tanggal_jt:tanggal_jt,total:total,potongan:potongan,potongan_persen:potongan_persen,tax:tax,cara_bayar:cara_bayar,pembayaran:pembayaran,sisa:sisa,sisa_kredit:sisa_kredit,total_hpp:total_hpp,harga:harga,sales:sales,keterangan:keterangan,jumlah_kredit_baru:jumlah_kredit_baru,x:x,ppn_input:ppn_input},function(info) {

   $("#total1").val('');
     $("#pembayaran_penjualan").val('');
     $("#sisa_pembayaran_penjualan").val('');
     $("#kredit").val('');

     $("#kd_pelanggan").val('');
     
     $("#table-baru").load("tabel-edit-tbs-penjualan.php?no_faktur=<?php echo $nomor_faktur; ?>");
     $("#alert_berhasil").show();
     $("#pembayaran_penjualan").val('');
     $("#sisa_pembayaran_penjualan").val('');
     $("#kredit").val('');
     $("#potongan_penjualan").val('');
     $("#potongan_persen").val('');
     $("#kode_meja").val('');
     $("#cetak_tunai").show();
     $("#cetak_tunai_besar").show();
       

     $("#span_tbs").hide();
   });

  }


else{

    if (x > total) {

    var no_faktur = $(this).attr("data-faktur");

    $.post('alert_piutang_penjualan.php',{no_faktur:no_faktur},function(data){
    
    
    $("#modal_alert").modal('show');
    $("#modal-alert").html(data);

  });

  }

}



 $("form").submit(function(){
    return false;
});

  });

      
  </script>


  
     <script>
       //perintah javascript yang diambil dari form proses_bayar_beli.php dengan id=form_beli
       $("#piutang").click(function(){

        var no_faktur = $("#nomor_faktur_penjualan").val();
        var sisa_pembayaran = bersihPemisah(bersihPemisah(bersihPemisah(bersihPemisah( $("#sisa_pembayaran_penjualan").val() ))));
        var kredit = bersihPemisah(bersihPemisah(bersihPemisah(bersihPemisah( $("#kredit").val() )))); 
        var kode_pelanggan = $("#kd_pelanggan").val();
        var tanggal_jt = $("#tanggal_jt").val();
        var total = bersihPemisah(bersihPemisah(bersihPemisah(bersihPemisah( $("#total1").val() )))); 
        var potongan =  bersihPemisah(bersihPemisah(bersihPemisah(bersihPemisah( $("#potongan_penjualan").val() ))));
        var biaya_adm =  bersihPemisah(bersihPemisah(bersihPemisah(bersihPemisah($("#biaya_adm").val()))));
        var real_id = $("#real_id").val();

        var potongan_persen = $("#potongan_persen").val();
        var tax = $("#tax_rp").val();
        var cara_bayar = $("#carabayar1").val();
        var pembayaran = bersihPemisah(bersihPemisah(bersihPemisah(bersihPemisah( $("#pembayaran_penjualan").val() ))));
        var total_hpp = $("#total_hpp").val();
        var harga = $("#harga_produk").val();
        var sales = $("#sales").val();
        var tanggal = $("#tanggal").val();
        var kode_gudang = $("#kode_gudang").val();
        var keterangan = $("#keterangan").val();
        var jumlah_bayar_lama = $("#jumlah_bayar_lama").val();
        var ppn_input = $("#ppn_input").val();
        var total2 = $("#total2").val();
        var sisa =  pembayaran - total;
        var sisa_kredit = total - pembayaran;

        var jumlah_kredit_baru = parseInt(kredit,10) - parseInt(jumlah_bayar_lama,10);
        var x = parseInt(jumlah_bayar_lama,10) + parseInt(pembayaran,10);
        $("#zxzx").val(x);
        



       
      if (sisa_pembayaran == "" )
      {

        alert ("Jika Ingin Piutang Isi Jumlah Pembayaran 0");
        $("#pembayaran_penjualan").focus();

      }

       else if (kode_pelanggan == "") 
       {
       
       alert("Kode Pelanggan Harus Di Isi");
       
       }
       else if (tanggal_jt == "")
       {

        alert ("Tanggal Jatuh Tempo Harus Di Isi");
        $("#tanggal_jt").focus();

       }


      else if (jumlah_bayar_lama == 0 || x <= total )
      {

        $("#penjualan").hide();
        $("#piutang").hide();
        $("#transaksi_baru").show(); 
        
        $.post("proses_bayar_edit_jual.php",{real_id:real_id,biaya_adm:biaya_adm,total2:total2,kode_gudang:kode_gudang,tanggal:tanggal,no_faktur:no_faktur,sisa_pembayaran:sisa_pembayaran,kredit:kredit,kode_pelanggan:kode_pelanggan,tanggal_jt:tanggal_jt,total:total,potongan:potongan,potongan_persen:potongan_persen,tax:tax,cara_bayar:cara_bayar,pembayaran:pembayaran,sisa:sisa,sisa_kredit:sisa_kredit,total_hpp:total_hpp,harga:harga,sales:sales,keterangan:keterangan,jumlah_kredit_baru:jumlah_kredit_baru,x:x,ppn_input:ppn_input},function(info) {
        
        $("#table-baru").html(info);
        $("#alert_berhasil").show();
        $("#pembayaran_penjualan").val('');
        $("#sisa_pembayaran_penjualan").val('');
        $("#kredit").val('');
        $("#potongan_penjualan").val('');
        $("#potongan_persen").val('');
        $("#tanggal_jt").val('');
        $("#cetak_piutang").show();
             $("#span_tbs").hide();

        $("#total1").val('');
       $("#pembayaran_penjualan").val('');
       $("#sisa_pembayaran_penjualan").val('');
       $("#kredit").val('');
       $("#tanggal_jt").val('');
        
        });


      }

      else
      {
             if (x > total)

             {
              var no_faktur = $(this).attr("data-faktur");
              
              $.post('alert_piutang_penjualan.php',{no_faktur:no_faktur},function(data){
              
              
              $("#modal_alert").modal('show');
              $("#modal-alert").html(data);
              
              });

            }
       

       
      }  

  });

 $("form").submit(function(){
       return false;
       });

  </script>   

<script type="text/javascript">
    $(document).ready(function(){

        var no_faktur = $("#nomor_faktur_penjualan").val();
        
        $.post("cek_total_edit_penjualan.php",
        {
        no_faktur: "<?php echo $nomor_faktur ?>"
        },
        function(data){
        $("#total2").val(data);

var subtotal = bersihPemisah(bersihPemisah(bersihPemisah(bersihPemisah($("#total2").val()))));
var biaya_adm = bersihPemisah(bersihPemisah(bersihPemisah(bersihPemisah($("#biaya_adm").val()))));
if (biaya_adm == '')
{
  biaya_adm = 0;
}
var potongan_penjualan = bersihPemisah(bersihPemisah(bersihPemisah(bersihPemisah($("#potongan_penjualan").val()))));
if (potongan_penjualan == '')
{
  potongan_penjualan = 0;
}

var total = (parseInt(subtotal,10) + parseInt(biaya_adm,10)) - parseInt(potongan_penjualan,10);
        $("#total1").val(tandaPemisahTitik(total));


        });
      });
</script>

        <script>
        
        //untuk menampilkan sisa penjualan secara otomatis
        $(document).ready(function(){
        $("#pembayaran_penjualan").keyup(function(){
        var pembayaran = bersihPemisah(bersihPemisah(bersihPemisah(bersihPemisah( $("#pembayaran_penjualan").val() ))));
        var total =  bersihPemisah(bersihPemisah(bersihPemisah(bersihPemisah( $("#total1").val() ))));
        var sisa = pembayaran - total;
        var sisa_kredit = total - pembayaran; 
        
        if (sisa < 0 )
        {
        $("#kredit").val( tandaPemisahTitik(sisa_kredit));
        $("#sisa_pembayaran_penjualan").val('0');
        $("#tanggal_jt").attr("disabled", false);
        
        }
        
        else  
        {
        
        
        
        $("#sisa_pembayaran_penjualan").val(tandaPemisahTitik(sisa));
        $("#kredit").val('0');
        $("#tanggal_jt").attr("disabled", true);
        
        } 
        
        
        });
        
        
        });
        </script>

<script>
$(document).ready(function(){

  //id kode pelanggan dari kode_pelanggan
    $("#kd_pelanggan").change(function(){
      var kode_pelanggan = $("#kd_pelanggan").val();

      //id yang di hidden
      $("#k_pelanggan").val(kode_pelanggan);
        
    });
});
</script>


<script type="text/javascript">
$(document).ready(function(){
    $("#kd_pelanggan").change(function(){
      var kode_pelanggan = $("#kd_pelanggan").val();

      var level_harga = $(".opt-pelanggan-"+kode_pelanggan+"").attr("data-level");



    $("#level_harga").val(level_harga);


    });

      });

          
        </script>


<script type="text/javascript">
//Perhitungan Diskon PERSEN pER FAKTUR
$(document).ready(function(){
  //KEY UP PERSEN
$("#potongan_persen").keyup(function(){

      var potongan_persen = $("#potongan_persen").val();
      var total = bersihPemisah(bersihPemisah(bersihPemisah(bersihPemisah( $("#total2").val() ))));
      var potongan_penjualan = ((total * potongan_persen) / 100);
      var tax = $("#tax").val();

      if (tax == "")
      {
        tax = 0;
      }

  var biaya_adm = bersihPemisah(bersihPemisah(bersihPemisah(bersihPemisah($("#biaya_adm").val()))));
  if (biaya_adm == '')
    {
      biaya_adm = 0;
    }
      var ambil_semula = parseInt(total,10) + parseInt(biaya_adm,10);/*parseInt(tax_rp,10)*/

        var sisa_potongan = total - potongan_penjualan;


             var t_tax = ((parseInt(sisa_potongan,10) * parseInt(tax,10)) / 100);
             var hasil_akhir = parseInt(sisa_potongan, 10) + parseInt(biaya_adm,10);/*+ parseInt(t_tax,10);*/
        
        if (potongan_persen > 100) {
          alert ("Potongan %, Tidak Boleh Lebih Dari 100%");
          $("#potongan_persen").val('');
          $("#potongan_penjualan").val('');
          $("#total1").val(tandaPemisahTitik(parseInt(ambil_semula)));

        }
        else
        {
        
        $("#total1").val(tandaPemisahTitik(parseInt(hasil_akhir)));
        $("#potongan_penjualan").val(tandaPemisahTitik(parseInt(potongan_penjualan)));

        }

      });

//KEY UP NOMINAL
$("#potongan_penjualan").keyup(function(){

  var potongan_penjualan =  bersihPemisah(bersihPemisah(bersihPemisah(bersihPemisah( $("#potongan_penjualan").val()))));
  if (potongan_penjualan == "")
  {
    potongan_penjualan = 0;
  }
  var total = bersihPemisah(bersihPemisah(bersihPemisah(bersihPemisah($("#total2").val()))));
  var potongan_persen = ((potongan_penjualan / total) * 100);
  var tax = $("#tax").val();

  if (tax == "")
  {
    tax = 0;
  }

  var biaya_adm = bersihPemisah(bersihPemisah(bersihPemisah(bersihPemisah($("#biaya_adm").val()))));
  if (biaya_adm == '')
    {
      biaya_adm = 0;
    }

if(potongan_persen > 100)
      {
        alert("Potongan Tidak Dapat Lebih dari 100% !!");
         $("#potongan_persen").val('');
        $("#potongan_penjualan").val('');
      
      var tax = $("#tax").val();

        if (tax == "")
        {
          tax = 0;
        }
        var t_tax = ((parseInt(total,10) * parseInt(tax,10)) / 100);

      var hasil_akhir = parseInt(total, 10) + parseInt(biaya_adm,10);/*+ parseInt(t_tax,10);*/

        $("#total1").val(tandaPemisahTitik(hasil_akhir));
      }
      else
      {
      
      $("#potongan_persen").val(parseInt(potongan_persen));
      $("#tax_rp").val(parseInt(t_tax));
      
      var sisa_potongan = parseInt(total,10) - parseInt(potongan_penjualan,10);
        
              if (tax == 0)
              {
               var t_tax = 0;
              }
              
              var t_tax = ((parseInt(sisa_potongan,10) * parseInt(tax,10)) / 100);

              

             var hasil_akhir = parseInt(sisa_potongan, 10) + parseInt(biaya_adm,10); /*+ parseInt(t_tax,10);*/
 
        
        $("#total1").val(tandaPemisahTitik(hasil_akhir));
        
}

      });

//KEY UP PAJAK / TAX DI FAKTUR      
$("#tax").keyup(function(){

        var potongan = bersihPemisah(bersihPemisah(bersihPemisah(bersihPemisah($("#potongan_penjualan").val() ))));
        var potongan_persen = $("#potongan_persen").val();
        var total = bersihPemisah(bersihPemisah(bersihPemisah(bersihPemisah($("#total2").val() ))));
       
              var cara_bayar = $("#carabayar1").val();
              var tax = $("#tax").val();
              var t_total = total - potongan;

              if (tax == "") {
                tax = 0;
              }
              else if (cara_bayar == "") {
                alert ("Kolom Cara Bayar Masih Kosong");
                 $("#tax").val('');
                 $("#potongan_penjualan").val('');
                 $("#potongan_persen").val('');
              }
              
              var t_tax = ((parseInt(bersihPemisah(bersihPemisah(bersihPemisah(bersihPemisah(t_total,10))))) * parseInt(bersihPemisah(bersihPemisah(bersihPemisah(bersihPemisah(tax,10)))))) / 100);

              var total_akhir = parseInt(bersihPemisah(bersihPemisah(bersihPemisah(bersihPemisah(t_total,10))))) + Math.round(parseInt(t_tax,10));
              
              
              $("#total1").val(tandaPemisahTitik(total_akhir));

              if (tax > 100) {
                alert ('Jumlah Tax Tidak Boleh Lebih Dari 100%');
                 $("#tax").val('');

              }
        

        $("#tax_rp").val(parseInt(t_tax));


        });
        });
        
        </script>


      <script type="text/javascript">
      
      $(".chosen").chosen({no_results_text: "Maaf, Data Tidak Ada!"});  
      
      </script>

<!-- KEMAREN SAMPAI EDIT PENJUALAN < DISINI -->


<!--<script type="text/javascript">
    $(document).ready(function(){
      
//fungsi hapus data 
$(document).on('click','.btn-hapus-tbs',function(e){

    
    var nama_barang = $(this).attr("data-barang");
    var id = $(this).attr("data-id");
     var kode_barang = $(this).attr("data-kode-barang");
    var subtotal_tbs = $(this).attr("data-subtotal");

      var total = bersihPemisah(bersihPemisah(bersihPemisah(bersihPemisah($("#total2").val()))));

      if (total == '') 
        {
          total = 0;
        };
      var total_akhir = parseInt(total,10) - parseInt(subtotal_tbs,10);

      $("#total1").val(tandaPemisahTitik(total_akhir));
      $("#total2").val(tandaPemisahTitik(total_akhir));


    $.post("hapus_edit_tbs_penjualan.php",{id:id,kode_barang:kode_barang},function(data){
    if (data == 'sukses') {


    $(".tr-id-"+id+"").remove();
    $("#pembayaran_penjualan").val('');
    
    }
    });

});
                  $('form').submit(function(){
              
              return false;
              });


    });
  
//end fungsi hapus data
</script>-->




<script type="text/javascript">
 
$(".btn-alert-hapus").click(function(){
     var no_faktur = $(this).attr("data-faktur");
    var kode_barang = $(this).attr("data-kode");

    $.post('alert_edit_penjualan.php',{no_faktur:no_faktur, kode_barang:kode_barang},function(data){
    
 
    $("#modal_alert").modal('show');
    $("#modal-alert").html(data); 

});

  });
</script>


<!-- AUTOCOMPLETE -->

<script>
$(function() {
    $( "#kode_barang" ).autocomplete({
        source: 'kode_barang_autocomplete.php'
    });
});
</script>

<!-- AUTOCOMPLETE -->


<script>
//untuk menampilkan data tabel
$(document).ready(function(){
    $("#kode_barang").focus();

});
</script>


<!-- Kode Barang blur untuk mengambil data hidden untuk submit produk ^_^ -->
<script type="text/javascript">
        $(document).ready(function(){
        $("#kode_barang").blur(function(){

          var kode_barang = $(this).val();
          var level_harga = $("#level_harga").val();
          var session_id = $("#session_id").val();
          var kode_barang = kode_barang.substr(0, kode_barang.indexOf('('));
          
          if (kode_barang != '')
          {

       
       
          $.post("cek_barang_penjualan.php",{kode_barang: kode_barang}, function(data){
          $("#jumlahbarang").val(data);
          });

          $.post('cek_kode_barang_tbs_penjualan.php',{kode_barang:kode_barang,session_id:session_id}, function(data){
          
          if(data == 1){
          alert("Anda Tidak Bisa Menambahkan Barang Yang Sudah Ada, Silakan Edit atau Pilih Barang Yang Lain !");

          $("#kode_barang").val('');
          $("#nama_barang").val('');
          }//penutup if
          
          });////penutup function(data)

      $.getJSON('lihat_nama_barang.php',{kode_barang:kode_barang}, function(json){
      
      if (json == null)
      {
        
        $('#nama_barang').val('');
        $('#limit_stok').val('');
        $('#harga_produk').val('');
        $('#harga_lama').val('');
        $('#harga_baru').val('');
        $('#satuan_produk').val('');
        $('#satuan_konversi').val('');
        $('#id_produk').val('');
        $('#ber_stok').val('');

      }

      else 
      {
        if (level_harga == "Level 1") {

        $('#harga_produk').val(json.harga_jual);
        $('#harga_baru').val(json.harga_jual);
        $('#harga_lama').val(json.harga_jual);
        }
        else if (level_harga == "Level 2") {

        $('#harga_produk').val(json.harga_jual2);
        $('#harga_baru').val(json.harga_jual2);
        $('#harga_lama').val(json.harga_jual2);
        }
        else if (level_harga == "Level 3") {

        $('#harga_produk').val(json.harga_jual3);
        $('#harga_baru').val(json.harga_jual3);
        $('#harga_lama').val(json.harga_jual3);
        }

        $('#nama_barang').val(json.nama_barang);
        $('#limit_stok').val(json.limit_stok);
        $('#satuan_produk').val(json.satuan);
        $('#satuan_konversi').val(json.satuan);
        $('#id_produk').val(json.id);
        $('#ber_stok').val(json.berkaitan_dgn_stok);

$.post("lihat_promo_alert.php",{id:json.id},function(data){

    if (data == '')
    {

    }
    else{
      $("#modal_promo_alert").modal('show');
      $("#tampil_alert").html(data);
    }

});

      }
                                              
        });
        
}

        });
        });   
</script>



<script type="text/javascript"> 
   $(document).ready(function(){

      var kode_pelanggan = $("#kd_pelanggan").val();

      var level_harga = $(".opt-pelanggan-"+kode_pelanggan+"").attr("data-level");

        
        if(kode_pelanggan == 'Umum')
        {
        $("#level_harga").val('Level 1');
        }

        else 
        {
        $("#level_harga").val(level_harga);
        }

   });
</script>

<script type="text/javascript">

  $(document).ready(function(){
    $(document).on('click','.edit-jumlah-jual',function(e){
      var kode_barang = $(this).attr("data-kode-barang-input");
      var tipe_produk = $('#opt-produk-'+kode_barang).attr("tipe_barang");
      
      $("#tipe_produk").val(tipe_produk);
    });
  });

</script>
                           
                            <script type="text/javascript">
                                 
                                 
                                  $(document).on('dblclick','.edit-jumlah-jual',function(e){

                                    var id = $(this).attr("data-id");

                                    $("#text-jumlah-"+id+"").hide();

                                    $("#input-jumlah-"+id+"").attr("type", "text");

                                 });

                                     $(document).on('blur','.input_jumlah_jual',function(e){


                                    var id = $(this).attr("data-id");
                                    var jumlah_baru = $(this).val();
                                    var kode_barang = $(this).attr("data-kode");
                                    var harga = $(this).attr("data-harga");
                                    var jumlah_lama = $("#text-jumlah-"+id+"").text();
                                    var potongan_persen = $("#potongan_persen").val();
                                     var satuan_konversi = $(this).attr("data-satuan");
                                     var no_faktur = $("#no_faktur0").val();

                                    var subtotal_lama = bersihPemisah(bersihPemisah(bersihPemisah(bersihPemisah($("#text-subtotal-"+id+"").text()))));
                                    var potongan = bersihPemisah(bersihPemisah(bersihPemisah(bersihPemisah($("#text-potongan-"+id+"").text()))));

                                    var tax = bersihPemisah(bersihPemisah(bersihPemisah(bersihPemisah($("#text-tax-"+id+"").text()))));
                                   
                                    var subtotal = harga * jumlah_baru - potongan;

                                    var subtotal_penjualan = bersihPemisah(bersihPemisah(bersihPemisah(bersihPemisah($("#total2").val()))));

                                  var biaya_adm = bersihPemisah(bersihPemisah(bersihPemisah(bersihPemisah($("#biaya_adm").val()))));
                                    if(biaya_adm == '')
                                    {
                                      biaya_adm = 0;
                                    }
                                    var ppn = $("#ppn").val();

    //////////////////////PPN
              if (ppn == 'Exclude') {

                                   var subtotal1 = harga * jumlah_baru - potongan;

                                    var subtotal_penjualan = bersihPemisah(bersihPemisah(bersihPemisah(bersihPemisah($("#total2").val()))));

                                    var subtotal_ex = parseInt(subtotal_lama,10) - parseInt(tax,10);

                                    var cari_tax = (parseInt(tax,10) * 100) / parseInt(subtotal_ex,10);


                                    var cari_tax1 = parseInt(subtotal1,10) * parseInt(cari_tax,10) / 100;

                                    var jumlah_tax = Math.round(cari_tax1);

                                    var subtotal = parseInt(subtotal1,10) + parseInt(jumlah_tax,10);

                                     var subtotal_penjualan = subtotal_penjualan - subtotal_lama + subtotal;
                                    }
                                    else
                                    {

                                   var subtotal1 = harga * jumlah_baru - potongan;

                                    var subtotal_penjualan = bersihPemisah(bersihPemisah(bersihPemisah(bersihPemisah($("#total2").val()))));

                                      var cari_tax = parseInt(subtotal_lama,10) - parseInt(tax,10);
                                    var cari_tax1 = parseInt(subtotal_lama,10) / parseInt(cari_tax,10);

                                    var tax_ex = cari_tax1.toFixed(2);

                                    var subtotal = subtotal1;
                                    var tax_ex1 = parseInt(subtotal,10) / tax_ex;
                                    var tax_ex2 = parseInt(subtotal,10) - parseInt(Math.round(tax_ex1));
                                    var jumlah_tax = Math.round(tax_ex2);
                                    

                    var subtotal_penjualan = subtotal_penjualan - subtotal_lama + subtotal;

                                    }
    ////////////////////////PPN
                                  

                                    var diskon_rupiah = parseInt(subtotal_penjualan,10) * parseInt(potongan_persen,10) / 100;

                                    
                                  potongaaan = subtotal_penjualan * potongan_persen / 100;
                                    if(potongaaan == '')
                                    {
                                      potongaaan = 0;
                                    }
                                    $("#potongan_penjualan").val(potongaaan);

                                    var total1_akhir = parseInt(subtotal_penjualan,10) - parseInt(potongaaan,10) + parseInt(biaya_adm,10);
                                    


                                    $.post("cek_stok_edit_penjualan.php",{kode_barang:kode_barang, jumlah_baru:jumlah_baru,satuan_konversi:satuan_konversi,no_faktur:no_faktur},function(data){

                                       if (data < 0) {

                                       alert ("Jumlah Yang Di Masukan Melebihi Stok !");

                                       $("#input-jumlah-"+id+"").val(jumlah_lama);
                                       $("#text-jumlah-"+id+"").text(jumlah_lama);
                                       $("#text-jumlah-"+id+"").show();
                                       $("#input-jumlah-"+id+"").attr("type", "hidden");

                                     }

                                      else{

                                     $.post("update_pesanan_barang.php",{jumlah_lama:jumlah_lama,tax:tax,id:id,jumlah_baru:jumlah_baru,kode_barang:kode_barang,potongan:potongan,harga:harga,jumlah_tax:jumlah_tax,subtotal:subtotal},function(info){
      
                                    $("#text-jumlah-"+id+"").show();
                                    $("#text-jumlah-"+id+"").text(jumlah_baru);
                                    $("#text-subtotal-"+id+"").text(tandaPemisahTitik(subtotal));
                                    $("#text-tax-"+id+"").text(jumlah_tax);
                                    $("#input-jumlah-"+id+"").attr("type", "hidden"); 
                                    $("#total2").val(tandaPemisahTitik(subtotal_penjualan));
                                    $("#potongan_penjualan").val(tandaPemisahTitik(potongaaan));    
                                    $("#total1").val(tandaPemisahTitik(total1_akhir));         

                                    });

                                   }

                                 });


       
                                    $("#kode_barang").focus();
                                    

                                 });

                             </script>


<script type="text/javascript">
  
                                      $(".edit-jumlah-alert").dblclick(function(){

                                      var no_faktur = $(this).attr("data-faktur");
                                      var kode_barang = $(this).attr("data-kode");
                                      
                                      $.post('alert_edit_penjualan.php',{no_faktur:no_faktur, kode_barang:kode_barang},function(data){
                                      
                                        $("#modal_alert").modal('show');
                                        $("#modal-alert").html(data);
              
                                      });
                                    });
</script>

<script type="text/javascript">
    $(document).ready(function(){

var ppn_input = $("#ppn_input").val();

      if (ppn_input == "Include"){

      $("#tax").attr("disabled", true);
      $("#tax1").attr("disabled", false);
  }

  else if (ppn_input == "Exclude") {
    $("#tax1").attr("disabled", true);
      $("#tax").attr("disabled", false);
  }
  else{

    $("#tax1").attr("disabled", true);
      $("#tax").attr("disabled", true);
  }


// PPN KETIKA DI UBAH
    $("#ppn").change(function(){

    var ppn = $("#ppn").val();
    $("#ppn_input").val(ppn);

  if (ppn == "Include"){

      $("#tax").attr("disabled", true);
      $("#tax1").attr("disabled", false);
  }

  else if (ppn == "Exclude") {
    $("#tax1").attr("disabled", true);
      $("#tax").attr("disabled", false);
  }
  else{

    $("#tax1").attr("disabled", true);
      $("#tax").attr("disabled", true);
  }


  });
  });
</script>



<!--Start Ajax Modal Cari Barang-->
<script type="text/javascript" language="javascript" >
   $(document).ready(function() {
        var dataTable = $('#tabel_cari').DataTable( {
          "processing": true,
          "serverSide": true,
          "ajax":{
            url :"modal_edit_penjualan.php", // json datasource
            "data": function ( d ) {
                d.no_faktur = $("#nomor_faktur_penjualan").val();
                // d.custom = $('#myInput').val();
                // etc
            },
            type: "post",  // method  , by default get
            error: function(){  // error handling
              $(".employee-grid-error").html("");
              $("#tabel_cari").append('<tbody class="employee-grid-error"><tr><th colspan="3">Data Tidak Ditemukan.. !!</th></tr></tbody>');
              $("#employee-grid_processing").css("display","none");
              
            }
          },

          "fnCreatedRow": function( nRow, aData, iDataIndex ) {

             $(nRow).attr('class', "pilih");
              $(nRow).attr('data-kode', aData[0]);
              $(nRow).attr('nama-barang', aData[1]);
              $(nRow).attr('harga', aData[2]);
              $(nRow).attr('harga_level_2', aData[3]);
              $(nRow).attr('harga_level_3', aData[4]);
              $(nRow).attr('harga_level_4', aData[5]);
              $(nRow).attr('harga_level_5', aData[6]);
              $(nRow).attr('harga_level_6', aData[7]);
              $(nRow).attr('harga_level_7f', aData[8]);
              $(nRow).attr('jumlah-barang', aData[9]);
              $(nRow).attr('satuan', aData[15]);
              $(nRow).attr('kategori', aData[11]);
              $(nRow).attr('status', aData[17]);
              $(nRow).attr('suplier', aData[12]);
              $(nRow).attr('limit_stok', aData[13]);
              $(nRow).attr('ber-stok', aData[14]);
              $(nRow).attr('tipe_barang', aData[16]);
              $(nRow).attr('id-barang', aData[18]);

          }

        });    
     
  });
 </script>
<!--Start Ajax Modal Cari-->


<script type="text/javascript">
// START script untuk pilih kode barang menggunakan chosen     
  $(document).ready(function(){
  $("#kode_barang").change(function(){

    var kode_barang = $(this).val();
    var nama_barang = $('#opt-produk-'+kode_barang).attr("nama-barang");
    var harga_jual = $('#opt-produk-'+kode_barang).attr("harga");
    var harga_jual2 = $('#opt-produk-'+kode_barang).attr('harga_jual_2');  
    var harga_jual3 = $('#opt-produk-'+kode_barang).attr('harga_jual_3');
    var harga_jual4 = $('#opt-produk-'+kode_barang).attr('harga_jual_4');
    var harga_jual5 = $('#opt-produk-'+kode_barang).attr('harga_jual_5');  
    var harga_jual6 = $('#opt-produk-'+kode_barang).attr('harga_jual_6');
    var harga_jual7 = $('#opt-produk-'+kode_barang).attr('harga_jual_7');
    var jumlah_barang = $('#opt-produk-'+kode_barang).attr("jumlah-barang");
    var satuan = $('#opt-produk-'+kode_barang).attr("satuan");
    var kategori = $('#opt-produk-'+kode_barang).attr("kategori");
    var status = $('#opt-produk-'+kode_barang).attr("status");
    var suplier = $('#opt-produk-'+kode_barang).attr("suplier");
    var limit_stok = $('#opt-produk-'+kode_barang).attr("limit_stok");
    var ber_stok = $('#opt-produk-'+kode_barang).attr("ber-stok");
    var tipe_produk = $('#opt-produk-'+kode_barang).attr("tipe_barang");
    var id_barang = $('#opt-produk-'+kode_barang).attr("id-barang");
    var level_harga = $("#level_harga").val();
    var no_faktur = $("#nomor_faktur_penjualan").val();


   if (level_harga == "harga_1") {

        $('#harga_produk').val(harga_jual);
        $('#harga_baru').val(harga_jual);
        $('#harga_lama').val(harga_jual);
        $('#kolom_cek_harga').val('1');
        }
    else if (level_harga == "harga_2") {

        $('#harga_produk').val(harga_jual2);
        $('#harga_baru').val(harga_jual2);
        $('#harga_lama').val(harga_jual2);
        $('#kolom_cek_harga').val('1');
        }
    else if (level_harga == "harga_3") {

        $('#harga_produk').val(harga_jual3);
        $('#harga_baru').val(harga_jual3);
        $('#harga_lama').val(harga_jual3);
        $('#kolom_cek_harga').val('1');
        }
    else if (level_harga == "harga_4") {

        $('#harga_produk').val(harga_jual4);
        $('#harga_baru').val(harga_jual4);
        $('#harga_lama').val(harga_jual4);
        $('#kolom_cek_harga').val('1');
        }
    else if (level_harga == "harga_5") {

        $('#harga_produk').val(harga_jual5);
        $('#harga_baru').val(harga_jual5);
        $('#harga_lama').val(harga_jual5);
        $('#kolom_cek_harga').val('1');
        }
    else if (level_harga == "harga_6") {

        $('#harga_produk').val(harga_jual6);
        $('#harga_baru').val(harga_jual6);
        $('#harga_lama').val(harga_jual6);
        $('#kolom_cek_harga').val('1');
        }
    else if (level_harga == "harga_7") {

        $('#harga_produk').val(harga_jual7);
        $('#harga_baru').val(harga_jual7);
        $('#harga_lama').val(harga_jual7);
        $('#kolom_cek_harga').val('1');
        }



    $("#tipe_produk").val(tipe_produk);
    $("#kode_barang").val(kode_barang);
    $("#nama_barang").val(nama_barang);
    $("#jumlah_barang").val(jumlah_barang);
    $("#satuan_produk").val(satuan);
    $("#satuan_konversi").val(satuan);
    $("#limit_stok").val(limit_stok);
    $("#ber_stok").val(ber_stok);
    $("#id_produk").val(id_barang);

if (ber_stok == 'Barang') {

    $.post('ambil_jumlah_produk.php',{kode_barang:kode_barang}, function(data){
      if (data == "") {
        data = 0;
      }
      $("#jumlahbarang").val(data);
      $('#kolom_cek_harga').val('1');
    });

}


$.post('cek_kode_barang_tbs_edit_penjualan.php',{kode_barang:kode_barang,no_faktur:no_faktur}, function(data){
          
  if(data == 1){
          alert("Anda Tidak Bisa Menambahkan Barang Yang Sudah Ada, Silakan Edit atau Pilih Barang Yang Lain !");

          $("#kode_barang").chosen("destroy");
          $("#kode_barang").val('');
          $("#kode_barang").trigger('chosen:updated');
          $("#kode_barang").trigger('chosen:open');
          $("#nama_barang").val('');

          $(".chosen").chosen({no_results_text: "Maaf, Data Tidak Ada!",search_contains:false}); 
   }//penutup if     



  });

    

  });
  }); 
  // end script untuk pilih kode barang menggunakan chosen   
</script>

<script>
//Choosen Open select
$(document).ready(function(){
    $("#kode_barang").trigger('chosen:open');

});
</script>


 <script type="text/javascript">
    $(document).ready(function(){
      
//fungsi hapus data TBS PENJUALAN
$(document).on('click','.btn-hapus-tbs',function(e){

      var no_faktur = $(this).attr("data-faktur");
      var nama_barang = $(this).attr("data-barang");
      var id = $(this).attr("data-id");
      var kode_barang = $(this).attr("data-kode-barang");
      var subtotal = $(this).attr("data-subtotal");
          if (subtotal == '') {
      subtotal = 0;
    };

   var tax_faktur = bersihPemisah(bersihPemisah(bersihPemisah(bersihPemisah($("#tax").val()))));
        if (tax_faktur == '') {
      tax_faktur = 0;
    };

var biaya_adm = bersihPemisah(bersihPemisah(bersihPemisah(bersihPemisah($("#biaya_adm").val()))));
  if (biaya_adm == '') {
      biaya_adm = 0;
    };
    var subtotal_tbs = bersihPemisah(bersihPemisah(bersihPemisah(bersihPemisah($("#total2").val()))));
    
    var pot_fakt_per = bersihPemisah(bersihPemisah(bersihPemisah(bersihPemisah($("#potongan_persen").val()))));


    var pot_fakt_rp = bersihPemisah(bersihPemisah(bersihPemisah(bersihPemisah($("#potongan_penjualan").val()))));
           if (pot_fakt_rp == '') {
      pot_fakt_rp = 0;
    };

    var total_akhir1 = parseInt(subtotal_tbs,10) - parseInt(subtotal,10);

   if (pot_fakt_per == 0) {

      var potongaaan = pot_fakt_rp;

      var potongaaan_per = parseInt(potongaaan,10) / parseInt(total_akhir1,10) * 100;
      var potongaaan = pot_fakt_rp;
      var hitung_tax = parseInt(total_akhir1,10) - parseInt(pot_fakt_rp,10);
      var tax_bener = parseInt(hitung_tax,10) * parseInt(tax_faktur,10) / 100;

      var total_akhir = parseInt(total_akhir1,10) - parseInt(pot_fakt_rp,10) + parseInt(biaya_adm,10);/*+ parseInt(Math.round(tax_bener,10));*/


    }
    else if(pot_fakt_rp == 0)
    {
      var potongaaan = pot_fakt_per;
      var pos = potongaaan.search("%");
      var potongan_persen = bersihPemisah(bersihPemisah(bersihPemisah(bersihPemisah(Math.round(potongaaan)))));
          potongan_persen = potongan_persen.replace("%","");

      potongaaan = total_akhir1 * potongan_persen / 100;
      
      var potongaaan_per = pot_fakt_per;
      var hitung_tax = parseInt(total_akhir1,10) - parseInt(potongaaan,10);
      var tax_bener = parseInt(hitung_tax,10) * parseInt(tax_faktur,10) / 100;

     var total_akhir = parseInt(total_akhir1,10) - parseInt(potongaaan,10) + parseInt(biaya_adm,10);
     /*+ parseInt(Math.round(tax_bener,10));*/

    }
     else if(pot_fakt_rp != 0 && pot_fakt_rp != 0)
    {
      var potongaaan = pot_fakt_per;
      var pos = potongaaan.search("%");
      var potongan_persen = bersihPemisah(bersihPemisah(bersihPemisah(bersihPemisah(Math.round(potongaaan)))));
          potongan_persen = potongan_persen.replace("%","");
      potongaaan = total_akhir1 * potongan_persen / 100;
      
      var potongaaan_per = pot_fakt_per;
      var hitung_tax = parseInt(total_akhir1,10) - parseInt(potongaaan,10);
      var tax_bener = parseInt(hitung_tax,10) * parseInt(tax_faktur,10) / 100;

    var total_akhir = parseInt(total_akhir1,10) - parseInt(potongaaan,10) + parseInt(biaya_adm,10);
      /*+ parseInt(Math.round(tax_bener,10));*/

    
    }



// start hapus ajax TBS PENJUALAN
var pesan_alert = confirm("Apakah Anda Yakin Ingin Menghapus "+nama_barang+""+ "?");
if (pesan_alert == true) {


        $("#total2").val(tandaPemisahTitik(total_akhir1));  
        $("#total1").val(tandaPemisahTitik(Math.round(total_akhir)));      
        $("#potongan_penjualan").val(Math.round(potongaaan));
        
        $("#pembayaran_penjualan").val('');
        $("#kredit").val('');
        $("#sisa_pembayaran_penjualan").val('');

        $.post("hapustbs_penjualan.php",{id:id,kode_barang:kode_barang},function(data){
          

      $("#kode_barang").val('');
      $("#kode_barang").val('').trigger("chosen:updated");
      $("#kode_barang").trigger("chosen:open");

          $('#tabel_tbs_penjualan').DataTable().destroy();

                        var dataTable = $('#tabel_tbs_penjualan').DataTable( {
                          "processing": true,
                          "serverSide": true,
                          "ajax":{
                            url :"data_tbs_edit_penjualan.php", // json datasource
                              "data": function ( d ) {
                                d.no_faktur = $("#nomor_faktur_penjualan").val();
                                // d.custom = $('#myInput').val();
                                // etc
                            },
                             
                              type: "post",  // method  , by default get
                            error: function(){  // error handling
                              $(".employee-grid-error").html("");
                              $("#tabel_tbs_penjualan").append('<tbody class="employee-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
                              $("#employee-grid_processing").css("display","none");
                              }
                          },
                             "fnCreatedRow": function( nRow, aData, iDataIndex ) {

                              $(nRow).attr('class','tr-id-'+aData[11]+'');         

                          }
                        });

            if (total_akhir1 == 0) {
              
            $("#potongan_persen").val('0');
                 $("#ppn").val('Non');
                 $("#ppn").attr('disabled',false);
             $("#tax1").attr("disabled", true);

            }
            else{

            $("#potongan_persen").val(Math.round(potongaaan_per));
            }
            /*
            $("#tax_rp").val(Math.round(tax_bener));*/
            $("#kode_barang").trigger('chosen:open');    


        });
}
else {
    
    }
//end hapus ajax

});
                  $('form').submit(function(){
              
              return false;
              });


    });
  
//end fungsi hapus data
</script>

<script type="text/javascript">
   $(document).on('ready', function (e) {                
// START DATATABLE AJAX START TBS PENJUALAN
      $('#tabel_tbs_penjualan').DataTable().destroy();
            var dataTable = $('#tabel_tbs_penjualan').DataTable( {
            "processing": true,
            "serverSide": true,
            "info":     false,
            "language": { "emptyTable":     "My Custom Message On Empty Table" },
            "ajax":{
              url :"data_tbs_edit_penjualan.php", // json datasource
             "data": function ( d ) {
                d.no_faktur = $("#nomor_faktur_penjualan").val();
                  // d.custom = $('#myInput').val();
                  },
                  type: "post",  // method  , by default get
              error: function(){  // error handling
                $(".tbody").html("");
                $("#tabel_tbs_penjualan").append('<tbody class="tbody"><tr><th colspan="3"></th></tr></tbody>');
                $("#tableuser_processing").css("display","none");
                
              }
            }   

      });
        
        $('#pembayaran_penjualan').val('');

// END DATATABLE AJAX END DATATABLE AJAX TBS PENJUALAN
});
 </script>



<script type="text/javascript">

$(document).ready(function(){
  //Hitung Biaya Admin

  $("#biaya_admin_select").change(function(){
  
  var biaya_admin = $("#biaya_admin_select").val();  
  var total2 = bersihPemisah(bersihPemisah(bersihPemisah(bersihPemisah($("#total2").val()))));
  var total1 = bersihPemisah(bersihPemisah(bersihPemisah(bersihPemisah($("#total1").val()))));
  var diskon = bersihPemisah(bersihPemisah(bersihPemisah(bersihPemisah($("#potongan_penjualan").val()))));
      if(diskon == '')
      {
      diskon = 0
      }

  var data_admin = biaya_admin;

  if (biaya_admin == 0) {
      var hasilnya = parseInt(total2,10) - parseInt(diskon,10);
      $("#total1").val(tandaPemisahTitik(hasilnya));
      $("#biaya_adm").val(0);
      $("#biaya_admin_persen").val(data_admin);

  }
  else if (biaya_admin > 0) {

      var hitung_biaya = parseInt(total2,10) * parseInt(data_admin,10) / 100;
       if (total2 == "" || total2 == 0) {
       hitung_biaya = 0;
       }

      $("#biaya_adm").val(Math.round(hitung_biaya));
      var biaya_admin = bersihPemisah(bersihPemisah(bersihPemisah(bersihPemisah($("#biaya_adm").val()))));      
      var hasilnya = parseInt(total2,10) + parseInt(biaya_admin,10) - parseInt(diskon,10);

        if (total2 == "" || total2 == 0) {
        hasilnya = 0;
        }

      $("#total1").val(tandaPemisahTitik(hasilnya));
      $("#biaya_admin_persen").val(data_admin);
      


  }
      
    });
});
//end Hitu8ng Biaya Admin
</script>


<script type="text/javascript">
  $(document).ready(function(){
    
  //START KEYUP BIAYA ADMIN RUPIAH

    $("#biaya_adm").keyup(function(){
      var biaya_adm = bersihPemisah(bersihPemisah(bersihPemisah(bersihPemisah($("#biaya_adm").val()))));
      if (biaya_adm == '') {
        biaya_adm = 0;
      }
      var subtotal = bersihPemisah(bersihPemisah(bersihPemisah(bersihPemisah($("#total2").val()))));
      if (subtotal == '') {
        subtotal = 0;
      }
      var potongan = bersihPemisah(bersihPemisah(bersihPemisah(bersihPemisah($("#potongan_penjualan").val()))));
      if (potongan == '') {
        potongan = 0;
      }
      var pembayaran = bersihPemisah(bersihPemisah(bersihPemisah(bersihPemisah($("#pembayaran_penjualan").val()))));
      if (pembayaran == '') {
        pembayaran = 0;
      }  
      /*    
      var tax = bersihPemisah(bersihPemisah(bersihPemisah(bersihPemisah($("#tax").val()))));
      if (tax == '') {
        tax = 0;
      }*/

      var t_total = parseInt(subtotal,10) - parseInt(potongan,10);
      var biaya_admin_persen = parseInt(biaya_adm,10) / parseInt(subtotal,10) * 100;
      /*
      var t_tax = parseInt(t_total,10) * parseInt(tax,10) / 100;
      var total_akhir1 = parseInt(t_total,10) + Math.round(parseInt(t_tax,10));
      */

      var total_akhir = parseInt(t_total,10) + parseInt(Math.round(biaya_adm,10));


      $("#total1").val(tandaPemisahTitik(total_akhir));
      $("#biaya_admin_persen").val(Math.round(biaya_admin_persen));

      if (biaya_admin_persen > 100) {
            

            var total_akhir = parseInt(subtotal,10) - parseInt(potongan,10);
            alert ("Biaya Amin %, Tidak Boleh Lebih Dari 100%");
            $("#biaya_admin_persen").val('');
            $("#biaya_admin_select").val('0');            
            $("#biaya_admin_select").trigger('chosen:updated');
            $("#biaya_adm").val('');
            $("#biaya_adm").val('');
            $("#total1").val(tandaPemisahTitik(total_akhir));
          }
          
        else
          {
          }

    });

  //END KEYUP BIAYA ADMIN RUPIAH

  //START KEYUP BIAYA ADMIN PERSEN

    $("#biaya_admin_persen").keyup(function(){
      var biaya_admin_persen = bersihPemisah(bersihPemisah(bersihPemisah(bersihPemisah($("#biaya_admin_persen").val()))));
      if (biaya_admin_persen == '') {
        biaya_admin_persen = 0;
      }
      var subtotal = bersihPemisah(bersihPemisah(bersihPemisah(bersihPemisah($("#total2").val()))));
      if (subtotal == '') {
        subtotal = 0;
      }
      var potongan = bersihPemisah(bersihPemisah(bersihPemisah(bersihPemisah($("#potongan_penjualan").val()))));
      if (potongan == '') {
        potongan = 0;
      }
      var pembayaran = bersihPemisah(bersihPemisah(bersihPemisah(bersihPemisah($("#pembayaran_penjualan").val()))));
      if (pembayaran == '') {
        pembayaran = 0;
      }  


      var t_total = parseInt(subtotal,10) - parseInt(potongan,10);
      var biaya_admin_rupiah = parseInt(biaya_admin_persen,10) * parseInt(subtotal,10) / 100;
 

      var total_akhir = parseInt(t_total,10) + parseInt(Math.round(biaya_admin_rupiah,10));

      $("#total1").val(tandaPemisahTitik(total_akhir));
      $("#biaya_adm").val(Math.round(biaya_admin_rupiah));

      if (biaya_admin_persen > 100) {
            

            var total_akhir = parseInt(subtotal,10) - parseInt(potongan,10);
            alert ("Biaya Amin %, Tidak Boleh Lebih Dari 100%");
            $("#biaya_admin_persen").val('');
            $("#biaya_admin_select").val('0');            
            $("#biaya_admin_select").trigger('chosen:updated');
            $("#biaya_adm").val('');
            $("#total1").val(tandaPemisahTitik(total_akhir));
          }
          
        else
          {
          }

    });

  //END KEYUP BIAYA ADMIN PERSEN
  });
  
</script>

<!--START CEK PPN-->
<script type="text/javascript">
    $(document).ready(function(){

    // cek ppn exclude 
    var no_faktur = $("#no_faktur0").val();
    $.get("cek_ppn_ex_simpan.php",{no_faktur:no_faktur},function(data){
      if (data == 1) {
      $("#ppn").val('Exclude');
     $("#ppn").attr("disabled", true);
     $("#tax1").attr("disabled", false);
      }
      else if(data == 2){

      $("#ppn").val('Include');
     $("#ppn").attr("disabled", true);
       $("#tax1").attr("disabled", false);
      }
      else
      {

     $("#ppn").val('Non');
     $("#tax1").attr("disabled", true);

      }

    });


    $("#ppn").change(function(){

    var ppn = $("#ppn").val();
    $("#ppn_input").val(ppn);

  if (ppn == "Include"){

      $("#tax1").attr("disabled", false);

  }

  else if (ppn == "Exclude") {
    $("#tax1").attr("disabled", false);
  }
  else{

    $("#tax1").attr("disabled", true);
  }


  });
  });
</script>
<!--ENDING CEK PPN-->
<!-- memasukan file footer.php -->
<?php include 'footer.php'; ?>