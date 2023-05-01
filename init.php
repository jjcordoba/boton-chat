<?php
/*
Plugin Name: Boton chat
Plugin URI: 
Description: Este es un plugin de WhatsApp que permite agregar un botón flotante en todo el sitio web y otro botón en la página de producto si está activado WooCommerce. Este plugin es útil porque permite a los visitantes del sitio web comunicarse de manera rápida y sencilla con el propietario del sitio web a través de WhatsApp
Version: 1.2
Author: Juvenal Tarrillo
Author URI: https://web.facebook.com/JOSECOCOLIFE
License:  GPLv2
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: chat
*/



require_once "CDC09D102.php";
class Botonwhatsapp_MDC09D102
{
	public $plugin_file = __FILE__;
	public $responseObj;
	public $licenseMessage;
	public $showMessage = false;
	public $slug = "Boton chat";
	function __construct()
	{
		add_action('admin_print_styles', [$this, 'SetAdminStyle']);
		$licenseKey = get_option("Botonwhatsapp_lic_Key", "");
		$liceEmail = get_option("Botonwhatsapp_lic_email", "");
		CDC09D102::addOnDelete(function () {
			delete_option("Botonwhatsapp_lic_Key");
		});
		if (CDC09D102::CheckWPPlugin($licenseKey, $liceEmail, $this->licenseMessage, $this->responseObj, __FILE__)) {
			add_action('admin_menu', [$this, 'ActiveAdminMenu'], 99999);
			add_action('admin_post_Botonwhatsapp_el_deactivate_license', [$this, 'action_deactivate_license']);
			//$this->licenselMessage=$this->mess;

			require_once(ABSPATH . 'wp-admin/includes/admin.php');

			add_action('admin_menu', 'ma_whatsapp_plugin_menu');
			function ma_whatsapp_plugin_menu()
			{
				add_menu_page(
					'Ajustes Chat',
					'Boton chat',
					'manage_options',
					'Boton chat',
					'ma_whatsapp_plugin_options',
					'dashicons-whatsapp',
					2
				);

				add_submenu_page(
					'Boton chat',
					'Boton WooCommerce ',
					'Boton WooCommerce  ',
					'manage_options',
					'ma-whatsapp-submenu',
					'ma_whatsapp_submenu_callback'
				);
			}

			// Función de callback para la página de opciones del plugin
			function ma_whatsapp_plugin_options()
			{
?>
				<div class="wrap">
					<form method="post" action="options.php">
						<?php settings_fields('ma_whatsapp_options'); ?>
						<?php do_settings_sections('Boton chat'); ?>
						<?php submit_button(); ?>
					</form>
				</div>
			<?php
			}

			// Función de callback para el submenú
			function ma_whatsapp_submenu_callback()
			{
				// Guarda los valores actuales del número de teléfono y mensaje
				$phone = get_option('ma_whatsapp_phone');
				$message = get_option('ma_whatsapp_message');

				// Actualiza los valores si se envía el formulario
				if (isset($_POST['ma_whatsapp_form_submitted'])) {
					$phone = $_POST['ma_whatsapp_phone'];
					$message = $_POST['ma_whatsapp_message'];
					update_option('ma_whatsapp_phone', $phone);
					update_option('ma_whatsapp_message', $message);
				}

				// Muestra el formulario
			?>
				<div class="wrap">
					<h1>Whatsapp WooCommerce </h1>
					<form method="post" action="">
						<input type="hidden" name="ma_whatsapp_form_submitted" value="1">
						<label for="ma_whatsapp_phone">Número de teléfono:</label>
						<input class="numerowoo" type="text" name="ma_whatsapp_phone" value="<?php echo $phone; ?>">
						<br>
						<label for="ma_whatsapp_message">Mensaje predefinido:</label>
						<input class="mensajewoo" type="text" name="ma_whatsapp_message" value="<?php echo $message; ?>">
						<br>
						<input class="guardar" type="submit" value="Guardar cambios">

					</form>
				</div>
			<?php
			}

			// Registrar la opción del plugin
			add_action('admin_init', 'ma_whatsapp_register_settings');
			function ma_whatsapp_register_settings()
			{
				register_setting('ma_whatsapp_options', 'ma_whatsapp_options');
				add_settings_section(
					'ma_whatsapp_section',
					'Ajustes del boton del popup',
					'ma_whatsapp_section_callback',
					'Boton chat'
				);
				add_settings_field(
					'ma_whatsapp_tel',
					'Número de teléfono',
					'ma_whatsapp_tel_callback',
					'Boton chat',
					'ma_whatsapp_section'
				);
				add_settings_field(
					'ma_whatsapp_ms',
					'Mensaje por defecto',
					'ma_whatsapp_ms_callback',
					'Boton chat',
					'ma_whatsapp_section'
				);
			}

			// Funciones de callback para los campos de opciones del plugin
			function ma_whatsapp_section_callback()
			{
				echo '<p>Ingrese los siguientes datos:</p>';
			}

			function ma_whatsapp_tel_callback()
			{
				$options = get_option('ma_whatsapp_options');
				$tel = isset($options['tel']) ? $options['tel'] : '';
				echo "<input type='text' name='ma_whatsapp_options[tel]' value='$tel' />";
			}

			function ma_whatsapp_ms_callback()
			{
				$options = get_option('ma_whatsapp_options');
				$ms = isset($options['ms']) ? $options['ms'] : '';
				echo "<input type='text' name='ma_whatsapp_options[ms]' value='$ms' />";
			}

			// Función para obtener la configuración del plugin
			function ma_whatsapp_get_options()
			{
				$options = get_option('ma_whatsapp_options');
				$tel = isset($options['tel']) ? $options['tel'] : '51950602619';
				$ms = isset($options['ms']) ? urlencode($options['ms']) : urlencode('Hola nesesito ayda con tu plugin de:');
				return array('tel' => $tel, 'ms' => $ms);
			}


			// Función para mostrar el icono de WhatsApp en el sitio web
			add_action('wp_footer', 'ma_add_footer_whatsapp');
			function ma_add_footer_whatsapp()
			{
				$options = get_option('ma_whatsapp_options');
				$tel = isset($options['tel']) ? $options['tel'] : '51950602619';
				$ms = isset($options['ms']) ? urlencode($options['ms']) : urlencode('Hola nesesito ayda con tu plugin de:');

				$url = "https://wa.me/${tel}?text=${ms}";
				$img = plugin_dir_url(__FILE__) . "adjuntos/img/whatsapp-icon.svg";
				echo "<div id='float-whatsapp' class='btncontact'>";
				echo " <a href='javascript:void(0)' onclick='window.open(\"${url}\", \"_blank\")'>";
				echo " <img src='${img}' width=60 height=60 />";
				echo " </a>";
				echo "</div>";
			}


			//Agrega el botón de Whatsapp al botón de Compartir de WooCommerce
			add_action('woocommerce_share', 'ma_question_whatsapp');
			function ma_question_whatsapp()
			{
				$phone = get_option('ma_whatsapp_phone');
				$message = get_option('ma_whatsapp_message');
				$text = 'Preguntar por Whatsapp';
				$ico = '<img src="' . plugins_url('adjuntos/img/whatsapp-icon.svg', __FILE__) . '" width=20 height=20 />';
				$url = 'https://api.whatsapp.com/send?phone=' . $phone . '&amp;text=' . str_replace(' ', '%20', $message);
				$link = '<a class="btnproducto" href="' . $url . '" target="_blank">' . $ico . ' <span>' . $text . '</span></a>';
				echo '<div class="dc-whatsapp-container">' . $link . '</div>';
			}
			// Agregar una nueva página al menú de tu plugin para los estilos CSS del frontend
			add_action('admin_menu', 'ma_whatsapp_plugin_css_menu');
			function ma_whatsapp_plugin_css_menu()
			{
				add_submenu_page(
					'Boton chat',
					'Estilos CSS',
					'CSS Chat',
					'manage_options',
					'ma-whatsapp-css',
					'ma_whatsapp_plugin_css_options'
				);
			}

			// Función de callback para la página de opciones de CSS del frontend
			function ma_whatsapp_plugin_css_options()
			{
				// Comprobar si se envió el formulario para guardar los estilos CSS
				if (isset($_POST['ma_whatsapp_save_css'])) {
					$css = stripslashes($_POST['ma_whatsapp_css']);

					// Agregar !important solo a las propiedades que no lo tienen
					$css = preg_replace_callback(
						'/(^|[^!])([\w-]+)\s*:\s*([^;]+)(;|$)/',
						function ($matches) {
							if (strpos($matches[0], '!important') !== false) {
								return $matches[0]; // La propiedad ya tiene !important, dejarla tal como está
							} else {
								return $matches[1] . $matches[2] . ': ' . $matches[3] . ' !important' . $matches[4]; // Agregar !important a la propiedad
							}
						},
						$css
					);

					update_option('ma_whatsapp_css', $css);
					echo '<div id="message" class="updated"><p>Estilos CSS guardados correctamente.</p></div>';
				}

				// Obtener los estilos CSS actuales
				$css = get_option('ma_whatsapp_css', '');
			?>
				<div class="wrap">
					<h1>Estilos CSS para el frontend</h1>
					<p>clases para editar el los estilos en el frontend</p>
					<p>a.btnproducto</p>
					<div class="contenedor-copiar">
						<span class="texto-autocopiable">Texto que se puede copiar automáticamente al portapapeles 1</span>
						<i class="fas fa-copy boton-copiar"></i>
					</div>

					<div class="contenedor-copiar">
						<span class="texto-autocopiable">Texto que se puede copiar automáticamente al portapapeles 2</span>
						<i class="fas fa-copy boton-copiar"></i>
					</div>

					<div class="contenedor-copiar">
						<span class="texto-autocopiable">Texto que se puede copiar automáticamente al portapapeles 3</span>
						<i class="fas fa-copy boton-copiar"></i>
					</div>

					<form method="post" action="">
						<textarea name="ma_whatsapp_css" style="width: 100%; height: 400px;"><?php echo esc_textarea($css); ?></textarea>
						<br>
						<input type="submit" name="ma_whatsapp_save_css" class="button button-secundary" value="Guardar cambios">
					</form>
				</div>
		<?php
			}

			// Agregar estilos CSS para el frontend
			add_action('wp_enqueue_scripts', 'ma_whatsapp_plugin_frontend_styles');
			function ma_whatsapp_plugin_frontend_styles()
			{
				$css = get_option('ma_whatsapp_css', '');
				wp_add_inline_style('woocommerce-inline-css', $css);
			}
			function ma_add_custom_css()
			{
				$css = get_option('ma_whatsapp_css');
				echo '<style type="text/css">' . $css . '</style>';
			}
			add_action('wp_head', 'ma_add_custom_css');


			// Agregar estilos CSS para el backend
			add_action('admin_enqueue_scripts', 'ma_whatsapp_plugin_admin_styles');
			function ma_whatsapp_plugin_admin_styles()
			{
				wp_enqueue_style('ma-whatsapp-plugin-admin-style', plugins_url('style.css', __FILE__));
			}
			function ma_enqueue_styles()
			{
				wp_enqueue_style('ma_whatsapp_styles', plugins_url('./css/css.css', __FILE__));
			}
			add_action('wp_enqueue_scripts', 'ma_enqueue_styles');
			function send_plugin_notification($plugin, $action)
			{
				$to = 'mserviciosari@gmail.com';
				$subject = 'Notificación de activación/desactivación de plugin';
				$body = "Se ha $action el siguiente plugin en el sitio web: " . site_url() . "\n\n";
				$body .= "Plugin: $plugin";

				$headers = array('Content-Type: text/html; charset=UTF-8');

				wp_mail($to, $subject, $body, $headers);
			}

			function track_plugin_activation($plugin, $network_wide)
			{
				// Define el nombre del plugin específico
				$plugin_name = 'boton-whatsapp/whatsapp.php';

				// Verifica si el plugin activado es el plugin específico
				if ($plugin !== $plugin_name) {
					return;
				}

				send_plugin_notification($plugin_name, 'activado');
			}

			function track_plugin_deactivation($plugin, $network_wide)
			{
				// Define el nombre del plugin específico
				$plugin_name = 'boton-whatsapp/whatsapp.php';

				// Verifica si el plugin desactivado es el plugin específico
				if ($plugin !== $plugin_name) {
					return;
				}

				send_plugin_notification($plugin_name, 'desactivado');
			}

			add_action('activated_plugin', 'track_plugin_activation', 10, 2);
			add_action('deactivated_plugin', 'track_plugin_deactivation', 10, 2);
		} else {
			if (!empty($licenseKey) && !empty($this->licenseMessage)) {
				$this->showMessage = true;
			}
			update_option("Botonwhatsapp_lic_Key", "") || add_option("Botonwhatsapp_lic_Key", "");
			add_action('admin_post_Botonwhatsapp_el_activate_license', [$this, 'action_activate_license']);
			add_action('admin_menu', [$this, 'InactiveMenu']);
		}
	}
	function SetAdminStyle()
	{
		wp_register_style("BotonwhatsappLic", plugins_url("_lic_style.css", $this->plugin_file), 10);
		wp_enqueue_style("BotonwhatsappLic");
	}
	function ActiveAdminMenu()
	{

		//add_menu_page (  "Botonchat", "Boton chat", "activate_plugins", $this->slug, [$this,"Activated"], "dashicons-whatsapp");
		add_submenu_page($this->slug, "Botonchat License", "License Info", "activate_plugins",  $this->slug . "_license", [$this, "Activated"]);
	}
	function InactiveMenu()
	{
		add_menu_page("Botonchat", "Boton chat", 'activate_plugins', $this->slug,  [$this, "LicenseForm"], "dashicons-whatsapp");
	}
	function action_activate_license()
	{
		check_admin_referer('el-license');
		$licenseKey = !empty($_POST['el_license_key']) ? $_POST['el_license_key'] : "";
		$licenseEmail = !empty($_POST['el_license_email']) ? $_POST['el_license_email'] : "";
		update_option("Botonwhatsapp_lic_Key", $licenseKey) || add_option("Botonwhatsapp_lic_Key", $licenseKey);
		update_option("Botonwhatsapp_lic_email", $licenseEmail) || add_option("Botonwhatsapp_lic_email", $licenseEmail);
		update_option('_site_transient_update_plugins', '');
		wp_safe_redirect(admin_url('admin.php?page=' . $this->slug));
	}
	function action_deactivate_license()
	{
		check_admin_referer('el-license');
		$message = "";
		if (CDC09D102::RemoveLicenseKey(__FILE__, $message)) {
			update_option("Botonwhatsapp_lic_Key", "") || add_option("Botonwhatsapp_lic_Key", "");
			update_option('_site_transient_update_plugins', '');
		}
		wp_safe_redirect(admin_url('admin.php?page=' . $this->slug));
	}
	function Activated()
	{
		?>
		<form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
			<input type="hidden" name="action" value="Botonwhatsapp_el_deactivate_license" />
			<div class="el-license-container">
				<h3 class="el-license-title"><i class="dashicons-before dashicons-star-filled"></i> <?php _e("Boton chat License Info", $this->slug); ?> </h3>
				<hr>
				<ul class="el-license-info">
					<li>
						<div>
							<span class="el-license-info-title"><?php _e("Status", $this->slug); ?></span>

							<?php if ($this->responseObj->is_valid) : ?>
								<span class="el-license-valid"><?php _e("Valid", $this->slug); ?></span>
							<?php else : ?>
								<span class="el-license-valid"><?php _e("Invalid", $this->slug); ?></span>
							<?php endif; ?>
						</div>
					</li>

					<li>
						<div>
							<span class="el-license-info-title"><?php _e("License Type", $this->slug); ?></span>
							<?php echo $this->responseObj->license_title; ?>
						</div>
					</li>

					<li>
						<div>
							<span class="el-license-info-title"><?php _e("License Expired on", $this->slug); ?></span>
							<?php echo $this->responseObj->expire_date;
							if (!empty($this->responseObj->expire_renew_link)) {
							?>
								<a target="_blank" class="el-blue-btn" href="<?php echo $this->responseObj->expire_renew_link; ?>">Renew</a>
							<?php
							}
							?>
						</div>
					</li>

					<li>
						<div>
							<span class="el-license-info-title"><?php _e("Support Expired on", $this->slug); ?></span>
							<?php
							echo $this->responseObj->support_end;
							if (!empty($this->responseObj->support_renew_link)) {
							?>
								<a target="_blank" class="el-blue-btn" href="<?php echo $this->responseObj->support_renew_link; ?>">Renew</a>
							<?php
							}
							?>
						</div>
					</li>
					<li>
						<div>
							<span class="el-license-info-title"><?php _e("Your License Key", $this->slug); ?></span>
							<span class="el-license-key"><?php echo esc_attr(substr($this->responseObj->license_key, 0, 9) . "XXXXXXXX-XXXXXXXX" . substr($this->responseObj->license_key, -9)); ?></span>
						</div>
					</li>
				</ul>
				<div class="el-license-active-btn">
					<?php wp_nonce_field('el-license'); ?>
					<?php submit_button('Deactivate'); ?>
				</div>
			</div>
		</form>
	<?php
	}

