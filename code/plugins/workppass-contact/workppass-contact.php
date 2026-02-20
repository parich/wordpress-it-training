<?php
/**
 * Plugin Name: Workppass Contact Form
 * Description: Contact form ที่บันทึกข้อมูลลง Custom Post Type แสดงผลใน Admin Dashboard
 * Version:     1.0.0
 * Author:      Workppass
 * Text Domain: workppass-contact
 */

defined( 'ABSPATH' ) || exit;

// ─────────────────────────────────────────────
// 1. REGISTER CUSTOM POST TYPE
// ─────────────────────────────────────────────
add_action( 'init', function () {
	register_post_type( 'contact_submission', [
		'label'           => 'Contact Submissions',
		'labels'          => [
			'name'          => 'Contact Submissions',
			'singular_name' => 'Submission',
			'menu_name'     => 'Contact Form',
			'all_items'     => 'All Submissions',
		],
		'public'          => false,
		'show_ui'         => true,
		'show_in_menu'    => true,
		'show_in_rest'    => false,
		'supports'        => [ 'title' ],
		'capability_type' => 'post',
		'map_meta_cap'    => true,
		'menu_icon'       => 'dashicons-email-alt',
		'menu_position'   => 25,
	] );
} );

// ─────────────────────────────────────────────
// 2. REST API ENDPOINT
// ─────────────────────────────────────────────
add_action( 'rest_api_init', function () {
	register_rest_route( 'workppass/v1', '/contact', [
		'methods'             => 'POST',
		'callback'            => 'workppass_handle_contact',
		'permission_callback' => '__return_true',
	] );
} );

function workppass_handle_contact( WP_REST_Request $req ) {

	// 2.1 รับค่าและ Sanitize
	$name    = sanitize_text_field( $req->get_param( 'name' ) );
	$email   = sanitize_email( $req->get_param( 'email' ) );
	$phone   = sanitize_text_field( $req->get_param( 'phone' ) );
	$company = sanitize_text_field( $req->get_param( 'company' ) );
	$topic   = sanitize_text_field( $req->get_param( 'topic' ) );
	$message = sanitize_textarea_field( $req->get_param( 'message' ) );
	$hp      = $req->get_param( 'website' );
	$nonce   = $req->get_param( 'workppass_nonce' );

	// 2.2 Nonce (ป้องกัน CSRF)
	if ( ! wp_verify_nonce( $nonce, 'workppass_contact_submit' ) ) {
		return new WP_REST_Response( [ 'success' => false, 'message' => 'Invalid nonce' ], 403 );
	}

	// 2.3 Honeypot (ป้องกันบอท)
	if ( ! empty( trim( (string) $hp ) ) ) {
		return new WP_REST_Response( [ 'success' => false, 'message' => 'Spam detected' ], 400 );
	}

	// 2.4 Validate
	$allowed_topics = [ 'support', 'sales', 'partnership', 'other' ];

	if ( mb_strlen( trim( $name ) ) < 2 ) {
		return new WP_REST_Response( [ 'success' => false, 'message' => 'ชื่อต้องมีอย่างน้อย 2 ตัวอักษร' ], 400 );
	}
	if ( ! is_email( $email ) ) {
		return new WP_REST_Response( [ 'success' => false, 'message' => 'อีเมลไม่ถูกต้อง' ], 400 );
	}
	if ( ! in_array( $topic, $allowed_topics, true ) ) {
		return new WP_REST_Response( [ 'success' => false, 'message' => 'กรุณาเลือกหัวข้อ' ], 400 );
	}
	if ( mb_strlen( trim( $message ) ) < 10 ) {
		return new WP_REST_Response( [ 'success' => false, 'message' => 'ข้อความต้องมีอย่างน้อย 10 ตัวอักษร' ], 400 );
	}

	// 2.5 บันทึกลง wp_posts
	$post_id = wp_insert_post( [
		'post_type'   => 'contact_submission',
		'post_status' => 'private',
		'post_title'  => sprintf( '[%s] %s <%s>', strtoupper( $topic ), $name, $email ),
		'post_author' => 0,
	], true );

	if ( is_wp_error( $post_id ) ) {
		return new WP_REST_Response( [
			'success' => false,
			'message' => 'Database error: ' . $post_id->get_error_message(),
		], 500 );
	}

	// 2.6 บันทึก Meta Data ลง wp_postmeta
	$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';

	$meta_fields = [
		'_contact_name'    => $name,
		'_contact_email'   => $email,
		'_contact_phone'   => $phone,
		'_contact_company' => $company,
		'_contact_topic'   => $topic,
		'_contact_message' => $message,
		'_contact_ip'      => $ip,
		'_contact_date'    => current_time( 'mysql' ),
	];

	foreach ( $meta_fields as $key => $value ) {
		update_post_meta( $post_id, $key, $value );
	}

	return new WP_REST_Response( [ 'success' => true, 'id' => $post_id ], 201 );
}

