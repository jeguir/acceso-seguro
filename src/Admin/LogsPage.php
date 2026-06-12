<?php
namespace AccesoSeguro\Admin;

use AccesoSeguro\Infra\LogRepository;

defined('ABSPATH') || exit;

final class LogsPage {

	public static function renderStatic(): void {
		(new self())->render();
	}

	public function render(): void {
		if (!current_user_can('manage_options')) return;

		global $wpdb;
		$table = LogRepository::table();

		// Export CSV
		if (isset($_GET['export']) && $_GET['export'] === 'csv') {
			$this->exportCsv($table);
			return;
		}

		// Vaciar logs (con nonce)
		if (isset($_GET['purge']) && $_GET['purge'] === '1') {
			$this->purgeLogs($table);
			return;
		}

		// ¿Vista detalle?
		$view = isset($_GET['view']) ? sanitize_key((string)$_GET['view']) : '';
		if ($view === 'detail') {
			$this->renderDetail($table);
			return;
		}

		$actionFilter = isset($_GET['action_filter']) ? sanitize_key((string)$_GET['action_filter']) : '';
		$reasonFilter = isset($_GET['reason_filter']) ? sanitize_text_field((string)$_GET['reason_filter']) : '';
		$reasons = $wpdb->get_col("SELECT DISTINCT reason_code FROM {$table} ORDER BY reason_code ASC");
		$reasons = is_array($reasons) ? $reasons : [];
		if ($reasonFilter !== '' && !in_array($reasonFilter, $reasons, true)) {
			$reasonFilter = '';
		}

		$allowedActions = ['','login','register'];
		if (!in_array($actionFilter, $allowedActions, true)) {
			$actionFilter = '';
		}

		$limit = 50;
		
		$whereParts = [];
		$params = [];

		if ($actionFilter !== '') {
			$whereParts[] = "action = %s";
			$params[] = $actionFilter;
		}
		if ($reasonFilter !== '') {
			$whereParts[] = "reason_code = %s";
			$params[] = $reasonFilter;
		}

		$where = '';
		if (!empty($whereParts)) {
			$where = "WHERE " . implode(' AND ', $whereParts);
		}

		$sql = "SELECT id, created_at, action, source, reason_code, score, ip_hash
				FROM {$table}
				{$where}
				ORDER BY id DESC
				LIMIT {$limit}";

		$rows = empty($params)
			? $wpdb->get_results($sql, ARRAY_A)
			: $wpdb->get_results($wpdb->prepare($sql, ...$params), ARRAY_A);

		?>
		<div class="wrap">
			<h1>Acceso Seguro – Log antispam</h1>
			<p>Mostrando los últimos <?php echo (int)$limit; ?> intentos bloqueados.</p>

			<form method="get" style="margin: 10px 0;">
				<input type="hidden" name="page" value="acceso-seguro-logs">
				<label for="action_filter"><strong>Acción:</strong></label>
				<select name="action_filter" id="action_filter">
					<option value="" <?php selected($actionFilter, ''); ?>>Todas</option>
					<option value="register" <?php selected($actionFilter, 'register'); ?>>Registro</option>
					<option value="login" <?php selected($actionFilter, 'login'); ?>>Login</option>
				</select>
				<label for="reason_filter" style="margin-left:10px;"><strong>Motivo:</strong></label>
				<select name="reason_filter" id="reason_filter">
					<option value="" <?php selected($reasonFilter, ''); ?>>Todos</option>
					<?php foreach ($reasons as $rc) : ?>
						<option value="<?php echo esc_attr($rc); ?>" <?php selected($reasonFilter, $rc); ?>>
							<?php echo esc_html($rc); ?>
						</option>
					<?php endforeach; ?>
				</select>
				<button class="button">Filtrar</button>
				<?php if ($actionFilter !== '') :
					$clearUrl = add_query_arg(['page'=>'acceso-seguro-logs'], admin_url('admin.php'));
				?>
					<a class="button button-secondary" href="<?php echo esc_url($clearUrl); ?>">Quitar filtro</a>
				<?php endif; ?>
				<?php
				$exportArgs = ['page' => 'acceso-seguro-logs', 'export' => 'csv'];
				if ($actionFilter !== '') $exportArgs['action_filter'] = $actionFilter;
				if ($reasonFilter !== '') $exportArgs['reason_filter'] = $reasonFilter;
				$exportUrl = add_query_arg($exportArgs, admin_url('admin.php'));
				?>
				<a class="button button-secondary" href="<?php echo esc_url($exportUrl); ?>">Exportar CSV</a>
				<?php
				$purgeUrl = wp_nonce_url(
					add_query_arg(['page' => 'acceso-seguro-logs', 'purge' => '1'], admin_url('admin.php')),
					'as_purge_logs'
				);
				?>
				<a class="button button-danger"
				href="<?php echo esc_url($purgeUrl); ?>"
				onclick="return confirm('¿Seguro que quieres vaciar todos los logs?');">
					Vaciar logs
				</a>
			</form>

			<table class="widefat striped">
				<thead>
					<tr>
						<th>ID</th>
						<th>Fecha</th>
						<th>Acción</th>
						<th>Origen</th>
						<th>Motivo</th>
						<th>Score</th>
						<th>IP (hash)</th>
						<th>Detalles</th>
					</tr>
				</thead>
				<tbody>
				<?php if (empty($rows)) : ?>
					<tr><td colspan="8">Sin registros todavía.</td></tr>
				<?php else : ?>
					<?php foreach ($rows as $r) :
						$args = [
							'page' => 'acceso-seguro-logs',
							'view' => 'detail',
							'id'   => (int)$r['id'],
						];

						if ($actionFilter !== '') {
							$args['action_filter'] = $actionFilter;
						}

						if ($reasonFilter !== '') {
							$args['reason_filter'] = $reasonFilter;
						}

						$detailUrl = add_query_arg($args, admin_url('admin.php'));
					?>
						<tr>
							<td><?php echo (int)$r['id']; ?></td>
							<td>
								<?php
									$ts = strtotime($r['created_at'] . ' UTC');
									echo esc_html( wp_date('Y-m-d H:i:s', $ts) );
								?>
							</td>
							<td><?php echo esc_html($r['action']); ?></td>
							<td><?php echo esc_html($r['source']); ?></td>
							<td><?php echo esc_html($r['reason_code']); ?></td>
							<td><?php echo esc_html($r['score']); ?></td>
							<td><code><?php echo esc_html($r['ip_hash']); ?></code></td>
							<td><a href="<?php echo esc_url($detailUrl); ?>">Ver detalles</a></td>
						</tr>
					<?php endforeach; ?>
				<?php endif; ?>
				</tbody>
			</table>
		</div>
		<?php
	}