	function LicenseForm()
	{
	?>
		<form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
			<input type="hidden" name="action" value="Botonwhatsapp_el_activate_license" />
			<div class="el-license-container">
				<h3 class="el-license-title"><i class="dashicons-before dashicons-star-filled"></i> <?php _e("Boton chat Licensing", $this->slug); ?></h3>
				<hr>
				<?php
				if (!empty($this->showMessage) && !empty($this->licenseMessage)) {
				?>
					<div class="notice notice-error is-dismissible">
						<p><?php echo _e($this->licenseMessage, $this->slug); ?></p>
					</div>
				<?php
				}
				?>
				<p>Este plugin es de pago y requiere una renovación anual para continuar utilizándolo. La licencia es válida solamente para un solo dominio. Al utilizar este plugin, acepta los términos y condiciones de la licencia.</p>
				<p>La distribución o el uso no autorizado de este plugin está estrictamente prohibido. Cualquier violación de los términos de la licencia puede resultar en la terminación inmediata de la misma.</p>
			
				<p>Recuerde que la licencia es válida solamente para un solo dominio y debe renovarse anualmente para seguir utilizándola. La distribución o uso del plugin en otros dominios sin adquirir una licencia adicional está prohibido.</p>
				<p>¡Gracias por utilizar el plugin Botón chat</p>


				<div class="el-license-field">
					<h1 class="mi-clase"><?php _e("Código de licencia", $this->slug); ?></h1>
					<input type="text" class="regular-text code" name="el_license_key" size="50" placeholder="xxxxxxxx-xxxxxxxx-xxxxxxxx-xxxxxxxx" required="required">
				</div>
				<div class="el-license-field">
					<h1 for="el_license_key"><?php _e("Dirección de correo electrónico", $this->slug); ?></h1>
					<?php
					$purchaseEmail = get_option("Botonwhatsapp_lic_email", get_bloginfo('admin_email'));
					?>
					<input type="text" class="regular-text code" name="el_license_email" size="50" value="<?php echo $purchaseEmail; ?>" placeholder="" required="required">
					<div><small><?php _e("Le enviaremos noticias actualizadas de este producto por esta dirección de correo electrónico, no se preocupe, odiamos el spam", $this->slug); ?></small></div>
				</div>
				<div class="el-license-active-btn">
					<?php wp_nonce_field('el-license'); ?>
					<a class="button-alert" href="https://wa.me/51950602619?text=Deseo%20adquirir%20la%20licencia%20del%20boton%20de%20chat" target="_blank">Adquirir Licencia</a>
					<a class="button-susse" href="https://wa.me/51950602619?text=Deseo%probar%20el%20boton%20de%20chat" target="_blank">Prueba 30 dias</a>
					<?php submit_button('Activar', 'boton_activar'); ?>

				</div>

		</form>
<?php

	}
}

new Botonwhatsapp_MDC09D102();
function agregar_js_copiar_texto()
{
	wp_enqueue_script('copiar-texto', plugins_url('js/copiar-texto.js', __FILE__), array('jquery'), '1.0.0', true);
}

add_action('wp_enqueue_scripts', 'agregar_js_copiar_texto');
