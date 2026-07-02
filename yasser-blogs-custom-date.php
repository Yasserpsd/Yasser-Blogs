<?php
/**
 * Yasser Blogs - Custom Date Module
 * إضافة تاريخ مخصص لكل مقال يظهر بدل التاريخ الافتراضي
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * ====== 1) صندوق التاريخ المخصص في صفحة تحرير المقال ======
 */
function yasser_blogs_add_date_metabox() {
    add_meta_box(
        'yasser_custom_date_box',
        __( 'تاريخ المقال المخصص (Yasser Blogs)', 'yasser-blogs' ),
        'yasser_blogs_date_metabox_html',
        'post',
        'side',
        'high'
    );
}
add_action( 'add_meta_boxes', 'yasser_blogs_add_date_metabox' );

/**
 * محتوى الصندوق
 */
function yasser_blogs_date_metabox_html( $post ) {
    wp_nonce_field( 'yasser_blogs_save_date', 'yasser_blogs_date_nonce' );
    $custom_date = get_post_meta( $post->ID, '_yasser_custom_date', true );
    ?>
    <p>
        <label for="yasser_custom_date_field" style="display:block;margin-bottom:6px;font-weight:600;">
            <?php esc_html_e( 'اختر التاريخ الذي تريد عرضه:', 'yasser-blogs' ); ?>
        </label>
        <input
            type="date"
            id="yasser_custom_date_field"
            name="yasser_custom_date_field"
            value="<?php echo esc_attr( $custom_date ); ?>"
            style="width:100%;"
        />
    </p>
    <p style="color:#666;font-size:12px;margin-top:6px;">
        <?php esc_html_e( 'لو تركت الحقل فارغاً سيتم عرض تاريخ النشر الافتراضي تلقائياً.', 'yasser-blogs' ); ?>
    </p>
    <?php
}

/**
 * ====== 2) حفظ التاريخ ======
 */
function yasser_blogs_save_custom_date( $post_id ) {
    if ( ! isset( $_POST['yasser_blogs_date_nonce'] ) ||
         ! wp_verify_nonce( $_POST['yasser_blogs_date_nonce'], 'yasser_blogs_save_date' ) ) {
        return;
    }
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    if ( isset( $_POST['yasser_custom_date_field'] ) ) {
        $value = sanitize_text_field( $_POST['yasser_custom_date_field'] );
        if ( ! empty( $value ) ) {
            update_post_meta( $post_id, '_yasser_custom_date', $value );
        } else {
            delete_post_meta( $post_id, '_yasser_custom_date' );
        }
    }
}
add_action( 'save_post', 'yasser_blogs_save_custom_date' );

/**
 * ====== 3) دالة إرجاع التاريخ (المخصص أو الافتراضي) ======
 */
function yasser_blogs_get_display_date( $post_id = null ) {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }

    $custom_date = get_post_meta( $post_id, '_yasser_custom_date', true );

    if ( ! empty( $custom_date ) ) {
        $timestamp = strtotime( $custom_date );
        if ( $timestamp ) {
            return date_i18n( get_option( 'date_format' ), $timestamp );
        }
    }

    return get_the_date( '', $post_id );
}
