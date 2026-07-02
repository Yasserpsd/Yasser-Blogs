<?php
/**
 * Plugin Name:       Yasser Blogs
 * Plugin URI:        https://momentummix.com/
 * Description:       عرض المقالات بشكل شيك في شبكة 3 أعمدة مع أزرار مشاركة على واتساب، لينكدإن، X، ونسخ الرابط، بالإضافة لشريط مشاركة عائم ثابت داخل المقالة + تاريخ مخصص لكل مقال.
 * Version:           1.3.0
 * Author:            Yasser Momentum
 * Author URI:        https://momentummix.com/
 * License:           GPL v3 or later
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       yasser-blogs
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'YASSER_BLOGS_VERSION', '1.3.0' );
define( 'YASSER_BLOGS_PATH', plugin_dir_path( __FILE__ ) );
define( 'YASSER_BLOGS_URL', plugin_dir_url( __FILE__ ) );

// تحميل موديول التاريخ المخصص
require_once YASSER_BLOGS_PATH . 'yasser-blogs-custom-date.php';

/**
 * تحميل ملفات CSS و JS
 */
function yasser_blogs_enqueue_assets() {
    wp_enqueue_style(
        'yasser-blogs-style',
        YASSER_BLOGS_URL . 'assets/css/yasser-blogs.css',
        array(),
        YASSER_BLOGS_VERSION
    );

    wp_enqueue_script(
        'yasser-blogs-script',
        YASSER_BLOGS_URL . 'assets/js/yasser-blogs.js',
        array(),
        YASSER_BLOGS_VERSION,
        true
    );

    wp_localize_script( 'yasser-blogs-script', 'yasserBlogsData', array(
        'copied_text' => __( 'تم نسخ الرابط!', 'yasser-blogs' ),
    ) );
}
add_action( 'wp_enqueue_scripts', 'yasser_blogs_enqueue_assets' );

/**
 * دالة مساعدة لإنشاء أزرار المشاركة
 */
function yasser_blogs_get_share_buttons( $post_url, $post_title, $prefix = '' ) {
    $share_text = rawurlencode( $post_title );
    $share_url  = rawurlencode( $post_url );
    $copy_class = $prefix ? 'yasser-single-copy' : 'yasser-copy';

    $whatsapp_svg = '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>';
    $linkedin_svg = '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.063 2.063 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>';
    $x_svg        = '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>';
    $copy_svg     = '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M16 1H4c-1.1 0-2 .9-2 2v14h2V3h12V1zm3 4H8c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h11c1.1 0 2-.9 2-2V7c0-1.1-.9-2-2-2zm0 16H8V7h11v14z"/></svg>';

    ob_start();
    ?>
    <a href="https://wa.me/?text=<?php echo $share_text; ?>%20<?php echo $share_url; ?>"
       class="<?php echo esc_attr( $prefix ); ?>yasser-share-btn yasser-whatsapp"
       target="_blank" rel="noopener" aria-label="WhatsApp"><?php echo $whatsapp_svg; ?></a>

    <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo $share_url; ?>"
       class="<?php echo esc_attr( $prefix ); ?>yasser-share-btn yasser-linkedin"
       target="_blank" rel="noopener" aria-label="LinkedIn"><?php echo $linkedin_svg; ?></a>

    <a href="https://twitter.com/intent/tweet?text=<?php echo $share_text; ?>&url=<?php echo $share_url; ?>"
       class="<?php echo esc_attr( $prefix ); ?>yasser-share-btn yasser-x"
       target="_blank" rel="noopener" aria-label="X (Twitter)"><?php echo $x_svg; ?></a>

    <button type="button"
            class="<?php echo esc_attr( $prefix ); ?>yasser-share-btn <?php echo esc_attr( $copy_class ); ?>"
            data-url="<?php echo esc_url( $post_url ); ?>"
            aria-label="Copy Link"><?php echo $copy_svg; ?></button>
    <?php
    return ob_get_clean();
}

/**
 * الشورت كود [yasser_blogs]
 */
