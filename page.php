<?php
/**
 * Default page (legal pages, editor-built pages).
 *
 * @package foundvital-reset
 */

get_header();
?>
<section class="page-hero page-hero--plain">
	<div class="container">
		<?php fvr_breadcrumbs(); ?>
		<h1><?php the_title(); ?></h1>
	</div>
</section>
<section class="section section--tight">
	<div class="container prose">
		<?php
		while ( have_posts() ) :
			the_post();
			the_content();
		endwhile;
		?>
	</div>
</section>
<?php
get_footer();
