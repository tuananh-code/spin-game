SELECT * FROM `cl_points_information` WHERE `owner_id` = <?php echo($data['user_id']); ?>

	<?php if($data['offset']): ?>
		AND `id` < <?php echo($data['offset']); ?>
	<?php endif; ?>
    ORDER BY `id` desc
    <?php if($data['limit']): ?>
	LIMIT <?php echo($data['limit']); ?>
    <?php endif; ?>