function yasser_blogs_shortcode( $atts ) {
    $atts = shortcode_atts( array(
        'posts'    => -1, // الافتراضي: عرض كل المقالات
        'columns'  => 3,
        'category' => '',
        'orderby'  => 'date',
        'order'    => 'DESC',
    ), $atts, 'yasser_blogs' );

    // دعم -1 أو "all" لعرض كل المقالات
    $posts_value = $atts['posts'];
    if ( is_string( $posts_value ) && strtolower( $posts_value ) === 'all' ) {
        $posts_per_page = -1;
    } else {
        $posts_per_page = intval( $posts_value );
        if ( $posts_per_page === 0 ) {
            $posts_per_page = -1;
        }
    }

    $args = array(
        'post_type'      => 'post',
        'posts_per_page' => $posts_per_page,
        'orderby'        => sanitize_text_field( $atts['orderby'] ),
        'order'          => sanitize_text_field( $atts['order'] ),
        'post_status'    => 'publish',
    );

    if ( ! empty( $atts['category'] ) ) {
        $args['category_name'] = sanitize_text_field( $atts['category'] );
    }

    $query = new WP_Query( $args );

    ob_start();

    if ( $query->have_posts() ) : ?>
        <div class="yasser-blogs-grid" data-columns="<?php echo esc_attr( $atts['columns'] ); ?>">
            <?php while ( $query->have_posts() ) : $query->the_post();
                $post_url   = get_permalink();
                $post_title = get_the_title();
            ?>
                <article class="yasser-blog-card">
                    <div class="yasser-blog-thumb">
                        <a href="<?php echo esc_url( $post_url ); ?>" class="yasser-blog-thumb-link" aria-label="<?php echo esc_attr( $post_title ); ?>">
                            <?php if ( has_post_thumbnail() ) : ?>
                                <?php the_post_thumbnail( 'medium_large' ); ?>
                            <?php else : ?>
                                <div class="yasser-blog-no-image">
                                    <span><?php echo esc_html( get_bloginfo( 'name' ) ); ?></span>
                                </div>
                            <?php endif; ?>
                        </a>

                        <div class="yasser-share-overlay">
                            <div class="yasser-share-buttons">
                                <?php echo yasser_blogs_get_share_buttons( $post_url, $post_title ); ?>
                            </div>
                        </div>
                    </div>

                    <div class="yasser-blog-content">
                        <?php
                        $categories = get_the_category();
                        if ( ! empty( $categories ) ) : ?>
                            <span class="yasser-blog-category"><?php echo esc_html( $categories[0]->name ); ?></span>
                        <?php endif; ?>

                        <h3 class="yasser-blog-title">
                            <a href="<?php echo esc_url( $post_url ); ?>"><?php the_title(); ?></a>
                        </h3>

                        <p class="yasser-blog-excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 18, '...' ) ); ?></p>

                        <div class="yasser-blog-meta">
                            <span class="yasser-blog-date">
                                <svg viewBox="0 0 24 24" fill="currentColor" width="14" height="14"><path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z"/></svg>
                                <?php echo esc_html( yasser_blogs_get_display_date() ); ?>
                            </span>
                            <a href="<?php echo esc_url( $post_url ); ?>" class="yasser-blog-readmore">
                                <?php esc_html_e( 'اقرأ المزيد', 'yasser-blogs' ); ?>
                                <span>←</span>
                            </a>
                        </div>
                    </div>
                </article>
            <?php endwhile; ?>
        </div>
    <?php else : ?>
        <p class="yasser-blogs-empty"><?php esc_html_e( 'لا توجد مقالات للعرض حالياً.', 'yasser-blogs' ); ?></p>
    <?php endif;

    wp_reset_postdata();
    return ob_get_clean();
}
add_shortcode( 'yasser_blogs', 'yasser_blogs_shortcode' );

/**
 * عرض التاريخ المخصص فوق محتوى المقالة المنفردة (Single Post)
 */
