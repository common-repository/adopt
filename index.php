<?php
/*
  Plugin Name: Adopt
  Author: Adopt
  Author URI:  https://goadopt.io/
  Version: 1.0.4
  Description: Safe and Intuitive cookie banner in a complete Consent Management Platform. Thousands of companies trust their visitors' consents to AdOpt. Start now and get full website compliance for privacy regulations like GDPR, CCPA, LGPD…
  Text Domain: adopt
*/
if (!function_exists('add_action')) {
  echo __('O plugin não pode ser passado direto', 'adopt');
  exit;
}

register_activation_hook(__FILE__, 'Adp_activate');
register_deactivation_hook(__FILE__, 'Adp_desactivate');

require_once('includes/activate.php');
require_once('includes/ConfigTags.php');

add_action('wp_head', 'Adp_wpbHook_javascript');

$dir = plugin_dir_url(__FILE__);
wp_enqueue_style( 'adopt-stylesheet', $dir . 'includes/style.css');

function adopt_config()
{

  add_settings_section(
    'minha_secao',
    null,
    function ($args) {
    ?>
    <div class="masthead-adopt">
      <img class="masthead-logo-adopt" src="<?php echo esc_url(plugins_url('_inc/img/Logo-hirizontal-gradiente.png', __FILE__)); ?>" alt="adop" />
    </div>


    <div class="card-adopt">
      <h2>
        <span style="color:rgb(0, 221, 128)">AdOpt</span> Cookie Banner | Consent Management
        Platform.
      </h2>
      <p>
        Not registered yet? Click the link below to create your free account.
        Already a user? <br />Please provide the <b>Disclaimer ID</b> you
        created for this website's URL.
      </p>

      <div class="btn-container">
        <a target='_blank' href="https://dash.goadopt.io/register" class="btn" ><b>Create an Account</b></a
        >
      </div>
      <div class="documentation-link">
        <a target='_blank' href="https://goadopt.io/en/support/integrations/wordpress/adopt-cookie-banner-wordpress/">Documentation</a>
      </div>
    </div>


  <?php

    },
    'Adopt'
  );

  register_setting(
    'Adopt',
    'chave_de_integracao',
    array(
      'sanitize_callback' => function ($value) {
        $adpvalue = strip_tags($value);
        $guid_regex = '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i';

        if (!preg_match($guid_regex, $adpvalue)) {
          add_settings_error(
            'chave_de_integracao',
            esc_attr('chave_de_integracao_erro'),
            'Invalid disclaimer ID format',
            'error'
          );
        } else {
          AdpExecComand($adpvalue);
        }
        return $adpvalue;

      },
    )
  );


  add_settings_field(
    'chave_de_integracao',
    'AdOpt Disclaimer ID',
    function ($args) {
      $options = get_option('chave_de_integracao');
  ?>

    <div class="adopt-box">
      <input type="text" class="inputadopt" id="<?php echo esc_attr($args['label_for']); ?>" name="chave_de_integracao" value="<?php echo esc_attr(AdpExecComand(null)) ?>">
    </div>

  <?php
    },
    'Adopt',
    'minha_secao',
    [
      'label_for' => 'chave_de_integracao_html_id',
      'class'     => 'classe-html-tr',
    ]
  );
}

add_action('admin_init', 'adopt_config');

function adopt_config_menu()
{
  add_options_page(
    null,
    'Adopt',
    'manage_options',
    'minhas-configuracoes',
    'adopt_config_html'
  );
}


add_action('admin_menu', 'adopt_config_menu');


function adopt_config_html()
{
  ?>
  <div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    <form action="options.php" method="post" id="form">

      <?php
      settings_fields('Adopt');
      do_settings_sections('Adopt');
      submit_button("Save your settings");
      ?>
    </form>
  </div>
<?php
}
