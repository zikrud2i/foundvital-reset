<?php
/**
 * Template Name: Reset – Thank You / Confirmation
 * Variant is chosen from the page slug: checklist, masterclass, fit-call, enrollment.
 *
 * @package foundvital-reset
 */

get_header();
while ( have_posts() ) :
	the_post();
	$type  = get_post_field( 'post_name', get_the_ID() );
	$email = (string) fvr_opt( 'support_email' );

	$copy = array(
		'checklist'  => array( 'Thanks for signing up', 'Here\'s what happens next.', 'checklist_signup_success', 'checklist' ),
		'masterclass' => array( 'You\'re registered', 'Here\'s what happens next.', 'masterclass_registration_success', 'masterclass' ),
		'fit-call'   => array( 'Thank you for booking', 'Here\'s what to expect.', 'fit_call_booked', 'fitcall' ),
		'enrollment' => array( 'Thank you', 'Here\'s what to expect next.', 'enrollment_page_view', 'enroll' ),
	);
	$c = $copy[ $type ] ?? $copy['checklist'];
	?>
	<section class="page-hero">
		<div class="container container--narrow text-center" data-success="<?php echo esc_attr( $c[2] ); ?>" data-success-cookie="<?php echo esc_attr( $c[3] ); ?>">
			<span class="check-badge"><?php echo fvr_icon( 'check', 'icon icon--lg' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
			<h1><?php echo esc_html( fvr_field( 'page_headline', $c[0] ) ); ?></h1>
			<?php if ( fvr_field( 'page_intro', '' ) ) : ?>
				<p class="lead"><?php echo esc_html( fvr_field( 'page_intro', '' ) ); ?></p>
			<?php endif; ?>
		</div>
	</section>

	<section class="section section--tight">
		<div class="container container--narrow">
			<div class="panel">
				<h2 class="h3"><?php echo esc_html( $c[1] ); ?></h2>

				<?php if ( 'checklist' === $type ) : ?>
					<?php if ( fvr_opt( 'checklist_url' ) ) : ?>
						<p><?php esc_html_e( 'Your checklist is ready to download.', 'fvr' ); ?></p>
						<?php fvr_btn( __( 'Download the Checklist', 'fvr' ), (string) fvr_opt( 'checklist_url' ), 'primary', 'checklist_download_click' ); ?>
					<?php else : ?>
						<p><?php esc_html_e( 'Please check your inbox for an email with your checklist. If it doesn\'t arrive in a few minutes, check your spam or promotions folder.', 'fvr' ); ?></p>
						<?php fvr_pending( 'Confirm Kit delivers the checklist by email, or add the download URL in Reset Settings > Checklist & masterclass.' ); ?>
					<?php endif; ?>

				<?php elseif ( 'masterclass' === $type ) : ?>
					<p><?php esc_html_e( 'Please watch your inbox for a confirmation and reminders with the session details.', 'fvr' ); ?></p>
					<?php if ( fvr_opt( 'mc_datetime' ) ) : ?>
						<p class="meta-line"><?php echo fvr_icon( 'calendar' ); // phpcs:ignore WordPress.Security.EscapeOutput ?> <?php echo esc_html( (string) fvr_opt( 'mc_datetime' ) ); ?></p>
					<?php endif; ?>
					<?php if ( fvr_opt( 'mc_join_url' ) ) : ?>
						<?php fvr_btn( __( 'Attendance Link', 'fvr' ), (string) fvr_opt( 'mc_join_url' ), 'primary', 'masterclass_join_click' ); ?>
					<?php else : ?>
						<?php fvr_pending( 'Add the attendance link and date in Reset Settings > Checklist & masterclass; confirm the Kit reminder sequence is active.' ); ?>
					<?php endif; ?>

				<?php elseif ( 'fit-call' === $type ) : ?>
					<p><?php esc_html_e( 'If you\'ve just booked, Calendly will email you a confirmation with the time and details. You can use that email to reschedule or cancel if you need to.', 'fvr' ); ?></p>
					<p><?php esc_html_e( 'There\'s nothing you need to prepare, but it can help to jot down your questions and goals.', 'fvr' ); ?></p>

				<?php else : ?>
					<p><?php esc_html_e( 'If you completed checkout, Stripe will email a receipt to the address you used. Next steps for getting started will be shared with you directly.', 'fvr' ); ?></p>
					<?php fvr_pending( 'Define the Reset access/fulfilment process, then replace this wording. The theme does not provision program access from a Stripe payment.' ); ?>
				<?php endif; ?>

				<?php if ( $email ) : ?>
					<p class="muted"><?php esc_html_e( 'Questions?', 'fvr' ); ?> <a href="mailto:<?php echo esc_attr( antispambot( $email ) ); ?>"><?php echo esc_html( antispambot( $email ) ); ?></a></p>
				<?php endif; ?>
			</div>

			<?php if ( 'fit-call' !== $type && 'enrollment' !== $type ) : ?>
				<div class="next-grid">
					<?php if ( 'checklist' === $type ) : ?>
						<a class="next-card" href="<?php echo esc_url( fvr_page_url( 'quiz' ) ); ?>"><strong><?php esc_html_e( 'Take the quiz', 'fvr' ); ?></strong><span><?php esc_html_e( 'Find your best next step.', 'fvr' ); ?></span></a>
						<?php $mc = get_page_by_path( 'masterclass' ); ?>
						<?php if ( $mc && 'publish' === $mc->post_status ) : ?>
							<a class="next-card" href="<?php echo esc_url( fvr_page_url( 'masterclass' ) ); ?>"><strong><?php esc_html_e( 'Free masterclass', 'fvr' ); ?></strong><span><?php esc_html_e( 'What to prioritize now.', 'fvr' ); ?></span></a>
						<?php endif; ?>
					<?php endif; ?>
					<a class="next-card" href="<?php echo esc_url( fvr_page_url( 'fit-call' ) ); ?>"><strong><?php esc_html_e( 'Book a fit conversation', 'fvr' ); ?></strong><span><?php esc_html_e( 'Ask questions about Reset.', 'fvr' ); ?></span></a>
				</div>
			<?php endif; ?>

			<p class="text-center"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( '← Back to FoundVital Reset', 'fvr' ); ?></a></p>
		</div>
	</section>
	<?php
endwhile;
get_footer();
