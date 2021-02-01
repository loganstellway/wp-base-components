<?php

namespace LoganStellway\Base\App\Admin\Options;

use LoganStellway\Base\App\Admin\Options;
use LoganStellway\Base\Helper\Encryption;
use LoganStellway\Base\Helper\Session;

class Salt extends AbstractOptions
{
    /**
     * Section identifier
     * @var string
     */
    protected $section = "generate_salt";

    /**
     * Register Options
     */
    public function registerOptions()
    {
        $section = $this->getSection();

        // Register setting
        \register_setting(
            Options::OPTIONS_KEY,
            $section,
            [
                // "type" => "object",
                "description" => __("Generate Salt"),
                "sanitize_callback" => [$this, "validate"],
                "show_in_rest" => false,
                "default" => null,
            ]
        );

        // Section
        \add_settings_section(
            $section,
            __(""),
            function () {
            },
            Options::OPTIONS_KEY
        );

        // Fields
        $this->addInput("test", "", "password", $section);
    }

    /**
     * Validate data
     */
    public function validate($data)
    {
        Session::set("generated_salt", 1);
        return null;
    }

    /**
     * Render salt message
     */
    public function renderSaltNotice($name, $value)
    {
        if (in_array($name, ["_transient_timeout_settings_errors", "_transient_settings_errors"])) {
            return;
        }
        print_r($name);
        exit;
        if (Session::get("generated_salt")) {
            return;
        }
        Session::uns("generated_salt");
?>
        <div class="notice notice-success is-dismissible">
            <p>
                <?= __("Please save this generated salt to the environment variable") ?> <strong>"<?= Encryption::SALT ?>"</strong><br />
                <span style="user-select: all"><?= $this->getEncryption()->buildSalt() ?></span>
            </p>
        </div>
<?php
    }
}
