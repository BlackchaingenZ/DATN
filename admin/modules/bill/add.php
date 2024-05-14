<?php

if(!defined('_INCODE'))
die('Access denied...');


$data = [
    'pageTitle' => 'Thêm hóa đơn mới'
];

layout('header', 'admin', $data);
layout('breadcrumb', 'admin', $data);

// Dịch vụ
$donGiaNuoc = firstRaw("SELECT giadichvu FROM services WHERE tendichvu = 'Tiền nước'");
$dongiaDien = firstRaw("SELECT giadichvu FROM services WHERE tendichvu = 'Tiền điện'");
$dongiaRac = firstRaw("SELECT giadichvu FROM services WHERE tendichvu = 'Tiền rác'");
$dongiaWifi = firstRaw("SELECT giadichvu FROM services WHERE tendichvu = 'Tiền Wifi'");


$allTenant = getRaw("SELECT tenant.id, tenkhach, tenphong FROM tenant INNER JOIN contract ON contract.tenant_id = tenant.id INNER JOIN room ON tenant.room_id = room.id ORDER BY tenphong");
$allRoom = getRaw("SELECT room.id, tenphong, giathue, soluong, chuky FROM room INNER JOIN contract ON contract.room_id  = room.id ORDER BY tenphong");

// Xử lý thêm người dùng
if(isPost()) {
    // Validate form
    $body = getBody(); // lấy tất cả dữ liệu trong form
    $errors = [];  // mảng lưu trữ các lỗi
    
    //Valide họ tên: Bắt buộc phải nhập, >=5 ký tự
   
   // Kiểm tra mảng error
  if(empty($errors)) {
    // không có lỗi nào
    $dataInsert = [
        'room_id' => $body['room_id'],
        'tenant_id' => $body['tenant_id'],
        'chuky' => $body['chuky'],
        'tienphong' => $body['tienphong'],
        'sodiencu' => $body['sodiencu'],
        'sodienmoi' => $body['sodienmoi'],
        'tiendien' => $body['tiendien'],
        'sonuoccu' => $body['sonuoccu'],
        'sonuocmoi' => $body['sonuocmoi'],
        'tiennuoc' => $body['tiennuoc'],
        'songuoi' => $body['soluong'],
        'tienrac' => $body['tienrac'],
        'tienmang' => $body['tienmang'],
        'nocu' => $body['nocu'],
        'tongtien' => $body['tongtien'],
    ];

    $insertStatus = insert('bill', $dataInsert);
    if ($insertStatus) {
        setFlashData('msg', 'Thêm thông tin hóa đơn thành công');
        setFlashData('msg_type', 'suc');
        redirect('admin/?module=bill');
    }else {
    setFlashData('msg', 'Hệ thống đang gặp sự cố, vui lòng thử lại sau');
    setFlashData('msg_type', 'err');
    redirect('admin/?module=bill&action=add'); 
    }

  }else {
    // Có lỗi xảy ra
    setFlashData('msg', 'Vui lòng kiểm tra chính xác thông tin nhập vào');
    setFlashData('msg_type', 'err');
    setFlashData('errors', $errors);
    setFlashData('old', $body);  // giữ lại các trường dữ liệu hợp lê khi nhập vào
    redirect('admin/?module=bill&action=add'); 
  }

}
$msg =getFlashData('msg');
$msgType = getFlashData('msg_type');
$errors = getFlashData('errors');
$old = getFlashData('old');
?>
<?php
layout('navbar', 'admin', $data);
?>

    <div class="container">
            <div id="MessageFlash">
                <?php getMsg($msg, $msgType);?> 
            </div>

            <div class="box-content">
                <form action="" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    
                <!-- hàng 1 -->
                    <div class="row">
                        <div class="col-5">
                            <div class="form-group">
                                <label for="">Chọn phòng lập hóa đơn <span style="color: red">*</span></label>
                                <select name="room_id" id="room_id" class="form-select" onchange="updateTienPhong(); updateChuky(); updateSoluong()">
                                    <option value="">Chọn phòng</option>
                                    <?php
                                        if(!empty($allRoom)) {
                                            foreach($allRoom as $item) {                                            
                                                    ?>
                                                        <option data-soluong="<?php echo $item['soluong']; ?>" data-chuky="<?php echo $item['chuky']; ?>" data-giaphong="<?php echo $item['giathue']; ?>" value="<?php echo $item['id'] ?>" <?php echo (!empty($roomId) && $roomId == $item['id'])?'selected':'' ?>><?php echo $item['tenphong'] ?></option> 
                                                    <?php                                           
                                            }
                                        }
                                    ?>
                                </select>
                                <?php echo form_error('room_id', $errors, '<span class="error">', '</span>'); ?>
                            </div>
                        </div>
                        
                        <div class="col-5">
                            <div class="form-group">
                                <label for="">Người đại diện <span style="color: red">*</span></label>
                                <select name="tenant_id" id="" class="form-select">
                                    <option value="">Chọn người đại diện</option>
                                    <?php
                                        if(!empty($allTenant)) {
                                            foreach($allTenant as $item) {                                            
                                                    ?>
                                                        <option value="<?php echo $item['id'] ?>" <?php echo (!empty($tenantId) && $tenantId == $item['id'])?'selected':'' ?>><?php echo $item['tenkhach']?> - <?php echo $item['tenphong'] ?></option> 
                                                    <?php                                           
                                            }
                                        }
                                    ?>
                                </select>
                                <?php echo form_error('tenant_id', $errors, '<span class="error">', '</span>'); ?>
                            </div>
                        </div>
                    </div>

                <!-- Hàng 2 -->

                <div class="row">
                    <div class="col-5">
                        <div class="form-group">
                            <label for="">Chu kỳ <span style="color: red">*</span></label>
                            <input type="text" name="chuky" id="chuky" class="form-control" > 
                            <?php echo form_error('chuky', $errors, '<span class="error">', '</span>'); ?>
                        </div>
                    </div>

                   <div class="col-5">
                        <div class="form-group">
                            <label for="tienphong">Tiền Phòng</label>
                            <input type="text" class="form-control" id="tienphong" name="tienphong" >
                        </div>
                   </div>
                </div>

                <!-- Hàng 3 -->
                <div class="row">            
                    <div class="col-3">
                        <div class="water">
                            <div class="form-group">
                                <label for="sodiencu">Số điện cũ (KWh)</label>
                                <input type="number" min="0" id="sodiencu" class="form-control" name="sodiencu" required oninput="calculateTienDien()">
                            </div>

                            <div class="form-group">
                                <label for="sodienmoi">Số điện mới (KWh)</label>
                                <input type="number" min="0" id="sodienmoi" class="form-control" name="sodienmoi" required oninput="calculateTienDien()">
                            </div>

                            <div class="form-group">
                                <label for="tiennuoc">Tiền điện (4000đ/1KWh)</label>
                                <input type="text" class="form-control" id="tiendien" name="tiendien" >
                             </div>
                        </div>
                    </div>

                    <div class="col-3">
                        <div class="water">
                            <div class="form-group">
                                <label for="sonuoccu">Số nước cũ (m/3)</label>
                                <input type="number" min="0" id="sonuoccu" class="form-control" name="sonuoccu" required oninput="calculateTienNuoc()">
                            </div>

                            <div class="form-group">
                                <label for="sonuocmoi">Số nước mới (m/3)</label>
                                <input type="number" min="0" id="sonuocmoi" class="form-control" name="sonuocmoi" required oninput="calculateTienNuoc()">
                            </div>

                            <div class="form-group">
                                <label for="tiennuoc">Tiền Nước (20000đ/1m3)</label>
                                <input type="text" class="form-control" id="tiennuoc" name="tiennuoc" >
                             </div>
                        </div>
                    </div>

                    <div class="col-3">
                        <div class="water">                      
                            <div class="form-group">
                                <label for="soluong">Số lượng người</label>
                                <input type="number" min="0" id="soluongNguoi" class="form-control" name="soluong" required onchange="calculateTienRac()">
                            </div>

                            <div class="form-group">
                                <label for="tienrac">Tiền rác</label>
                                <input type="text" class="form-control" id="tienrac" name="tienrac" >
                             </div>
                        </div>
                    </div>

                    <div class="col-3">
                        <div class="water">
                            <div class="form-group">
                                <label for="tienmang">Tiền Wifi</label>
                                <input type="text" class="form-control" id="tienmang" name="tienmang" >
                             </div>
                        </div>
                        <div class="water">
                            <div class="form-group">
                                <label for="nocu">Nợ cũ</label>
                                <input type="text" class="form-control" id="nocu" name="nocu" >
                             </div>
                        </div>
                    </div>

                </div>
                
                <!-- Hàng 4 -->
                <div class="col-6">
                    <div class="form-group">
                        <label for="tongtien">Tổng tiền</label>
                        <input type="text" class="form-control" id="tongtien" name="tongtien" >
                </div>

                </div>
                    <div class="from-group" style="margin-top: 20px">                    
                            <div class="btn-row">
                                <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-plus"></i> Thêm hóa đơn</button>
                                <a style="margin-left: 20px " href="<?php echo getLinkAdmin('bill') ?>" class="btn btn-success"><i class="fa fa-forward"></i></a>
                            </div>
                    </div>
                </form>

            </div>
    </div>


