<?php

declare(strict_types=1);

namespace Azonmedia\ModelFrontendMap;


use Azonmedia\Utilities\AlphaNumUtil;
use Azonmedia\Utilities\FilesUtil;
use Azonmedia\Exceptions\InvalidArgumentException;

class ModelFrontendMap
{

    protected const CONFIG_DEFAULTS = [
        'services' => [
            'FrontendHooks'
        ]
    ];

    protected const CONFIG_RUNTIME = [];

    /**
     * A mapping between model and view frontend component
     * @exmaple
     * GuzabaPlatform\Cms\Models\Page => @GuzabaPlatform.Cms/ViewPage.vue
     * @var string[]
     */
    protected array $view_map = [];

    /**
     * A mapping between model and management frontend component
     * @example
     * GuzabaPlatform\Cms\Models\Page => @GuzabaPlatform.Cms/CmsAdmin.vue
     * @var string[]
     */
    protected array $manage_map = [];

    protected string $frontend_view_map_file;

    protected string $frontend_manage_map_file;

    private bool $is_view_map_dumped_flag = false;

    private bool $is_manage_map_dumped_flag = false;

    public function __construct(string $frontend_view_map_file, string $frontend_manage_map_file)
    {
        $file_error = FilesUtil::file_error(dirname($frontend_view_map_file), $is_writeable = true, $is_dir = true, $arg_name = 'frontend_view_map_dir');
        if ($file_error) {
            throw new InvalidArgumentException($file_error);
        }
        $this->frontend_view_map_file = $frontend_view_map_file;

        $file_error = FilesUtil::file_error(dirname($frontend_manage_map_file), $is_writeable = true, $is_dir = true, $arg_name = 'frontend_manage_map_dir');
        if ($file_error) {
            throw new InvalidArgumentException($file_error);
        }
        $this->frontend_manage_map_file = $frontend_manage_map_file;
    }

    public function add_view_mapping(string $model_class, string $frontend_component): void
    {
        if (!$model_class) {
            throw new InvalidArgumentException(sprintf(t::_('No model_class provided.')));
        }
        if (!class_exists($model_class)) {
            throw new InvalidArgumentException(sprintf(t::_('No class %1$s exists.'), $model_class));
        }
        if (!$frontend_component) {
            throw new InvalidArgumentException(sprintf(t::_('There is not frontend_component provided.')));
        }
        $this->view_map[$model_class] = $frontend_component;
    }

    public function get_view_mapping(string $model_class): ?string
    {

    }

    public function get_view_map_file(): string
    {
        return $this->frontend_view_map_file;
    }

    public function get_view_map(): array
    {
        return $this->view_map;
    }

    public function get_view_map_as_string(): string
    {
        return $this->generate_view_map();
    }

    protected function generate_view_map(): string
    {
        $ret = '/** File generated by Azonmedia\ModelFrontendMap (azonmedia\model-frontend-map) */'.PHP_EOL;
        $ret .= '/** Contains a mapping between server-side models and the frontend components that render these */'.PHP_EOL;
        $ret .= '/* eslint max-len: 0 */'.PHP_EOL;//the lines will be longer
        $ret .= 'export default {'.PHP_EOL;
        foreach ($this->get_view_map() as $model_class => $frontend_component) {
            $ret .= AlphaNumUtil::indent(sprintf('"%s" : "%s",', addslashes($model_class), $frontend_component) ).PHP_EOL;
        }
        $ret .= '}'.PHP_EOL;
        return $ret;
    }

    public function dump_view_map(): void
    {
        $view_map_str = $this->get_view_map_as_string();
        file_put_contents($this->get_view_map_file(), $view_map_str);//replace the old file
        $this->set_view_map_dumped(true);
    }

    public function is_view_map_dumped(): bool
    {
        return $this->is_view_map_dumped_flag;
    }

    public function set_view_map_dumped(bool $flag): void
    {
        $this->is_view_map_dumped_flag = $flag;
    }

    public function add_management_mapping(string $model_class, string $frontend_component): void
    {

    }
}