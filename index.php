<?php
/*
  Plugin Name: Saksh WP SMTP
  Version: 4.1.1
  Plugin URI: #
  Author: susheelhbti
  Author URI: http://www.aistore2030.com/
  Description: Integrate wordpress to your mandrill , sendgrid , getresponse, email-marketing247 SMTP Server, Amazon SES or any SMTP Server.
 */

class saksh_settings_page {

    function __construct() {
        $saksh_email_by_smtp = new Saksh_Email_By_SMTP();
        add_action('admin_menu', array($saksh_email_by_smtp, 'saksh_settings_page'));
    }

}

new saksh_settings_page;

class Saksh_Email_By_SMTP {

    /**
     * Holds the values to be used in the fields callbacks
     */ private $sebso;

    /**
     * Start up
     */ public function __construct() {
        $this->sebso = get_option('saksh_email_admin');
        add_action('admin_init', array($this, 'saksh_page_init'));
    }

    public function saksh_settings_page() {
// This page will be under "Settings"
        add_options_page('Settings Admin', 'Saksh WP SMTP', 'manage_options', 'saksh_wordpress_smtp', array($this, 'saksh_smtp_admin_page'));
        add_options_page('Settings Admin', 'RoboM Add Portal', 'manage_options', 'roboM_add_portal', array($this, 'roboM_add_portal'));
    }


    public function saksh_smtp_admin_page() {
        $this->sebso = get_option('saksh_email_admin');
        ?>
        <div class="wrap">
            <H2>Saksh Wordpress SMTP</h2>
            <div id="poststuff">
                <div id="post-body" class="metabox-holder columns-2">
                     
                    <div class="postbox">
                        <div class="inside">
                            <form method="post" action="options.php">
                                <?php
                                // This prints out all hidden setting fields
                                settings_fields('saksh_email_group');
                                do_settings_sections('saksh_email_admin');
                                submit_button();
                                ?>
                            </form>
                            <?php
                            $this->saksh_sendtestmail();
                            ?>



                        </div>
                    </div> </div> </div></div>
        <style>
            .form-table {
                clear: none;
            }
        </style>
        <?php
    }

    public function saksh_sendtestmail() {

        $action = $_REQUEST['action'];
        if ($action == "") /* display the contact form */ {
            ?> <p> Send a test email </p>
            <form method="post" action="options-general.php?page=saksh_wordpress_smtp">
                <input type="hidden" name="action" value="submit"> 
                <input type="email" id="testmail" name="testmail" placeholder="email id" value="" required  />


                <input type="submit" value="submit"> 
            </form>

            <?php
        } else /* send the submitted data */ {
            $testmail = $_REQUEST['testmail'];
            $ar = array();
            $ar['to'] = $testmail;
            $ar['message'] = "Test Email by plugin saksh wp smtp";
            $ar['subject'] = "Test Email by plugin saksh wp smtp";

            saksh_send_email_direct($ar);
        }
    }

    public function saksh_page_init() {
        register_setting('saksh_email_group', // Option group
                'saksh_email_admin', // Option name
                array($this, 'sanitize') // Sanitize
        );

        add_settings_section(
                'setting_section_id', // ID
                '', // Title
                array($this, 'print_section_info'), // Callback
                'saksh_email_admin' // Page
        );


        add_settings_field('from_name', 'From Name', array($this, 'from_name_callback'), //
                'saksh_email_admin', 'setting_section_id' // Section
        );
        add_settings_field('from_email', //
                'From Email', // Title
                array($this, 'from_email_callback'), //
                'saksh_email_admin', //
                'setting_section_id' //
        );
        add_settings_field('host', //
                'SMTP Server', // Title
                array($this, 'host_callback'), //
                'saksh_email_admin', //
                'setting_section_id' //
        );
        add_settings_field('auth', //
                'SMTPAuth', // Title
                array($this, 'auth_callback'), //
                'saksh_email_admin', //
                'setting_section_id' //
        );
        add_settings_field('port', //
                'Port', // Title
                array($this, 'port_callback'), //
                'saksh_email_admin', //
                'setting_section_id' //
        );
        add_settings_field('smtp', //
                'SMTPSecure', // Title
                array($this, 'smtpsecure_callback'), //
                'saksh_email_admin', //
                'setting_section_id' //
        );
        add_settings_field('username', //
                'User Name', // Title
                array($this, 'username_callback'), //
                'saksh_email_admin', //
                'setting_section_id' //
        );
        add_settings_field('password', //
                'Password', // Title
                array($this, 'password_callback'), //
                'saksh_email_admin', //
                'setting_section_id' //
        );
    }

