<?php

namespace LoganStellway\Base\App\Admin\Options;

use LoganStellway\Base\App\Admin\Options;

abstract class AbstractOptions implements OptionsInterface
{
    /**
     * Section identifier
     * @var string
     */
    protected $section = "";

    /**
     * Options
     * @var array
     */
    protected $options = [];

    /**
     * Get section identifier
     */
    protected function getSection(): string
    {
        return Options::OPTIONS_KEY . "_" . $this->section;
    }

    /**
     * Get options
     */
    protected function getOption(string $option, string $key, $default = "")
    {
        if (!isset($this->options[$option])) {
            $this->options[$option] = get_option($option, []);
        }

        return $key
            ? $this->options[$option][$key] ?? $default
            : $this->options[$option];
    }

    /**
     * Render text field
     */
    protected function addText(string $id, string $title, string $type = "text", string $section = null)
    {
        \add_settings_field(
            Options::OPTIONS_KEY . "_" . $id,
            $title,
            function () use ($id, $type, $section) {
                printf(
                    '<input id="%1$s" name="%2$s[%1$s]" type="%3$s" value="%4$s" />',
                    $id,
                    $section,
                    $type,
                    esc_attr($this->getOption($section, $id))
                );
            },
            Options::OPTIONS_KEY,
            $section
        );
    }
}
