<?php

//connect db
$db = mysqli_connect(
    "localhost",
    "root",
    "",
    "database_data01"
);

function query($query){
	global $db;
	$res = mysqli_query($db, $query);
	$data = [];
	while ($row = mysqli_fetch_assoc($res)) {
		$data[] = $row;
	}
	return $data;
}

function getData($id=null)
{
    global $db;
    if($id==null){
        $jmldataperhalaman = 5;
        $jmldata = count(query("SELECT * FROM `my_tbl`"));
        $jmlhal = ceil($jmldata/$jmldataperhalaman);
        $halamanaktif = (isset($_GET['page']))?$_GET['page']:1;
        $dataawal = $halamanaktif * $jmldataperhalaman - $jmldataperhalaman;
        //h1 = 0
        //h2 = 5
        //h3 = 10
        //h4 = 15

        $res = mysqli_query($db, "SELECT * FROM `my_tbl` LIMIT $dataawal, $jmldataperhalaman ");
        $data = [];
        while ($row = mysqli_fetch_assoc($res)) {
            $data[] = $row;
        }
        return [$data, $jmlhal, $dataawal];
    }
    
    $res = mysqli_query($db, "SELECT * FROM `my_tbl` WHERE `my_tbl`.`id` = $id");
    $data = [];
    while ($row = mysqli_fetch_assoc($res)) {
        $data[] = $row;
    }
    return $data[0];
}

function insertData($data)
{
    global $db;
    $gambar = upload();
    if(!$gambar){
        return false;
    }
    $data = [
        'nama' => htmlspecialchars($_POST['nama']),
        'alamat' => htmlspecialchars($_POST['alamat']),
        'tgl_lahir' => htmlspecialchars($_POST['tgl_lahir']),
        'gambar' => htmlspecialchars($gambar)
    ];
    mysqli_query($db, "INSERT INTO my_tbl VALUES (
        '', '{$data['nama']}', '{$data['tgl_lahir']}', '{$data['alamat']}', '{$data['gambar']}'
    )");
    return mysqli_affected_rows($db);
}

function deleteData($id)
{
    global $db;
    mysqli_query($db, "DELETE FROM `my_tbl` WHERE `my_tbl`.`id` = $id");
    return mysqli_affected_rows($db);
}

function updateData($data,$id)
{
    global $db;
    $data = [
        'id' => htmlspecialchars($id),
        'nama' => htmlspecialchars($_POST['nama']),
        'alamat' => htmlspecialchars($_POST['alamat']),
        'tgl_lahir' => htmlspecialchars($_POST['tgl_lahir']),
        'gambar' => htmlspecialchars($_POST['gambar']),
        'gambarlama'=>$_POST['gambarlama']
    ];
    $gambarlama = $data['gambarlama'];
    
    $gambar = ($_FILES['gambar']['error']===4)?$gambarlama:upload();

    mysqli_query($db, "UPDATE my_tbl SET 
 id = '{$data['id']}', nama = '{$data['nama']}', tgl_lahir = '{$data['tgl_lahir']}', alamat = '{$data['alamat']}', gambar = '{$gambar}'
 WHERE id = {$data['id']}");
    return mysqli_affected_rows($db);
}

function cari($key)
{
    $q = "SELECT * FROM my_tbl WHERE nama LIKE '%$key%'";
    return query($q);
}

function upload()
{
    $namafile = $_FILES['gambar']['name'];
    $ukuranfile = $_FILES['gambar']['size'];
    $error = $_FILES['gambar']['error'];
    $tmpname = $_FILES['gambar']['tmp_name'];

    if($error === 4){
        echo "<script>alert('gambar kosong!')</script>";
        return false;
    }
    $ekstensigambarvalid = ['jpg', 'jpeg', 'png'];
    $ekstensigambar = end(explode('.', $namafile));
    $ekstensigambar = strtolower($ekstensigambar);
    if(!in_array($ekstensigambar, $ekstensigambarvalid)){
        echo "<script>alert('gambar harus berekstensi jpg, jpeg atau png')</script>";
        return false;
    }
    if($ukuranfile > 1_000_000){
        echo "<script>alert('gambar harus berukuran kurang dari 1 mb')</script>";
        return false;
    }
    $namafilebaru = uniqid().$ekstensigambar;

    move_uploaded_file($tmpname,'profile/'.$namafilebaru);
    return $namafilebaru;
}