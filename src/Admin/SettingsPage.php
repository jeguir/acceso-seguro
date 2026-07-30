<?php

namespace AccesoSeguro\Admin;



use AccesoSeguro\Infra\Options;



defined('ABSPATH') || exit;



final class SettingsPage {



	public function register(): void {

		add_action('admin_menu', [$this, 'menu']);

		add_action('admin_init', [$this, 'registerSettings']);

		add_action('admin_enqueue_scripts', [$this, 'enqueueAdminAssets']);

		add_action('admin_enqueue_scripts', [$this, 'enqueueAssets']);

	}



	public function menu(): void {

		add_menu_page(

			'Acceso Seguro',

			'Acceso Seguro',

			'manage_options',

			'acceso-seguro',

			[$this, 'render'],

			'dashicons-shield',

			56

		);



		add_submenu_page(

			'acceso-seguro',

			'Ajustes',

			'Ajustes',

			'manage_options',

			'acceso-seguro',

			[$this, 'render']

		);



		add_submenu_page(

			'acceso-seguro',

			'Log antispam',

			'Log antispam',

			'manage_options',

			'acceso-seguro-logs',

			[LogsPage::class, 'renderStatic']

		);

	}



	public function registerSettings(): void {

		register_setting('as_settings_group', Options::KEY, [

			'type' => 'array',

			'sanitize_callback' => [$this, 'sanitize'],

			'default' => [],

		]);

	}



