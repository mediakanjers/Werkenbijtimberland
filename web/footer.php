					<footer class="mk-footer">
						<?php get_template_part('template-parts/footer/layouts/footer-default'); ?>
					</footer> <!-- #main-footer -->
				</div> <!-- #mk-main-area -->
			</div> <!-- #page-container -->

			<!--  Notifications from core theme -->
			<?php get_template_part('template-parts/notifications/notifications'); ?>

			<!-- Mobile menu -->
			<?php get_template_part('template-parts/header/nav-mobile'); ?>

			<!-- Job alert popup -->
			<?php get_template_part('template-parts/header/jobalert-popup'); ?>
			<?php get_template_part('template-parts/header/jobalert-bedankt'); ?>

			<!-- Scroll to top -->
			<button type="button" class="mk-scroll-top" aria-label="Scroll naar boven">
				<?php get_template_part( 'template-parts/blocks/partials/arrow-icon', null, array( 'color' => 'white' ) ); ?>
			</button>

		<?php wp_footer(); ?>
	</body>
</html>