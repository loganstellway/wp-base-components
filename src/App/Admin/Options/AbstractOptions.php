<?php

namespace LoganStellway\Base\App\Admin\Options;

use LoganStellway\Base\App\Admin\Options;
use LoganStellway\Base\Helper\Encryption;

abstract class AbstractOptions implements OptionsInterface
{
    /**
     * Password placeholder
     */
    const PASSWORD_PLACEHOLDER = "*******";

    /**
     * @var Encryption
     */
    protected $encryption;

    /**
     * Section identifier
     * @var string
     */
    public static $section = "";

    /**
     * Options
     * @var array
     */
    protected $options = [];

    /**
     * Password values
     * @var array
     */
    protected $passwords = [];

    /**
     * Get encryption class
     * 
     * @return Encryption
     */
    protected function getEncryption(): Encryption
    {
        if (!$this->encryption) {
            $this->encryption = (new Encryption());
        }
        return $this->encryption;
    }

    /**
     * Get section identifier
     */
    protected function getSection(): string
    {
        return Options::OPTIONS_KEY . "_" . self::$section;
    }

    /**
     * Get options
     * 
     * @param string $option
     * @param string $key
     * @param string $default
     * @return mixed
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
     * Get password value
     * 
     * @param string $option
     * @param string $key
     * @return string
     */
    protected function getPasswordValue(string $option, string $key, string $value)
    {
        if (!empty($value)) {
            if (trim($value) === self::PASSWORD_PLACEHOLDER) {
                $value = $this->getOption($option, $key);
            } else {
                $value = $this->getEncryption()->encrypt($value);
            }
        }

        return $value;
    }

    /**
     * Render text field
     */
    protected function addInput(string $id, string $title, string $type = "text", string $default = null, string $section = null, string $help = null)
    {
        \add_settings_field(
            $id,
            $title,
            function () use ($id, $type, $default, $section, $help) {
                $val = $this->getOption($section, $id);

                printf(
                    '<input id="%1$s" name="%2$s[%1$s]" type="%3$s" value="%4$s" />%5$s',
                    $id,
                    $section,
                    $type,
                    !empty($val) && $type === "password"
                        ? self::PASSWORD_PLACEHOLDER
                        : esc_attr($val ?: $default ?: ''),
                    $help ? '<p class="description"><i>' . $help . '</i></p>' : ''
                );
            },
            Options::OPTIONS_KEY,
            $section
        );
    }

    /**
     * Render checkbox field
     */
    protected function addCheckbox(string $id, string $title, string $section = null, string $help = null)
    {
        \add_settings_field(
            $id,
            $title,
            function () use ($id, $section, $help) {
                $val = $this->getOption($section, $id);

                printf(
                    '<input id="%1$s" name="%2$s[%1$s]" type="checkbox" value="1" %3$s />%4$s',
                    $id,
                    $section,
                    isset($val) && $val == 1 ? "checked" : "",
                    $help ? '<p class="description"><i>' . $help . '</i></p>' : ''
                );
            },
            Options::OPTIONS_KEY,
            $section
        );
    }

    /**
     * Validate data
     */
    public function validate($data)
    {
        // Encrypt password values
        foreach ($this->passwords as $key) {
            if (isset($data[$key])) {
                $data[$key] = $this->getPasswordValue(
                    $this->getSection(),
                    $key,
                    $data[$key]
                );
            }
        }

        return $data;
    }
}
