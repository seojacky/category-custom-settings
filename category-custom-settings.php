<?php
/**
 * Plugin Name: Category Custom Settings
 * Plugin URI: https://example.com/
 * Description: Adds custom settings fields for WordPress categories and provides helper functions for frontend output.
 * Version: 1.0.0
 * Author: OpenAI
 * License: GPL2+
 * Text Domain: category-custom-settings
 */

if (!defined('ABSPATH')) {
	exit;
}

final class Category_Custom_Settings_Plugin {

	const NONCE_ACTION = 'ccs_save_category_fields';
	const NONCE_NAME   = 'ccs_category_fields_nonce';

	public function __construct() {
		add_action('category_edit_form_fields', array($this, 'render_edit_fields'));
		add_action('edited_category', array($this, 'save_fields'));

		add_action('admin_enqueue_scripts', array($this, 'admin_assets'));
	}

	public function admin_assets($hook_suffix) {
		if ($hook_suffix !== 'term.php') {
			return;
		}

		$screen = get_current_screen();
		if (!$screen || $screen->taxonomy !== 'category') {
			return;
		}

		$css = '
			.form-table th.ccs-field-heading,
			.form-table td.ccs-field-heading {
				padding-top: 22px;
			}
			.ccs-row-add-description th,
			.ccs-row-add-description td {
				background: #8977f3;
			}
			.ccs-row-version th,
			.ccs-row-version td {
				background: #5b9dd9;
			}
			.ccs-row-video th,
			.ccs-row-video td {
				background: #ffa500;
			}
			.ccs-row-bg-category th,
			.ccs-row-bg-category td,
			.ccs-row-rss th,
			.ccs-row-rss td {
				background: #f2f377;
			}
			.ccs-row-leftlink th,
			.ccs-row-leftlink td,
			.ccs-row-rightlink th,
			.ccs-row-rightlink td {
				background: #5bd99d;
			}
			.ccs-row-adsense1 th,
			.ccs-row-adsense1 td,
			.ccs-row-headerbanner th,
			.ccs-row-headerbanner td {
				background: #1565c0;
				color: #fff;
			}
			.ccs-row-adsense1 label,
			.ccs-row-headerbanner label {
				color: #fff;
			}
			.ccs-textarea-large {
				width: 100%;
				min-height: 110px;
			}
			.ccs-textarea-medium {
				width: 100%;
				min-height: 70px;
			}
			.ccs-input-text {
				width: 100%;
				max-width: 420px;
			}
			.ccs-admin-note {
				margin: 0;
				font-weight: 600;
			}
		';

		wp_register_style('ccs-admin-inline', false);
		wp_enqueue_style('ccs-admin-inline');
		wp_add_inline_style('ccs-admin-inline', $css);
	}