	private function renderDetail(string $table): void {
		if (!current_user_can('manage_options')) return;

		global $wpdb;
		$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

		$row = null;
		if ($id > 0) {
			$row = $wpdb->get_row(
				$wpdb->prepare("SELECT * FROM {$table} WHERE id = %d", $id),
				ARRAY_A
			);
		}

		$args = ['page' => 'acceso-seguro-logs'];

		if (!empty($_GET['action_filter'])) {
			$args['action_filter'] = sanitize_key((string)$_GET['action_filter']);
		}
		if (!empty($_GET['reason_filter'])) {
			$args['reason_filter'] = sanitize_text_field((string)$_GET['reason_filter']);
		}

		$backUrl = add_query_arg($args, admin_url('admin.php'));

		?>
		<div class="wrap">
			<h1>Acceso Seguro – Detalle de log</h1>
			<p><a href="<?php echo esc_url($backUrl); ?>">&larr; Volver al log</a></p>

			<?php if (!$row) : ?>
				<div class="notice notice-error"><p>No se ha encontrado el registro.</p></div>
			<?php else :
				$meta = [];
				if (!empty($row['meta_json'])) {
					$decoded = json_decode((string)$row['meta_json'], true);
					if (is_array($decoded)) $meta = $decoded;
				}

				// Info de bloqueo progresivo (si existe)
				$blockInfo = null;
				if (($row['reason_code'] ?? '') === 'PROGRESSIVE_BLOCK' && !empty($row['ip_hash']) && !empty($row['action'])) {
					$blocksTable = $wpdb->prefix . 'as_blocks';
					$blockInfo = $wpdb->get_row(
						$wpdb->prepare(
							"SELECT strikes, until_ts, last_ts FROM {$blocksTable} WHERE ip_hash = %s AND action = %s",
							(string) $row['ip_hash'],
							(string) $row['action']
						),
						ARRAY_A
					);
				}
			?>
				<table class="widefat striped">
					<tbody>
						<tr><th>ID</th><td><?php echo (int)$row['id']; ?></td></tr>
						<tr>
							<th>Fecha</th>
							<td>
								<?php
									$ts = strtotime($row['created_at'] . ' UTC');
									echo esc_html( wp_date('Y-m-d H:i:s', $ts) );
								?>
							</td>
						</tr>
						<tr><th>Acción</th><td><?php echo esc_html($row['action']); ?></td></tr>
						<tr><th>Origen</th><td><?php echo esc_html($row['source']); ?></td></tr>
						<tr><th>Motivo</th><td><?php echo esc_html($row['reason_code']); ?></td></tr>
						<tr><th>Score</th><td><?php echo esc_html($row['score']); ?></td></tr>
						<tr><th>IP (hash)</th><td><code><?php echo esc_html($row['ip_hash']); ?></code></td></tr>
					</tbody>
				</table>

				<?php if (is_array($blockInfo) && !empty($blockInfo)) :
					$now = time();
					$strikes = (int) ($blockInfo['strikes'] ?? 0);
					$until   = (int) ($blockInfo['until_ts'] ?? 0);
					$isActive = $until > $now;
				?>
					<h2>Bloqueo progresivo</h2>
					<table class="widefat striped">
						<tbody>
							<tr><th>Reincidencias (strikes)</th><td><?php echo esc_html((string)$strikes); ?></td></tr>
							<tr>
								<th>Bloqueado hasta</th>
								<td>
									<?php
										echo esc_html( wp_date('Y-m-d H:i:s', $until) );
										echo $isActive ? ' <strong>(activo)</strong>' : ' (expirado)';
									?>
								</td>
							</tr>
						</tbody>
					</table>
				<?php endif; ?>

				<h2>Señales</h2>
				<textarea class="large-text" rows="18" readonly><?php echo esc_textarea(wp_json_encode($meta, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)); ?></textarea>
			<?php endif; ?>
		</div>
		<?php
	}

