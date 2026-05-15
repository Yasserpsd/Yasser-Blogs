<?php
/**
 * Plugin Name:       Yasser Blogs
 * Plugin URI:        https://momentummix.com/
 * Description:       عرض المقالات بشكل شيك في شبكة 3 أعمدة مع أزرار مشاركة على واتساب، لينكدإن، X، ونسخ الرابط.
 * Version:           1.0.0
 * Author:            Yasser Momentum
 * Author URI:        https://momentummix.com/
 * License:           GPL v3 or later
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       yasser-blogs
 */

// منع الوصول المباشر
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// تعريف ثوابت البلجن
define( 'YASSER_BLOGS_VERSION', '1.0.0' );
define( 'YASSER_BLOGS_PATH', plugin_dir_path( __FILE__ ) );
define( 'YASSER_BLOGS_URL', plugin_dir_url( __FILE__ ) );

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

    // تمرير بيانات للجافاسكريبت
    wp_localize_script( 'yasser-blogs-script', 'yasserBlogsData', array(
        'copied_text' => __( 'تم نسخ الرابط!', 'yasser-blogs' ),
    ) );
}
add_action( 'wp_enqueue_scripts', 'yasser_blogs_enqueue_assets' );

/**
 * الشورت كود [yasser_blogs]
 * الاستخدام: [yasser_blogs posts="6" columns="3" category=""]
 */
function yasser_blogs_shortcode( $atts ) {
    $atts = shortcode_atts( array(
        'posts'    => 6,
        'columns'  => 3,
        'category' => '',
        'orderby'  => 'date',
        'order'    => 'DESC',
    ), $atts, 'yasser_blogs' );

    $args = array(
        'post_type'      => 'post',
        'posts_per_page' => intval( $atts['posts'] ),
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
                $share_text = rawurlencode( $post_title );
                $share_url  = rawurlencode( $post_url );
            ?>
                <article class="yasser-blog-card">
                    <a href="<?php echo esc_url( $post_url ); ?>" class="yasser-blog-thumb">
                        <?php if ( has_post_thumbnail() ) : ?>
                            <?php the_post_thumbnail( 'medium_large' ); ?>
                        <?php else : ?>
                            <div class="yasser-blog-no-image">
                                <span><?php echo esc_html( get_bloginfo( 'name' ) ); ?></span>
                            </div>
                        <?php endif; ?>

                        <div class="yasser-share-overlay">
                            <div class="yasser-share-buttons">
                                <a href="https://wa.me/?text=<?php echo $share_text; ?>%20<?php echo $share_url; ?>"
                                   class="yasser-share-btn yasser-whatsapp"
                                   target="_blank" rel="noopener"
                                   onclick="event.stopPropagation();"
                                   aria-label="WhatsApp">
                                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                </a>

                                <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo $share_url; ?>"
                                   class="yasser-share-btn yasser-linkedin"
                                   target="_blank" rel="noopener"
                                   onclick="event.stopPropagation();"
                                   aria-label="LinkedIn">
                                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.063 2.063 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                                </a>

                                <a href="https://twitter.com/intent/tweet?text=<?php echo $share_text; ?>&url=<?php echo $share_url; ?>"
                                   class="yasser-share-btn yasser-x"
                                   target="_blank" rel="noopener"
                                   onclick="event.stopPropagation();"
                                   aria-label="X (Twitter)">
                                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                                </a>

                                <button type="button"
                                        class="yasser-share-btn yasser-copy"
                                        data-url="<?php echo esc_url( $post_url ); ?>"
                                        onclick="event.stopPropagation();"
                                        aria-label="Copy Link">
                                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M16 1H4c-1.1 0-2 .9-2 2v14h2V3h12V1zm3 4H8c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h11c1.1 0 2-.9 2-2V7c0-1.1-.9-2-2-2zm0 16H8V7h11v14z"/></svg>
                                </button>
                            </div>
                        </div>
                    </a>

                    <div class="yasser-blog-content">
                        <?php
                        $categories = get_the_category();
                        if ( ! empty( $categories ) ) : ?>
                            <span class="yasser-blog-category"><?php echo esc_html( $categories[0]->name ); ?></span>
                        <?php endif; ?>

                        <h3 class="yasser-blog-title">
                            <a href="<?php echo esc_url( $post_url ); ?>"><?php the_title(); ?></a>
                        </h3>

                        <p class="yasser-blog-excerpt"><?php echo wp_trim_words( get_the_excerpt(), 18, '...' ); ?></p>

                        <div class="yasser-blog-meta">
                            <span class="yasser-blog-date">
                                <svg viewBox="0 0 24 24" fill="currentColor" width="14" height="14"><path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z"/></svg>
                                <?php echo esc_html( get_the_date() ); ?>
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
 * إضافة رابط "مشاهدة صاحب الإضافة" في صفحة البلجنز
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
