<?php

namespace LoganStellway\Base\App\Admin;

class Options
{
    /**
     * Options form key
     */
    const OPTIONS_KEY = "wp_base_components";

    /**
     * Construct
     */
    public function __construct()
    {
        if (\is_admin()) {
            \add_action("admin_menu", [$this, "registerOptionsPage"]);
            \add_action("admin_init", [$this, "registerOptions"]);
        }
    }

    /**
     * Add options page
     */
    public function registerOptionsPage()
    {
        \add_options_page(
            __("Base Component Options"),
            __("Base Component Options"),
            "manage_options",
            "base-component-options",
            [$this, "renderOptionsPage"]
        );
    }

    /**
     * Register options
     */
    public function registerOptions()
    {
        (new Options\SendGrid())->registerOptions();
    }

    /**
     * Render options page
     */
    public function renderOptionsPage()
    {
?>
        <div class="wrap">
            <h1>WP Base Component Settings</h1>
            <form method="post" action="options.php">
                <?php
                \settings_fields(self::OPTIONS_KEY);
                \do_settings_sections(self::OPTIONS_KEY);
                \submit_button();
                ?>
            </form>
        </div>
<?php
    }
}
