<?php

namespace LoganStellway\Base\App\Admin\Options;

use LoganStellway\Base\App\Admin\Options;

class SendGrid extends AbstractOptions
{
    /**
     * Section identifier
     * @var string
     */
    protected $section = "sendgrid";

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
                "description" => __("SendGrid Options"),
                "sanitize_callback" => [$this, "validate"],
                "show_in_rest" => false,
                "default" => null,
            ]
        );

        // Section
        \add_settings_section(
            $section,
            __("SendGrid"),
            function () {
                echo __('SendGrid API options');
            },
            Options::OPTIONS_KEY
        );

        // Fields
        $this->addText("sendgrid_api_key", "API Key", "password", $section);
    }

    /**
     * Validate data
     */
    public function validate($data)
    {
        return $data;
    }
}