// ─────────────────────────────────────────────
// 3. SHORTCODE [workppass_contact_form]
// ─────────────────────────────────────────────
add_shortcode( 'workppass_contact_form', function () {
	ob_start();
	$rest_url = esc_url( rest_url( 'workppass/v1/contact' ) );
	?>
	<form id="workppassContactForm" class="wpc-card" novalidate>

		<h3 class="wpc-title">ติดต่อ Workppass</h3>
		<p class="wpc-muted">ส่งคำถาม / ขอเดโม / แจ้งปัญหา เราจะติดต่อกลับโดยเร็ว</p>

		<div class="wpc-grid wpc-two">
			<div class="wpc-field">
				<label for="wpc_name">ชื่อ-นามสกุล <span class="wpc-req">*</span></label>
				<input id="wpc_name" name="name" type="text" autocomplete="name" required />
			</div>
			<div class="wpc-field">
				<label for="wpc_email">อีเมล <span class="wpc-req">*</span></label>
				<input id="wpc_email" name="email" type="email" autocomplete="email" required />
			</div>
		</div>

		<div class="wpc-grid wpc-two">
			<div class="wpc-field">
				<label for="wpc_phone">เบอร์โทร</label>
				<input id="wpc_phone" name="phone" type="tel" autocomplete="tel" />
			</div>
			<div class="wpc-field">
				<label for="wpc_topic">หัวข้อ <span class="wpc-req">*</span></label>
				<select id="wpc_topic" name="topic" required>
					<option value="">-- เลือกหัวข้อ --</option>
					<option value="support">แจ้งปัญหา (Support)</option>
					<option value="sales">ขอเดโม / ราคา (Sales)</option>
					<option value="partnership">พาร์ทเนอร์ (Partnership)</option>
					<option value="other">อื่นๆ</option>
				</select>
			</div>
		</div>

		<div class="wpc-field">
			<label for="wpc_company">บริษัท / องค์กร</label>
			<input id="wpc_company" name="company" type="text" autocomplete="organization" />
		</div>

		<div class="wpc-field">
			<div class="wpc-row">
				<label for="wpc_message">รายละเอียด <span class="wpc-req">*</span></label>
				<span class="wpc-muted wpc-counter"><span id="wpcMsgCount">0</span> / 1000</span>
			</div>
			<textarea id="wpc_message" name="message" maxlength="1000" required></textarea>
		</div>

		<!-- Honeypot: ซ่อนจากคน แต่บอทจะกรอก -->
		<div class="wpc-hp" aria-hidden="true">
			<label>Website</label>
			<input name="website" autocomplete="off" tabindex="-1" />
		</div>

		<?php wp_nonce_field( 'workppass_contact_submit', 'workppass_nonce' ); ?>

		<button type="submit" id="wpcSubmitBtn" class="wpc-btn">ส่งข้อความ</button>
		<div id="wpcStatus" class="wpc-status" role="alert" aria-live="polite"></div>

	</form>

	<style>
	.wpc-card{max-width:720px;padding:28px;border:1px solid #e5e7eb;border-radius:16px;background:#fff;font-family:inherit}
	.wpc-title{margin:0 0 6px;font-size:20px}
	.wpc-muted{color:#6b7280;font-size:13px;margin:0 0 20px}
	.wpc-req{color:#ef4444}
	.wpc-grid{display:grid;gap:16px}
	.wpc-two{grid-template-columns:1fr}
	@media(min-width:640px){.wpc-two{grid-template-columns:1fr 1fr}}
	.wpc-field{display:flex;flex-direction:column;gap:6px;margin-bottom:16px}
	.wpc-field label{font-size:13px;font-weight:600;color:#374151}
	.wpc-field input,.wpc-field select,.wpc-field textarea{width:100%;box-sizing:border-box;border:1px solid #d1d5db;border-radius:10px;padding:10px 14px;font-size:14px;color:#111827;transition:border-color .15s,box-shadow .15s}
	.wpc-field input:focus,.wpc-field select:focus,.wpc-field textarea:focus{outline:none;border-color:#38bdf8;box-shadow:0 0 0 3px rgba(56,189,248,.15)}
	.wpc-field textarea{min-height:140px;resize:vertical;font-family:inherit}
	.wpc-row{display:flex;justify-content:space-between;align-items:center}
	.wpc-counter{margin-bottom:6px}
	.wpc-btn{display:inline-block;margin-top:4px;padding:12px 28px;background:#38bdf8;color:#0f172a;font-size:15px;font-weight:700;border:none;border-radius:10px;cursor:pointer;transition:background .15s}
	.wpc-btn:hover{background:#0ea5e9}
	.wpc-btn:disabled{opacity:.6;cursor:not-allowed}
	.wpc-status{margin-top:14px;font-size:13px;min-height:20px}
	.wpc-status.ok{color:#059669}
	.wpc-status.bad{color:#dc2626}
	.wpc-hp{position:absolute;left:-9999px;height:0;overflow:hidden}
	</style>

	<script>
	(function () {
		var form    = document.getElementById('workppassContactForm');
		if (!form) return;

		var msgEl   = document.getElementById('wpc_message');
		var countEl = document.getElementById('wpcMsgCount');
		var status  = document.getElementById('wpcStatus');
		var btn     = document.getElementById('wpcSubmitBtn');
		var apiUrl  = '<?php echo $rest_url; ?>';

		// นับตัวอักษร
		msgEl.addEventListener('input', function () {
			countEl.textContent = msgEl.value.length;
		});

		form.addEventListener('submit', function (e) {
			e.preventDefault();

			btn.disabled     = true;
			status.className = 'wpc-status';
			status.textContent = 'กำลังส่ง...';

			fetch(apiUrl, {
				method: 'POST',
				credentials: 'same-origin',
				body: new FormData(form),
			})
			.then(function (res) {
				return res.json().then(function (data) {
					return { ok: res.ok, data: data };
				});
			})
			.then(function (result) {
				if (!result.ok || !result.data.success) {
					throw new Error(result.data.message || 'ส่งไม่สำเร็จ');
				}
				status.className   = 'wpc-status ok';
				status.textContent = '✅ ส่งข้อความเรียบร้อยแล้ว เราจะติดต่อกลับโดยเร็ว';
				form.reset();
				countEl.textContent = '0';
			})
			.catch(function (err) {
				status.className   = 'wpc-status bad';
				status.textContent = '❌ ' + err.message;
			})
			.finally(function () {
				btn.disabled = false;
			});
		});
	})();
	</script>
	<?php
	return ob_get_clean();
} );

// ─────────────────────────────────────────────
// 4. ADMIN: COLUMNS ในหน้า List
// ─────────────────────────────────────────────
add_filter( 'manage_contact_submission_posts_columns', function ( $cols ) {
	return [
		'cb'      => $cols['cb'],
		'title'   => 'หัวเรื่อง',
		'email'   => 'อีเมล',
		'phone'   => 'เบอร์โทร',
		'topic'   => 'หัวข้อ',
		'date'    => 'วันที่ส่ง',
	];
} );

add_action( 'manage_contact_submission_posts_custom_column', function ( $col, $post_id ) {
	$map = [
		'email' => '_contact_email',
		'phone' => '_contact_phone',
		'topic' => '_contact_topic',
	];
	if ( isset( $map[ $col ] ) ) {
		echo esc_html( get_post_meta( $post_id, $map[ $col ], true ) );
	}
}, 10, 2 );

// ─────────────────────────────────────────────
// 5. ADMIN: META BOX รายละเอียด
// ─────────────────────────────────────────────
add_action( 'add_meta_boxes', function () {
	add_meta_box(
		'wpc_details',
		'รายละเอียดการติดต่อ',
		'workppass_render_metabox',
		'contact_submission',
		'normal',
		'high'
	);
} );

function workppass_render_metabox( $post ) {
	$fields = [
		'_contact_name'    => 'ชื่อ-นามสกุล',
		'_contact_email'   => 'อีเมล',
		'_contact_phone'   => 'เบอร์โทร',
		'_contact_company' => 'บริษัท / องค์กร',
		'_contact_topic'   => 'หัวข้อ',
		'_contact_message' => 'ข้อความ',
		'_contact_ip'      => 'IP Address',
		'_contact_date'    => 'วันที่ส่ง',
	];

	echo '<table class="form-table"><tbody>';
	foreach ( $fields as $key => $label ) {
		$value = get_post_meta( $post->ID, $key, true );
		printf(
			'<tr><th scope="row" style="width:160px">%s</th><td>%s</td></tr>',
			esc_html( $label ),
			nl2br( esc_html( (string) $value ) )
		);
	}
	echo '</tbody></table>';
}

// ─────────────────────────────────────────────
// 6. ป้องกันการแก้ไข Submission จาก Admin
// ─────────────────────────────────────────────
add_action( 'current_screen', function ( $screen ) {
	if ( $screen->post_type === 'contact_submission' && $screen->base === 'post' ) {
		add_action( 'admin_notices', function () {
			echo '<div class="notice notice-info"><p>Submission นี้เป็น <strong>read-only</strong> — แก้ไขข้อมูลจาก form โดยตรงได้เท่านั้น</p></div>';
		} );
	}
} );
