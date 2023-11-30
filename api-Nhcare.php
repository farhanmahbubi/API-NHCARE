<?php

require_once 'koneksi.php';

if (function_exists($_GET['function'])) {
    $_GET['function']();
}

function loginUser()
{
    global $koneksi;
    $email = $_GET['email'];
    $password = $_GET['password'];

    $query = "SELECT * FROM tb_donatur WHERE email = ? AND password = ?";
    $stmt = mysqli_prepare($koneksi, $query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ss", $email, $password);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        $result = mysqli_stmt_num_rows($stmt);

        if ($result > 0) {
            $idDonatur = getDataDonaturByEmail($email, $koneksi);
            $response = ["status" => "success", "id_donatur" => $idDonatur];
        } else {
            $response = ["status" => "failed", "message" => "Email atau password salah"];
        }

        mysqli_stmt_close($stmt);
    } else {
        $response = ["status" => "failed", "message" => "Error in the prepared statement."];
    }

    header('Content-Type: application/json');
    echo json_encode($response);
}

function getDataDonaturByEmail($email, $koneksi)
{
    $email = mysqli_real_escape_string($koneksi, $email);
    $query = "SELECT id_donatur, nama, email, no_hp FROM tb_donatur WHERE email = '$email'";
    $result = mysqli_query($koneksi, $query);

    if (!$result) {
        die('Error in query: ' . mysqli_error($koneksi));
    }

    $row = mysqli_fetch_assoc($result);
    return $row;
}

function registerDonatur()
{
    global $koneksi;
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $nohp = $_POST['no_hp'];
    $password = $_POST['password'];

    if (!empty($nama) && !empty($email) && !empty($password) && !empty($nohp)) {
        $idDonatur = "DNTR-" . generateUniqueId(5);
        $regis = "INSERT INTO tb_donatur(id_donatur, nama, email, no_hp, password) VALUES ('$idDonatur', '$nama', '$email', '$nohp', '$password')";
        $msqlRegis = mysqli_query($koneksi, $regis);

        if ($msqlRegis) {
            $response = ["status" => "success", "message" => "Daftar Berhasil"];
        } else {
            $response = ["status" => "failed", "message" => "Gagal mendaftar: " . mysqli_error($koneksi)];
        }
    } else {
        $response = ["status" => "failed", "message" => "Semua data harus diisi"];
    }

    header('Content-Type: application/json');
    echo json_encode($response);
}

function generateUniqueId($length)
{
    $characters = '0123456789';
    $randomString = '';
    $charactersLength = strlen($characters);

    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }

    return $randomString;
}

function insertDonasi()
{
    global $koneksi;
    $data = $_POST;

    if (isset($data['transaction_id'], $data['gross_amount'], $data['order_id'], $data['settlement_time'], $data['id_donatur'], $data['nama_donatur'], $data['keterangan'], $data['doa'])) {
        $transactionId = $data['transaction_id'];
        $grossAmount = $data['gross_amount'];
        $orderId = $data['order_id'];
        $settlementTime = $data['settlement_time'];
        $idDonatur = $data['id_donatur'];
        $namaDon = $data['nama_donatur'];
        $keterangan = $data['keterangan'];
        $doa = $data['doa'];

        $query = "INSERT INTO tb_donasi (transaction_id, gross_amount, order_id, settlement_time, id_donatur, nama_donatur, keterangan, doa) VALUES ('$transactionId', '$grossAmount', '$orderId', '$settlementTime', '$idDonatur', '$namaDon', '$keterangan', '$doa')";
        $result = mysqli_query($koneksi, $query);

        if ($result) {
            $response = ['status' => 'success', 'message' => 'Data berhasil disimpan'];
        } else {
            $response = ['status' => 'failed', 'message' => 'Gagal menyimpan data. Error: ' . mysqli_error($koneksi)];
        }
    } else {
        $response = ['status' => 'failed', 'message' => 'Data tidak lengkap. Pastikan semua parameter tersedia.'];
    }

    header('Content-Type: application/json');
    echo json_encode($response);
}

function getAnakAsuhData()
{
    global $koneksi;

    $query = "SELECT nama, kelas, nama_sekolah, deskripsi, img_anak FROM tb_anakasuh";
    $result = mysqli_query($koneksi, $query);

    if ($result) {
        $data_anakasuh = array();

        while ($row = mysqli_fetch_assoc($result)) {
            $row['img_anak'] = base64_encode($row['img_anak']);
            $data_anakasuh[] = $row;
        }

        $response = ['status' => 1, 'message' => 'Success', 'data' => $data_anakasuh];
        echo json_encode($response);

        mysqli_free_result($result);
    } else {
        echo json_encode(['status' => 0, 'message' => 'Gagal menjalankan query: ' . mysqli_error($koneksi)]);
    }

    mysqli_close($koneksi);
}

function getProgramData()
{
    global $koneksi;

    $query = "SELECT * FROM tb_program";
    $result = mysqli_query($koneksi, $query);

    if ($result) {
        $data_program = array();

        while ($row = mysqli_fetch_assoc($result)) {
            $row['img_program'] = base64_encode($row['img_program']);
            $data_program[] = $row;
        }

        echo json_encode($data_program);

        mysqli_free_result($result);
    } else {
        echo json_encode(['status' => 0, 'message' => 'Gagal menjalankan query: ' . mysqli_error($koneksi)]);
    }

    mysqli_close($koneksi);
}

