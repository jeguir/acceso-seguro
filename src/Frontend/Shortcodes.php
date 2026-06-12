<?php
namespace AccesoSeguro\Frontend;

defined('ABSPATH') || exit;

final class Shortcodes {

	public function register(): void {
		add_shortcode('acceso_seguro_popup', [$this, 'renderPopup']);
		add_shortcode('acceso_seguro_boton', [$this, 'renderButton']);
	}

	public function renderPopup(): string {
		if (is_user_logged_in()) {
			return '';
		}

		// Marcamos que el popup se va a usar (para cargar assets luego)
		if (!defined('AS_POPUP_ENABLED')) {
			define('AS_POPUP_ENABLED', true);
		}

		$allowRegister = true;
		if (class_exists('\AccesoSeguro\Infra\Options')) {
			$allowRegister = \AccesoSeguro\Infra\Options::get('general.allow_register', true);
		}

		ob_start();
		?>
		<div id="as-auth-modal" style="display:none;">
			<div id="as-auth-box">
				<div id="as-auth-msg" style="display:none;"></div>
				<div id="as-login-block">
					<h3><?php echo esc_html__('Acceder', 'acceso-seguro'); ?></h3>

					<form id="as-login-form">
						<p>
							<label>Email o usuario</label>
							<input type="text" name="login" required>
						</p>
						<p>
							<label>Contraseña</label>
							<input type="password" name="password" required>
						</p>
						<p>
							<button type="submit">Entrar</button>
						</p>
					</form>

					<p style="margin-top:8px;">
						<a href="#" id="as-open-forgot"><?php echo esc_html__('¿Has olvidado tu contraseña?', 'acceso-seguro'); ?></a>
					</p>

					<?php if ($allowRegister) : ?>
						<p style="margin-top:8px;">
							<a href="#" id="as-open-register">¿No tienes cuenta? Regístrate</a>
						</p>
					<?php endif; ?>
				</div>

				<form id="as-forgot-form" style="display:none;">
					<p>
						<label>Email</label>
						<input type="email" name="email" required>
					</p>
					<p class="grid">
						<button type="submit">Enviar enlace de recuperación</button>
						<button type="button" id="as-cancel-forgot">Cancelar</button>
					</p>
				</form>

				<?php if ($allowRegister) : ?>
				<div id="as-register-block" style="display:none;">

					<h3><?php echo esc_html__('Registrarse', 'acceso-seguro'); ?></h3>
					<form id="as-register-form">
						<p>
							<label><?php echo esc_html__('Nombre / Razón social', 'acceso-seguro'); ?></label>
							<input type="text" name="first_name">
						</p>
						<p>
							<label>Email</label>
							<input type="email" name="email" required>
						</p>
						<p>
							<label>Contraseña</label>
							<input type="password" name="password" required>
						</p>

						<?php
							$privacyId = (int) \AccesoSeguro\Infra\Options::get('general.privacy_page_id', 0);

							if ($privacyId > 0) {
								$privacyUrl = get_permalink($privacyId);
							} else {
								$privacyUrl = function_exists('get_privacy_policy_url') ? get_privacy_policy_url() : '';
							}

							if (!$privacyUrl) {
								$privacyUrl = home_url('/');
							}
						?>
						<p>
							<label class="acepto-privacidad">
								<input type="checkbox" name="privacy" required>
								<?php echo wp_kses_post(sprintf(
									__('He leído y acepto la <a href="%s" target="_blank" rel="noopener noreferrer">Política de privacidad</a>.', 'acceso-seguro'),
									esc_url($privacyUrl)
								)); ?>
							</label>
						</p>
						<div style="position:absolute; left:-9999px; top:auto; width:1px; height:1px; overflow:hidden;">
							<label><?php echo esc_html__('No rellenar este campo', 'acceso-seguro'); ?></label>
							<input type="text" name="as_hp" value="" tabindex="-1" autocomplete="off">
						</div>
						<p>
							<button type="submit">Crear cuenta</button>
						</p>
					</form>
					<p style="margin-top:8px;">
						<a href="#" id="as-open-login">¿Ya tienes cuenta? Accede</a>
					</p>
				</div>
				<?php endif; ?>

				<p><button type="button" id="as-close-modal">Cerrar</button></p>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	public function renderButton($atts): string {
		$atts = shortcode_atts([
			'texto' => __('Acceso / Registro', 'acceso-seguro'),
			'texto_salir' => __('Salir', 'acceso-seguro'),
		], is_array($atts) ? $atts : []);

		$redirect = wp_validate_redirect(wp_get_referer() ?: home_url('/'), home_url('/'));

		if (is_user_logged_in()) {
			$logoutUrl = wp_logout_url($redirect);
			return '<a href="' . esc_url($logoutUrl) . '" class="btn-acceso">' . esc_html($atts['texto_salir']) . '</a>';
		}

		return '<a href="#" class="btn-acceso" data-as-auth-open>' . esc_html($atts['texto']) . '</a>';
	}
}