	public function sanitize($input): array {

		$in = is_array($input) ? $input : [];



		$out = Options::getAll(); // partimos de lo existente y sobreescribimos



		// General

		$out['general']['enabled'] = !empty($in['general']['enabled']);

		$out['general']['popup_auto'] = !empty($in['general']['popup_auto']);

		$out['general']['public_error_register'] = sanitize_text_field($in['general']['public_error_register'] ?? $out['general']['public_error_register'] ?? '');

		$out['general']['public_error_login']    = sanitize_text_field($in['general']['public_error_login'] ?? $out['general']['public_error_login'] ?? '');

		$out['general']['allow_register'] = !empty($in['general']['allow_register']);

		$out['general']['privacy_page_id'] = isset($in['general']['privacy_page_id'])

			? max(0, (int) $in['general']['privacy_page_id'])

			: (int) ($out['general']['privacy_page_id'] ?? 0);



		$out['general']['rate_limit_enabled'] = !empty($in['general']['rate_limit_enabled']);

		$out['general']['rate_limit_login_window_seconds'] = max(30, (int)($in['general']['rate_limit_login_window_seconds'] ?? 300));

		$out['general']['rate_limit_login_max_attempts'] = max(1, (int)($in['general']['rate_limit_login_max_attempts'] ?? 8));

		$out['general']['rate_limit_register_window_seconds'] = max(30, (int)($in['general']['rate_limit_register_window_seconds'] ?? 900));

		$out['general']['rate_limit_register_max_attempts'] = max(1, (int)($in['general']['rate_limit_register_max_attempts'] ?? 4));

		$out['general']['rate_limit_forgot_window_seconds'] = max(30, (int)($in['general']['rate_limit_forgot_window_seconds'] ?? 900));

		$out['general']['rate_limit_forgot_max_attempts'] = max(1, (int)($in['general']['rate_limit_forgot_max_attempts'] ?? 4));



		$out['general']['score_enabled'] = !empty($in['general']['score_enabled']);

		$out['general']['score_threshold'] = max(1, (int)($in['general']['score_threshold'] ?? 8));



		$storage = isset($in['general']['rate_limit_storage']) ? (string) $in['general']['rate_limit_storage'] : 'transient';

		$storage = in_array($storage, ['transient', 'db'], true) ? $storage : 'transient';

		$out['general']['rate_limit_storage'] = $storage;



		$out['general']['progressive_enabled'] = !empty($in['general']['progressive_enabled']);

		$out['general']['progressive_steps_minutes'] = $this->sanitizeList($in['general']['progressive_steps_minutes'] ?? '');



		// UI (estilos modal)

		$out['ui']['modal_max_width'] = max(280, min(720, (int)($in['ui']['modal_max_width'] ?? ($out['ui']['modal_max_width'] ?? 420))));

		$out['ui']['border_radius']   = max(0,  min(40,  (int)($in['ui']['border_radius'] ?? ($out['ui']['border_radius'] ?? 14))));

		$out['ui']['modal_padding'] = max(0, min(48, (int)($in['ui']['modal_padding'] ?? ($out['ui']['modal_padding'] ?? 18))));



		$op = isset($in['ui']['overlay_opacity']) ? (float) $in['ui']['overlay_opacity'] : (float) ($out['ui']['overlay_opacity'] ?? 0.55);

		$out['ui']['overlay_opacity'] = max(0, min(0.9, $op));



		$out['ui']['accent_color'] = sanitize_hex_color($in['ui']['accent_color'] ?? ($out['ui']['accent_color'] ?? '#111111')) ?: '#111111';

		$out['ui']['bg_color']     = sanitize_hex_color($in['ui']['bg_color'] ?? ($out['ui']['bg_color'] ?? '#ffffff')) ?: '#ffffff';

		$out['ui']['text_color']   = sanitize_hex_color($in['ui']['text_color'] ?? ($out['ui']['text_color'] ?? '#111111')) ?: '#111111';



		$out['ui']['title_color'] = sanitize_hex_color($in['ui']['title_color'] ?? ($out['ui']['title_color'] ?? '#111111')) ?: '#111111';

		$out['ui']['input_bg'] = sanitize_hex_color($in['ui']['input_bg'] ?? ($out['ui']['input_bg'] ?? '#ffffff')) ?: '#ffffff';

		$out['ui']['input_border_color'] = sanitize_hex_color($in['ui']['input_border_color'] ?? ($out['ui']['input_border_color'] ?? '#dddddd')) ?: '#dddddd';

		$out['ui']['input_border_width'] = max(0, min(6, (int)($in['ui']['input_border_width'] ?? ($out['ui']['input_border_width'] ?? 1))));



		// Scoring

		$out['scoring']['deny_threshold'] = max(1, (int)($in['scoring']['deny_threshold'] ?? ($out['scoring']['deny_threshold'] ?? 70)));



		// Email

		$out['email']['enabled'] = !empty($in['email']['enabled']);

		$out['email']['check_mx'] = !empty($in['email']['check_mx']);

		$out['email']['mx_no_record_points'] = max(0, (int)($in['email']['mx_no_record_points'] ?? ($out['email']['mx_no_record_points'] ?? 45)));



		$out['email']['block_tlds'] = $this->sanitizeList($in['email']['block_tlds'] ?? '');

		$out['email']['block_domains'] = $this->sanitizeList($in['email']['block_domains'] ?? '');



		// Username

		$out['username']['enabled'] = !empty($in['username']['enabled']);

		$out['username']['min_length'] = max(1, (int)($in['username']['min_length'] ?? ($out['username']['min_length'] ?? 6)));

		$out['username']['min_vowel_ratio'] = max(0, min(1, (float)($in['username']['min_vowel_ratio'] ?? ($out['username']['min_vowel_ratio'] ?? 0.25))));

		$out['username']['max_consonant_run'] = max(1, (int)($in['username']['max_consonant_run'] ?? ($out['username']['max_consonant_run'] ?? 6)));



		$out['username']['name_dot_surname_points'] = max(0, (int)($in['username']['name_dot_surname_points'] ?? ($out['username']['name_dot_surname_points'] ?? 15)));

		$out['username']['short_points'] = max(0, (int)($in['username']['short_points'] ?? ($out['username']['short_points'] ?? 40)));

		$out['username']['low_vowel_points'] = max(0, (int)($in['username']['low_vowel_points'] ?? ($out['username']['low_vowel_points'] ?? 30)));

		$out['username']['consonant_run_points'] = max(0, (int)($in['username']['consonant_run_points'] ?? ($out['username']['consonant_run_points'] ?? 30)));

		$out['username']['suspicious_fragments_enabled'] = !empty($in['username']['suspicious_fragments_enabled']);

		$out['username']['suspicious_fragments_points'] = max(
			0,
			(int)($in['username']['suspicious_fragments_points'] ?? ($out['username']['suspicious_fragments_points'] ?? 30))
		);

		$out['username']['suspicious_fragments'] = $this->sanitizeList(
			$in['username']['suspicious_fragments']
				?? ($out['username']['suspicious_fragments'] ?? [])
		);



		// Logging

		$out['logging']['enabled'] = !empty($in['logging']['enabled']);

		$out['logging']['hash_ip'] = !empty($in['logging']['hash_ip']);

		$out['logging']['retention_days'] = max(1, (int)($in['logging']['retention_days'] ?? ($out['logging']['retention_days'] ?? 60)));



		return $out;

	}



