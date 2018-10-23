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

        $value = app('request')->all()[$key] ?? null;

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
     * @param string $url
     * @param boolean $isJson
     * @param array $data
     * @param array $headers
     * @return array
     */
    function fetch($url, $data = [], $rawHeaders = [], $isJson = true, $returnHeader = false)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, $returnHeader ? 1 : 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        if (!empty($rawHeaders)) {
            $headers = [];
            foreach ($rawHeaders as $k => $v) {
                $headers[] = $k . ': ' . $v;
            }
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        if (!empty($data)) {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            if (is_array($data)) {
                if (isset($rawHeaders['Content-Type']) && $rawHeaders['Content-Type'] == 'application/json') {
                    $postData = json_encode($data);
                } else {
                    $postData = http_build_query($data);
                }
            } else {
                $postData = $data;
            }

            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        }
        $result = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $time = curl_getinfo($ch, CURLINFO_TOTAL_TIME);
        curl_close($ch);

        if (!empty($result)) {
            return $isJson ? json_decode($result, true) : $result;
        }

        \Log::info('[fetch][fail][' . $code . ']' . $url . (isset($postData) ? '?' . str_replace(PHP_EOL, '', $postData) : ''));

        return $isJson ? [] : '';
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

if (!function_exists('abort_sys')) {
    function abort_sys($code, $msg = '')
    {
        abort(substr($code, 0, 3), trans('errors.' . $code) . PHP_EOL . $msg);
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

if (!function_exists('split_text')) {
    function split_text($str)
    {
        return explode("\n", str_replace(["\r\n", "\r"], ["\n", "\n"], $str));
    }
}

if (!function_exists('clear_emoji')) {
    function clear_emoji($str)
    {
        $str = preg_replace_callback(
            '/./u',
            function (array $match) {
                return strlen($match[0]) >= 4 ? '' : $match[0];
            },
            $str
        );
        return $str;
    }
}
