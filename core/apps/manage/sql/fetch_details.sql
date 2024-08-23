SELECT * FROM `<?php echo($data['t_transactions']); ?>`
	
	WHERE `business_id` = <?php echo($data['user_id']); ?> And `id` = <?php echo($data['id_transaction']); ?>
