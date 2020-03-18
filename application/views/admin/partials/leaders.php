<div class="col-xl-12 col-lg-12">
	<div class="card mb-4">
		<!-- Card Header - Dropdown -->
		<div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
			<h6 class="m-0 font-weight-bold text-primary">Leader Boards</h6>
		</div>
		<!-- Card Body -->
		<div class="card-body">
			<div class="row">
				<div class="col-md-3">
					<div class="card shadow mb-4">
						<!-- Card Header - Dropdown -->
						<div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
							<h6 class="m-0 font-weight-bold text-info">National</h6>
						</div>
						<!-- Card Body -->
						<div class="card-body" style="min-height:500px;">
							<?php if (empty($national_leaders)): ?>
								<h3>No Players</h3>
							<?php else: ?>
								<?php foreach ($national_leaders as $player): ?>
									<div class="row">
										<div class="col-md-2">
											<img src="<?= $player->image ?>" class="img-thumbnail" style="width:40px;" alt="<?= $player->name ?>">
										</div>
										<div class="col-md-8">
											<strong><?= $player->name ?></strong><br/>
											<small><?= $player->school ?></small>
										</div>
										<div class="col-md-2">
											<span class="badge badge-dark"><?= $player->level ?></span>
										</div>
									</div>
									<hr>
								<?php endforeach; ?>
								<a href="<?= base_url() ?>dashboard/spellers/National" class="text-success float-right mt-2">More</a>
							<?php endif; ?>
						</div>
					</div>
				</div>
				<?php foreach ($leaders as $key => $division): ?>
					<div class="col-md-3">
						<div class="card shadow mb-4">
							<!-- Card Header - Dropdown -->
							<div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
								<h6 class="m-0 font-weight-bold text-info"><?= $key ?></h6>
							</div>
							<!-- Card Body -->
							<div class="card-body" style="min-height:500px;">
								<?php if (empty($division)): ?>
									<h3 class="text-center">No Spellers</h3>
								<?php else: ?>
									<?php foreach ($division as $player): ?>
										<div class="row">
											<div class="col-md-2">
												<img src="<?= $player->image ?>" class="img-thumbnail" style="width:40px;" alt="<?= $player->name ?>">
											</div>
											<div class="col-md-8">
												<strong><?= $player->name ?></strong><br/>
												<small><?= $player->school ?></small>
											</div>
											<div class="col-md-2">
												<span class="badge badge-dark"><?= $player->level ?></span>
											</div>
										</div>
										<hr>
									<?php endforeach; ?>
									<a href="<?= base_url() ?>dashboard/spellers/<?= $key ?>" class="text-success float-right mt-2">More</a>
								<?php endif; ?>
							</div>
						</div>
					</div>
				<?php endforeach; ?>

			</div>
		</div>
	</div>
</div>
