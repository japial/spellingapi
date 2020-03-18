<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
<ul class="navbar-nav">
	<!-- Nav Item - User Information -->
	<li class="nav-item">
		<a class="nav-link text-dark" href="<?= base_url() ?>dashboard">
			<img class="rounded-circle" style="width: 60px;"  src="<?= base_url() ?>assets/img/logo.png">
			<span class="ml-4 d-none d-lg-inline">Dashboard</span>
		</a>
	</li>
	<li class="nav-item">
		<a class="nav-link text-dark" href="<?= base_url() ?>dashboard/user_history">
			<span class="ml-4 d-none d-lg-inline">User History</span>
		</a>
	</li>
	<li class="nav-item">
		<a class="nav-link text-dark" href="<?= base_url() ?>dashboard/leaderboard">
			<span class="ml-4 d-none d-lg-inline">Leaderboard</span>
		</a>
	</li>
</ul>
<ul class="navbar-nav ml-auto">
	<!-- Nav Item - User Information -->
	<li class="nav-item dropdown">
		<a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
			<span class="mr-2 d-none d-lg-inline text-gray-600 small">Admin</span>
		</a>
		<!-- Dropdown - User Information -->
		<div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
			<a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
				<i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
				Logout
			</a>
		</div>
	</li>
</ul>
</nav>
