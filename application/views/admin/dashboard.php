<?php include 'layouts/template_top.php' ?>
	<?php include 'partials/toprow.php' ?>
	<div class="row">
		<?php include 'partials/game_overview.php' ?>
    </div>
	<div class="row">
		<?php include 'partials/game_hours.php' ?>
	</div>
<!-- Page level plugins -->
<script src="<?= base_url() ?>assets/vendor/chart.js/Chart.min.js"></script>
<!-- Page level custom scripts -->
<script src="<?= base_url() ?>assets/js/spellchart.js"></script>
<script type="text/javascript" language="javascript">
	var gameDatesArray = new Array();
	<?php foreach($history['dates'] as $val){ ?>
	gameDatesArray.push('<?php echo $val; ?>');
	<?php } ?>

	var gamePlayArray = new Array();
	<?php foreach($history['games'] as $val){ ?>
	gamePlayArray.push('<?php echo $val; ?>');
	<?php } ?>

	
	var gameLevels = new Array();
	<?php foreach($level_players['levels'] as $val){ ?>
	gameLevels.push('<?php echo $val; ?>');
	<?php } ?>

	var levelPlayers = new Array();
	<?php foreach($level_players['players'] as $val){ ?>
	levelPlayers.push('<?php echo $val; ?>');
	<?php } ?>
	
	var userRegistrationDates = new Array();
	<?php foreach($user_registrations['dates'] as $val){ ?>
	userRegistrationDates.push('<?php echo $val; ?>');
	<?php } ?>

	var userRegistrations = new Array();
	<?php foreach($user_registrations['regitration'] as $val){ ?>
	userRegistrations.push('<?php echo $val; ?>');
	<?php } ?>
	
	var gameHours = new Array();
	<?php foreach($hourly_history['hours'] as $val){ ?>
	gameHours.push('<?php echo $val; ?>');
	<?php } ?>

	var gamePlayHourly = new Array();
	<?php foreach($hourly_history['games'] as $val){ ?>
	gamePlayHourly.push('<?php echo $val; ?>');
	<?php } ?>

	
	renderAreaChart("dailyGamePlayChart", gameDatesArray, gamePlayArray, "Games");
	renderBarChart("userRegistrationChart", userRegistrationDates, userRegistrations, "Users");
	renderBarChart("hourlyGamePlayChart", gameHours, gamePlayHourly, "Games");
	renderBarChart("gameLevelsChart", gameLevels, levelPlayers, "Players");
</script>
<?php include 'layouts/template_bottom.php' ?>