	public function render_edit_fields($term) {
		if (!($term instanceof WP_Term) || $term->taxonomy !== 'category') {
			return;
		}

		if (!current_user_can('manage_categories')) {
			return;
		}

		$term_id = (int) $term->term_id;
		$is_parent = ((int) $term->parent === 0);

		wp_nonce_field(self::NONCE_ACTION, self::NONCE_NAME);

		$cat_add_description = get_term_meta($term_id, 'cat_add_description', true);
		$cat_version         = get_term_meta($term_id, 'cat_version', true);
		$cat_video           = get_term_meta($term_id, 'cat_video', true);
		$bg_category         = get_term_meta($term_id, 'bg_category', true);
		$cat_rss             = get_term_meta($term_id, 'cat_rss', true);
		$cat_leftlink        = get_term_meta($term_id, 'cat_leftlink', true);
		$cat_rightlink       = get_term_meta($term_id, 'cat_rightlink', true);
		$cat_adsense1        = get_term_meta($term_id, 'cat_adsense1', true);
		$cat_headerbanner    = get_term_meta($term_id, 'cat_headerbanner', true);
		?>
		<tr class="form-field ccs-row-add-description">
			<th scope="row" valign="top">
				<label for="ccs_cat_add_description">Дополнительное описание под листингом</label>
			</th>
			<td>
				<textarea class="ccs-textarea-large" name="ccs_fields[cat_add_description]" id="ccs_cat_add_description"><?php echo esc_textarea($cat_add_description); ?></textarea>
			</td>
		</tr>

		<?php if ($is_parent) : ?>
			<tr>
				<th class="ccs-field-heading" scope="row" valign="top"></th>
				<td class="ccs-field-heading">
					<p class="ccs-admin-note">Поля для главных категорий</p>
				</td>
			</tr>

			<tr class="form-field ccs-row-version">
				<th scope="row" valign="top">
					<label for="ccs_cat_version">Версия игры</label>
				</th>
				<td>
					<input class="ccs-input-text" type="text" name="ccs_fields[cat_version]" id="ccs_cat_version" value="<?php echo esc_attr($cat_version); ?>" />
				</td>
			</tr>

			<tr class="form-field ccs-row-video">
				<th scope="row" valign="top">
					<label for="ccs_cat_video">ID Видео "в тему" <br>(пример: a--RlPV3ZBo)</label>
				</th>
				<td>
					<input class="ccs-input-text" type="text" name="ccs_fields[cat_video]" id="ccs_cat_video" value="<?php echo esc_attr($cat_video); ?>" />
				</td>
			</tr>

			<tr>
				<th class="ccs-field-heading" scope="row" valign="top"></th>
				<td class="ccs-field-heading">
					<p class="ccs-admin-note">Поля для администратора</p>
				</td>
			</tr>

			<tr class="form-field ccs-row-bg-category">
				<th scope="row" valign="top">
					<label for="ccs_bg_category">Цвет фона категорий (нужен спец плагин)</label>
				</th>
				<td>
					<input class="ccs-input-text" type="text" name="ccs_fields[bg_category]" id="ccs_bg_category" value="<?php echo esc_attr($bg_category); ?>" />
				</td>
			</tr>

			<tr class="form-field ccs-row-rss">
				<th scope="row" valign="top">
					<label for="ccs_cat_rss">RSS новостей</label>
				</th>
				<td>
					<input class="ccs-input-text" type="url" name="ccs_fields[cat_rss]" id="ccs_cat_rss" value="<?php echo esc_attr($cat_rss); ?>" />
				</td>
			</tr>

			<tr class="form-field ccs-row-leftlink">
				<th scope="row" valign="top">
					<label for="ccs_cat_leftlink">Ссылка слева</label>
				</th>
				<td>
					<input class="ccs-input-text" type="url" name="ccs_fields[cat_leftlink]" id="ccs_cat_leftlink" value="<?php echo esc_attr($cat_leftlink); ?>" />
				</td>
			</tr>

			<tr class="form-field ccs-row-rightlink">
				<th scope="row" valign="top">
					<label for="ccs_cat_rightlink">Ссылка справа</label>
				</th>
				<td>
					<input class="ccs-input-text" type="url" name="ccs_fields[cat_rightlink]" id="ccs_cat_rightlink" value="<?php echo esc_attr($cat_rightlink); ?>" />
				</td>
			</tr>

			<tr class="form-field ccs-row-adsense1">
				<th scope="row" valign="top">
					<label for="ccs_cat_adsense1">Код Adsense 1</label>
				</th>
				<td>
					<textarea class="ccs-textarea-medium" name="ccs_fields[cat_adsense1]" id="ccs_cat_adsense1"><?php echo esc_textarea($cat_adsense1); ?></textarea>
				</td>
			</tr>

			<tr class="form-field ccs-row-headerbanner">
				<th scope="row" valign="top">
					<label for="ccs_cat_headerbanner">HTML код баннера в шапке</label>
				</th>
				<td>
					<textarea class="ccs-textarea-medium" name="ccs_fields[cat_headerbanner]" id="ccs_cat_headerbanner"><?php echo esc_textarea($cat_headerbanner); ?></textarea>
				</td>
			</tr>
		<?php endif; ?>
		<?php
	}

