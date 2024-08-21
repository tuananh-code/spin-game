SELECT store_id, game_name FROM `<?php echo($data['t_game']); ?>` WHERE game_name = "<?php echo($data['game']); ?>" AND store_id = "<?php echo($data['store_id']); ?>";
