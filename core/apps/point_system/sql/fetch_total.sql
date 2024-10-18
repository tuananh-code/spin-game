SELECT COUNT(*) AS total FROM `<?php echo($data['t_info']); ?>`
	
	WHERE `owner_id` = <?php echo($data['user_id']); ?>
