<?php
namespace Tenweb_Builder;
class PreviewUpgrade {

    public function __construct() {
      $this->enqueue_sidebar();
      $this->bottombar();
      $this->upgrade_popup();
    }

    public function enqueue_sidebar() {
        wp_enqueue_style( TWBB_PREFIX . '-preview-upgrade-style', TWBB_URL . '/includes/PreviewUpgrade/assets/style/preview_upgrade.css', array(), TWBB_VERSION );
        wp_enqueue_script( TWBB_PREFIX . '-preview-upgrade-script', TWBB_URL . '/includes/PreviewUpgrade/assets/script/preview_upgrade.js', [ 'jquery' ], TWBB_VERSION );

      $domain_id = get_site_option(TENWEB_PREFIX . '_domain_id');

      $upgrade_url = add_query_arg(array('showUpgradePopup' => 1, 'step' => 2),TENWEB_DASHBOARD . '/websites/'. $domain_id . '/ai-builder');
      wp_localize_script( TWBB_PREFIX . '-preview-upgrade-script', 'twbb_sidebar_vars', array(
        'nonce' => wp_create_nonce("twb_pu_nonce"),
        'ajax_url' => admin_url('admin-ajax.php'),
        'upgrade_url' => $upgrade_url,
      ));
    }

    /**
     * Print the bottom bar.
     *
     * @return void
     */
    public function bottombar() {
      ?>
      <div class="twbb-pu-bar twbb-pu-bottom-bar" style="display: none;">
        <div>
          <span><?php esc_html_e('Edit your website with 10Web editor based on Elementor.', 'twbb'); ?></span>
          <button class="twbb-pu-button twbb-button-blue"><?php esc_html_e('Edit', 'twbb'); ?></button>
        </div>
      </div>
      <?php
    }

    private function upgrade_popup() {
    $videos = array(
      array('title' => esc_html__('Drag & drop builder for effortless editing', 'twbb'), 'video_url' => TWBB_URL . '/includes/PreviewUpgrade/assets/images/drag_drop.mp4', 'duration' => '17'),
      array('title' => esc_html__('50+ widgets to enhance your website', 'twbb'), 'video_url' => TWBB_URL . '/includes/PreviewUpgrade/assets/images/widgets.mp4', 'duration' => '10'),
      array('title' => esc_html__('Responsive design for any screen size', 'twbb'), 'video_url' => TWBB_URL . '/includes/PreviewUpgrade/assets/images/mobile_desktop.mp4', 'duration' => '12'),
    );

    ?>
    <div class="twbb-pu-upgrade-layout twbb-pu-hidden" style="display: none"></div>
    <div class="twbb-pu-upgrade-container twbb-pu-hidden" style="display: none">
      <span class="twbb-pu-upgrade-close"></span>
      <div class="twbb-pu-upgrade-left">
        <p class="twbb-pu-upgrade-title"><?php esc_html_e('Try AI Builder Pro for free for 7 days', 'twbb'); ?></p>
        <p class="twbb-pu-upgrade-descr"><?php esc_html_e('Edit your website, generate more content and images, and host a superfast website on 10Web. Own all the content and images you generate.', 'twbb'); ?></p>
        <p class="twbb-pu-upgrade-subtitle"><?php esc_html_e('AI Builder', 'twbb'); ?></p>
        <ul class="twbb-pu-upgrade-videos">
          <?php
          foreach ( $videos as $key => $video ) { ?>
            <li class="twbb-pu-video-item<?php echo $key == 0 ? ' twbb-pu-video-active' : ''; ?>" data-index="<?php echo esc_html($key); ?>" data-video_url="<?php echo esc_url($video['video_url']) ?>" data-video_duration="<?php echo esc_html($video['duration']) ?>">
              <span></span>
              <div class="twbb-pu-countdown twbb-pu-hidden">
                <svg>
                  <circle r="6" cx="9" cy="9"></circle>
                </svg>
              </div>

              <?php echo esc_html($video['title']); ?>
            </li>
          <?php } ?>
            <li class="twbb-pu-video-item-text">
                <span></span>
                <?php esc_html_e('Regenerate website content with AI', 'twbb') ?>
            </li>
        </ul>
        <div class="twbb-pu-info-container">
          <p class="twbb-pu-upgrade-subtitle"><?php esc_html_e('Unlock all of 10Web', 'twbb'); ?></p>
          <p class="twbb-pu-upgrade-item"><span></span><?php esc_html_e('Get a reliable Google Cloud Partner hosting', 'twbb'); ?></p>
          <p class="twbb-pu-upgrade-item"><span></span><?php esc_html_e('Get 90+ PageSpeed with 10Web Booster', 'twbb'); ?></p>
          <p class="twbb-pu-upgrade-item"><span></span><?php esc_html_e('Enable real-time automated backups', 'twbb'); ?></p>


          <p class="twbb-pu-upgrade-subdescr twbb-pu-cancel-row"><b><?php esc_html_e('Cancel Anytime. ', 'twbb-pu'); ?></b><?php esc_html_e('We will send you a reminder email 24 hours before the end of the period.', 'twbb'); ?></p>
        </div>
        <a  class="twbb-pu-upgrade-button twbb-pu-upgrade-button-desktop"><?php esc_html_e('TRY 10WEB PRO FOR 7 DAYS', 'twbb'); ?></a>
      </div>
      <div class="twbb-pu-upgrade-right">
        <video width="740" height="600" muted>
          <source src="<?php echo esc_url($videos[0]['video_url']); ?>" type="video/mp4">
        </video>
        <div class="twbb-pu-info-container twbb-pu-info-container-mobile">
          <p class="twbb-pu-upgrade-subtitle"><?php esc_html_e('Unlock all of 10Web', 'twbb'); ?></p>
          <p class="twbb-pu-upgrade-item"><span></span><?php esc_html_e('Get a reliable Google Cloud Partner hosting', 'twbb'); ?></p>
          <p class="twbb-pu-upgrade-item"><span></span><?php esc_html_e('Get 90+ PageSpeed with 10Web Booster', 'twbb'); ?></p>
          <p class="twbb-pu-upgrade-item"><span></span><?php esc_html_e('Enable real-time automated backups', 'twbb'); ?></p>

          <p class="twbb-pu-upgrade-subdescr twbb-pu-cancel-row"><b><?php esc_html_e('Cancel Anytime. ', 'twbb-pu'); ?></b><?php esc_html_e('We will send you a reminder email 24 hours before the end of the period.', 'twbb'); ?></p>
        </div>
        <a class="twbb-pu-upgrade-button twbb-pu-upgrade-button-mobile"><?php esc_html_e('TRY 10WEB PRO FOR 7 DAYS', 'twbb'); ?></a>
      </div>
    </div>
    <?php
  }

}