	public function save_fields($term_id) {
		$term_id = (int) $term_id;

		if (!current_user_can('manage_categories')) {
			return;
		}

		if (!isset($_POST[self::NONCE_NAME]) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST[self::NONCE_NAME])), self::NONCE_ACTION)) {
			return;
		}

		if (!isset($_POST['ccs_fields']) || !is_array($_POST['ccs_fields'])) {
			return;
		}

		$term = get_term($term_id, 'category');
		if (!$term || is_wp_error($term)) {
			return;
		}

		$is_parent = ((int) $term->parent === 0);
		$raw = wp_unslash($_POST['ccs_fields']);

		$fields = array(
			'cat_add_description' => array(
				'type' => 'html_limited',
				'allowed_for_child' => true,
			),
			'cat_version' => array(
				'type' => 'text',
				'allowed_for_child' => false,
			),
			'cat_video' => array(
				'type' => 'text',
				'allowed_for_child' => false,
			),
			'bg_category' => array(
				'type' => 'text',
				'allowed_for_child' => false,
			),
			'cat_rss' => array(
				'type' => 'url',
				'allowed_for_child' => false,
			),
			'cat_leftlink' => array(
				'type' => 'url',
				'allowed_for_child' => false,
			),
			'cat_rightlink' => array(
				'type' => 'url',
				'allowed_for_child' => false,
			),
			'cat_adsense1' => array(
				'type' => 'code',
				'allowed_for_child' => false,
			),
			'cat_headerbanner' => array(
				'type' => 'code',
				'allowed_for_child' => false,
			),
		);

		foreach ($fields as $meta_key => $config) {
			if (!$is_parent && empty($config['allowed_for_child'])) {
				delete_term_meta($term_id, $meta_key);
				continue;
			}

			$value = isset($raw[$meta_key]) ? $raw[$meta_key] : '';

			switch ($config['type']) {
				case 'html_limited':
					$value = wp_kses_post($value);
					break;

				case 'url':
					$value = esc_url_raw(trim($value));
					break;

				case 'code':
					if (current_user_can('unfiltered_html')) {
						$value = $this->sanitize_custom_code($value);
					} else {
						$value = '';
					}
					break;

				case 'text':
				default:
					$value = sanitize_text_field($value);
					break;
			}

			if ($value === '') {
				delete_term_meta($term_id, $meta_key);
			} else {
				update_term_meta($term_id, $meta_key, $value);
			}
		}
	}

	private function sanitize_custom_code($value) {
		if (!is_string($value)) {
			return '';
		}

		$value = trim($value);

		$allowed_html = wp_kses_allowed_html('post');

		$allowed_html['ins'] = array(
			'class'        => true,
			'style'        => true,
			'data-ad-client' => true,
			'data-ad-slot'   => true,
			'data-ad-format' => true,
			'data-full-width-responsive' => true,
		);

		$allowed_html['script'] = array(
			'async' => true,
			'src'   => true,
			'crossorigin' => true,
		);

		$allowed_html['iframe'] = array(
			'src'             => true,
			'width'           => true,
			'height'          => true,
			'frameborder'     => true,
			'allow'           => true,
			'allowfullscreen' => true,
			'loading'         => true,
			'referrerpolicy'  => true,
		);

		$allowed_html['div'] = array_merge(
			isset($allowed_html['div']) ? $allowed_html['div'] : array(),
			array(
				'class' => true,
				'id'    => true,
				'style' => true,
				'data-*' => true,
			)
		);

		$allowed_html['a'] = array_merge(
			isset($allowed_html['a']) ? $allowed_html['a'] : array(),
			array(
				'target' => true,
				'rel'    => true,
			)
		);

		return wp_kses($value, $allowed_html);
	}
}

new Category_Custom_Settings_Plugin();

/**
 * Get category custom field value.
 *
 * @param string   $field_name Field name.
 * @param int|null $term_id    Optional category term ID.
 * @return string
 */
function ccs_get_category_field($field_name, $term_id = null) {
	if (empty($field_name) || !is_string($field_name)) {
		return '';
	}

	if ($term_id === null) {
		$term = get_queried_object();
		if (!$term || is_wp_error($term) || empty($term->term_id) || empty($term->taxonomy) || $term->taxonomy !== 'category') {
			return '';
		}
		$term_id = (int) $term->term_id;
	} else {
		$term_id = (int) $term_id;
	}

	return (string) get_term_meta($term_id, $field_name, true);
}

/**
 * Echo category custom field safely depending on field type.
 *
 * @param string   $field_name Field name.
 * @param int|null $term_id    Optional category term ID.
 * @return void
 */
function ccs_the_category_field($field_name, $term_id = null) {
	$value = ccs_get_category_field($field_name, $term_id);

	if ($value === '') {
		return;
	}

	switch ($field_name) {
		case 'cat_add_description':
			echo wp_kses_post($value);
			break;

		case 'cat_rss':
		case 'cat_leftlink':
		case 'cat_rightlink':
			echo esc_url($value);
			break;

		case 'cat_adsense1':
		case 'cat_headerbanner':
			if (current_user_can('unfiltered_html')) {
				echo $value;
			} else {
				echo wp_kses_post($value);
			}
			break;

		default:
			echo esc_html($value);
			break;
	}
}
