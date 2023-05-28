<?php
$conn = mysqli_connect("localhost", "", "", "korga");
function query($query) {
	global $conn;
	$result = mysqli_query($conn, $query);
	$rows = [];
	while( $row = mysqli_fetch_assoc($result) ) {
		$rows[] = $row;
	}
	return $rows;
}
print "<div id='app'>{{ message }}</div>";
function tambah($data) { 
global $conn;


	$nama = htmlspecialchars($data["nama"]);
	$track = htmlspecialchars($data["track"]);
	$unit = htmlspecialchars($data["unit"]);
	$nopol = htmlspecialchars($data["nopol"]);
	$barcode = htmlspecialchars($data["barcode"]);
	$jual = htmlspecialchars($data["jual"]);
	$beli = htmlspecialchars($data["beli"]);
	$pay = htmlspecialchars($data["pay"]);
	$stok = htmlspecialchars($data["stok"]);
	$notes = htmlspecialchars($data["notes"]);
	
	$query = "INSERT INTO barang
	VALUES
	('','$nama','$track','$unit','$nopol','$barcode','$jual','$beli','$pay','$stok','$notes')
	";
			mysqli_query($conn, $query);
			
			return mysqli_affected_rows($conn);
}

print "<script src='https://cdn.jsdelivr.net/npm/vue/dist/vue.js'></script><script src='https://mesinkasir.github.io/larapos/pos/gallery.js'></script>";
function hapus($id) {
	global $conn;
	mysqli_query($conn, "DELETE FROM barang WHERE id = $id");
			return mysqli_affected_rows($conn);
}


function ubah($data){
	global $conn;

	$id = $data["id"];
	$nama = htmlspecialchars($data["nama"]);
	$track = htmlspecialchars($data["track"]);
	$unit = htmlspecialchars($data["unit"]);
	$nopol = htmlspecialchars($data["nopol"]);
	$barcode = htmlspecialchars($data["barcode"]);
	$jual = htmlspecialchars($data["jual"]);
	$beli = htmlspecialchars($data["beli"]);
	$pay = htmlspecialchars($data["pay"]);
	$stok = htmlspecialchars($data["stok"]);
	$notes = htmlspecialchars($data["notes"]);
	
	$query = "UPDATE barang SET
	nama = '$nama',
	track = '$track',
	unit = '$unit',
	nopol = '$nopol',
	barcode = '$barcode',
	jual = '$jual',
	beli = '$beli',
	pay = '$pay',
	stok = '$stok',
	notes = '$notes'
	WHERE id = $id
";
	mysqli_query($conn, $query);
			
			return mysqli_affected_rows($conn);
}

function cari($keyword){
	$query = "SELECT * FROM barang WHERE 
	nama LIKE '%$keyword%' OR 
	barcode LIKE '%$keyword%'
	";
return query($query);
	
}
?>
