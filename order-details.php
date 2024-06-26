<?php
    require_once "inc/header.php";
    require_once "class/Database.php";
    require_once "class/Cart.php";
    require_once "class/Order.php";
    require_once "class/OrderDetail.php";

    $conn =  new Database();
    $pdo = $conn->getConnect();

    $auth = new Auth();
    $auth->restrictAccess();
    $total = 0;

    $customer_id = $_SESSION['user_id'];

    $order_id;

    $data = OrderDetail::getOrderCustomer($pdo, $customer_id);
    // $data_order = OrderDetail::getOneOrder($pdo, $customer_id, $order_id);

    // $product_id = "";
    // $customer_id = "";
    // $price = "";

?>
<div class="container-md" id="cart">
    <div class="cart-grid row py-5">
        <div class="col-md-12 col-xs-12 check-info">
            <h1 class="title-page">Đơn hàng của bạn</h1>
            <?php if(empty($data)):?>
                <div class="row">
                    <img class="img-fluid w-25" src="assets/images/empty-order.png" alt="" style="margin: 0 auto;">
                </div>
            <?php else:?>
                <?php foreach($data as $d):?>
                    <div class="d-flex justify-content-between align-items-center">
                        <p class="py-3 fs-4">Mã đơn hàng: <?=$d->order_id?></p>
                        <div class="buttons_added  fs-5 my-4 d-flex align-items-center">
                            <span class="label px-3">Trạng thái:</span>
                            <span class="fw-bold <?=$d->status==0?'text-danger':'text-success'?>"><?=$d->status==0?'Đang chờ xác nhận':'Đã xác nhận'?></span>
                        </div>
                    </div>
                    <div class="cart-container">
                        <div class="cart-overview js-cart">
                            <ul class="cart-items">
                                <span class="visually-hidden"><?$data_order = OrderDetail::getOneOrder($pdo, $customer_id, $d->order_id);?></span>
                                <?php foreach($data_order as $product): ?>
                                    <span class="visually-hidden"><?$order_id = $product->order_id?></span>
                                    <li class="cart-item">
                                        <div class="product-line-grid row justify-content-between">
                                            <!--  product left content: image-->
                                            <div class="product-line-grid-left col-md-2">
                                                <span class="product-image media-middle">
                                                    <a href="product-detail.php?product_id=<?=$product->product_id?>">
                                                        <img class="img-fluid" src="assets/images/img_pro/<?=$product->product_image?>" alt="Organic Strawberry Fruits">
                                                    </a>
                                                </span>
                                            </div>
                                            <div class="product-line-grid-body col-md-5">
                                                <div class="product-line-info">
                                                    <span class="label-atrr"><a class="label" href="product.php?brand_id=<?=$product->brand_id?>" data-id_customization="0"><?=$product->brand_name?></a></span>
                                                </div>
                                                <div class="product-line-info">
                                                    <span class="label-atrr"><a href="product-detail.php?product_id=<?=$product->product_id?>" data-id_customization="0"><?=$product->product_name?></a></span>
                                                </div>
                                                <div class="product-line-info product-price">
                                                    <span class="value"><?=number_format($product->product_price, 0, ',', '.')?> ₫</span>
                                                </div>
                                            </div>
                                            <div class="product-line-grid-right text-center product-line-actions col-md-5">
                                                <div class="row text-center">
                                                    <div class="col-md-6 col">
                                                        <div class="label">Số lượng:</div>
                                                        <div class="buttons_added my-4">
                                                            <div class="input-text qty text fs-6 text-center border-0">
                                                                <?=$product->quantity?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col price">
                                                        <div class="label">Tổng cộng:</div>
                                                        <div class="product-price total my-4">
                                                            <?=number_format($product->price*$product->quantity, 0, ',', '.')?> ₫
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                    <span class="visually-hidden"><?$total = Order::getTotalOrder($pdo, $order_id)?></span>
                    <h5 class="text-end fw-bold mb-3" style="color: red;">Thành tiền: <?=number_format($total, 0, ',', '.')?></h5>
                <?php endforeach;?>
                <a href="product.php" class="continue btn btn-primary pull-xs-right">
                    Tiếp tục mua sắm
                </a>
            <?php endif;?>
        </div>

    </div>
</div>


<?php
    require_once "inc/footer.php";
?>