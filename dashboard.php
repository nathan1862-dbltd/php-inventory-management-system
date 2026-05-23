<?php require_once 'includes/header.php'; ?>

<?php 

$sql = "SELECT * FROM product WHERE status = 1";
$query = $connect->query($sql);
$countProduct = $query->num_rows;

$orderSql = "SELECT * FROM orders WHERE order_status = 1";
$orderQuery = $connect->query($orderSql);
$countOrder = $orderQuery->num_rows;

$totalRevenue = "";
while ($orderResult = $orderQuery->fetch_assoc()) {
	$totalRevenue += $orderResult['paid'];
}

$lowStockSql = "SELECT * FROM product WHERE quantity <= 3 AND status = 1";
$lowStockQuery = $connect->query($lowStockSql);
$countLowStock = $lowStockQuery->num_rows;

$userwisesql = "SELECT users.username , SUM(orders.grand_total) as totalorder FROM orders INNER JOIN users ON orders.user_id = users.user_id WHERE orders.order_status = 1 GROUP BY orders.user_id";
$userwiseQuery = $connect->query($userwisesql);
$userwieseOrder = $userwiseQuery->num_rows;

$connect->close();
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>

body {
    background: #f4f7fc;
    font-family: 'Segoe UI', sans-serif;
}

.dashboard-card {
    border-radius: 18px;
    padding: 24px;
    color: white;
    margin-bottom: 25px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.08);
    transition: 0.3s ease;
}

.dashboard-card:hover {
    transform: translateY(-5px);
}

.dashboard-card h2 {
    font-size: 34px;
    font-weight: 700;
    margin: 10px 0;
}

.dashboard-card p {
    margin: 0;
    opacity: 0.9;
    font-size: 15px;
}

.dashboard-card i {
    font-size: 30px;
    opacity: 0.9;
}

.gradient-blue {
    background: linear-gradient(135deg, #4facfe, #00f2fe);
}

.gradient-green {
    background: linear-gradient(135deg, #43e97b, #38f9d7);
}

.gradient-red {
    background: linear-gradient(135deg, #fa709a, #fee140);
}

.gradient-purple {
    background: linear-gradient(135deg, #667eea, #764ba2);
}

.dashboard-link {
    text-decoration: none !important;
}

.date-card {
    background: white;
    border-radius: 18px;
    padding: 25px;
    text-align: center;
    box-shadow: 0 6px 20px rgba(0,0,0,0.06);
    margin-bottom: 25px;
}

.date-card h1 {
    font-size: 58px;
    margin: 0;
    color: #333;
    font-weight: 700;
}

.date-card p {
    color: #777;
    margin-top: 10px;
}

.revenue-card {
    background: linear-gradient(135deg, #141e30, #243b55);
    color: white;
    border-radius: 18px;
    padding: 25px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.08);
}

.revenue-card h1 {
    font-size: 42px;
    margin: 0;
    font-weight: 700;
}

.revenue-card p {
    margin-top: 10px;
    opacity: 0.8;
}

.custom-panel {
    background: white;
    border-radius: 18px;
    padding: 25px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.06);
}

.custom-panel h3 {
    margin-top: 0;
    margin-bottom: 20px;
    font-weight: 600;
}

.table {
    margin-bottom: 0;
}

.table thead {
    background: #f8fafc;
}

.table thead th {
    border: none !important;
    padding: 15px !important;
    font-weight: 600;
}

.table tbody td {
    padding: 15px !important;
    vertical-align: middle !important;
}

.badge-sales {
    background: #4facfe;
    color: white;
    padding: 8px 14px;
    border-radius: 30px;
    font-size: 13px;
}

@media(max-width:768px){

    .dashboard-card h2 {
        font-size: 26px;
    }

    .date-card h1 {
        font-size: 42px;
    }

}

</style>

<div class="container-fluid">

<div class="row">

<?php if(isset($_SESSION['userId']) && $_SESSION['userId']==1) { ?>

<div class="col-md-4">
    <a href="product.php" class="dashboard-link">
        <div class="dashboard-card gradient-blue">
            <i class="fa-solid fa-box"></i>
            <h2><?php echo $countProduct; ?></h2>
            <p>Total Products</p>
        </div>
    </a>
</div>

<div class="col-md-4">
    <a href="product.php" class="dashboard-link">
        <div class="dashboard-card gradient-red">
            <i class="fa-solid fa-triangle-exclamation"></i>
            <h2><?php echo $countLowStock; ?></h2>
            <p>Low Stock Items</p>
        </div>
    </a>
</div>

<?php } ?>

<div class="col-md-4">
    <a href="orders.php?o=manord" class="dashboard-link">
        <div class="dashboard-card gradient-green">
            <i class="fa-solid fa-cart-shopping"></i>
            <h2><?php echo $countOrder; ?></h2>
            <p>Total Orders</p>
        </div>
    </a>
</div>

</div>

<div class="row">

<div class="col-md-4">

    <div class="date-card">
        <h1><?php echo date('d'); ?></h1>
        <p><?php echo date('l') . ', ' . date('F Y'); ?></p>
    </div>

    <div class="revenue-card">
        <h1>₹ <?php echo number_format($totalRevenue,2); ?></h1>
        <p>Total Revenue</p>
    </div>

</div>

<?php if(isset($_SESSION['userId']) && $_SESSION['userId']==1) { ?>

<div class="col-md-8">

    <div class="custom-panel">

        <h3>
            <i class="fa-solid fa-chart-line"></i>
            User Wise Orders
        </h3>

        <div class="table-responsive">

            <table class="table table-hover">

                <thead>
                    <tr>
                        <th>User</th>
                        <th>Total Sales</th>
                    </tr>
                </thead>

                <tbody>

                <?php while ($orderResult = $userwiseQuery->fetch_assoc()) { ?>

                    <tr>
                        <td>
                            <strong><?php echo $orderResult['username']; ?></strong>
                        </td>

                        <td>
                            <span class="badge-sales">
                                ₹ <?php echo number_format($orderResult['totalorder'],2); ?>
                            </span>
                        </td>
                    </tr>

                <?php } ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

<?php } ?>

</div>

</div>

<script>
$(function () {
    $('#navDashboard').addClass('active');
});
</script>

<?php require_once 'includes/footer.php'; ?>