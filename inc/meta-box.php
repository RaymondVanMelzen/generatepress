<?php
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'admin_enqueue_scripts','generate_enqueue_meta_box_scripts' );
/**
 * Add our metabox scripts
 *
 * @since 1.4
 */
function generate_enqueue_meta_box_scripts( $hook ) {
	if ( in_array( $hook, array( 'post.php', 'post-new.php' ) ) ){
		$post_types = get_post_types( array( 'public' => true ) );
		$screen = get_current_screen();
		$post_type = $screen->id;

		if ( in_array( $post_type, ( array ) $post_types ) ){
			wp_enqueue_style( 'generate-layout-metabox', get_template_directory_uri() . '/css/admin/meta-box.css', array(), GENERATE_VERSION ); 
		}
	}
}

add_action( 'add_meta_boxes', 'generate_register_layout_meta_box' );
/**
 * Generate the layout metabox
 *
 * @since 1.4
 */
function generate_register_layout_meta_box() { 
	if ( ! current_user_can( apply_filters( 'generate_metabox_capability', 'edit_theme_options' ) ) ) {
		return;
	}

	$post_types = get_post_types( array( 'public' => true ) );
	foreach ($post_types as $type) {
		if ( 'attachment' !== $type ) {
			add_meta_box (  
				'generate_layout_meta_box', 
				__( 'Layout','generatepress' ),
				'generate_do_layout_meta_box',
				$type,
				'normal',
				'high'
			); 
		}
	}
}

/**
 * Build our meta box.
 *
 * @since 1.4
 *
 * @param object $post All post information.
 */
