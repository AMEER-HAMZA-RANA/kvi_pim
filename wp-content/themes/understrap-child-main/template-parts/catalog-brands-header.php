<?php
	$sm = StateManager::GI();
	$options = $sm->options;
	$active_theme_prefix =  $sm->current_brand->brand_code;
	$user_roles = $sm->current_user->user_role;
	$brands = $sm->get_all_brands();
?>
<nav class="navbar navbar-expand-lg p-0 menu-1">
	<div class="container">
		<a class="navbar-brand" href="#">
			<img src="<?php echo $options[$active_theme_prefix.'_logo_index_header']['url'];?>" alt="">
		</a>
		<button class="navbar-toggler" type="button" data-bs-toggle="collapse"
			data-bs-target="#navbarNavDropdown1" aria-controls="navbarNavDropdown" aria-expanded="false"
			aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"><i class="fa fa-bars"></i></span>
		</button>
		<div class="collapse navbar-collapse justify-content-end" id="navbarNavDropdown1">
			<ul class="navbar-nav">
				<?php
					foreach($brands as $brand){
						$class = "";
						if($brand->brand_code ==$active_theme_prefix){
							$class = "active";
						}
						?>
						<li class="nav-item">
							<a class="nav-link <?php echo $class?>" href="<?php echo home_url( '/'. $brand->slug.'/catalog' );?>"><?php echo $brand->brand_name?></a>
						</li>
					<?php
					}
				?>

				<li class="nav-item">
					<a class="nav-link" href="#">Saved</a>
				</li>
				
			</ul>
		</div>
	</div>
</nav>
