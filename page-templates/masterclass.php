<?php
/**
 * Template Name: Reset – Masterclass Registration
 *
 * @package foundvital-reset
 */

get_header();
while ( have_posts() ) :
	the_post();
	$title    = (string) fvr_opt( 'mc_title', 'The Midlife Health Reset: What to Prioritize When the Old Routine Isn\'t Working Like It Used To' );
	$when     = (string) fvr_opt( 'mc_datetime' );
	$replay   = (string) fvr_opt( 'mc_replay_url' );
	$is_over  = 'replay' === fvr_opt( 'mc_mode', 'register' );
	$learn    = fvr_lines( fvr_field( 'page_points', "How to choose priorities when your old routine stops working the way it used to\nWhere nutrition, strength, recovery, and everyday habits fit together\nHow Reset's 12-week structure works" ) );
	$photo    = fvr_founder_photo( 'medium', array( 'class' => 'presenter__img', 'loading' => 'lazy' ) );
	?>
	<section class="page-hero page-hero--split">
		<div class="container split">
			<div class="split__copy">
				<?php fvr_breadcrumbs(); ?>
				<p class="eyebrow"><?php echo esc_html( $is_over ? 'Masterclass replay' : 'Free live masterclass' ); ?></p>
				<h1 class="h1--long"><?php echo esc_html( $title ); ?></h1>
				<?php if ( fvr_field( 'page_intro', '' ) ) : ?>
					<p class="lead"><?php echo esc_html( fvr_field( 'page_intro', '' ) ); ?></p>
				<?php endif; ?>

				<p class="meta-line"><?php echo fvr_icon( 'calendar' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<?php
					if ( $is_over ) {
						esc_html_e( 'The live session has finished.', 'fvr' );
					} elseif ( $when ) {
						echo esc_html( $when );
					} else {
						esc_html_e( 'Session date and time will be announced soon.', 'fvr' );
					}
					?>
				</p>
				<?php
				if ( ! $when && ! $is_over ) {
					fvr_pending( 'Enter the confirmed masterclass date/time in Reset Settings > Checklist & masterclass.' );
				}
				?>

				<h2 class="h3 mt"><?php echo esc_html( fvr_field( 'page_points_title', 'What you\'ll learn' ) ); ?></h2>
				<ul class="check-list">
					<?php foreach ( $learn as $l ) : ?>
						<li><?php echo fvr_icon( 'check' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo esc_html( $l ); ?></span></li>
					<?php endforeach; ?>
				</ul>

				<div class="presenter">
					<?php echo $photo; // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<div>
						<p class="presenter__label"><?php esc_html_e( 'Your presenter', 'fvr' ); ?></p>
						<p class="presenter__name"><?php echo esc_html( fvr_founder_line() ); ?></p>
					</div>
				</div>
			</div>

			<div class="split__form">
				<div class="form-card">
					<?php if ( $is_over ) : ?>
						<h2 class="form-card__title"><?php esc_html_e( 'Missed it? Watch the replay.', 'fvr' ); ?></h2>
						<?php if ( $replay ) : ?>
							<?php fvr_btn( __( 'Watch the Replay', 'fvr' ), $replay, 'primary', 'masterclass_replay_click', 'btn--block' ); ?>
						<?php else : ?>
							<p class="muted"><?php esc_html_e( 'The replay link will be shared soon.', 'fvr' ); ?></p>
							<?php fvr_pending( 'Add the replay URL in Reset Settings > Checklist & masterclass.' ); ?>
						<?php endif; ?>
						<hr class="rule">
						<p><?php esc_html_e( 'Ready to talk about what comes next?', 'fvr' ); ?></p>
						<?php fvr_fit_btn( 'secondary', '', 'cta_fit_call_masterclass' ); ?>
					<?php else : ?>
						<h2 class="form-card__title"><?php esc_html_e( 'Save my seat', 'fvr' ); ?></h2>
						<?php fvr_lead_form( 'masterclass', __( 'Register for the Masterclass', 'fvr' ), 'Yes, register me and email me the session details, reminders, and occasional education and updates from FoundVital. I can unsubscribe at any time.' ); ?>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</section>
	<?php
	get_template_part( 'template-parts/final-cta' );
endwhile;
get_footer();
