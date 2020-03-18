<?php include 'layouts/template_top.php' ?>

<div class="row">
	<div class="col-xl-12 col-lg-12">
		<div class="card">
			<!-- Card Header - Dropdown -->
			<div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
				<h6 class="m-0 font-weight-bold text-primary">Leaders of <?= $division ?></h6>
			</div>
			<!-- Card Body -->
			<div class="card-body">
				<table class="table table-bordered">
					<thead>
					<tr>
						<th>Ranking</th>
						<th>Photo</th>
						<th>Name</th>
						<th>School</th>
						<th>Level</th>
						<th>Score</th>
						<th>Time</th>
					</tr>
					</thead>
					<tbody>
					<?php if (empty($players)): ?>
						<tr>
							<td colspan="5">
								<h3 class="text-center">No Spellers</h3>
							</td>
						</tr>
					<?php else: ?>
						<?php foreach ($players as $key => $player): ?>
							<tr>
								<td>
									<?= $key + 1 ?>
								</td>
								<td>
									<img src="<?= $player->image ?>" class="img-thumbnail" style="width:60px;"
										 alt="<?= $player->name ?>">
								</td>
								<td>
									<?= $player->name ?>
								</td>
								<td>
									<?= $player->school ?>
								</td>
								<td>
									<span class="badge badge-dark"><?= $player->level ?></span>
								</td>
								<td>
									<?= $player->score ?>
								</td>
								<td>
									<?= $player->time ?> Sec
								</td>
							</tr>
						<?php endforeach; ?>
					<?php endif; ?>
					</tbody>
				</table>
			</div>
			<div class="card-footer">
                <div class="float-right"> <?php echo $links; ?></div>
            </div>
		</div>
	</div>
</div>
<!-- End of Main Content -->
<?php include 'layouts/template_bottom.php' ?>
