<?php
    require_once "inc/header.php";
    require_once "class/Database.php";
    require_once "class/Product.php";
    require_once "class/Cart.php";
    require_once "class/Order.php";
    require_once "class/OrderDetail.php";

    $conn =  new Database();
    $pdo = $conn->getConnect();

    $customer_id = $_SESSION['user_id'];
    $data_cart = Cart::getAll($pdo, $customer_id);

    $index = 1;
    $total = 0;
    $total_price = 0;
    $sum = 0;

    $nameErrors = "";
    $addressError = "";
    $phoneErrors = "";
    $paymentErrors = "";
    $result = "";

    $recipient_name = "";
    $recipient_phone = "";
    $recipient_address = "";
    $payment_method = "";
    $note = "";
    $city = "";
    $district = "";
    $ward = "";

    if($_SERVER["REQUEST_METHOD"] == "POST") {
        $recipient_address = $_POST['address'];
        $recipient_name = $_POST['name'];
        $recipient_phone =  $_POST['phone'];
        $note =  $_POST['note'];
        $total_price = $_POST['total_price'];
        // $payment_method = $_POST['payment'];

        $city = $_POST['city'];
        $district = $_POST['district'];
        $ward = $_POST['ward'];

        $address = $recipient_address . ", " . $ward . ", " . $district . ", " . $city;

        if(empty($recipient_name)) {
            $nameErrors ="Họ tên người nhận không được bỏ trống!";
        }

        if(empty($recipient_address)) {
            $addressError ="Địa chỉ không được bỏ trống!";
        }
        
        if (empty($recipient_phone)) {
            $phoneErrors = "Số điện thoại không được bỏ trống!";
        }
        elseif (!ctype_digit($recipient_phone)) {
            // Số điện thoại không hợp lệ
            $phoneErrors = "Số điện thoại phải là chữ số nguyên";
        }
        elseif(!preg_match("/^[0-9]{10}$/", $recipient_phone)) {
            $phoneErrors = "Số điện thoại phải đủ 10 số";
        }

        if(empty($_POST['payment'])) {
            $paymentErrors = "Vui lòng chọn phương thức thanh toán";
        }else if($_POST['payment'] == 1) {
            $payment_method = "Tiền mặt";
        }
        else if($_POST['payment'] == 2) {
            $payment_method = "Chuyển khoản";
        }

        if(!$nameErrors && !$addressError && !$phoneErrors && !$paymentErrors) {
            $status = 0;
            $order_id = Order::saveOrder($pdo, $customer_id, $recipient_name, $address,
            $recipient_phone, $note, $total_price, $status, $payment_method);
            
            if(!empty($order_id)){
                $data_infocart = OrderDetail::getInfoCartItem($pdo, $customer_id);
                foreach($data_infocart as $item) {
                    OrderDetail::saveOrderDetail($pdo, $order_id, $item->product_id, $item->quantity, $item->price);
                }
                Cart::deleteCart($pdo, $customer_id);
                header("location:order-success.php");
            }
        }
    }
?>

