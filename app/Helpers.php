<?php

if (!function_exists('config_path')) {
    /**
     * Get the configuration path.
     *
     * @param  string  $path
     * @return string
     */
    function config_path($path = '')
    {
        return app()->basePath() . '/config' . ($path ? '/' . $path : $path);
    }
}

if (!function_exists('request')) {
    /**
     * Get an instance of the current request or an input item from the request.
     *
     * @param  array|string  $key
     * @param  mixed   $default
     * @return \Illuminate\Http\Request|string|array
     */
    function request($key = null, $default = null)
    {
        if (is_null($key)) {
            return app('request');
        }

        if (is_array($key)) {
            return app('request')->only($key);
        }

        $value = app('request')->__get($key);

        return is_null($value) ? value($default) : $value;
    }
}

if (!function_exists('route_parameter')) {
    /**
     * Get a given parameter from the route.
     *
     * @param $name
     * @param null $default
     * @return mixed
     */
    function route_parameter($name, $default = null)
    {
        $routeInfo = app('request')->route();

        return array_get($routeInfo[2], $name, $default);
    }
}

if (!function_exists('fetch')) {
    /**
     * Fetch Remote Data
     *
     * @param [string] $url
     * @return array
     */
    function fetch($url, $isJson = true)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        $result = curl_exec($ch);
        if (!empty($result)) {
            return $isJson ? json_decode($result, true) : $result;
        }
        return [];
    }
}

if (!function_exists('trans_fb')) {
    /**
     * Makes translation fall back to specified value if definition does not exist
     *
     * @param string $key
     * @param null|string $fallback
     * @param null|string $locale
     * @param array|null $replace
     *
     * @return array|\Illuminate\Contracts\Translation\Translator|null|string
     */
    function trans_fb(string $key, ? string $fallback = null, ? string $locale = null, ? array $replace = [])
    {
        if (\Illuminate\Support\Facades\Lang::has($key, $locale)) {
            return trans($key, $replace, $locale);
        }

        return $fallback;
    }
}

if (!function_exists('auth_user')) {
    /**
     * return current logged-in user
     *
     * @return \App\Models\User|null
     */
    function auth_user()
    {
        return \Auth::user();
    }
}