	private function sanitizeList($raw): array {

		// Acepta string o array (por compatibilidad/guardados previos)

		if (is_array($raw)) {

			// Si viene como array, lo unimos como texto

			$raw = implode("\n", array_map('strval', $raw));

		} else {

			$raw = (string) $raw;

		}



		$raw = strtolower($raw);

		$raw = str_replace(["\r\n", "\r"], "\n", $raw);



		$parts = preg_split('/[\n, ]+/', $raw, -1, PREG_SPLIT_NO_EMPTY) ?: [];

		$clean = [];



		foreach ($parts as $p) {

			$p = trim($p);

			if ($p === '') continue;



			// permitimos letras/números/.-_

			$p = preg_replace('/[^a-z0-9\.\-_]/', '', $p);



			if ($p !== '') $clean[] = $p;

		}



		return array_values(array_unique($clean));

	}



	public function enqueueAdminAssets(string $hook): void {

	    // Solo cargar en nuestra página

	    if ($hook !== 'toplevel_page_acceso-seguro') {

	        return;

	    }



	    wp_enqueue_style('wp-color-picker');

	    wp_enqueue_script(

	        'as-admin-ui',

	        plugins_url('../../assets/admin-ui.js', __FILE__),

	        ['wp-color-picker'],

	        AS_VERSION,

	        true

	    );

	}



	public function enqueueAssets(string $hook): void {

		// Solo en la página de ajustes del plugin

		if ($hook !== 'toplevel_page_acceso-seguro') {

			return;

		}



		wp_enqueue_script(

			'as-admin-settings',

			plugins_url('../../assets/admin-settings.js', __FILE__),

			['jquery'],

			AS_VERSION,

			true

		);

	}