<?php
layout('footer', 'admin');
?>
<script>
   
    // Cập nhật tiền rác & Wifi
    document.addEventListener('DOMContentLoaded', function() {
        const dongiaNuoc = <?php echo $donGiaNuoc['giadichvu']; ?>;
        const dongiaDien = <?php echo $dongiaDien['giadichvu']; ?>;
        const dongiaRac = <?php echo $dongiaRac['giadichvu']; ?>;
        const dongiaWifi = <?php echo $dongiaWifi['giadichvu']; ?>;

    function updateRoomDetails() { 
        // Tính toán tiền rác & Wifi
        calculateTienRac();
        calculateTienMang();
        calculateTotal();
    }

    function calculateTienNuoc() {
        const sonuoccu = parseFloat(document.getElementById('sonuoccu').value) || 0;
        const sonuocmoi = parseFloat(document.getElementById('sonuocmoi').value) || 0;
        const tiennuoc = (sonuocmoi - sonuoccu) * dongiaNuoc;
        document.getElementById('tiennuoc').value = numberWithCommas(tiennuoc) + ' đ';
        calculateTotal();
    }

    function calculateTienDien() {
        const sodiencu = parseFloat(document.getElementById('sodiencu').value) || 0;
        const sodienmoi = parseFloat(document.getElementById('sodienmoi').value) || 0;
        const tiendien = (sodienmoi - sodiencu) * dongiaDien;
        document.getElementById('tiendien').value = numberWithCommas(tiendien) + ' đ';
        calculateTotal();
    }

    function calculateTienRac() {
        const soluongNguoi = parseFloat(document.getElementById('soluongNguoi').value) || 0;
        const tienrac = soluongNguoi * dongiaRac;
        const formattedTienRac = numberWithCommas(tienrac);
        document.getElementById('tienrac').value = formattedTienRac + ' đ';
        calculateTotal();
    }

    function calculateTienMang() {
        const chuky = parseFloat(document.getElementById('chuky').value) || 0;
        const tienmang = chuky * dongiaWifi;
        const formattedTienMang = numberWithCommas(tienmang);
        document.getElementById('tienmang').value = formattedTienMang + ' đ';
        calculateTotal();
    }

    function calculateTotal() {
        const tienphong = parseFloat(document.getElementById('tienphong').value.replace(/,/g, '').replace(' đ', '')) || 0;
        const tiendien = parseFloat(document.getElementById('tiendien').value.replace(/,/g, '').replace(' đ', '')) || 0;
        const tiennuoc = parseFloat(document.getElementById('tiennuoc').value.replace(/,/g, '').replace(' đ', '')) || 0;
        const tienrac = parseFloat(document.getElementById('tienrac').value.replace(/,/g, '').replace(' đ', '')) || 0;
        const tienmang = parseFloat(document.getElementById('tienmang').value.replace(/,/g, '').replace(' đ', '')) || 0;
        const nocu = parseFloat(document.getElementById('nocu').value.replace(/,/g, '').replace(' đ', '')) || 0;

        const tongtien = tienphong + tiendien + tiennuoc + tienrac + tienmang + nocu;
        document.getElementById('tongtien').value = numberWithCommas(tongtien) + ' đ';
    }

    function numberWithCommas(x) {
        return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }

    function removeCommas(x) {
        return x.replace(/,/g, '');
    }

    document.getElementById('room_id').addEventListener('change', updateRoomDetails);
    document.getElementById('sonuoccu').addEventListener('input', calculateTienNuoc);
    document.getElementById('sonuocmoi').addEventListener('input', calculateTienNuoc);
    document.getElementById('sodiencu').addEventListener('input', calculateTienDien);
    document.getElementById('sodienmoi').addEventListener('input', calculateTienDien);
    document.getElementById('soluongNguoi').addEventListener('input', calculateTienRac);
    document.getElementById('chuky').addEventListener('input', calculateTienMang);
    document.getElementById('nocu').addEventListener('input', calculateTotal);

    document.querySelector('form').addEventListener('submit', function(e) {
        document.getElementById('tienphong').value = removeCommas(document.getElementById('tienphong').value);
        document.getElementById('tiendien').value = removeCommas(document.getElementById('tiendien').value);
        document.getElementById('tiennuoc').value = removeCommas(document.getElementById('tiennuoc').value);
        document.getElementById('tienrac').value = removeCommas(document.getElementById('tienrac').value);
        document.getElementById('tienmang').value = removeCommas(document.getElementById('tienmang').value);
        document.getElementById('nocu').value = removeCommas(document.getElementById('nocu').value);
        document.getElementById('tongtien').value = removeCommas(document.getElementById('tongtien').value);
    });

    // Gán sự kiện onchange cho thẻ select chọn phòng
    document.getElementById('room_id').addEventListener('change', updateRoomDetails);

    updateRoomDetails();
});

</script>





