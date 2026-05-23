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

<style type="text/css">
	.ui-datepicker-calendar {
		display: none;
	}
	
	/* ----- Modern Dashboard Cards ----- */
	.dash-card {
		border-radius: 8px;
		box-shadow: 0 2px 8px rgba(0,0,0,0.1);
		transition: transform 0.2s, box-shadow 0.2s;
		margin-bottom: 20px;
	}
	.dash-card:hover {
		transform: translateY(-2px);
		box-shadow: 0 4px 12px rgba(0,0,0,0.15);
	}
	.dash-card .panel-heading {
		border-top-left-radius: 8px;
		border-top-right-radius: 8px;
		padding: 20px 15px;
		font-size: 16px;
		font-weight: 600;
		letter-spacing: 0.3px;
	}
	.dash-card .panel-heading a {
		display: flex;
		align-items: center;
		justify-content: space-between;
		text-decoration: none;
		color: #fff;
	}
	.dash-card .panel-heading .badge {
		background: rgba(255,255,255,0.25);
		font-size: 18px;
		padding: 6px 12px;
		border-radius: 20px;
	}
	
	/* ----- Date & Revenue Cards ----- */
	.info-card {
		border-radius: 8px;
		overflow: hidden;
		box-shadow: 0 2px 8px rgba(0,0,0,0.08);
		margin-bottom: 20px;
	}
	.info-card .cardHeader {
		padding: 20px 15px 10px;
		font-size: 32px;
		font-weight: bold;
		color: #fff;
	}
	.info-card .cardContainer {
		padding: 10px 15px 20px;
		font-size: 14px;
		color: #555;
		background: #f9f9f9;
	}
	
	/* ----- Table improvements ----- */
	.user-order-table {
		background: #fff;
		border-radius: 8px;
		overflow: hidden;
		box-shadow: 0 1px 4px rgba(0,0,0,0.06);
	}
	.user-order-table th {
		background: #f5f5f5;
		border-bottom: 2px solid #ddd;
	}
	
	/* ----- Admin panel colors ----- */
	.panel-product { background: #5cb85c; border-color: #4cae4c; }
	.panel-lowstock { background: #d9534f; border-color: #c9302c; }
	.panel-orders { background: #5bc0de; border-color: #46b8da; }
	
	/* Responsive tweaks */
	@media (max-width: 768px) {
		.dash-card .panel-heading { font-size: 14px; }
		.info-card .cardHeader { font-size: 26px; }
	}
</style>

<!-- fullCalendar 2.2.5-->
<link rel="stylesheet" href="assests/plugins/fullcalendar/fullcalendar.min.css">
<link rel="stylesheet" href="assests/plugins/fullcalendar/fullcalendar.print.css" media="print">

<!-- ==================== FIRST ROW: METRIC CARDS ==================== -->
<div class="row">
	<?php  if(isset($_SESSION['userId']) && $_SESSION['userId']==1) { ?>
	<!-- Admin only: Total Product -->
	<div class="col-md-4">
		<div class="panel dash-card panel-product">
			<div class="panel-heading">
				<a href="product.php">
					<span><i class="glyphicon glyphicon-gift"></i> Total Product</span>
					<span class="badge pull-right"><?php echo $countProduct; ?></span>
				</a>
			</div>
		</div>
	</div>

	<!-- Admin only: Low Stock -->
	<div class="col-md-4">
		<div class="panel dash-card panel-lowstock">
			<div class="panel-heading">
				<a href="product.php">
					<span><i class="glyphicon glyphicon-exclamation-sign"></i> Low Stock</span>
					<span class="badge pull-right"><?php echo $countLowStock; ?></span>
				</a>
			</div>
		</div>
	</div>
	<?php } ?>

	<!-- Visible to all: Total Orders -->
	<div class="col-md-4">
		<div class="panel dash-card panel-orders">
			<div class="panel-heading">
				<a href="orders.php?o=manord">
					<span><i class="glyphicon glyphicon-shopping-cart"></i> Total Orders</span>
					<span class="badge pull-right"><?php echo $countOrder; ?></span>
				</a>
			</div>
		</div>
	</div>
</div>

<!-- ==================== SECOND ROW: DATE / REVENUE & USER TABLE ==================== -->
<div class="row">
	<div class="col-md-4">
		<!-- Date Card -->
		<div class="info-card" style="background: #337ab7;">
			<div class="cardHeader">
				<?php echo date('d'); ?>
			</div>
			<div class="cardContainer">
				<p><?php echo date('l') .' '.date('d').', '.date('Y'); ?></p>
			</div>
		</div>

		<!-- Revenue Card -->
		<div class="info-card" style="background: #245580;">
			<div class="cardHeader">
				<?php echo $totalRevenue ? $totalRevenue : '0'; ?>
			</div>
			<div class="cardContainer">
				<p><i class="glyphicon glyphicon-inr"></i> Total Revenue</p>
			</div>
		</div>
	</div>

	<?php if(isset($_SESSION['userId']) && $_SESSION['userId']==1) { ?>
	<!-- Admin only: User Wise Order -->
	<div class="col-md-8">
		<div class="panel panel-default user-order-table">
			<div class="panel-heading">
				<i class="glyphicon glyphicon-user"></i> User Wise Order
			</div>
			<div class="panel-body table-responsive">
				<table class="table table-hover table-striped" id="productTable">
					<thead>
						<tr>
							<th style="width:50%;">User</th>
							<th style="width:50%;">Total Orders (₹)</th>
						</tr>
					</thead>
					<tbody>
						<?php while ($orderResult = $userwiseQuery->fetch_assoc()) { ?>
						<tr>
							<td><?php echo $orderResult['username']; ?></td>
							<td><span class="label label-success"><?php echo number_format($orderResult['totalorder'], 2); ?></span></td>
						</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<?php } ?>
</div>

<!-- ==================== CALENDAR (commented in original, kept for reference) ==================== -->
<!-- fullCalendar was commented out in the original dashboard; we leave that as is -->
<!--
<div class="row">
	<div class="col-md-12">
		<div class="panel panel-default">
			<div class="panel-heading"><i class="glyphicon glyphicon-calendar"></i> Calendar</div>
			<div class="panel-body">
				<div id="calendar"></div>
			</div>
		</div>
	</div>
</div>
-->

<!-- fullCalendar 2.2.5 -->
<script src="assests/plugins/moment/moment.min.js"></script>
<script src="assests/plugins/fullcalendar/fullcalendar.min.js"></script>

<script type="text/javascript">
	$(function () {
		// top bar active
		$('#navDashboard').addClass('active');

		// Date for the calendar events (dummy data)
		var date = new Date();
		var d = date.getDate(),
		m = date.getMonth(),
		y = date.getFullYear();

		// Only initialize if the calendar element exists (currently commented out)
		if ($('#calendar').length) {
			$('#calendar').fullCalendar({
				header: {
					left: '',
					center: 'title'
				},
				buttonText: {
					today: 'today',
					month: 'month'
				}
			});
		}
	});
</script>

<?php require_once 'includes/footer.php'; ?>