function generate_do_layout_meta_box( $post ) {  
	wp_nonce_field( basename( __FILE__ ), 'generate_layout_nonce' );
	$stored_meta = get_post_meta( $post->ID );
	$stored_meta['_generate-sidebar-layout-meta'][0] = ( isset( $stored_meta['_generate-sidebar-layout-meta'][0] ) ) ? $stored_meta['_generate-sidebar-layout-meta'][0] : '';
	$stored_meta['_generate-footer-widget-meta'][0] = ( isset( $stored_meta['_generate-footer-widget-meta'][0] ) ) ? $stored_meta['_generate-footer-widget-meta'][0] : '';
	$stored_meta['_generate-full-width-content'][0] = ( isset( $stored_meta['_generate-full-width-content'][0] ) ) ? $stored_meta['_generate-full-width-content'][0] : '';
	$stored_meta['_generate-disable-headline'][0] = ( isset( $stored_meta['_generate-disable-headline'][0] ) ) ? $stored_meta['_generate-disable-headline'][0] : '';
	?>
	<script>
		jQuery(document).ready(function($) {
			$( '.generate-meta-box-menu li' ).on( 'click', function( event ) {
				event.preventDefault();
				$( this ).addClass( 'current' );
				$( this ).siblings().removeClass( 'current' );
				var tab = $( this ).data( 'content' );
				$( '.generate-meta-box-content' ).children( 'div' ).not( '#generate-' + tab ).css( 'display', 'none' );
				$( '#generate-' + tab ).fadeIn( 100 );
			});
		});
	</script>
	<div id="generate-meta-box-container">
		<ul class="generate-meta-box-menu">
			<li class="current" data-content="sidebars"><a href="#"><?php _e( 'Sidebars', 'generatepress' ); ?></a></li>
			<li data-content="footer-widgets"><a href="#"><?php _e( 'Footer Widgets', 'generatepress' ); ?></a></li>
			<?php if ( ! defined( 'GENERATE_DE_VERSION' ) || defined( 'GENERATE_DE_LAYOUT_META_BOX' ) ) : ?>
				<li data-content="disable-elements"><a href="#"><?php _e( 'Disable Elements', 'generatepress' ); ?></a></li>
			<?php endif; ?>
			<li data-content="page-builder-container"><a href="#"><?php _e( 'Page Builder Container', 'generatepress' ); ?></a></li>
			<?php do_action( 'generate_layout_meta_box_menu_item' ); ?>
		</ul>
		<div class="generate-meta-box-content">
			<div id="generate-sidebars">
				<div class="generate_layouts">
					<label for="meta-generate-layout-global" style="display:block;margin-bottom:10px;">
						<input type="radio" name="_generate-sidebar-layout-meta" id="meta-generate-layout-global" value="" <?php checked( $stored_meta['_generate-sidebar-layout-meta'][0], '' ); ?>>
						<?php _e('Default','generatepress');?>
					</label>
					<label for="meta-generate-layout-one" style="display:block;margin-bottom:3px;" title="<?php _e('Right Sidebar','generatepress');?>">
						<input type="radio" name="_generate-sidebar-layout-meta" id="meta-generate-layout-one" value="right-sidebar" <?php checked( $stored_meta['_generate-sidebar-layout-meta'][0], 'right-sidebar' ); ?>>
						<?php _e('Content','generatepress');?> / <strong><?php _ex( 'Sidebar','Short name for meta box','generatepress' );?></strong>
					</label>
					<label for="meta-generate-layout-two" style="display:block;margin-bottom:3px;" title="<?php _e('Left Sidebar','generatepress');?>">
						<input type="radio" name="_generate-sidebar-layout-meta" id="meta-generate-layout-two" value="left-sidebar" <?php checked( $stored_meta['_generate-sidebar-layout-meta'][0], 'left-sidebar' ); ?>>
						<strong><?php _ex( 'Sidebar','Short name for meta box','generatepress' );?></strong> / <?php _e('Content','generatepress');?>
					</label>
					<label for="meta-generate-layout-three" style="display:block;margin-bottom:3px;" title="<?php _e('No Sidebars','generatepress');?>">
						<input type="radio" name="_generate-sidebar-layout-meta" id="meta-generate-layout-three" value="no-sidebar" <?php checked( $stored_meta['_generate-sidebar-layout-meta'][0], 'no-sidebar' ); ?>>
						<?php _e('Content (no sidebars)','generatepress');?>
					</label>
					<label for="meta-generate-layout-four" style="display:block;margin-bottom:3px;" title="<?php _e('Both Sidebars','generatepress');?>">
						<input type="radio" name="_generate-sidebar-layout-meta" id="meta-generate-layout-four" value="both-sidebars" <?php checked( $stored_meta['_generate-sidebar-layout-meta'][0], 'both-sidebars' ); ?>>
						<strong><?php _ex( 'Sidebar','Short name for meta box','generatepress' );?></strong> / <?php _e('Content','generatepress');?> / <strong><?php _ex( 'Sidebar','Short name for meta box','generatepress' );?></strong>
					</label>
					<label for="meta-generate-layout-five" style="display:block;margin-bottom:3px;" title="<?php _e('Both Sidebars on Left','generatepress');?>">
						<input type="radio" name="_generate-sidebar-layout-meta" id="meta-generate-layout-five" value="both-left" <?php checked( $stored_meta['_generate-sidebar-layout-meta'][0], 'both-left' ); ?>>
						<strong><?php _ex( 'Sidebar','Short name for meta box','generatepress' );?></strong> / <strong><?php _ex( 'Sidebar','Short name for meta box','generatepress' );?></strong> / <?php _e('Content','generatepress');?>
					</label>
					<label for="meta-generate-layout-six" style="display:block;margin-bottom:3px;" title="<?php _e('Both Sidebars on Right','generatepress');?>">
						<input type="radio" name="_generate-sidebar-layout-meta" id="meta-generate-layout-six" value="both-right" <?php checked( $stored_meta['_generate-sidebar-layout-meta'][0], 'both-right' ); ?>>
						<?php _e('Content','generatepress');?> / <strong><?php _ex( 'Sidebar','Short name for meta box','generatepress' );?></strong> / <strong><?php _ex( 'Sidebar','Short name for meta box','generatepress' );?></strong>
					</label>
				</div>
			</div>
			
			<div id="generate-footer-widgets" style="display: none;">
				<div class="generate_footer_widget">
					<label for="meta-generate-footer-widget-global" style="display:block;margin-bottom:10px;">
						<input type="radio" name="_generate-footer-widget-meta" id="meta-generate-footer-widget-global" value="" <?php checked( $stored_meta['_generate-footer-widget-meta'][0], '' ); ?>>
						<?php _e('Default','generatepress');?>
					</label>
					<label for="meta-generate-footer-widget-zero" style="display:block;margin-bottom:3px;" title="<?php _e('0 Widgets','generatepress');?>">
						<input type="radio" name="_generate-footer-widget-meta" id="meta-generate-footer-widget-zero" value="0" <?php checked( $stored_meta['_generate-footer-widget-meta'][0], '0' ); ?>>
						<?php _e('0 Widgets','generatepress');?>
					</label>
					<label for="meta-generate-footer-widget-one" style="display:block;margin-bottom:3px;" title="<?php _e('1 Widget','generatepress');?>">
						<input type="radio" name="_generate-footer-widget-meta" id="meta-generate-footer-widget-one" value="1" <?php checked( $stored_meta['_generate-footer-widget-meta'][0], '1' ); ?>>
						<?php _e('1 Widget','generatepress');?>
					</label>
					<label for="meta-generate-footer-widget-two" style="display:block;margin-bottom:3px;" title="<?php _e('2 Widgets','generatepress');?>">
						<input type="radio" name="_generate-footer-widget-meta" id="meta-generate-footer-widget-two" value="2" <?php checked( $stored_meta['_generate-footer-widget-meta'][0], '2' ); ?>>
						<?php _e('2 Widgets','generatepress');?>
					</label>
					<label for="meta-generate-footer-widget-three" style="display:block;margin-bottom:3px;" title="<?php _e('3 Widgets','generatepress');?>">
						<input type="radio" name="_generate-footer-widget-meta" id="meta-generate-footer-widget-three" value="3" <?php checked( $stored_meta['_generate-footer-widget-meta'][0], '3' ); ?>>
						<?php _e('3 Widgets','generatepress');?>
					</label>
					<label for="meta-generate-footer-widget-four" style="display:block;margin-bottom:3px;" title="<?php _e('4 Widgets','generatepress');?>">
						<input type="radio" name="_generate-footer-widget-meta" id="meta-generate-footer-widget-four" value="4" <?php checked( $stored_meta['_generate-footer-widget-meta'][0], '4' ); ?>>
						<?php _e('4 Widgets','generatepress');?>
					</label>
					<label for="meta-generate-footer-widget-five" style="display:block;margin-bottom:3px;" title="<?php _e('5 Widgets','generatepress');?>">
						<input type="radio" name="_generate-footer-widget-meta" id="meta-generate-footer-widget-five" value="5" <?php checked( $stored_meta['_generate-footer-widget-meta'][0], '5' ); ?>>
						<?php _e('5 Widgets','generatepress');?>
					</label>
				</div>
			</div>
			
			<div id="generate-page-builder-container" style="display: none;">
				<p class="page-builder-content" style="color:#666;font-size:13px;margin-top:0;">
					<?php _e( 'Choose your page builder content container type. Both options remove the content padding for you.', 'generatepress' ) ;?>
				</p>
				<p class="generate_full_width_template">
					<label for="default-content" style="display:block;margin-bottom:10px;">
						<input type="radio" name="_generate-full-width-content" id="default-content" value="" <?php checked( $stored_meta['_generate-full-width-content'][0], '' ); ?>>
						<?php _e( 'Default','generatepress' );?>
					</label>
					<label id="full-width-content" for="_generate-full-width-content" style="display:block;margin-bottom:10px;">
						<input type="radio" name="_generate-full-width-content" id="_generate-full-width-content" value="true" <?php checked( $stored_meta['_generate-full-width-content'][0], 'true' ); ?>>
						<?php _e( 'Full Width','generatepress' );?>
					</label>
					<label id="generate-remove-padding" for="_generate-remove-content-padding" style="display:block;margin-bottom:10px;">
						<input type="radio" name="_generate-full-width-content" id="_generate-remove-content-padding" value="contained" <?php checked( $stored_meta['_generate-full-width-content'][0], 'contained' ); ?>>
						<?php _e( 'Contained','generatepress' );?>
					</label>
				</p>
			</div>
			
			<div id="generate-disable-elements" style="display: none;">
				<?php if ( ! defined( 'GENERATE_DE_VERSION' ) ) : ?>
					<div class="generate_disable_elements">
						<label for="meta-generate-disable-headline" style="display:block;margin: 0 0 1em;" title="<?php _e( 'Content Title','generatepress' );?>">
							<input type="checkbox" name="_generate-disable-headline" id="meta-generate-disable-headline" value="true" <?php checked( $stored_meta['_generate-disable-headline'][0], 'true' ); ?>>
							<?php _e( 'Content Title','generatepress' );?>
						</label>
						<?php if ( ! defined( 'GP_PREMIUM_VERSION' ) ) : ?>
							<span style="display:block;padding-top:1em;border-top:1px solid #EFEFEF;">
								<a href="<?php echo generate_get_premium_url( 'https://generatepress.com/downloads/generate-disable-elements' );?>" target="_blank"><?php _e( 'Add-on available', 'generatepress' ); ?></a>
							</span>
						<?php endif; ?>
					</div>
				<?php endif; ?>
				
				<?php do_action( 'generate_layout_disable_elements_section', $stored_meta ); ?>
			</div>
			
			<?php do_action( 'generate_layout_meta_box_content', $stored_meta ); ?>
		</div>
	</div>
    <?php
}

