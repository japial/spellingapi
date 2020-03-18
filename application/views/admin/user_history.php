<?php include 'layouts/template_top.php' ?>

<div class="row">
	<div class="col-xl-12 col-lg-12">
	<div class="card mb-4">
		<!-- Card Header - Dropdown -->
		<div class="card-header">
			<h6 class="font-weight-bold text-primary text-center">User Wise Game History</h6>
		</div>
		<!-- Card Body -->
		<div class="card-body bg-white">
			<table class="table table-bordered">
			    <thead>
			        <tr>
			            <th>Name</th>
			            <th>School</th>
			            <th>Phone</th>
			            <th>Division</th>
			            <th>Game Played</th>
			        </tr>
			    </thead>
			    <tbody>
			        <?php foreach($player_history as $player): ?>
			        <tr>
			           <td><?= $player->name ?></td>
			           <td><?= $player->school ?></td>
			           <td><?= $player->phone ?></td>
			           <td><?= $player->division ?></td>
			           <td><?= $player->played ?></td>
			        </tr>
			        <?php endforeach; ?>
			    </tbody>
			</table>
		</div>
	</div>
</div>
<div class="col-lg-12">
   <div class="float-right"> <?php echo $links; ?></div>
</div>

</div>
<!-- End of Main Content -->
<?php include 'layouts/template_bottom.php' ?>
