<?php
require 'functions.php';
if( isset($_POST["submit"]) ) {
if( tambah($_POST) > 0 ) {
	echo "
	<script>
	alert('Success update databased');
	document.location.href ='stok.php';
	</script>
	";
	} else {
	echo "
	<script>
	alert('Failed Update databased');
	document.location.href ='stok.php';
	</script>
	";
}
}
?>
<!DOCTYPE html>
<html>
   <meta charset="utf-8">
    <title>Add New Transaksi</title>
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<link rel="icon" type="image/x-icon" href="https://axcora.com/img/angular.png" />
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
  </head>
  <body>
<div class="container">
  <?php require 'head.php';?>
<?php require 'nav.php';?>
  <div class="row shadow rounded">
    <div class="col-12 col-md-12 p-3 p-md-5">
  <div class="text-center">
  <h3>Axcora Car apps</h3>
  <p>Plugins for get axcora cms - 
  <a href="https://axcora.com/getaxcoracms">axcora.com/getaxcoracms</a>
  </div>
  <form action="" method="post">
  <div class="row">
   <div class="col-12 col-md-6 p-3">
  <label for="id"><h6>Customer </h6></label>
  <input type="text" name="nama" class="form-control col-md-9 rounded float-right" placeholder="nama penyewa" id="nama" required>
</div>

<div class="col-12 col-md-6 p-3">
    <label class="text-start" for="id"><h6>ID number</h6></label>
  <input type="text" name="track" class="form-control col-md-9 rounded float-right" placeholder="ID Penyewa KTP / SIM " id="track" required>
  </div>
  
  <div class="col-12 col-md-6 p-3">
    <label class="text-start" for="id"><h6>Kendaraan</h6></label>
  <input type="text" name="unit" class="form-control col-md-9 rounded float-right" placeholder="Kendaraan" id="unit" required>
  </div>
  
  <div class="col-12 col-md-6 p-3">
    <label class="text-start" for="id"><h6>Nopol</h6></label>
  <input type="text" name="nopol" class="form-control col-md-9 rounded float-right" placeholder="Nomor Polisi " id="nopol" required>
  </div>

  <div class="col-12 col-md-6 p-3">
    <label for="id" class="text-start"><h6>Start</h6></label>
   <input type="datetime-local" name="barcode" class="form-control col-md-9 rounded float-right" id="barcode" required>
   </div>
 <div class="col-12 col-md-6 p-3">
    <label for="id"><h6>Kembali </h6></label>
   <input type="datetime-local" name="jual" class="form-control col-md-9 rounded float-right" id="jual" required>
 </div>
 <div class="col-12 col-md-6 p-3">
    <label for="id"><h6>Fee Rp.</h6></label>
  <input type="number" name="beli" class="col-md-9 rounded float-right form-control" id="beli" required>
 </div>
 <div class="col-12 col-md-3 p-3">
	<select name="pay" class="col-md-9 rounded float-right form-control" id="pay" aria-label="Default select example" required>
  <option selected >Status Pembayaran</option>
  <option name="Belum Lunas" value="Belum Lunas">Belum Lunas</option>
  <option name="Lunas" value="Lunas">Lunas</option>
  </select>
</div>
 <div class="col-12 col-md-3 p-3">
	<select name="stok" class="col-md-9 rounded float-right form-control" id="stok" aria-label="Default select example" required>
  <option selected >Status Kendaraan</option>
  <option name="Proses" value="Proses">Proses</option>
  <option name="Telat" value="Telat">Telat</option>
  <option name="Selesai" value="Selesai">Selesai</option>
</select>
</div>

  
  <div class="col-12 col-md-12 p-3">
    <label class="text-start" for="id"><h6>Keterangan</h6></label>
  <textarea type="text" name="notes" class="form-control col-md-12 rounded float-right" rows="5" id="notes" placeholder="masukan keterangan disini" required></textarea>
  </div>
</div>
<button type="hidden" class="btn btn-dark float-right col-3" type="submit" name="submit">
Save
</button>
</div>
  </form>
    <div class="col-12 col-md-4"></div>
    <div class="col-12 col-md-12 text-center">
<hr>
<p class="text-center"><small>present by <a href="https://mesinkasironline.web.app" class="text-dark"> https://mesinkasironline.web.app</a></small></p>
</div>
</div>
</div>
</div>
</body>
</html>