function yasser_blogs_single_custom_date( $content ) {
    if ( is_singular( 'post' ) && in_the_loop() && is_main_query() ) {
        $custom_date_raw = get_post_meta( get_the_ID(), '_yasser_custom_date', true );

        // نعرض الشارة فقط لو المستخدم حدد تاريخ مخصص
        if ( ! empty( $custom_date_raw ) ) {
            $display_date = yasser_blogs_get_display_date();

            ob_start();
            ?>
            <div class="yasser-single-custom-date">
                <svg viewBox="0 0 24 24" fill="currentColor" width="16" height="16"><path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z"/></svg>
                <span><?php echo esc_html( $display_date ); ?></span>
            </div>
            <?php
            $date_badge = ob_get_clean();
            $content = $date_badge . $content;
        }
    }
    return $content;
}
add_filter( 'the_content', 'yasser_blogs_single_custom_date', 5 ); // أولوية 5 عشان تظهر قبل شريط المشاركة

/**
 * إضافة شريط المشاركة العائم الثابت + الشريط العادي داخل صفحة المقالة (Single Post)
 */
function yasser_blogs_single_share_bar( $content ) {
    if ( is_singular( 'post' ) && in_the_loop() && is_main_query() ) {
        $post_url   = get_permalink();
        $post_title = get_the_title();

        // 1) شريط المشاركة العائم الثابت (Sticky/Floating)
        ob_start();
        ?>
        <div class="yasser-floating-share" aria-label="<?php esc_attr_e( 'مشاركة المقالة', 'yasser-blogs' ); ?>">
            <span class="yasser-floating-share-title"><?php esc_html_e( 'شارك', 'yasser-blogs' ); ?></span>
            <?php echo yasser_blogs_get_share_buttons( $post_url, $post_title, 'single-' ); ?>
        </div>
        <?php
        $floating_bar = ob_get_clean();

        // 2) شريط المشاركة العادي في آخر المقالة
        ob_start();
        ?>
        <div class="yasser-single-share-bar">
            <div class="yasser-single-share-inner">
                <span class="yasser-single-share-label">
                    <svg viewBox="0 0 24 24" fill="currentColor" width="16" height="16"><path d="M18 16.08c-.76 0-1.44.3-1.96.77L8.91 12.7c.05-.23.09-.46.09-.7s-.04-.47-.09-.7l7.05-4.11c.54.5 1.25.81 2.04.81 1.66 0 3-1.34 3-3s-1.34-3-3-3-3 1.34-3 3c0 .24.04.47.09.7L8.04 9.81C7.5 9.31 6.79 9 6 9c-1.66 0-3 1.34-3 3s1.34 3 3 3c.79 0 1.5-.31 2.04-.81l7.12 4.16c-.05.21-.08.43-.08.65 0 1.61 1.31 2.92 2.92 2.92s2.92-1.31 2.92-2.92-1.31-2.92-2.92-2.92z"/></svg>
                    <?php esc_html_e( 'شارك المقالة:', 'yasser-blogs' ); ?>
                </span>
                <div class="yasser-single-share-buttons">
                    <?php echo yasser_blogs_get_share_buttons( $post_url, $post_title, 'single-' ); ?>
                </div>
            </div>
        </div>
        <?php
        $share_bar = ob_get_clean();

        // نضيف العائم في الأول ثم محتوى المقالة ثم الشريط العادي
        $content = $floating_bar . $content . $share_bar;
    }
    return $content;
}
add_filter( 'the_content', 'yasser_blogs_single_share_bar' );

/**
 * رابط "مشاهدة صاحب الإضافة" في صفحة البلجنز
 */
function yasser_blogs_plugin_row_meta( $links, $file ) {
    if ( plugin_basename( __FILE__ ) === $file ) {
        $row_meta = array(
            'author_site' => '<a href="https://momentummix.com/" target="_blank" rel="noopener" style="color:#0073aa;font-weight:600;">🌐 ' . esc_html__( 'مشاهدة صاحب الإضافة', 'yasser-blogs' ) . '</a>',
        );
        return array_merge( $links, $row_meta );
    }
    return $links;
}
add_filter( 'plugin_row_meta', 'yasser_blogs_plugin_row_meta', 10, 2 );