add_action( 'save_post', 'generate_save_layout_meta_data' );
/**
 * Saves the sidebar layout meta data.
 *
 * @since 1.4
 *
 * @param int Post ID.
 */
function generate_save_layout_meta_data( $post_id ) {  
	$is_autosave = wp_is_post_autosave( $post_id );
	$is_revision = wp_is_post_revision( $post_id );
	$is_valid_nonce = ( isset( $_POST[ 'generate_layout_nonce' ] ) && wp_verify_nonce( sanitize_key( $_POST[ 'generate_layout_nonce' ] ), basename( __FILE__ ) ) ) ? true : false;
 
	if ( $is_autosave || $is_revision || ! $is_valid_nonce ) {
		return;
	}
	
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return $post_id;
	}
 
	$sidebar_layout_key   = '_generate-sidebar-layout-meta';
	$sidebar_layout_value = filter_input( INPUT_POST, $sidebar_layout_key, FILTER_SANITIZE_STRING );

	if ( $sidebar_layout_value ) {
		update_post_meta( $post_id, $sidebar_layout_key, $sidebar_layout_value );
	} else {
		delete_post_meta( $post_id, $sidebar_layout_key );
	}
	
	$footer_widget_key   = '_generate-footer-widget-meta';
	$footer_widget_value = filter_input( INPUT_POST, $footer_widget_key, FILTER_SANITIZE_STRING );

	if ( $footer_widget_value ) {
		update_post_meta( $post_id, $footer_widget_key, $footer_widget_value );
	} else {
		delete_post_meta( $post_id, $footer_widget_key );
	}
	
	$page_builder_container_key   = '_generate-full-width-content';
	$page_builder_container_value = filter_input( INPUT_POST, $page_builder_container_key, FILTER_SANITIZE_STRING );

	if ( $page_builder_container_value ) {
		update_post_meta( $post_id, $page_builder_container_key, $page_builder_container_value );
	} else {
		delete_post_meta( $post_id, $page_builder_container_key );
	}
	
	// We only need this if the Disable Elements module doesn't exist
	if ( ! defined( 'GENERATE_DE_VERSION' ) ) {
		$disable_content_title_key   = '_generate-disable-headline';
		$disable_content_title_value = filter_input( INPUT_POST, $disable_content_title_key, FILTER_SANITIZE_STRING );

		if ( $disable_content_title_value ) {
			update_post_meta( $post_id, $disable_content_title_key, $disable_content_title_value );
		} else {
			delete_post_meta( $post_id, $disable_content_title_key );
		}
	}
	
	do_action( 'generate_layout_meta_box_save', $post_id );
}