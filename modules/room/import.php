<?php

require './vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\PhpSpreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

if(isset($_POST['save_excel_data'])) {
    $fileName = $_FILES['import_file']['name'];
    $file_ext = pathinfo($fileName, PATHINFO_EXTENSION);

    $allowed_ext = ['xls', 'csv', 'xlsx'];

    if(in_array($file_ext, $allowed_ext)) {
        $inputFileNamePath = $_FILES['import_file']['tmp_name'];
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($inputFileNamePath);
        $data = $spreadsheet->getActiveSheet()->toArray();
        
        $count = "0";
        foreach($data as $row) {

            if($count > 0) {
                $tenphong = $row['0'];
                $dientich = $row['1'];
                $giathue = $row['2'];
                $tiencoc = $row['3'];
                $soluong = $row['4'];
                $ngaylaphd = $row['5'];
                $chuky = $row['6'];
                $ngayvao = $row['7'];
                $ngayra = $row['8'];
    
                $dataInsert = [
                    'tenphong' => $tenphong,
                    'dientich' => $dientich,
                    'giathue' => $giathue,
                    'tiencoc' => $tiencoc,
                    'soluong' => $soluong,
                    'ngaylaphd' => $ngaylaphd,
                    'chuky' => $chuky,
                    'ngayvao' => $ngayvao,
                    'ngayra' => $ngayra,
                ];
                
                $insertStatus = insert('room', $dataInsert);
                $msg = true;
            } else {
                $count = "1";
            }
        }

        if(isset($msg)) {
            setFlashData('msg', 'Import dữ liệu phòng trọ thành công');
            setFlashData('msg_type', 'suc');
            redirect('?module=room');
        } else {
            setFlashData('msg', 'Import dữ liệu phòng trọ thất bại');
            setFlashData('msg_type', 'err');
            redirect('?module=room');
        }

    } else {
            setFlashData('msg', 'Dữ liệu đầu vào không hợp lệ, mời chọn lại');
            setFlashData('msg_type', 'err');
            redirect('?module=room');
    }
}


?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import Excel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>
<body>
    <h4>Import Excel</h4>

    <form action="" method="post" enctype="multipart/form-data">
        <div class="col-6">
            <input type="file" name="import_file" class="form-control">
        </div>
        <button type="submit" name="save_excel_data" class="btn ">Import</button>
    </form>

</body>
</html>
