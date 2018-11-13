<?php get_header(); ?>

<?php 
	$get_username_from_url = af_get_url()[1];
	$user = get_user_by( 'login' , $get_username_from_url );
	$user_meta = get_user_meta( $user->ID, 'af_url_list', true );
	
	$page_created = get_user_meta( $user->ID, 'af_page_created', false )[0];
	$page_updated = get_user_meta( $user->ID, 'af_page_updated', true );
?>
	<div class="af-wrapper" style="margin: 10px 10px 10px 10px; padding: 15px 10px 10px 10px; border-radius: 10px 10px 10px 10px;">
		<div class="container">
			<div class="row">
			
				<?php if( array_key_exists('afer_login', $page_created) or array_key_exists('afer_register', $page_created) and $page_created != '1'): ?>
						
					<div style="color: #5CB85C; text-align: center;" class="af-username-link">Your page has been successfully created.</div>
					<?php if( array_key_exists( 'afer_register', $page_created ) ): ?>
						<div style="color: #5CB85C; text-align: center;" class="af-username-link">Registration complete. Please check your email.</div>
					<?php endif; ?>
					
					<?php update_user_meta( $user->ID, 'af_page_created', array() ); ?>
					<br><br><br>	
					
				<?php endif; ?>
				
				<?php if( $page_updated == 'updated' ): ?>
					<div style="color: #5CB85C; text-align: center;" class="af-username-link">Your page has been successfully updated.</div>
					<br><br><br>	
					<?php update_user_meta( $user->ID, 'af_page_updated', 0 ); ?>
				<?php endif; ?>
				
				<br>
				<h2 style="color: black; font-size: 130%;" >My URL: <?php echo home_url() . '/' . $get_username_from_url; ?></h2>
				<br><br>
						
				<div class="list-group">
				
					<?php foreach( $user_meta as $url ): ?>
						<?php
							// Sanitize url for valid form.
							$url_link = str_replace('www.', '', $url);
							if( stripos($url_link, 'http://') === false and stripos($url_link, 'https://') === false ){
								$url_link = 'http://' . $url_link;
							}
						?>
						<div>
							<span class="af-favicon-view"></span><a href="<?php echo $url_link; ?>" class="list-group-item af-url-view" target="_blank"><?php echo $url; ?></a>
						</div>
					<?php endforeach; ?>
					
				</div>
						
				<?php if( is_user_logged_in() and get_current_user_id() == $user->ID ): ?>
					<br><a href="<?php echo home_url() . '/' . $get_username_from_url; ?>/edit"><input type="button" name="af-button-edit" class="btn btn-success" value="Edit my page"></a><br><br>
				<?php endif; ?>
								
			</div>	
		</div>
	</div>
	
<?php get_footer(); ?>