	private function exportCsv(string $table): void {
		if (!current_user_can('manage_options')) {
			wp_die('No autorizado');
		}

		global $wpdb;

		$actionFilter = isset($_GET['action_filter']) ? sanitize_key((string)$_GET['action_filter']) : '';
		$allowedActions = ['','login','register'];
		if (!in_array($actionFilter, $allowedActions, true)) {
			$actionFilter = '';
		}

		$reasonFilter = isset($_GET['reason_filter']) ? sanitize_text_field((string)$_GET['reason_filter']) : '';

		$whereParts = [];
		$params = [];

		if ($actionFilter !== '') {
			$whereParts[] = "action = %s";
			$params[] = $actionFilter;
		}
		if ($reasonFilter !== '') {
			$whereParts[] = "reason_code = %s";
			$params[] = $reasonFilter;
		}

		$where = '';
		if (!empty($whereParts)) {
			$where = "WHERE " . implode(' AND ', $whereParts);
		}

		$sql = "SELECT created_at, action, source, reason_code, score, ip_hash, meta_json
				FROM {$table}
				{$where}
				ORDER BY id DESC";

		$rows = empty($params)
			? $wpdb->get_results($sql, ARRAY_A)
			: $wpdb->get_results($wpdb->prepare($sql, ...$params), ARRAY_A);

		// Limpia cualquier salida previa para que no se cuele HTML en el CSV
		while (ob_get_level() > 0) {
			ob_end_clean();
		}

		nocache_headers();
		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename=acceso-seguro-logs.csv');

		$out = fopen('php://output', 'w');
		fputcsv($out, ['created_at','action','source','reason_code','score','ip_hash','signals_json']);

		foreach ($rows as $r) {
			$signals = '';
			if (!empty($r['meta_json'])) {
				$signals = (string) $r['meta_json'];
			}
			fputcsv($out, [
				$r['created_at'] ?? '',
				$r['action'] ?? '',
				$r['source'] ?? '',
				$r['reason_code'] ?? '',
				$r['score'] ?? '',
				$r['ip_hash'] ?? '',
				$signals,
			]);
		}

		fclose($out);
		exit;
	}

	private function purgeLogs(string $table): void {
		if (!current_user_can('manage_options')) {
			wp_die('No autorizado');
		}

		check_admin_referer('as_purge_logs');

		global $wpdb;
		$wpdb->query("TRUNCATE TABLE {$table}");

		$backUrl = add_query_arg(['page' => 'acceso-seguro-logs'], admin_url('admin.php'));
		wp_safe_redirect($backUrl);
		exit;
	}
}
