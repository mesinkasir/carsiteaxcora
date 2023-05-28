<?php
require 'functions.php';
$id = $_GET["id"];
$brg = query("SELECT * FROM barang WHERE ID = $id")[0];
if( isset($_POST["submit"]) ) {
if( ubah($_POST) > 0 ) {
	echo "
	<script>
	alert('Success update data');
	document.location.href ='stok.php';
	</script>
	
	";
	} else {
	echo "
	<script>
	alert('Opps.. failed to update');
	document.location.href ='stok.php';
	</script>
	";
	}
}
?>
<!DOCTYPE html>
<html>
   <meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<link rel="icon" type="image/x-icon" href="https://axcora.com/img/angular.png" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
    <title>Car App - Edit Transaksi</title>	
  </head>
  <body>

<div class="container">
  <?php require 'head.php';?>
<?php require 'nav.php';?>
  
  <div class="row rounded shadow">
    <div class="col-12 col-md-12 p-3 p-md-5">
  <h3>Update Transaksi</h3>
  <form action="" method="post">
  <input type="hidden" name="id" class="col-9 rounded float-right" value="<?= $brg["id"];?>"><br/>

  <label for="id">Customer </label>
  <input type="text" name="nama" class="col-9 rounded float-right" id="nama" required value="<?= $brg["nama"];?>"><br/>

    <label for="id">No ID </label>
	<input type="text" name="track" class="col-9 rounded float-right" id="track" required value="<?= $brg["track"];?>"><br/>
    
    <label for="id">Kendaraan </label>
	<input type="text" name="unit" class="col-9 rounded float-right" id="unit" required value="<?= $brg["unit"];?>"><br/>
    
    <label for="id">Nomor Polisi </label>
	<input type="text" name="nopol" class="col-9 rounded float-right" id="nopol" required value="<?= $brg["nopol"];?>"><br/>
	
    <label for="id">Tanggal Sewa </label>
	<input type="text" name="barcode" class="col-9 rounded float-right" id="barcode" required value="<?= $brg["barcode"];?>"><br/>
  
    <label for="id">Tanggal Kembali </label>
    <input type="text" name="jual" class="col-9 rounded float-right" id="jual" value="<?= $brg["jual"];?>"><br/>

    <label for="id">Fee  </label>
  <input type="text" name="beli" class="col-9 rounded float-right" id="beli" value="<?= $brg["beli"];?>"><br/>
  
    <label for="id">Status Pembayaran  </label>	
	<select class="form-select col-9 rounded float-right" name="pay" id="pay" aria-label="Default select example" required>
  <option selected value="<?= $brg["pay"];?>" name="pay"><?= $brg["pay"];?></option>
  <option name="Belum Lunas" value="Belum Lunas">Belum Lunas</option>
  <option name="Lunas" value="Lunas">Lunas</option>
</select><br/>

	
    <label for="id">Status Kendaraan</label>
		<select class="form-select col-9 rounded float-right" name="stok" id="stok" aria-label="Default select example" required>
  <option selected value="<?= $brg["stok"];?>" name="stok"><?= $brg["stok"];?></option>
  <option name="Proses" value="Proses">Proses</option>
  <option name="Telat" value="Telat">Telat</option>
  <option name="Selesai" value="Selesai">Selesai</option>
</select><br/>

    <label for="id">Keterangan</label>
	<textarea type="text" name="notes" class="col-9 rounded float-right" id="notes"><?= $brg["notes"];?></textarea><br/>
	
	<br/>
	<div class="col-md-12 p-1">
<button type="submit" class="btn btn-dark btn-lg rounded mt-5" name="submit">
Update Transaksi
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