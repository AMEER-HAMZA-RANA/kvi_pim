<?php
global $wp;
$current_url = home_url( add_query_arg( array(), $wp->request ) );
$current_url = rtrim($current_url,"/");
?>
<div class="modal fade" id="wikiModal" tabindex="-1" role="dialog" aria-labelledby="wikiModalTitle" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="popup-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h3 class="modal-title w-100 text-center">ARROW PIM USER GUIDE WIKI</h3>
				<div class="_updated">Last updated: <span class="wiki_date">November 30, 2020</span></div>

			</div>
			<div class="modal-body">
				<div class="container">
					<div class="row">
						<div class="col-md-3 popup-left">
							<div class="search-field-wrapper">
								<div class="search-field">
									<input type="text" id="search-field" class="form-control" placeholder="Search">
								</div>
								<div class="search-result"></div>
							</div>
							<div class="accordion" id="modal_accordion">
								<?php $args = array(
									'post_type' 	=> 'wiki' ,
									// 'tax_query' => array(
									// 	array(
									// 		'taxonomy' => 'category',
									// 		'field' => 'slug',
									// 		'terms' => 'wiki'
									// 	)
									// ),
									// 'cat'       	=> '191',
									'post_parent' => 0,
									'orderby' => 'ID',
									'order' => 'ASC',
									'posts_per_page' => -1
								);
								$counter = 0;
								$q = new WP_Query($args);
								if ( $q->have_posts() ) {
								while ( $q->have_posts() ) {
									$q->the_post(); $counter++; ?>
									<div class="card">
										<div class="card-header" id="heading<?php echo $counter; ?>">
											<?php
											$current_page_class = "";
											$referenced_url = get_post_meta(get_the_ID(),"referenced_url", true);
											$referenced_url = rtrim($referenced_url,"/");
											if($current_url == $referenced_url){
												$current_page_class = "current_wiki";
											}
											?>

											<!-- <button class="btn btn-link btn-block text-left collapsed btn-arrow align-middle" type="button" ></button> -->
											<a data-toggle="collapse" data-target="#collapse<?php echo $counter; ?>" aria-expanded="true" aria-controls="collapse<?php echo $counter; ?>" data-id="<?php echo get_the_ID(); ?>" class="get-post main_anchor align-middle btn-arrow collapsed <?php echo $current_page_class;?>" href="<?php echo $child->ID; ?>" data-date="<?php echo get_the_date( 'l F j, Y' );?>" data-page="<?php echo $referenced_url;?>"><?php echo the_title(); ?></a>

										</div>
										<div id="collapse<?php echo $counter; ?>" class="collapse" aria-labelledby="headingOne" data-parent="#modal_accordion">
											<div class="card-body">
												<?php $c_args = array(
														'posts_per_page' => -1,
														'order'          => 'ASC',
														'post_parent'    => get_the_ID()
													);
												$child_posts = get_children( $c_args ); ?>
												<ul class="parent">
													<?php
													foreach($child_posts as $child){

														$current_page_class = "";
														$referenced_url = get_post_meta($child->ID ,"referenced_url", true);
														$referenced_url = rtrim($referenced_url,"/");
														if($current_url == $referenced_url){
															$current_page_class = "current_wiki";
														}
													?>

															<?php $tc_args = array(
															'posts_per_page' => -1,
															'order'          => 'ASC',
															'post_parent'    => $child->ID
														);
															$thrd_child_posts = get_children( $tc_args ); ?>
															
															<?php if(!empty($thrd_child_posts)): ?>
																<li class="has-child">
																	<div class="accordion" id="modal_accordion1">
																		<div class="card">
																			<div class="card-header" id="heading<?php echo $child->ID; ?>">
																				<!-- <button class="btn btn-link btn-block text-left collapsed btn-arrow align-middle" type="button"></button> -->
																				<a  data-toggle="collapse" data-target="#collapse<?php echo $child->ID; ?>" aria-expanded="true" aria-controls="collapse<?php echo $child->ID; ?>" data-id="<?php echo $child->ID; ?>" data-date="<?php echo get_the_date( 'l F j, Y', $child->ID );?>" class="get-post collapsed btn-arrow  <?php echo $current_page_class;?>" href="<?php echo $child->ID; ?>" data-page="<?php echo $referenced_url;?>"><?php echo $child->post_title; ?></a>
																			</div>
																		</div>
																		<div id="collapse<?php echo $child->ID; ?>" class="collapse" aria-labelledby="headingtwo" data-parent="#modal_accordion1">
																			<div class="card-body">
																				<ul >
																					<?php
																					foreach($thrd_child_posts as $tchild){

																						$tcurrent_page_class = "";
																						$treferenced_url = get_post_meta($tchild->ID ,"referenced_url", true);
																						$treferenced_url = rtrim($treferenced_url,"/");
																						if($current_url == $treferenced_url){
																							$tcurrent_page_class = "current_wiki";
																						}
																					?>
																						<li><a data-id="<?php echo $tchild->ID; ?>" data-date="<?php echo get_the_date( 'l F j, Y', $tchild->ID );?>" class="get-post <?php echo $tcurrent_page_class;?>" href="<?php echo $tchild->ID; ?>" data-page="<?php echo $treferenced_url;?>"><?php echo $tchild->post_title; ?></a></li>
																					<?php
																					} ?>
																				</ul>
																			</div>
																		</div>
																	</div>
																</li>
																<?php else:?> 
																<li class="no-child">
																	<a data-id="<?php echo $child->ID; ?>" data-date="<?php echo get_the_date( 'l F j, Y', $child->ID );?>" class="get-post <?php echo $current_page_class;?>" href="<?php echo $child->ID; ?>" data-page="<?php echo $referenced_url;?>"><?php echo $child->post_title; ?></a>
																</li>
																<?php endif;?>
														
													<?php
													} ?>
												</ul>
											</div>
										</div>
									</div>
								<?php }
								} ?>
							</div>

						</div>
						<div class="col-md-9 popup-right">


						</div>
					</div>
				</div>


			</div>

		</div>
	</div>
</div>