    public function sanitize($input) {
        $new_input = array();

        if (isset($input['from_name'])) {
            $new_input['from_name'] = sanitize_text_field($input['from_name']);
        }

        if (isset($input['from_email'])) {
            $new_input['from_email'] = sanitize_email($input['from_email']);
        }

        if (isset($input['host'])) {
            $new_input['host'] = sanitize_text_field($input['host']);
        }

        if (isset($input['auth'])) {
            $new_input['auth'] = sanitize_text_field($input['auth']);
        }

        if (isset($input['SMTPAuth'])) {
            $new_input['SMTPAuth'] = sanitize_text_field($input['SMTPAuth']);
        }

        if (isset($input['port'])) {
            $new_input['port'] = sanitize_text_field($input['port']);
        }

        if (isset($input['smtpsecure'])) {
            $new_input['smtpsecure'] = sanitize_text_field($input['smtpsecure']);
        }

        if (isset($input['username'])) {
            $new_input['username'] = sanitize_text_field($input['username']);
        }

        if (isset($input['password'])) {
            $new_input['password'] = sanitize_text_field($input['password']);
        }
        return $new_input;
    }

    /**
     * Print the Section text
     */
    public function print_section_info() {
        
    }

    /**
     * Get the settings option array and print one of its values
     */ public function from_name_callback() {
        printf('<input type="text" id="from_name" name="saksh_email_admin[from_name]" value="%s" />', isset($this->sebso['from_name']) ? esc_attr($this->sebso['from_name']) :
                        'email-marketing247' );
    }

    /**
     * Get the settings option array and print one of its values
     */ public function from_email_callback() {
        printf('<input type="text" id="from_email" name="saksh_email_admin[from_email]" value="%s" />', isset($this->sebso['from_email']) ? esc_attr($this->sebso['from_email']) :
                        '' );
    }

    /**
     * Get the settings option array and print one of its values
     */ public function host_callback() {
        printf('<input type="text" id="host" name="saksh_email_admin[host]" value="%s" />', isset($this->sebso['host']) ? esc_attr($this->sebso['host']) :
                        'smtp.email-marketing247.com' );
    }

    /**
     * Get the settings option array and print one of its values
     */ public function auth_callback() {
        printf('<input type="text" id="auth" name="saksh_email_admin[auth]" value="%s" />( yes / no)', isset($this->sebso['auth']) ? esc_attr($this->sebso['auth']) :
                        'yes' );
    }

    /**
     * Get the settings option array and print one of its values
     */ public function port_callback() {
        printf('<input type="text" id="port" name="saksh_email_admin[port]" value="%s" />', isset($this->sebso['port']) ? esc_attr($this->sebso['port']) :
                        '587' );
    }

    /**
     * Get the settings option array and print one of its values
     */ public function smtpsecure_callback() {
        printf('<input type="text" id="smtpsecure" name="saksh_email_admin[smtpsecure]" value="%s" />( ssl / tls / none)', isset($this->sebso['smtpsecure']) ? esc_attr($this->sebso['smtpsecure']) :
                        'tls' );
    }

    /**
     * Get the settings option array and print one of its values
     */ public function username_callback() {
        printf('<input type="text" id="username" name="saksh_email_admin[username]" value="%s" />', isset($this->sebso['username']) ? esc_attr($this->sebso['username']) :
                        '' );
    }

    /**
     * Get the settings option array and print one of its values
     */ public function password_callback() {
        printf('<input type="password" id="password" name="saksh_email_admin[password]" value="%s" />', isset($this->sebso['password']) ? esc_attr($this->sebso['password']) :
                        '' );
    }

}

if (is_admin()) {
    $saksh_email_by_smtp = new Saksh_Email_By_SMTP();
}
add_action('phpmailer_init', 'saksh_settings');

function saksh_settings($phpmailer) {
    $saksh = get_option('saksh_email_admin');
    $phpmailer->isSMTP();
    $phpmailer->FromName = $saksh['from_name'];
    $phpmailer->From = $saksh['from_email'];
    $phpmailer->Host = $saksh['host'];
    $phpmailer->SMTPAuth = $saksh['auth'];

    $phpmailer->Port = $saksh['port'];
    $phpmailer->SMTPSecure = $saksh['smtpsecure'];
    $phpmailer->Username = $saksh['username'];
    $phpmailer->Password = $saksh['password'];

    $phpmailer->XMailer = "Saksh WP SMTP by http://www.aistore2030.com/";
}
 
 
 

