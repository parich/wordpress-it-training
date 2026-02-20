# WordPress Contact Form → Save to Database (Custom Post Type)

แทนการส่งอีเมล เปลี่ยนเป็นบันทึกลง WordPress database ผ่าน **Custom Post Type** ชื่อ `contact_submission`

---

## สารบัญ

1. [โครงสร้างไฟล์](#1-โครงสร้างไฟล์)
2. [Register Custom Post Type](#2-register-custom-post-type)
3. [REST API Endpoint (รับ Form แล้ว Save DB)](#3-rest-api-endpoint)
4. [Shortcode: Contact Form](#4-shortcode-contact-form)
5. [ดูข้อมูลใน Admin Dashboard](#5-ดูข้อมูลใน-admin-dashboard)
6. [อธิบาย Flow ทั้งหมด](#6-อธิบาย-flow-ทั้งหมด)

---

## 1. โครงสร้างไฟล์

ใส่ code ทั้งหมดใน plugin หรือ `functions.php` ของ theme

```
wp-content/
└── plugins/
    └── workppass-contact/
        └── workppass-contact.php   ← ไฟล์เดียว ใส่ทุกอย่างที่นี่
```

Header ของ plugin file:

```php
<?php
/**
 * Plugin Name: Workppass Contact Form
 * Description: Contact form ที่บันทึกข้อมูลลง Custom Post Type
 * Version: 1.0.0
 */
defined('ABSPATH') || exit;
```

---

## 2. Register Custom Post Type

บันทึกข้อมูล contact ลง post type `contact_submission`
แต่ละ submission = 1 post

```php
add_action('init', function () {
    register_post_type('contact_submission', [
        'label'               => 'Contact Submissions',
        'labels'              => [
            'name'          => 'Contact Submissions',
            'singular_name' => 'Submission',
            'menu_name'     => 'Contact Form',
            'all_items'     => 'All Submissions',
        ],
        'public'              => false,   // ไม่แสดงหน้าเว็บ
        'show_ui'             => true,    // แสดงใน Admin
        'show_in_menu'        => true,
        'show_in_rest'        => false,   // ไม่เปิด REST public
        'supports'            => ['title'],
        'capability_type'     => 'post',
        'map_meta_cap'        => true,
        'menu_icon'           => 'dashicons-email-alt',
        'menu_position'       => 25,
    ]);
});
```

**ทำไม `public => false`?**
เพราะ contact submissions เป็นข้อมูลส่วนตัว ไม่ควรเข้าถึงได้จากหน้าเว็บ

---

## 3. REST API Endpoint

รับ POST request จาก form แล้ว validate + บันทึกลง DB

```php
// ลงทะเบียน route: POST /wp-json/workppass/v1/contact
add_action('rest_api_init', function () {
    register_rest_route('workppass/v1', '/contact', [
        'methods'             => 'POST',
        'callback'            => 'workppass_handle_contact',
        'permission_callback' => '__return_true',
    ]);
});

function workppass_handle_contact(WP_REST_Request $req) {

    // ---------- 1. รับค่าและ Sanitize ----------
    $name    = sanitize_text_field($req->get_param('name'));
    $email   = sanitize_email($req->get_param('email'));
    $phone   = sanitize_text_field($req->get_param('phone'));
    $company = sanitize_text_field($req->get_param('company'));
    $topic   = sanitize_text_field($req->get_param('topic'));
    $message = sanitize_textarea_field($req->get_param('message'));
    $hp      = $req->get_param('website');         // honeypot
    $nonce   = $req->get_param('workppass_nonce');

    // ---------- 2. Nonce (ป้องกัน CSRF) ----------
    if (!wp_verify_nonce($nonce, 'workppass_contact_submit')) {
        return new WP_REST_Response([
            'success' => false,
            'message' => 'Invalid nonce',
        ], 403);
    }

    // ---------- 3. Honeypot (ป้องกันบอท) ----------
    if (!empty(trim($hp))) {
        return new WP_REST_Response([
            'success' => false,
            'message' => 'Spam detected',
        ], 400);
    }

    // ---------- 4. Validate ----------
    $allowed_topics = ['support', 'sales', 'partnership', 'other'];

    if (mb_strlen(trim($name)) < 2) {
        return new WP_REST_Response(['success' => false, 'message' => 'Name required'], 400);
    }
    if (!is_email($email)) {
        return new WP_REST_Response(['success' => false, 'message' => 'Valid email required'], 400);
    }
    if (!in_array($topic, $allowed_topics, true)) {
        return new WP_REST_Response(['success' => false, 'message' => 'Topic invalid'], 400);
    }
    if (mb_strlen(trim($message)) < 10) {
        return new WP_REST_Response(['success' => false, 'message' => 'Message too short'], 400);
    }

    // ---------- 5. บันทึกลง wp_posts ----------
    $post_id = wp_insert_post([
        'post_type'   => 'contact_submission',
        'post_status' => 'private',          // ซ่อนจาก public, เห็นเฉพาะ admin
        'post_title'  => sprintf('[%s] %s <%s>', strtoupper($topic), $name, $email),
        'post_author' => 0,                  // ไม่ผูกกับ user
    ], true); // true = return WP_Error ถ้าล้มเหลว

    if (is_wp_error($post_id)) {
        return new WP_REST_Response([
            'success' => false,
            'message' => 'Database error: ' . $post_id->get_error_message(),
        ], 500);
    }

    // ---------- 6. บันทึก Meta Data ลง wp_postmeta ----------
    $meta_fields = [
        '_contact_name'    => $name,
        '_contact_email'   => $email,
        '_contact_phone'   => $phone,
        '_contact_company' => $company,
        '_contact_topic'   => $topic,
        '_contact_message' => $message,
        '_contact_ip'      => $_SERVER['REMOTE_ADDR'] ?? '',
        '_contact_date'    => current_time('mysql'),
    ];

    foreach ($meta_fields as $key => $value) {
        update_post_meta($post_id, $key, $value);
    }

    return new WP_REST_Response(['success' => true, 'id' => $post_id], 201);
}
```

**ข้อมูลถูกเก็บที่ไหน?**

| ข้อมูล | ตาราง | Column |
|--------|-------|--------|
| หัวเรื่อง (ชื่อ + topic + email) | `wp_posts` | `post_title` |
| สถานะ | `wp_posts` | `post_status = 'private'` |
| วันที่ส่ง | `wp_posts` | `post_date` |
| ชื่อ, email, phone, message ฯลฯ | `wp_postmeta` | `meta_key` / `meta_value` |

---

## 4. Shortcode Contact Form

ใช้ shortcode `[workppass_contact_form]` ใส่ใน page ใดก็ได้

```php
add_shortcode('workppass_contact_form', function () {
    ob_start(); ?>

    <form id="workppassContactForm" class="wp-contact-card">
        <h3>ติดต่อ Workppass</h3>
        <p class="wp-contact-muted">ส่งคำถาม / ขอเดโม / แจ้งปัญหา เราจะติดต่อกลับโดยเร็ว</p>

        <div class="wp-grid wp-two">
            <div>
                <label>ชื่อ-นามสกุล *</label>
                <input name="name" required />
            </div>
            <div>
                <label>อีเมล *</label>
                <input name="email" type="email" required />
            </div>
        </div>

        <div class="wp-grid wp-two">
            <div>
                <label>เบอร์โทร</label>
                <input name="phone" type="tel" />
            </div>
            <div>
                <label>หัวข้อ *</label>
                <select name="topic" required>
                    <option value="">เลือกหัวข้อ</option>
                    <option value="support">แจ้งปัญหา (Support)</option>
                    <option value="sales">ขอเดโม/ราคา (Sales)</option>
                    <option value="partnership">พาร์ทเนอร์ (Partnership)</option>
                    <option value="other">อื่นๆ</option>
                </select>
            </div>
        </div>

        <div class="wp-grid">
            <div>
                <label>บริษัท/องค์กร</label>
                <input name="company" />
            </div>
            <div>
                <div class="wp-row">
                    <label>รายละเอียด *</label>
                    <span class="wp-contact-muted"><span id="wpMsgCount">0</span>/1000</span>
                </div>
                <textarea name="message" maxlength="1000" required></textarea>
            </div>
        </div>

        <!-- Honeypot: บอทจะกรอก, คนจริงไม่เห็น -->
        <div class="wp-hp" aria-hidden="true">
            <label>Website</label>
            <input name="website" autocomplete="off" tabindex="-1" />
        </div>

        <?php wp_nonce_field('workppass_contact_submit', 'workppass_nonce'); ?>

        <button type="submit" class="wp-btn-primary">ส่งข้อความ</button>
        <div id="workppassStatus" class="wp-status" role="alert"></div>
    </form>

    <style>
        .wp-contact-card { max-width: 720px; padding: 24px; border: 1px solid #e5e7eb; border-radius: 16px; background: #fff; }
        .wp-contact-muted { color: #6b7280; margin: 0 0 16px; font-size: 14px; }
        .wp-grid { display: grid; gap: 16px; margin-bottom: 16px; }
        .wp-two { grid-template-columns: 1fr; }
        @media (min-width: 640px) { .wp-two { grid-template-columns: 1fr 1fr; } }
        label { display: block; font-size: 13px; color: #374151; font-weight: 500; margin-bottom: 6px; }
        input, select, textarea { width: 100%; box-sizing: border-box; border: 1px solid #d1d5db; border-radius: 10px; padding: 10px 12px; font-size: 14px; transition: border-color .2s; }
        input:focus, select:focus, textarea:focus { outline: none; border-color: #38bdf8; }
        textarea { min-height: 140px; resize: vertical; }
        .wp-row { display: flex; justify-content: space-between; align-items: center; }
        .wp-btn-primary { margin-top: 8px; border: 0; border-radius: 10px; padding: 12px 24px; background: #38bdf8; color: #0f172a; font-weight: 700; cursor: pointer; font-size: 15px; }
        .wp-btn-primary:disabled { opacity: .6; cursor: not-allowed; }
        .wp-status { margin-top: 12px; font-size: 13px; min-height: 20px; }
        .wp-status.ok { color: #059669; }
        .wp-status.bad { color: #dc2626; }
        .wp-hp { position: absolute; left: -9999px; height: 0; overflow: hidden; }
    </style>

    <script>
    (function () {
        const form   = document.getElementById('workppassContactForm');
        if (!form) return;

        const msgEl  = form.querySelector('textarea[name="message"]');
        const countEl = document.getElementById('wpMsgCount');
        const status = document.getElementById('workppassStatus');
        const btn    = form.querySelector('button[type="submit"]');

        // นับตัวอักษร
        msgEl.addEventListener('input', () => {
            countEl.textContent = msgEl.value.length;
        });

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            btn.disabled = true;
            status.className = 'wp-status';
            status.textContent = 'กำลังส่ง...';

            try {
                const res = await fetch('<?php echo esc_url(rest_url('workppass/v1/contact')); ?>', {
                    method: 'POST',
                    credentials: 'same-origin',
                    body: new FormData(form),
                });

                const data = await res.json().catch(() => ({}));

                if (!res.ok || !data.success) {
                    throw new Error(data?.message || 'ส่งไม่สำเร็จ');
                }

                status.className = 'wp-status ok';
                status.textContent = 'ส่งข้อความเรียบร้อยแล้ว เราจะติดต่อกลับโดยเร็ว ✅';
                form.reset();
                countEl.textContent = '0';

            } catch (err) {
                status.className = 'wp-status bad';
                status.textContent = 'เกิดข้อผิดพลาด: ' + err.message;
            } finally {
                btn.disabled = false;
            }
        });
    })();
    </script>

    <?php
    return ob_get_clean();
});
```

---

## 5. ดูข้อมูลใน Admin Dashboard

### 5.1 เพิ่ม Column ใน List View

```php
// เพิ่มคอลัมน์ในหน้า Contact Form > All Submissions
add_filter('manage_contact_submission_posts_columns', function ($cols) {
    return [
        'cb'      => $cols['cb'],
        'title'   => 'หัวเรื่อง',
        'email'   => 'อีเมล',
        'phone'   => 'เบอร์โทร',
        'topic'   => 'หัวข้อ',
        'date'    => 'วันที่ส่ง',
    ];
});

add_action('manage_contact_submission_posts_custom_column', function ($col, $post_id) {
    $map = [
        'email' => '_contact_email',
        'phone' => '_contact_phone',
        'topic' => '_contact_topic',
    ];
    if (isset($map[$col])) {
        echo esc_html(get_post_meta($post_id, $map[$col], true));
    }
}, 10, 2);
```

### 5.2 เพิ่ม Meta Box แสดงรายละเอียดใน Edit Screen

```php
add_action('add_meta_boxes', function () {
    add_meta_box(
        'contact_details',
        'รายละเอียดการติดต่อ',
        'workppass_render_contact_metabox',
        'contact_submission',
        'normal',
        'high'
    );
});

function workppass_render_contact_metabox($post) {
    $fields = [
        '_contact_name'    => 'ชื่อ',
        '_contact_email'   => 'อีเมล',
        '_contact_phone'   => 'เบอร์โทร',
        '_contact_company' => 'บริษัท',
        '_contact_topic'   => 'หัวข้อ',
        '_contact_message' => 'ข้อความ',
        '_contact_ip'      => 'IP Address',
        '_contact_date'    => 'วันที่ส่ง',
    ];

    echo '<table class="form-table"><tbody>';
    foreach ($fields as $key => $label) {
        $value = get_post_meta($post->ID, $key, true);
        printf(
            '<tr><th>%s</th><td>%s</td></tr>',
            esc_html($label),
            nl2br(esc_html($value))
        );
    }
    echo '</tbody></table>';
}
```

---

## 6. อธิบาย Flow ทั้งหมด

```
User กรอก Form
      │
      ▼
JavaScript submit → fetch() POST → /wp-json/workppass/v1/contact
      │
      ▼
REST API Handler (workppass_handle_contact)
      │
      ├─ wp_verify_nonce()       ← ป้องกัน CSRF
      ├─ honeypot check          ← ป้องกันบอท
      ├─ sanitize_*()            ← ทำความสะอาด input
      ├─ validate (email, topic) ← ตรวจสอบความถูกต้อง
      │
      ▼
wp_insert_post() → บันทึกลง wp_posts
      │             post_type = 'contact_submission'
      │             post_status = 'private'
      │             post_title = '[SUPPORT] ชื่อ <email>'
      │
      ▼
update_post_meta() × 8 → บันทึกลง wp_postmeta
      │  _contact_name, _contact_email, _contact_phone
      │  _contact_company, _contact_topic, _contact_message
      │  _contact_ip, _contact_date
      │
      ▼
Response JSON { success: true, id: 123 }
      │
      ▼
JavaScript แสดง "ส่งเรียบร้อย ✅"
```

### Security ที่ใช้

| มาตรการ | ทำอะไร |
|---------|---------|
| `wp_verify_nonce()` | ป้องกัน CSRF — token หมดอายุใน 24 ชม. |
| Honeypot field | ดักบอทที่กรอกทุก field อัตโนมัติ |
| `sanitize_text_field()` | ตัด HTML tags และ whitespace |
| `sanitize_email()` | ทำความสะอาด email |
| `sanitize_textarea_field()` | ทำความสะอาด textarea (รักษา newline) |
| `is_email()` | ตรวจสอบ format email |
| `in_array(..., true)` | strict check topic ป้องกัน type juggling |
| `post_status = 'private'` | ซ่อน submission จากหน้าเว็บ |
| `is_wp_error()` | จัดการ DB error อย่างถูกต้อง |

---

## วิธีติดตั้ง

1. สร้างไฟล์ `wp-content/plugins/workppass-contact/workppass-contact.php`
2. วาง code ทั้งหมดในไฟล์เดียว (ตามลำดับที่อธิบายข้างบน)
3. เข้า WordPress Admin → Plugins → เปิดใช้งาน **Workppass Contact Form**
4. สร้าง Page ใหม่ → ใส่ shortcode `[workppass_contact_form]`
5. ดูข้อมูลที่ Admin → **Contact Form** → All Submissions
