SELECT * FROM `cl_points_distribution` WHERE `point_id` = <?php echo($data['point_id']); ?>

	<?php if($data['offset']): ?>
		AND `id` < <?php echo($data['offset']); ?>
	<?php endif; ?>
    ORDER BY `id` desc
    <?php if($data['limit']): ?>
	LIMIT <?php echo($data['limit']); ?>
    <?php endif; ?>