<?php
/**
 * Reset homepage – section order follows the client's Landing Page Structure & Messaging Guide.
 *
 * @package foundvital-reset
 */

get_header();

while ( have_posts() ) :
	the_post();
	get_template_part( 'template-parts/hero' );          // 1. Hero.
	get_template_part( 'template-parts/recognition' );   // 2. Recognition.
	get_template_part( 'template-parts/experience' );    // 3. 12-week experience.
	get_template_part( 'template-parts/phases' );        //    Four-phase method.
	get_template_part( 'template-parts/founder' );       // 4. Meet Tara.
	get_template_part( 'template-parts/fit' );           // 5. Fit & investment (+ FAQ).
	get_template_part( 'template-parts/final-cta' );     // 6. Final invitation.
endwhile;

get_footer();
