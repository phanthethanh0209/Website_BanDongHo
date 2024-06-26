<?php
    require_once "inc/header.php";
    require_once "class/Database.php";
    // require_once "class/Product.php";
    require_once "class/Cart.php";

    $conn =  new Database();
    $pdo = $conn->getConnect();

    $auth = new Auth();
    $auth->restrictAccess();
    $total = 0;
    $sum = 0;

    $customer_id = $_SESSION['user_id'];
    $data_cart = Cart::getAll($pdo, $customer_id);

    $product_id = "";
    $customer_id = "";
    $price = "";

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $product_id = $_POST['product_id'];
        $customer_id = $_SESSION['user_id'];

        if(isset($_POST["deleteCart"])) {
            Cart::deleteCartItem($pdo, $customer_id, $product_id);
        }
        else if(isset($_POST["deleteAll"])){
            Cart::deleteCart($pdo, $customer_id);
        }
        else {
            $price = $_POST['price'];
            $quantity = $_POST['quantity'];
    
            Cart::updateCartItemQuantityAndPrice($pdo, $customer_id, $product_id, $quantity, $price);
        }

    }
?>
<div class="container-md" id="cart">
    <div class="cart-grid row py-5">
        <div class="col-md-9 col-xs-12 check-info">
            <h1 class="title-page">Giỏ hàng</h1>
            <?php if(empty($data_cart)):?>
                <div class="text-center" style="margin: 0 auto; ">
                    <img class="img-fluid w-25" src="assets/images/cart-empty.png" alt="Organic Strawberry Fruits">
                    <h4 class="py-3 fw-bold text-black">Không có sản phẩm nào!</h4>
                    <p class="py-2 fs-6">Hãy mua sắm ngay lúc này để tận hưởng các ưu đãi hấp dẫn chỉ dành riêng cho bạn.</p>
                    <a href="product.php" class="continue btn btn-primary pull-xs-right text-uppercase fs-6">
                        Dạo một vòng xem nào!
                    </a>
                </div>
            <?php else:?>
                <div class="cart-container">
                    <div class="cart-overview js-cart">
                        <ul class="cart-items">
                            <?php foreach($data_cart as $product): ?>
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
                                            <!-- hiển thị thương hiệu -->
                                            <div class="product-line-info">
                                                <span class="label-atrr"><a class="label" href="product.php?brand_id=<?=$product->brand_id?>" data-id_customization="0"><?=$product->brand_name?></a></span>
                                            </div>
                                            <!-- hiển thị tên sp -->
                                            <div class="product-line-info">
                                                <span class="label-atrr"><a href="product-detail.php?product_id=<?=$product->product_id?>" data-id_customization="0"><?=$product->product_name?></a></span>
                                            </div>
                                            <div class="product-line-info product-price">
                                                <span class="value"><?=number_format($product->product_price, 0, ',', '.')?> ₫</span>
                                            </div>
                                            
                                            <!-- <div class="product-line-info">
                                                <span class="label-atrr">Color:</span>
                                                <span class="value">Blue</span>
                                            </div> -->
                                        </div>
                                        <div class="product-line-grid-right text-center product-line-actions col-md-5">
                                            <div class="row">
                                                <div class="col-md-5 col">
                                                    <div class="label">Số lượng:</div>
                                                    <div class="quantity buttons_added">
                                                        <form method="post">
                                                            <input type="submit" name="decrease" value="-" class="minus fs-6">
                                                            <input type="number" step="1" min="1" max="100" name="quantity" value="<?=$product->quantity?>" title="Qty" class="input-text qty text fs-6 text-center" size="4" pattern="" inputmode="" style="width: 70px;" readonly>
                                                            <input type="submit" name="increase" value="+" class="plus fs-6">
                                                            <input type="hidden" name="product_id" value="<?=$product->product_id?>">
                                                            <input type="hidden" name="price" value="<?=$product->product_price?>">
                                                        </form>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col price">
                                                    <div class="label">Tổng cộng:</div>
                                                    <div class="product-price total">
                                                        <div class="visually-hidden"><?= $sum = $product->price* $product->quantity?></div>
                                                        <?=number_format($sum, 0, ',', '.')?> ₫
                                                        <div class="visually-hidden"><?= $total+=$sum ?></div>
                                                        
                                                    </div>
                                                </div>
                                                <div class="col-md-1 col text-xs-right align-self-end">
                                                    <div class="cart-line-product-actions ">
                                                        <!-- <a class="remove-from-cart" rel="nofollow" href="#" data-link-action="delete-from-cart" data-id-product="1">
                                                            <i class="fa fa-trash-o" aria-hidden="true"></i>
                                                        </a> -->
                                                        <form method="post">
                                                            <input type="hidden" name="product_id" value="<?=$product->product_id?>">

                                                            <button type="submit" name="deleteCart" class="remove-from-cart border-0" style="background: transparent;" >
                                                                <i class="fa fa-trash-o" aria-hidden="true"></i>
                                                            </button>
                                                        </form>
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
                <a href="product.php" class="continue btn btn-primary pull-xs-right">
                    Tiếp tục mua sắm
                </a>
                <form method="post" class="float-end">
                    <button type="submit" name="deleteAll" class="continue btn btn-primary pull-xs-right float-end">
                        Xóa tất cả
                    </button>
                </form>
            <?php endif;?>
        </div>
        <div class="cart-grid-right col-xs-12 col-lg-3">
            <!-- <div class="cart-summary">
                <div class="cart-detailed-totals">
                    <div class="cart-summary-products">
                        <div class="summary-label">Bạn có 3 sản phẩm trong giỏ hàng</div>
                    </div>
                    <div class="cart-summary-line" id="cart-subtotal-products">
                        <span class="label js-subtotal">
                            Tổng tiền:
                        </span>
                        <span class="value">£200.00</span>
                    </div>
                    <div class="cart-summary-line" id="cart-subtotal-shipping">
                        <span class="label">
                            Phí vận chuyển:
                        </span>
                        <span class="value">Free</span>
                        <div>
                            <small class="value"></small>
                        </div>
                    </div>
                    <div class="cart-summary-line cart-total">
                        <span class="label">Thành tiền:</span>
                        <span class="value">£200.00 (tax incl.)</span>
                    </div>
                </div>
            </div> -->
            <!-- <div id="block-reassurance">
                <ul>
                    <li>
                        <div class="block-reassurance-item">
                            <img src="img/product/check1.png" alt="Security policy (edit with Customer reassurance module)">
                            <span>Security policy (edit with Customer reassurance module)</span>
                        </div>
                    </li>
                    <li>
                        <div class="block-reassurance-item">
                            <img src="img/product/check2.png" alt="Delivery policy (edit with Customer reassurance module)">
                            <span>Delivery policy (edit with Customer reassurance module)</span>
                        </div>
                    </li>
                    <li>
                        <div class="block-reassurance-item">
                            <img src="img/product/check3.png" alt="Return policy (edit with Customer reassurance module)">
                            <span>Return policy (edit with Customer reassurance module)</span>
                        </div>
                    </li>
                </ul>
            </div> -->
            <div class="cart__total">
                <h6>Bạn có <?=$count?> sản phẩm trong giỏ hàng</h6>
                <ul>
                    <li>Tổng cộng <span><?=number_format($total, 0, ',', '.')?> ₫</span></li>
                    <!-- <li>Phí vận chuyển <span>0</span></li> -->
                    <!-- <li>Thành tiền <span><?=number_format($total, 0, ',', '.')?> ₫</span></li> -->
                </ul>
                <?php if(!empty($data_cart)):?>
                    <a href="product-checkout.php" class="primary-btn">Thanh toán</a>
                <?php endif;?>
            </div>
        </div>

    </div>
</div>


<?php
    require_once "inc/footer.php";
?>