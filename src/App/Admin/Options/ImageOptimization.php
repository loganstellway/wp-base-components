<?php

namespace LoganStellway\Base\App\Admin\Options;

use LoganStellway\Base\App\Admin\Options;

class ImageOptimization extends AbstractOptions
{
    /**
     * Default image quality
     * @var string
     */
    const DEFAULT_QUALITY = 75;

    /**
     * Section identifier
     * @var string
     */
    public static $section = "image_optimization";

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
                "description" => __("Image Optimization"),
                "sanitize_callback" => [$this, "validate"],
                "show_in_rest" => false,
                "default" => null,
            ]
        );

        // Section
        \add_settings_section(
            $section,
            __("Image Optimization"),
            function () {
                // echo __('Image optimization options');
            },
            Options::OPTIONS_KEY
        );

        // Fields
        $this->addCheckbox("use_optimization", "Optimize Images", $section);
        $this->addInput("quality", "Quality", "number", self::DEFAULT_QUALITY, $section, "Number 1-100");
    }

    public function validate($data)
    {
        $data = parent::validate($data);

        // Validate quality
        if (isset($data["quality"])) {
            $data["quality"] = (int) $data["quality"];

            if ($data["quality"] > 100) {
                $data["quality"] = 100;
            } elseif ($data["quality"] < 0) {
                $data["quality"] = 0;
            }
        }

        return $data;
    }
}