	public function render(): void {

		if (!current_user_can('manage_options')) return;



		$opts = Options::getAll();

		// Garantiza defaults si aún no existen

		if (empty($opts)) {

			Options::setDefaultsIfEmpty();

			$opts = Options::getAll();

		}



		?>

		<div class="wrap">

			<h1>Acceso Seguro – Ajustes</h1>



			<form method="post" action="options.php">

				<?php settings_fields('as_settings_group'); ?>



				<h2>General</h2>

				<label>

					<input type="checkbox" name="<?php echo esc_attr(Options::KEY); ?>[general][enabled]" value="1"

						<?php checked(!empty($opts['general']['enabled'])); ?>>

					Activar protección

				</label>



				<p>

					<label>

						<input type="checkbox" name="<?php echo esc_attr(Options::KEY); ?>[general][popup_auto]" value="1"

							<?php checked(!empty($opts['general']['popup_auto'])); ?>>

						Activar popup automático (inyectar en todo el sitio)

					</label>

				</p>



				<p>

					<label>

						<input type="checkbox" name="<?php echo esc_attr(Options::KEY); ?>[general][allow_register]" value="1"

							<?php checked(!empty($opts['general']['allow_register'])); ?>>

						Permitir registro de usuarios (mostrar registro en el popup)

					</label>

				</p>



				<p>

					<label>Mensaje genérico (registro)<br>

					<input type="text" class="large-text"

						name="<?php echo esc_attr(Options::KEY); ?>[general][public_error_register]"

						value="<?php echo esc_attr($opts['general']['public_error_register'] ?? ''); ?>">

					</label>

				</p>



				<p>

					<label>Mensaje genérico (login)<br>

					<input type="text" class="large-text"

						name="<?php echo esc_attr(Options::KEY); ?>[general][public_error_login]"

						value="<?php echo esc_attr($opts['general']['public_error_login'] ?? ''); ?>">

					</label>

				</p>



				<?php

				$privacySelected = (int) ($opts['general']['privacy_page_id'] ?? 0);

				$wpPrivacyId = (int) get_option('wp_page_for_privacy_policy', 0);

				?>



				<p>

					<label><strong><?php echo esc_html__('Página de política de privacidad', 'acceso-seguro'); ?></strong></label><br>



					<select name="<?php echo esc_attr(Options::KEY); ?>[general][privacy_page_id]">

						<option value="0" <?php selected($privacySelected, 0); ?>>

							<?php

							echo $wpPrivacyId > 0

								? esc_html__('(Usar la página configurada en Ajustes → Privacidad)', 'acceso-seguro')

								: esc_html__('(Selecciona una página)', 'acceso-seguro');

							?>

						</option>



						<?php

						$pages = get_pages(['sort_column' => 'post_title', 'sort_order' => 'ASC']);

						foreach ($pages as $p) :

							?>

							<option value="<?php echo (int) $p->ID; ?>" <?php selected($privacySelected, (int) $p->ID); ?>>

								<?php echo esc_html($p->post_title); ?>

							</option>

						<?php endforeach; ?>

					</select>



					<?php if ($wpPrivacyId > 0) : ?>

						<p class="description">

							<?php echo esc_html__('Si no eliges una página aquí, se usará la definida en Ajustes → Privacidad.', 'acceso-seguro'); ?>

						</p>

					<?php endif; ?>

				</p>				



				<hr>



				<h2><?php echo esc_html__('Estilos del popup', 'acceso-seguro'); ?></h2>



				<table class="widefat striped" style="max-width:720px;">

					<tbody>

						<tr>

							<th style="width:260px;"><?php echo esc_html__('Ancho máximo (px)', 'acceso-seguro'); ?></th>

							<td>

								<input type="number" min="280" max="720" step="10"

									name="<?php echo esc_attr(Options::KEY); ?>[ui][modal_max_width]"

									value="<?php echo esc_attr((int)($opts['ui']['modal_max_width'] ?? 420)); ?>">

							</td>

						</tr>



						<tr>

							<th><?php echo esc_html__('Padding interno (px)', 'acceso-seguro'); ?></th>

							<td>

								<input type="number" min="0" max="48" step="1"

									name="<?php echo esc_attr(Options::KEY); ?>[ui][modal_padding]"

									value="<?php echo esc_attr((int)($opts['ui']['modal_padding'] ?? 18)); ?>">

							</td>

						</tr>



						<tr>

							<th><?php echo esc_html__('Radio de borde (px)', 'acceso-seguro'); ?></th>

							<td>

								<input type="number" min="0" max="40" step="1"

									name="<?php echo esc_attr(Options::KEY); ?>[ui][border_radius]"

									value="<?php echo esc_attr((int)($opts['ui']['border_radius'] ?? 14)); ?>">

							</td>

						</tr>



						<tr>

							<th><?php echo esc_html__('Opacidad del fondo (0–0.9)', 'acceso-seguro'); ?></th>

							<td>

								<input type="number" min="0" max="0.9" step="0.05"

									name="<?php echo esc_attr(Options::KEY); ?>[ui][overlay_opacity]"

									value="<?php echo esc_attr((float)($opts['ui']['overlay_opacity'] ?? 0.55)); ?>">

							</td>

						</tr>



						<tr>

							<th><?php echo esc_html__('Color principal', 'acceso-seguro'); ?></th>

							<td>

								<input type="color"

									class="as-color regular-text"

									name="<?php echo esc_attr(Options::KEY); ?>[ui][accent_color]"

									value="<?php echo esc_attr((string)($opts['ui']['accent_color'] ?? '#111111')); ?>">

							</td>

						</tr>



						<tr>

						<th><?php echo esc_html__('Color títulos', 'acceso-seguro'); ?></th>

						<td>

							<input type="text" class="as-color regular-text" style="width:120px"

							name="<?php echo esc_attr(Options::KEY); ?>[ui][title_color]"

							value="<?php echo esc_attr((string)($opts['ui']['title_color'] ?? '#111111')); ?>">

							<p class="description"><?php echo esc_html__('Color de los títulos (h3) del modal.', 'acceso-seguro'); ?></p>

						</td>

						</tr>



						<tr>

						<th><?php echo esc_html__('Fondo inputs', 'acceso-seguro'); ?></th>

						<td>

							<input type="text" class="as-color regular-text" style="width:120px"

							name="<?php echo esc_attr(Options::KEY); ?>[ui][input_bg]"

							value="<?php echo esc_attr((string)($opts['ui']['input_bg'] ?? '#ffffff')); ?>">

							<p class="description"><?php echo esc_html__('Color de fondo de los campos del formulario.', 'acceso-seguro'); ?></p>

						</td>

						</tr>



						<tr>

						<th><?php echo esc_html__('Color borde inputs', 'acceso-seguro'); ?></th>

						<td>

							<input type="text" class="as-color regular-text" style="width:120px"

							name="<?php echo esc_attr(Options::KEY); ?>[ui][input_border_color]"

							value="<?php echo esc_attr((string)($opts['ui']['input_border_color'] ?? '#dddddd')); ?>">

							<p class="description"><?php echo esc_html__('Color del borde de los inputs.', 'acceso-seguro'); ?></p>

						</td>

						</tr>



						<tr>

						<th><?php echo esc_html__('Grosor borde inputs (px)', 'acceso-seguro'); ?></th>

						<td>

							<input type="number" min="0" max="6" step="1"

							name="<?php echo esc_attr(Options::KEY); ?>[ui][input_border_width]"

							value="<?php echo esc_attr((int)($opts['ui']['input_border_width'] ?? 1)); ?>">

							<p class="description"><?php echo esc_html__('0 = sin borde. Recomendado 1–2.', 'acceso-seguro'); ?></p>

						</td>

						</tr>



						<tr>

							<th><?php echo esc_html__('Color de fondo', 'acceso-seguro'); ?></th>

							<td>

								<input type="color"

									class="as-color regular-text"

									name="<?php echo esc_attr(Options::KEY); ?>[ui][bg_color]"

									value="<?php echo esc_attr((string)($opts['ui']['bg_color'] ?? '#ffffff')); ?>">

							</td>

						</tr>



						<tr>

							<th><?php echo esc_html__('Color de texto', 'acceso-seguro'); ?></th>

							<td>

								<input type="color"

									class="as-color regular-text"

									name="<?php echo esc_attr(Options::KEY); ?>[ui][text_color]"

									value="<?php echo esc_attr((string)($opts['ui']['text_color'] ?? '#111111')); ?>">

							</td>

						</tr>

					</tbody>

				</table>



				<p>

				<button type="button" class="button" id="as-ui-reset-colors">

					<?php echo esc_html__('Restablecer colores del modal', 'acceso-seguro'); ?>

				</button>

				</p>



				<p class="description">

					<?php echo esc_html__('Estos ajustes se aplican al popup mediante variables CSS, sin necesidad de escribir CSS.', 'acceso-seguro'); ?>

				</p>



				<hr>



				<h2><?php echo esc_html__('Seguridad', 'acceso-seguro'); ?></h2>



				<p>

					<label>

						<input type="checkbox" name="<?php echo esc_attr(Options::KEY); ?>[general][rate_limit_enabled]" value="1"

							<?php checked(!empty($opts['general']['rate_limit_enabled'])); ?>>

						<?php echo esc_html__('Activar limitación de intentos (rate limit)', 'acceso-seguro'); ?>

					</label>

				</p>



				<p>

					<label>

						<?php echo esc_html__('Almacenamiento del rate limit', 'acceso-seguro'); ?>

						<select name="<?php echo esc_attr(Options::KEY); ?>[general][rate_limit_storage]">

							<option value="transient" <?php selected(($opts['general']['rate_limit_storage'] ?? 'transient'), 'transient'); ?>>

								<?php echo esc_html__('Transients (rápido, recomendado)', 'acceso-seguro'); ?>

							</option>

							<option value="db" <?php selected(($opts['general']['rate_limit_storage'] ?? 'transient'), 'db'); ?>>

								<?php echo esc_html__('Base de datos (persistente)', 'acceso-seguro'); ?>

							</option>

						</select>

					</label>

				</p>



				<table class="widefat striped" style="max-width:720px;">

					<thead>

						<tr>

							<th><?php echo esc_html__('Acción', 'acceso-seguro'); ?></th>

							<th><?php echo esc_html__('Ventana (seg)', 'acceso-seguro'); ?></th>

							<th><?php echo esc_html__('Máx intentos', 'acceso-seguro'); ?></th>

						</tr>

					</thead>

					<tbody>

						<tr>

							<td><?php echo esc_html__('Login', 'acceso-seguro'); ?></td>

							<td>

								<input type="number" min="30" step="1"

									name="<?php echo esc_attr(Options::KEY); ?>[general][rate_limit_login_window_seconds]"

									value="<?php echo esc_attr((int)($opts['general']['rate_limit_login_window_seconds'] ?? 300)); ?>">

							</td>

							<td>

								<input type="number" min="1" step="1"

									name="<?php echo esc_attr(Options::KEY); ?>[general][rate_limit_login_max_attempts]"

									value="<?php echo esc_attr((int)($opts['general']['rate_limit_login_max_attempts'] ?? 8)); ?>">

							</td>

						</tr>



						<tr>

							<td><?php echo esc_html__('Registro', 'acceso-seguro'); ?></td>

							<td>

								<input type="number" min="30" step="1"

									name="<?php echo esc_attr(Options::KEY); ?>[general][rate_limit_register_window_seconds]"

									value="<?php echo esc_attr((int)($opts['general']['rate_limit_register_window_seconds'] ?? 900)); ?>">

							</td>

							<td>

								<input type="number" min="1" step="1"

									name="<?php echo esc_attr(Options::KEY); ?>[general][rate_limit_register_max_attempts]"

									value="<?php echo esc_attr((int)($opts['general']['rate_limit_register_max_attempts'] ?? 4)); ?>">

							</td>

						</tr>



						<tr>

							<td><?php echo esc_html__('Recuperar contraseña', 'acceso-seguro'); ?></td>

							<td>

								<input type="number" min="30" step="1"

									name="<?php echo esc_attr(Options::KEY); ?>[general][rate_limit_forgot_window_seconds]"

									value="<?php echo esc_attr((int)($opts['general']['rate_limit_forgot_window_seconds'] ?? 900)); ?>">

							</td>

							<td>

								<input type="number" min="1" step="1"

									name="<?php echo esc_attr(Options::KEY); ?>[general][rate_limit_forgot_max_attempts]"

									value="<?php echo esc_attr((int)($opts['general']['rate_limit_forgot_max_attempts'] ?? 4)); ?>">

							</td>

						</tr>

					</tbody>

				</table>



				<p>

					<label>

						<input type="checkbox" name="<?php echo esc_attr(Options::KEY); ?>[general][score_enabled]" value="1"

							<?php checked(!empty($opts['general']['score_enabled'])); ?>>

						<?php echo esc_html__('Activar score dinámico (sumar señales)', 'acceso-seguro'); ?>

					</label>

				</p>



				<p>

					<label>

						<?php echo esc_html__('Umbral de bloqueo (score)', 'acceso-seguro'); ?>

						<input type="number" min="1" step="1"

							name="<?php echo esc_attr(Options::KEY); ?>[general][score_threshold]"

							value="<?php echo esc_attr((int)($opts['general']['score_threshold'] ?? 8)); ?>">

					</label>

				</p>



								<p>

					<label>

						<input type="checkbox" name="<?php echo esc_attr(Options::KEY); ?>[general][progressive_enabled]" value="1"

							<?php checked(!empty($opts['general']['progressive_enabled'])); ?>>

						<?php echo esc_html__('Activar bloqueo progresivo por reincidencia', 'acceso-seguro'); ?>

					</label>

				</p>



				<p>

					<label>

						<?php echo esc_html__('Pasos (minutos, separados por coma)', 'acceso-seguro'); ?>

						<?php

						$stepsVal = $opts['general']['progressive_steps_minutes'] ?? [5,30,120,1440];

						$stepsTxt = is_array($stepsVal) ? implode(',', array_map('strval', $stepsVal)) : (string) $stepsVal;

						?>



						<input type="text" style="width:280px"

						name="<?php echo esc_attr(Options::KEY); ?>[general][progressive_steps_minutes]"

						value="<?php echo esc_attr($stepsTxt); ?>">

					</label>

				</p>



				<h2>Puntuación</h2>

				<p>

					<label>Umbral de bloqueo (score)<br>

					<input type="number" min="1" class="small-text"

						name="<?php echo esc_attr(Options::KEY); ?>[scoring][deny_threshold]"

						value="<?php echo esc_attr((int)($opts['scoring']['deny_threshold'] ?? 70)); ?>">

					</label>

				</p>



				<hr>



				<h2>Email</h2>

				<label>

					<input type="checkbox" name="<?php echo esc_attr(Options::KEY); ?>[email][enabled]" value="1"

						<?php checked(!empty($opts['email']['enabled'])); ?>>

					Activar reglas de email

				</label>



				<p>

					<label>

					<input type="checkbox" name="<?php echo esc_attr(Options::KEY); ?>[email][check_mx]" value="1"

						<?php checked(!empty($opts['email']['check_mx'])); ?>>

					Comprobar MX (si falta, suma puntos)

					</label>

				</p>



				<p>

					<label>Puntos si falta MX<br>

					<input type="number" min="0" class="small-text"

						name="<?php echo esc_attr(Options::KEY); ?>[email][mx_no_record_points]"

						value="<?php echo esc_attr((int)($opts['email']['mx_no_record_points'] ?? 45)); ?>">

					</label>

				</p>



				<p>

					<label>

						TLDs bloqueados (uno por línea o separados por coma)<br>

						<textarea

							class="large-text as-tld-input"

							rows="5"

							name="<?php echo esc_attr(Options::KEY); ?>[email][block_tlds]"

							placeholder="Ejemplo: xyz, top, website, online (sin punto inicial)"

						><?php echo esc_textarea(implode("\n", (array)($opts['email']['block_tlds'] ?? []))); ?></textarea>

						<p class="description as-warning-tld" style="display:none; color:#b32d2e;">

							⚠️ Atención: has escrito uno o varios TLDs con punto inicial (por ejemplo <code>.xyz</code>).

							El sistema espera <strong>xyz</strong> (sin punto). Corrígelo para que el bloqueo funcione.

						</p>

					</label>

				</p>



				<p class="description">

					Introduce los TLDs <strong>sin el punto inicial</strong>.  

					Ejemplo: <code>xyz</code>, <code>top</code>, <code>website</code>.  

					Escribir <code>.xyz</code> no bloqueará el registro.

				</p>



				<p>

					<label>Dominios bloqueados (emails temporales)<br>

					<textarea class="large-text" rows="6"

						name="<?php echo esc_attr(Options::KEY); ?>[email][block_domains]"><?php echo esc_textarea(implode("\n", (array)($opts['email']['block_domains'] ?? []))); ?></textarea>

					</label>

				</p>



				<hr>



				<h2>Nombre de usuario</h2>

				<label>

					<input type="checkbox" name="<?php echo esc_attr(Options::KEY); ?>[username][enabled]" value="1"

						<?php checked(!empty($opts['username']['enabled'])); ?>>

					Activar reglas de username

				</label>



				<p>

					<label>Longitud mínima<br>

					<input type="number" min="1" class="small-text"

						name="<?php echo esc_attr(Options::KEY); ?>[username][min_length]"

						value="<?php echo esc_attr((int)($opts['username']['min_length'] ?? 6)); ?>">

					</label>

				</p>



				<p>

					<label>Ratio mínimo de vocales (0–1)<br>

					<input type="number" step="0.01" min="0" max="1" class="small-text"

						name="<?php echo esc_attr(Options::KEY); ?>[username][min_vowel_ratio]"

						value="<?php echo esc_attr((float)($opts['username']['min_vowel_ratio'] ?? 0.25)); ?>">

					</label>

				</p>



				<p>

					<label>Racha máxima de consonantes (>= bloquea por score)<br>

					<input type="number" min="1" class="small-text"

						name="<?php echo esc_attr(Options::KEY); ?>[username][max_consonant_run]"

						value="<?php echo esc_attr((int)($opts['username']['max_consonant_run'] ?? 6)); ?>">

					</label>

				</p>



				<p>

					<label>Puntos patrón nombre.apellido<br>

					<input type="number" min="0" class="small-text"

						name="<?php echo esc_attr(Options::KEY); ?>[username][name_dot_surname_points]"

						value="<?php echo esc_attr((int)($opts['username']['name_dot_surname_points'] ?? 15)); ?>">

					</label>

				</p>



				<p>

					<label>Puntos si username corto<br>

					<input type="number" min="0" class="small-text"

						name="<?php echo esc_attr(Options::KEY); ?>[username][short_points]"

						value="<?php echo esc_attr((int)($opts['username']['short_points'] ?? 40)); ?>">

					</label>

				</p>



				<p>

					<label>Puntos ratio vocales bajo<br>

					<input type="number" min="0" class="small-text"

						name="<?php echo esc_attr(Options::KEY); ?>[username][low_vowel_points]"

						value="<?php echo esc_attr((int)($opts['username']['low_vowel_points'] ?? 30)); ?>">

					</label>

				</p>



				<p>

					<label>Puntos racha consonantes larga<br>

					<input type="number" min="0" class="small-text"

						name="<?php echo esc_attr(Options::KEY); ?>[username][consonant_run_points]"

						value="<?php echo esc_attr((int)($opts['username']['consonant_run_points'] ?? 30)); ?>">

					</label>

				</p>

				<p>
					<label>
						<input
							type="checkbox"
							name="<?php echo esc_attr(Options::KEY); ?>[username][suspicious_fragments_enabled]"
							value="1"
							<?php checked(!empty($opts['username']['suspicious_fragments_enabled'])); ?>
						>
						Detectar palabras o fragmentos sospechosos
					</label>
				</p>

				<p>
					<label>
						Puntos por palabras o fragmentos sospechosos<br>
						<input
							type="number"
							min="0"
							class="small-text"
							name="<?php echo esc_attr(Options::KEY); ?>[username][suspicious_fragments_points]"
							value="<?php echo esc_attr((int)($opts['username']['suspicious_fragments_points'] ?? 30)); ?>"
						>
					</label>
				</p>

				<p>
					<label>
						Palabras o fragmentos sospechosos (uno por línea)<br>
						<textarea
							class="large-text"
							rows="10"
							name="<?php echo esc_attr(Options::KEY); ?>[username][suspicious_fragments]"
						><?php echo esc_textarea(implode("\n", (array)($opts['username']['suspicious_fragments'] ?? []))); ?></textarea>
					</label>
				</p>

				<p class="description">
					La comparación no distingue entre mayúsculas y minúsculas.
					Si el nombre de usuario contiene cualquiera de estos fragmentos,
					se aplicará una sola vez la puntuación indicada.
				</p>



				<hr>



				<h2>Logs</h2>

				<label>

					<input type="checkbox" name="<?php echo esc_attr(Options::KEY); ?>[logging][enabled]" value="1"

						<?php checked(!empty($opts['logging']['enabled'])); ?>>

					Activar logging interno

				</label>



				<p>

					<label>

					<input type="checkbox" name="<?php echo esc_attr(Options::KEY); ?>[logging][hash_ip]" value="1"

						<?php checked(!empty($opts['logging']['hash_ip'])); ?>>

					Guardar IP hasheada (recomendado)

					</label>

				</p>



				<p>

					<label>Retención (días)<br>

					<input type="number" min="1" class="small-text"

						name="<?php echo esc_attr(Options::KEY); ?>[logging][retention_days]"

						value="<?php echo esc_attr((int)($opts['logging']['retention_days'] ?? 60)); ?>">

					</label>

				</p>



				<?php submit_button('Guardar cambios'); ?>

			</form>

		</div>

		<?php

	}

}

