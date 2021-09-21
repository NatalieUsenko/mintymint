<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package WordPress
 * @subpackage Twenty_Twenty_One
 * @since Twenty Twenty-One 1.0
 */

?>
			</main><!-- #main -->
		</div><!-- #primary -->
	</div><!-- #content -->

    <div class="site-dark-wrapper">
        <footer id="colophon" class="site-footer" role="contentinfo">
            <div class="d-flex">
                <div>
                    <div class="footer-logo">Logo</div>
                    <div class="small-text green-text">
                        <p>All rights reserved.</p>
                        <p>&commat; <?php echo date('Y'); ?></p>
                    </div>
                </div>
                <?php get_template_part( 'template-parts/footer/footer-widgets' ); ?>
                <div class="width-360">
                    <p class="footer_menu_header">Subscribe</p>
                    <form  method="get" id="subscribeform" action="<?php echo home_url( '/' ) ?>" >
                        <input type="text" value="" placeholder="placeholder" name="subscribeinput" id="subscribeinput" />
                        <input type="submit" id="subscribesubmit" value="" />
                    </form>
                    <div class="small-text green-text">Depending on the company, a user experience designer may need to be a jack of all trades</div>
                </div>
            </div>

            <?php if ( has_nav_menu( 'footer' ) ) : ?>
                <nav aria-label="<?php esc_attr_e( 'Secondary menu', 'twentytwentyone' ); ?>" class="footer-navigation">
                    <ul class="footer-navigation-wrapper">
                        <?php
                        wp_nav_menu(
                            array(
                                'theme_location' => 'footer',
                                'items_wrap'     => '%3$s',
                                'container'      => false,
                                'depth'          => 1,
                                'link_before'    => '<span>',
                                'link_after'     => '</span>',
                                'fallback_cb'    => false,
                            )
                        );
                        ?>
                    </ul><!-- .footer-navigation-wrapper -->
                </nav><!-- .footer-navigation -->
            <?php endif; ?>
        </footer><!-- #colophon -->
    </div>
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
