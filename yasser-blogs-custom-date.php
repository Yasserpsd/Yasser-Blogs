<?php
/**
 * Yasser Blogs - Custom Date Module
 * إضافة تاريخ مخصص لكل مقال يظهر بدل التاريخ الافتراضي
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * ====== 1) إضافة صندوق التاريخ المخصص في صفحة تحرير المقال ======
 */
function yasser_blogs_add_date_metabox() {
    add_meta_box(
        'yasser_custom_date_box',                       // ID
        __( 'تاريخ المقال المخصص (Yasser Blogs)', 'yasser-blogs' ), // العنوان
        'yasser_blogs_date_metabox_html',               // دالة العرض
        'post',                                          // نوع المحتوى
        'side',                                          // المكان (جانبي)
        'high'                                           // الأولوية
    );
}
add_action( 'add_meta_boxes', 'yasser_blogs_add_date_metabox' );

/**
 * محتوى الصندوق (حقل اختيار التاريخ)
 */
function yasser_blogs_date_metabox_html( $post ) {
    // حماية بواسطة nonce
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
 * ====== 2) حفظ قيمة التاريخ عند حفظ المقال ======
 */
function yasser_blogs_save_custom_date( $post_id ) {
    // تحقق من الـ nonce
    if ( ! isset( $_POST['yasser_blogs_date_nonce'] ) ||
         ! wp_verify_nonce( $_POST['yasser_blogs_date_nonce'], 'yasser_blogs_save_date' ) ) {
        return;
    }

    // تجاهل الحفظ التلقائي
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    // تحقق من الصلاحيات
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    if ( isset( $_POST['yasser_custom_date_field'] ) ) {
        $value = sanitize_text_field( $_POST['yasser_custom_date_field'] );

        if ( ! empty( $value ) ) {
            update_post_meta( $post_id, '_yasser_custom_date', $value );
        } else {
            // لو فاضي احذف القيمة عشان يرجع للتاريخ الافتراضي
            delete_post_meta( $post_id, '_yasser_custom_date' );
        }
    }
}
add_action( 'save_post', 'yasser_blogs_save_custom_date' );

/**
 * ====== 3) دالة مساعدة: ترجع التاريخ المخصص لو موجود وإلا الافتراضي ======
 *
 * @param int|null $post_id  رقم المقال (اختياري)
 * @return string التاريخ الجاهز للعرض
 */
function yasser_blogs_get_display_date( $post_id = null ) {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }

    $custom_date = get_post_meta( $post_id, '_yasser_custom_date', true );

    if ( ! empty( $custom_date ) ) {
        // نحول التاريخ لنفس صيغة عرض تاريخ ووردبريس
        $timestamp = strtotime( $custom_date );
        if ( $timestamp ) {
            return date_i18n( get_option( 'date_format' ), $timestamp );
        }
    }

    // الافتراضي
    return get_the_date( '', $post_id );
}