function getAcaraData()
{
    global $koneksi;

    $query = "SELECT * FROM tb_acara";
    $result = mysqli_query($koneksi, $query);

    if ($result) {
        $data_acara = array();

        while ($row = mysqli_fetch_assoc($result)) {
            $data_acara[] = $row;
        }

        echo json_encode($data_acara);

        mysqli_free_result($result);
    } else {
        echo "Gagal menjalankan query: " . mysqli_error($koneksi);
    }

    mysqli_close($koneksi);
}

function getWebsiteData()
{
    global $koneksi;

    $query = "SELECT * FROM tb_website";
    $result = mysqli_query($koneksi, $query);

    if ($result) {
        $data_website = array();

        while ($row = mysqli_fetch_assoc($result)) {
            $row['img_website'] = base64_encode($row['img_website']);
            $data_website[] = $row;
        }

        echo json_encode($data_website);

        mysqli_free_result($result);
    } else {
        echo "Gagal menjalankan query: " . mysqli_error($koneksi);
    }

    mysqli_close($koneksi);
}

function getVideoData()
{
    global $koneksi;

    $query = "SELECT * FROM tb_video";
    $result = mysqli_query($koneksi, $query);

    if ($result) {
        $data_video = array();

        while ($row = mysqli_fetch_assoc($result)) {
            $data_video[] = $row;
        }

        echo json_encode($data_video);

        mysqli_free_result($result);
    } else {
        echo "Gagal menjalankan query: " . mysqli_error($koneksi);
    }

    mysqli_close($koneksi);
}

function getDonasiHistory()
{
    global $koneksi;

    if (isset($_GET['id_donatur'])) {
        $idDonatur = $_GET['id_donatur'];
        $idDonatur = mysqli_real_escape_string($koneksi, $idDonatur);
        
        $query = "SELECT order_id, nama_donatur, keterangan, doa, gross_amount, settlement_time FROM tb_donasi WHERE id_donatur = '$idDonatur'";
        $result = mysqli_query($koneksi, $query);

        if (!$result) {
            die('Error in query: ' . mysqli_error($koneksi));
        }

        $historiDonasi = array();

        while ($row = mysqli_fetch_assoc($result)) {
            $historiDonasi[] = $row;
        }

        mysqli_close($koneksi);

        echo json_encode($historiDonasi);
    } else {
        echo "Parameter id_donatur tidak valid atau tidak diset.";
    }
}

function getTotalDonasi()
{
    global $koneksi;

    $query = "SELECT SUM(gross_amount) AS TotalDonasi FROM tb_donasi";
    $result = mysqli_query($koneksi, $query);

    if (!$result) {
        die('Error in query: ' . mysqli_error($koneksi) . ' Query: ' . $query);
    }

    $row = mysqli_fetch_assoc($result);
    echo $row['TotalDonasi'];
}

function getDataSantunan()
{
    global $koneksi;

    $query = "SELECT SUM(gross_amount) AS TotalDonasiSantunan FROM tb_donasi WHERE keterangan = 'santunan'";
    $result = mysqli_query($koneksi, $query);

    if (!$result) {
        die('Error in query: ' . mysqli_error($koneksi) . ' Query: ' . $query);
    }

    $row = mysqli_fetch_assoc($result);
    echo $row['TotalDonasiSantunan'];
}

function getDataPembangunan()
{
    global $koneksi;

    $query = "SELECT SUM(gross_amount) AS TotalDonasiPembangunan FROM tb_donasi WHERE keterangan = 'pembangunan'";
    $result = mysqli_query($koneksi, $query);

    if (!$result) {
        die('Error in query: ' . mysqli_error($koneksi) . ' Query: ' . $query);
    }

    $row = mysqli_fetch_assoc($result);
    echo $row['TotalDonasiPembangunan'];
}
function getFaqData()
{
    global $koneksi;

    $query = "SELECT * FROM tb_faq";
    $result = mysqli_query($koneksi, $query);

    if ($result) {
        $data_faq = array();

        while ($row = mysqli_fetch_assoc($result)) {
            $data_faq[] = $row;
        }

        echo json_encode($data_faq);

        mysqli_free_result($result);
    } else {
        echo json_encode(['status' => 0, 'message' => 'Gagal menjalankan query: ' . mysqli_error($koneksi)]);
    }

    mysqli_close($koneksi);
}
function updateDonatur()
{
    global $koneksi;

    if (isset($_GET['id_donatur'])) {
        $idDonatur = $_GET['id_donatur'];
        $nama = $_POST['nama'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $noHp = $_POST['no_hp'];
        $jk = $_POST['jenis_kelamin'];
        $alamat = $_POST['alamat'];

        // Perbarui data pengguna berdasarkan id_donatur
        $updateQuery = "UPDATE `tb_donatur` SET nama ='$nama', email = '$email', password  = '$password', no_hp = '$noHp', alamat = '$alamat', jenis_kelamin ='$jk' WHERE id_donatur = '$idDonatur'";
        
        $updateResult = mysqli_query($koneksi, $updateQuery);

        if ($updateResult) {
            // Jika berhasil memperbarui data, kirim respons sukses
            echo json_encode(array("status" => "success", "message" => "Data berhasil diperbarui"));
        } else {
            // Jika gagal memperbarui data, kirim respons error
            echo json_encode(array("status" => "error", "message" => "Gagal memperbarui data: " . mysqli_error($koneksi)));
        }
    } else {
        echo "Parameter id_donatur tidak valid atau tidak diset.";
    }
}
?>
