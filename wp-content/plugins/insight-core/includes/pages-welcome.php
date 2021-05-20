<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

if ( isset( $_GET['ic_nonce'] ) && wp_verify_nonce( $_GET['ic_nonce'], 'deactivation' ) ) {
	delete_option( 'insight_core_purchase_code' );
	delete_option( 'insight_core_purchase_info' );
}

add_thickbox();

if ( isset( $_GET['tgmpa-deactivate'] ) && 'deactivate-plugin' == $_GET['tgmpa-deactivate'] ) {
	$plugins = TGM_Plugin_Activation::$instance->plugins;
	check_admin_referer( 'tgmpa-deactivate', 'tgmpa-deactivate-nonce' );
	foreach ( $plugins as $plugin ) {
		if ( $plugin['slug'] == $_GET['plugin'] ) {
			deactivate_plugins( $plugin['file_path'] );
		}
	}
}

if ( isset( $_GET['tgmpa-activate'] ) && 'activate-plugin' == $_GET['tgmpa-activate'] ) {
	check_admin_referer( 'tgmpa-activate', 'tgmpa-activate-nonce' );
	$plugins = TGM_Plugin_Activation::$instance->plugins;
	foreach ( $plugins as $plugin ) {
		if ( isset( $_GET['plugin'] ) && $plugin['slug'] == $_GET['plugin'] ) {
			activate_plugin( $plugin['file_path'] );
		}
	}
}

if ( isset( $_POST['purchase-code'] ) ) {
	$purchase_info = InsightCore::check_purchase_code( sanitize_key( $_POST['purchase-code'] ) );
	if ( is_array( $purchase_info ) && count( $purchase_info ) > 0 ) {
		update_option( 'insight_core_purchase_code', $_POST['purchase-code'] );
		update_option( 'insight_core_purchase_info', $purchase_info );
	}
}
$tgm_plugins          = array();
$tgm_plugins_action   = array();
$tgm_plugins_required = 0;
$plugins              = TGM_Plugin_Activation::$instance->plugins;
$tgm_plugins          = apply_filters( 'insight_core_tgm_plugins', $tgm_plugins );

