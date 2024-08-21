SELECT COUNT(*) AS total FROM `<?php echo($data['t_transactions']); ?>`
	
	WHERE `business_id` = <?php echo($data['user_id']); ?>
