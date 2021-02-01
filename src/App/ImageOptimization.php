<?php

namespace LoganStellway\Base\App;

use ImageOptimizer\Optimizer;
use ImageOptimizer\OptimizerFactory;

class ImageOptimization
{
    /**
     * @var array
     */
    protected $options;

    /**
     * @var Optimizer
     */
    private $optimizer;

    /**
     * @var array
     */
    private $optimizerOptions = [
        'PS_IMAGE_OPTIMIZER_BIN_ADVPNG'    => 'advpng_bin',
        'PS_IMAGE_OPTIMIZER_BIN_GIFSICLE'  => 'gifsicle_bin',
        'PS_IMAGE_OPTIMIZER_BIN_JPEGOPTIM' => 'jpegoptim_bin',
        'PS_IMAGE_OPTIMIZER_BIN_JPEGTRAN'  => 'jpegtran_bin',
        'PS_IMAGE_OPTIMIZER_BIN_OPTIPNG'   => 'optipng_bin',
        'PS_IMAGE_OPTIMIZER_BIN_PNGCRUSH'  => 'pngcrush_bin',
        'PS_IMAGE_OPTIMIZER_BIN_PNGOUT'    => 'pngout_bin',
        'PS_IMAGE_OPTIMIZER_BIN_PNGQUANT'  => 'pngquant_bin',
        'PS_IMAGE_OPTIMIZER_BIN_SVGO'      => 'svgo_bin',
    ];

    /**
     * @var array
     */
    protected $validContentTypes = [
        'image/gif',
        'image/jpeg',
        'image/png',
    ];

    /**
     * Setup
     */
    public function __construct()
    {
        if ($this->optimizeImages()) {
            // Compress uploaded images
            add_filter('wp_handle_upload_prefilter', [$this, 'handleUploadPrefilter'], 4);
            add_filter('wp_handle_sideload_prefilter', [$this, 'handleUploadPrefilter'], 4);
        }
    }

    /**
     * Get options
     * 
     * @return mixed
     */
    protected function getOption(string $key = null, $default = null)
    {
        if (!isset($this->options)) {
            $this->options = get_option(Admin\Options::OPTIONS_KEY . "_" . Admin\Options\ImageOptimization::$section, []);
        }

        return $this->options[$key] ?? $default;
    }

    /**
     * Optimize images
     * 
     * @return bool
     */
    public function optimizeImages(): bool
    {
        return (bool) $this->getOption("use_optimization", false);
    }

    /**
     * Optimize images
     * 
     * @return bool
     */
    public function getQuality(): int
    {
        return (int) $this->getOption("quality", Admin\Options\ImageOptimization::DEFAULT_QUALITY);
    }

    /**
     * Get optimizer options
     * 
     * @return array
     */
    protected function getOptimizerOptions()
    {
        $options = [
            'ignore_errors' => false,
            'jpegoptim_options' => ['--strip-all', '--all-progressive', '--max=' . $this->getQuality()],
            'pngquant_options' => ['--force', '--strip', '--quality=' . $this->getQuality()],
        ];

        foreach ($this->optimizerOptions as $key => $option) {
            if (defined($key)) {
                $options[$option] = constant($key);
            }
        }

        return apply_filters('ps_image_optimizer_options', $options);
    }

    /**
     * @return Optimizer
     */
    private function getOptimizer()
    {
        if (!$this->optimizer) {
            $this->optimizer = (new OptimizerFactory($this->getOptimizerOptions()))->get();
        }

        return $this->optimizer;
    }

    /**
     * Compress uploaded images
     */
    public function handleUploadPrefilter(array $file)
    {
        // Check file type
        if (!isset($file['type']) || !in_array($file['type'], $this->validContentTypes)) {
            return $file;
        }

        // Optimize image
        if (isset($file['tmp_name'])) {
            try {
                $this->getOptimizer()->optimize($file['tmp_name']);

                $stat = stat($file['tmp_name']);
                if (isset($stat['size'])) {
                    $file['size'] = $stat['size'];
                }
            } catch (\Exception $e) {
                // print_r($e);
            }
        }

        return $file;
    }
}
