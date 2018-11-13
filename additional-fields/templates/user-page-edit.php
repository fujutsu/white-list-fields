<?php
	$get_username_from_url = af_get_url()[1];
	$user = get_user_by( 'login' , $get_username_from_url );
	$user_meta = get_user_meta( $user->ID, 'af_url_list', true );
	
	if( get_current_user_id() != $user->ID ){
		header('Location:' . home_url()); 
		exit();
	}
	
	$admissible_fields = (int)get_option('af_option')['field_count'];
?>

<?php get_header(); ?>

	<div class="af-wrapper" style="margin: 10px 10px 10px 10px; padding: 15px 10px 10px 10px; border-radius: 10px 10px 10px 10px;">
		<div class="container">
			<div class="row">
			
				<br>
				<h2 style="color: black; font-size: 130%;" >My URL: <?php echo home_url() . '/' . $get_username_from_url; ?></h2>
				<br>
				<div class="control-group" id="fields">
					<div class="controls"> 
						<form action="<?php the_permalink(); ?>" method="POST" id="af-form" role="form" autocomplete="off">
							<div class="af-wrap">
							
								<?php foreach( $user_meta as $index => $url ): ?>
									<?php $iteration = $index+1;?>
									<div class="entry input-group col-xs-3">
										<span class="af-favicon"></span>
										<input class="form-control af-valid" name="fields[]" value="<?php echo $url; ?>" type="text" placeholder="Type URL">
										<span class="input-group-btn">
											
											<?php if( $iteration == $admissible_fields-1 and count($user_meta) != $admissible_fields or $iteration > 1 and $iteration == count($user_meta) and count($user_meta) != $admissible_fields or count($user_meta) == 1 ): ?>
												
												<?php if( count($user_meta) == 1 ): ?>
													<button class="btn btn-success btn-add" type="button">
														<span class="glyphicon glyphicon-plus"></span>
													</button>
												<?php else: ?>
													<button class="btn btn-remove btn-danger after-last-input" type="button">
														<span class="glyphicon glyphicon-minus half-height"></span>
													</button>
													<button class="btn btn-success btn-add half-button" type="button">
														<span class="glyphicon glyphicon-plus minus-place"></span>
													</button>
												<?php endif; ?>
												
												
											<?php elseif( $iteration < $admissible_fields-1 or $iteration == $admissible_fields or count($user_meta) == $admissible_fields): ?>
												<button class="btn btn-remove btn-danger" type="button">
													<span class="glyphicon glyphicon-minus"></span>
												</button>
											<?php endif; ?>
														
										</span>
									</div>
								<?php endforeach; ?>
							</div>
							<br>
							<input type="button" name="af-button-save" class="btn btn-success" value="Update my page"><br><br>
							<input type="hidden" name="edit-page">
						</form>
						<input type="hidden" id="allowable-number-of-fields" value="<?php echo get_option('af_option')['field_count']; ?>">
					</div>
				</div>
			</div>
		</div>
	</div>

<?php get_footer(); ?>