foreach ( $plugins as $plugin ) {
	$tgm_plugins_action[ $plugin['slug'] ] = InsightCore::plugin_action( $plugin );
}
?>
<div class="wrap insight-core-wrap">
	<?php
	$insight_core_info = InsightCore::$info;
	include_once( INSIGHT_CORE_INC_DIR . '/pages-header.php' );
	if ( InsightCore::is_theme_support() ) {
		?>
		<div class="insight-core-body">
			<?php if ( ! InsightCore::is_envato_hosted() ) { ?>
				<div class="box green box-purchase">
					<div class="box-header">
						Purchase Code
					</div>
					<div class="box-body">
						<?php if ( $purchase_info = get_option( 'insight_core_purchase_info' ) ) {
							?>
							<table class="wp-list-table widefat striped changelogs">
								<tr>
									<th>Item name</th>
									<td><?php echo $purchase_info['item_name']; ?></td>
								</tr>
								<tr>
									<th>Create at</th>
									<td><?php echo $purchase_info['created_at']; ?></td>
								</tr>
								<tr>
									<th>Buyer</th>
									<td><?php echo $purchase_info['buyer']; ?></td>
								</tr>
								<tr>
									<th>Licence</th>
									<td><?php echo $purchase_info['licence']; ?></td>
								</tr>
								<tr>
									<th>Support until</th>
									<td><?php echo $purchase_info['supported_until']; ?></td>
								</tr>
								<tr>
									<th>
										<a href="<?php print wp_nonce_url( admin_url( 'admin.php?page=insight-core' ), 'deactivation', 'ic_nonce' ); ?>"
										   onclick="return confirm('Are you sure?');">Deactivation</a>
									</th>
									<td></td>
								</tr>
							</table>
							<?php
						} else { ?>
							<form action="" method="post">
								<span class="purchase-icon"><i class="pe-7s-unlock"></i></span>
								<input name="purchase-code" type="text" placeholder="Purchase code" required/>
								<input type="submit" class="button action" value="Submit"/>
							</form>
							<div class="purchase-desc">
								Show us your ThemeForest purchase code to get the automatic update.
							</div>
						<?php } ?>
					</div>
				</div>
			<?php } ?>
			<div class="box orange box-update">
				<div class="box-header">
					Update
				</div>
				<div class="box-body">
					<div class="update-info">
						<div class="update-icon">
							<i class="pe-7s-cloud-download"></i>
						</div>
						<div class="update-text">
							<span>Installed Version</span>
							<?php echo INSIGHT_CORE_THEME_VERSION; ?>
						</div>
					</div>
					<?php
					$check_theme_update = InsightCore::instance()->check_theme_update();
					if ( is_array( $check_theme_update ) && count( $check_theme_update ) > 0 ) {
						?>
						<div class="update-info">
							<div class="update-icon">
								<i class="pe-7s-bell"></i>
							</div>
							<div class="update-text">
								<span>Latest Available Version</span>
								<?php echo $check_theme_update["new_version"]; ?>
							</div>
						</div>
						<div class="update-text">
							The latest version of this theme<br/>
							is available, update today!
						</div>
						<div class="update-action">
							<?php
							if ( current_user_can( 'update_themes' ) && InsightCore::check_valid_update() ) {
								printf( '<a href="%1$s" %2$s>Update</a>',
									wp_nonce_url( self_admin_url( 'update.php?action=upgrade-theme&theme=' ) . INSIGHT_CORE_THEME_SLUG, 'upgrade-theme_' . INSIGHT_CORE_THEME_SLUG ),
									sprintf( 'class="btn" aria-label="%s"',
										esc_attr( sprintf( __( 'Update %s now' ), INSIGHT_CORE_THEME_NAME ) )
									) );
							} elseif ( ! InsightCore::check_valid_update() ) { ?>
								<strong>Please enter your purchase code<br/>
									to update the theme.</strong>
							<?php } ?>
						</div>
						<?php
					} else {
						?>
						<div class="update-info">
							<div class="update-icon">
								<i class="pe-7s-bell"></i>
							</div>
							<div class="update-text">
								<span>Latest Available Version</span>
								<?php echo INSIGHT_CORE_THEME_VERSION; ?>
							</div>
						</div>
						<div class="update-text updated">
							Your theme is up to date!
						</div>
						<?php
					}
					?>
				</div>
			</div>
			<div class="box blue box-support">
				<div class="box-support-img">&nbsp;</div>
				<div class="box-header">
					Support
				</div>
				<div class="box-body">
					<table>
						<tr>
							<td>
								<i class="pe-7s-note2"></i>
							</td>
							<td>
								<a href="<?php echo esc_url( $insight_core_info['docs'] ); ?>"
								   target="_blank"><span>Online Documentation</span></a>
								Detailed instruction to get<br/>
								the right way with our theme
							</td>
						</tr>
						<tr>
							<td>
								<i class="pe-7s-comment"></i>
							</td>
							<td>
								<a href="<?php echo esc_url( $insight_core_info['faqs'] ); ?>"
								   target="_blank"><span>FAQs</span></a>
								Check it before you ask for support.
							</td>
						</tr>
						<tr>
							<td>
								<i class="pe-7s-users"></i>
							</td>
							<td>
								<a href="<?php echo esc_url( $insight_core_info['support'] ); ?>"
								   target="_blank"><span>Human support</span></a>
								Our WordPress gurus'd love to help you to shot issues one by one.
							</td>
						</tr>
					</table>
					<div class="support-action">
						<a href="<?php echo esc_url( $insight_core_info['support'] ); ?>" target="_blank"
						   class="btn">Support Centre</a> <a
							href="<?php echo esc_url( $insight_core_info['faqs'] ); ?>" target="_blank"
							class="btn">FAQs</a>
					</div>
				</div>
			</div>
			<div class="box box-step red2">
				<div class="box-header">
					<span class="num">1</span>
					Install Required Plugins
				</div>
				<?php if ( count( $tgm_plugins ) > 0 ) { ?>
					<div class="box-body">
						<table class="wp-list-table widefat striped plugins">
							<thead>
							<tr>
								<th>Plugin</th>
								<th>Version</th>
								<th>Type</th>
								<th>Action</th>
							</tr>
							</thead>
							<tbody>
							<?php
							foreach ( $tgm_plugins as $tgm_plugin ) {
								?>
								<tr>
									<td>
										<?php
										if ( isset( $tgm_plugin['required'] ) && ( $tgm_plugin['required'] == true ) ) {
											if ( ! TGM_Plugin_Activation::$instance->is_plugin_active( $tgm_plugin['slug'] ) ) {
												echo '<span>' . $tgm_plugin['name'] . '</span>';
												$tgm_plugins_required++;
											} else {
												echo '<span class="actived">' . $tgm_plugin['name'] . '</span>';
											}
										} else {
											//echo TGM_Plugin_Activation::$instance->get_info_link( $tgm_plugin['slug'] );
											echo $tgm_plugin['name'];
										}
										?>
									</td>
									<td><?php echo( isset( $tgm_plugin['version'] ) ? $tgm_plugin['version'] : '' ); ?></td>
									<td><?php echo( isset( $tgm_plugin['required'] ) && ( $tgm_plugin['required'] == true ) ? 'Required' : 'Recommended' ); ?></td>
									<td>
										<?php echo $tgm_plugins_action[ $tgm_plugin['slug'] ]; ?>
									</td>
								</tr>
								<?php
							}
							?>
							</tbody>
						</table>
					</div>
					<div class="box-footer">
						<?php echo( $tgm_plugins_required > 0 ? '<span style="color: #dc433f">Please install and active all required plugins (' . $tgm_plugins_required . ')</span>' : '<span style="color: #6fbcae">All required plugins are activated. Now you can import the demo data.</span>' ); ?>
					</div>
				<?php } else { ?>
					<div class="box-body">
						This theme doesn't require any plugins.
					</div>
				<?php } ?>
			</div>
			<div class="box box-step blue2">
				<div class="box-header">
					<span class="num">2</span>
					Import Demos
				</div>
				<div class="box-body">
					<?php esc_html_e( 'Our demo data import lets you have the whole data package in minutes, delivering all kinds of essential things quickly and simply. You may not have enough time for a coffee as the import is too fast!', 'insight-core' ) ?>
					<br/>
					<br/>
					<i>
						<?php esc_html_e( 'Notice: Before import, Make sure your website data is empty (posts, pages, menus...etc...)', 'insight-core' ); ?>
						</br>
						<?php esc_html_e( 'We suggest you use the plugin', 'insight-core' ); ?>
						<a href="<?php echo esc_url( INSIGHT_CORE_SITE_URI ); ?>/wp-admin/plugin-install.php?tab=plugin-information&plugin=wordpress-reset&TB_iframe=true&width=800&height=550"
						   class="thickbox" title="Install Wordpress Reset">"Wordpress Reset"</a>
						<?php esc_html_e( 'to reset your website before import.', 'insight-core' ); ?>
					</i>
				</div>
				<div class="box-footer">
					<?php
					if ( get_option( 'insight_core_import' ) != false ) {
						echo 'You\'ve imported demo data ' . sprintf( _n( '%s time', '%s times', get_option( 'insight_core_import' ), 'insight-core' ), get_option( 'insight_core_import' ) ) . '.';
					}
					if ( $tgm_plugins_required > 0 ) {
						echo '<a class="btn" href="javascript:alert(\'Please install and active all required plugins first!\');"><i class="fa fa-download" aria-hidden="true"></i>&nbsp; Start Import Demos</a>';
					} else {
						echo '<a class="btn" href="' . admin_url( "admin.php?page=insight-core-import" ) . '"><i class="fa fa-download" aria-hidden="true"></i>&nbsp; Start Import Demos</a>';
					}
					?>
				</div>
			</div>
			<div class="box box-changelogs">
				<div class="box-header">
					<span class="icon"><i class="pe-7s-note2"></i></span>
					<?php esc_html_e( 'Changelog', 'insight-core' ); ?>
				</div>
				<div class="box-body">
					<div id="changelogs-content">
						<?php
						$changelog_content = InsightCore::instance()->get_changelog();
						echo $changelog_content;
						?>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
	include_once( INSIGHT_CORE_INC_DIR . '/pages-footer.php' );
	?>
</div>
