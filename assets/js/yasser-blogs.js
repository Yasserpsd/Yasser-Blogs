(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {

        // ===== منع أزرار المشاركة من فتح المقالة =====
        document.querySelectorAll('.yasser-share-btn').forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.stopPropagation();
            });
        });

        // ===== زرار نسخ الرابط (في البطاقات + شريط المقالة) =====
        document.querySelectorAll('.yasser-copy, .yasser-single-copy').forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();

                var url = this.getAttribute('data-url');
                var button = this;

                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(url).then(function () {
                        showCopiedFeedback(button);
                    }).catch(function () {
                        fallbackCopy(url, button);
                    });
                } else {
                    fallbackCopy(url, button);
                }
            });
        });

        function fallbackCopy(text, button) {
            var textarea = document.createElement('textarea');
            textarea.value = text;
            textarea.style.position = 'fixed';
            textarea.style.opacity = '0';
            document.body.appendChild(textarea);
            textarea.select();
            try {
                document.execCommand('copy');
                showCopiedFeedback(button);
            } catch (err) {
                console.error('Copy failed', err);
            }
            document.body.removeChild(textarea);
        }

        function showCopiedFeedback(button) {
            button.classList.add('copied');
            showToast(
                (window.yasserBlogsData && window.yasserBlogsData.copied_text) || 'Link copied!'
            );
            setTimeout(function () {
                button.classList.remove('copied');
            }, 2000);
        }

        function showToast(message) {
            var existing = document.querySelector('.yasser-toast');
            if (existing) existing.remove();

            var toast = document.createElement('div');
            toast.className = 'yasser-toast';
            toast.textContent = message;
            document.body.appendChild(toast);

            setTimeout(function () { toast.classList.add('show'); }, 10);
            setTimeout(function () {
                toast.classList.remove('show');
                setTimeout(function () { toast.remove(); }, 400);
            }, 2500);
        }

        // ===== إظهار/إخفاء شريط المشاركة في المقالة عند السكرول =====
        var shareBar = document.querySelector('.yasser-single-share-bar');
        if (shareBar) {
            var lastScroll = 0;
            window.addEventListener('scroll', function () {
                var currentScroll = window.pageYOffset;
                if (currentScroll > 300) {
                    shareBar.classList.add('visible');
                } else {
                    shareBar.classList.remove('visible');
                }
                lastScroll = currentScroll;
            });
        }
    });
})();