<section class="checkout spad">
    <div class="container">
        <div class="checkout__form">
            <form method="post">
                <div class="row">
                    <div class="col-lg-8 col-md-6">
                        <!-- <h6 class="coupon__code"><span class="icon_tag_alt"></span> Have a coupon? <a href="#">Click
                        here</a> to enter your code</h6> -->
                        <h6 class="checkout__title">Chi tiết thanh toán</h6>
                        <div class="checkout__input">
                            <p>Họ và tên <span>*</span></p>
                            <input type="text" placeholder="Họ và tên" name="name"  value="<?=$recipient_name?>">
                            <p class="text-danger text-start fw-bold"><?=$nameErrors?></p>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="checkout__input">
                                    <p>Địa chỉ <span>*</span></p>
                                    <input type="text" placeholder="Địa chỉ nhận hàng" name="address" value="<?=$recipient_address?>">
                                    <p class="text-danger text-start fw-bold"><?=$addressError?></p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="checkout__input">
                                    <p>Tỉnh / Thành <span>*</span></p>
                                    <select class="form-select form-select-md mb-3" id="city" name="city" aria-label=".form-select-md" style="height: 50px;" required>
                                        <option value="" selected>Chọn tỉnh thành</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="checkout__input">
                                    <p>Quận / Huyện <span>*</span></p>
                                    <select class="form-select form-select-md mb-3" id="district" name="district" aria-label=".form-select-md" style="height: 50px;" required>
                                        <option value="" selected>Chọn quận huyện</option>
                                    </select>
                                </div>
                            </div>
                            
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="checkout__input">
                                    <p>Phường / Xã <span>*</span></p>
                                    <select class="form-select form-select-md" id="ward" name="ward" aria-label=".form-select-md" style="height: 50px;" required>
                                        <option value="" selected>Chọn phường xã</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="checkout__input">
                                    <p>Số điện thoại <span>*</span></p>
                                    <input type="text" name="phone"  value="<?=$recipient_phone?>">
                                    <p class="text-danger text-start fw-bold"><?=$phoneErrors?></p>
                                </div>
                            </div>
                            <!-- <div class="col-lg-6">
                                <div class="checkout__input">
                                    <p>Email <span>*</span></p>
                                    <input type="text">
                                </div>
                            </div> -->
                        </div>
                        <!-- <div class="checkout__input__checkbox">
                            <label for="acc">
                                Create an account?
                                <input type="checkbox" id="acc">
                                <span class="checkmark"></span>
                            </label>
                            <p>Create an account by entering the information below. If you are a returning customer
                            please login at the top of the page</p>
                        </div>
                        <div class="checkout__input">
                            <p>Account Password<span>*</span></p>
                            <input type="text">
                        </div> -->
                        <!-- <div class="checkout__input__checkbox">
                            <label for="diff-acc">
                                <input type="checkbox" id="diff-acc" class="checkmark check_notes">
                                Note about your order, e.g, special noe for delivery
                            </label>
                        </div> -->
                        <div class="checkout__input order_notes">
                            <p>Ghi chú đơn hàng</p>
                            <input type="text" name="note"  value="<?=$note?>"
                            placeholder="Notes about your order, e.g. special notes for delivery.">
                        </div>
                        <ul class="css-price-filter" style="gap: 7px;">
                            <p>Phương thức thanh toán <span style="color: red;">*</span></p>

                            <li class="border" style="border-color: #ccc; padding:15px">
                                <input type="radio" name="payment" value="1" id="payment-1" class="input_filter">
                                <label for="payment-1" style="line-height: 0;">Thanh toán tiền mặt khi nhận (COD)</label>
                            </li>
                            <li class="border" style="border-color: #ccc; padding:15px">
                                <input type="radio" name="payment" value="2" id="payment-2" class="input_filter">
                                <label for="payment-2" style="line-height: 0;">Chuyển khoản ngân hàng</label>
                            </li>
                        </ul>
                        <p class="text-danger text-start fw-bold"><?=$paymentErrors?></p>

                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="checkout__order">
                            <h4 class="order__title">Hóa đơn của bạn</h4>
                            <div class="checkout__order__products fw-bold">Sản phẩm <span>Tổng tiền</span></div>
                            <ul class="checkout__total__products">
                                <?php foreach($data_cart as $product): ?>
                                    <li> 
                                        <div class="row">
                                            
                                            <div class="col-md-7">
                                                <b><?= sprintf("%02d", $index) ?></b>. <?=$product->product_name?>
                                                <div>Số lượng: <?=$product->quantity?></div>
                                            </div>
                                            <span class="visually-hidden"><?$sum=$product->price*$product->quantity?></span>
                                            <div class="col-md-5 text-end"><?=number_format($sum, 0, ',', '.')?> ₫</div>
                                        </div>
                                    </li>
                                    <span class="visually-hidden"><?=$index++;?><?$total+=$sum?></span>
                                <?php endforeach; ?>
                            </ul>
                            <ul class="checkout__total__all">
                                <!-- <li>Tổng cộng <span><?=number_format($total, 0, ',', '.')?> ₫</span></li>
                                <li>Phí vận chuyển <span>0 ₫</span></li> -->
                                <li>Thành tiền <span><?=number_format($total, 0, ',', '.')?> ₫</span></li>                                    
                            </ul>
                            <input type="hidden" name="total_price" value="<?=$total?>"></input>
                            <button type="submit" class="site-btn text-uppercase">Hoàn tất thanh toán</button>
                        </div>
                    </div>
                </div>
            </form>

        </div>
    </div>
</section>

<?php

    require_once "inc/footer.php";
?>