<div class="row">

	<div class="col mb-4">
		<div class="card border-left-primary shadow h-100 py-2">
			<div class="card-body">
				<div class="row no-gutters align-items-center">
					<div class="col mr-2">
						<div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Users
						</div>
						<div class="h5 mb-0 font-weight-bold text-gray-800"><?= number_format($total_user) ?></div>
					</div>
					<div class="col-auto">
						<i class="fas fa-users fa-2x text-gray-300"></i>
					</div>
				</div>
			</div>
		</div>
	</div>
    <div class="col mb-4">
		<div class="card border-left-danger shadow h-100 py-2">
			<div class="card-body">
				<div class="row no-gutters align-items-center">
					<div class="col">
						<div class="text-xs font-weight-bold text-danger text-uppercase mb-1"> 
						    <span>Web Reg.</span>  
						    <span class="float-right">Mobile Reg.</span>
						</div>
						<div class="h5 mb-0 font-weight-bold text-gray-800">
						    <span> <?= number_format($total_user - $mobile_users) ?></span>
						    <span class="float-right"> <?= number_format($mobile_users) ?></span>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col mb-4">
		<div class="card border-left-success shadow h-100 py-2">
			<div class="card-body">
				<div class="row no-gutters align-items-center">
					<div class="col mr-2">
						<div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Game Played
						</div>
						<div class="h5 mb-0 font-weight-bold text-gray-800"><?=  number_format($played)  ?></div>
					</div>
					<div class="col-auto">
						<i class="fas fa-gamepad fa-2x text-gray-300"></i>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<div class="col mb-4">
		<div class="card border-left-warning shadow h-100 py-2">
			<div class="card-body">
				<div class="row no-gutters align-items-center">
					<div class="col">
						<div class="text-xs font-weight-bold text-warning text-uppercase mb-1"> 
						    <span>Web Games</span>  
						    <span class="float-right">Mobile Games</span>
						</div>
						<div class="h5 mb-0 font-weight-bold text-gray-800">
						  <span> <?= number_format($played - $mobile) ?></span> 
						  <span class="float-right"> <?= number_format($mobile) ?></span>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col mb-4">
		<div class="card border-left-info shadow h-100 py-2">
			<div class="card-body">
				<div class="row no-gutters align-items-center">
					<div class="col mr-2">
						<div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Words</div>
						<div class="row no-gutters align-items-center">
							<div class="col-auto">
								<div class="h5 mb-0 mr-3 font-weight-bold text-gray-800"><?= number_format($words) ?></div>
							</div>
						</div>
					</div>
					<div class="col-auto">
						<i class="fas fa-file-word fa-2x text-gray-300"></i>
					</div>
				</div>
			</div>
		</div>
	</div>

</div>
