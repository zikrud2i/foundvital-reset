<?php
/**
 * Pricing block. Visible only when "Show pricing block" is on AND an approved price is entered.
 *
 * @package foundvital-reset
 */

$show     = (bool) fvr_opt( 'price_show' ) && fvr_opt( 'price_text' );
$terms    = fvr_lines( fvr_opt( 'price_terms' ) );
$optimize = fvr_lines( fvr_opt( 'optimize_terms' ) );
?>
<div class="pricing reveal" id="investment">
	<?php if ( $show ) : ?>
		<div class="pricing__card">
			<p class="pricing__label">FoundVital Reset™ · <?php esc_html_e( '12-Week Coaching & Education Program', 'fvr' ); ?></p>
			<p class="pricing__price"><span><?php esc_html_e( 'Total investment', 'fvr' ); ?></span> <?php echo esc_html( (string) fvr_opt( 'price_text' ) ); ?></p>
			<?php if ( $terms ) : ?>
				<ul class="check-list check-list--small">
					<?php foreach ( $terms as $t ) : ?>
						<li><?php echo fvr_icon( 'check' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo esc_html( $t ); ?></span></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>
	<?php else : ?>
		<?php fvr_pending( 'Pricing is hidden until the approved price and terms are entered and "Show pricing block" is ticked (Reset Settings > Pricing).' ); ?>
	<?php endif; ?>

	<p class="pricing__note"><?php esc_html_e( 'Lab testing, medication, and other clinical services are separate and are not included in Reset.', 'fvr' ); ?></p>

	<?php if ( $optimize ) : ?>
		<div class="callout">
			<h4><?php esc_html_e( 'Optional Optimize continuation', 'fvr' ); ?></h4>
			<ul class="check-list check-list--small">
				<?php foreach ( $optimize as $t ) : ?>
					<li><?php echo fvr_icon( 'check' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo esc_html( $t ); ?></span></li>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php endif; ?>
</div>
