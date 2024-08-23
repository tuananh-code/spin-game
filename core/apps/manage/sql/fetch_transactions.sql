SELECT * FROM `cl_transaction` WHERE `business_id` = <?php echo($data['user_id']); ?> And `del` = '0' 

	<?php if($data['offset']): ?>
		AND `id` < <?php echo($data['offset']); ?>
	<?php endif; ?>
    ORDER BY `id` desc
    <?php if($data['limit']): ?>
	LIMIT <?php echo($data['limit']); ?>
    <?php endif; ?>