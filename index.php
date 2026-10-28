<?php
/**
 * Fallback template.
 *
 * @package foundvital-reset
 */

get_header();
?>
<section class="section">
	<div class="container container--narrow">
		<?php if ( have_posts() ) : ?>
			<?php
			while ( have_posts() ) :
				the_post();
				?>
				<article <?php post_class( 'prose' ); ?>>
					<h1><?php the_title(); ?></h1>
					<?php the_content(); ?>
				</article>
			<?php endwhile; ?>
		<?php else : ?>
			<h1><?php esc_html_e( 'Nothing found', 'fvr' ); ?></h1>
			<p><?php esc_html_e( 'Try the links in the menu above.', 'fvr' ); ?></p>
		<?php endif; ?>
	</div>
</section>
<?php
get_footer();
