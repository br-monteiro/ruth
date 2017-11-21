<?php
namespace Ruth\Router;

class Route
{

    private static $allowedMethods = ['GET', 'POST', 'PUT', 'DELETE'];
    private static $routeMap = [];

    public static function get($path, array $options)
    {
        self::registerRoute('GET', $path, $options);
    }

    public static function post($path, array $options)
    {
        self::registerRoute('POST', $path, $options);
    }

    public static function put($path, array $options)
    {
        self::registerRoute('PUT', $path, $options);
    }

    public static function delete($path, array $options)
    {
        self::registerRoute('DELETE', $path, $options);
    }

    public static function all($path, array $options)
    {
        self::registerRoute('GET', $path, $options);
        self::registerRoute('POST', $path, $options);
        self::registerRoute('PUT', $path, $options);
        self::registerRoute('DELETE', $path, $options);
    }

    public static function many(array $methods, $path, array $options)
    {
        foreach ($methods as $method) {
            $method = strtoupper($method);
            if (in_array($method, self::$allowedMethods)) {
                self::$method($path, $options);
            } else {
                throw new \Exception('O método [' . $method . '] não é permitido.');
            }
        }
    }

    public static function lengthRoute(string $path): int
    {
        $path = self::normalizeRoute($path);
        return count(explode('/', $path));
    }

    public static function normalizeRoute(string $path): string
    {
        $path = strtolower($path);
        // validate path
        if (strpos($path, '/') != 0) {
            throw new \Exception('The route ["' . $path . '"] path is not valid');
        }

        // remove last '/' from path
        if (strrpos($path, '/') == (strlen($path) - 1) && strlen($path) > 1) {
            $path = rtrim($path, '/');
        }

        $path = self::removeDuplicatedSlashes($path);

        return $path;
    }

    public static function getRouteMap(): array
    {
        return self::$routeMap;
    }

    private static function removeDuplicatedSlashes(string $path): string
    {

        if (preg_match('/\/\//', $path)) {
            $path = str_replace('//', '/', $path);
            return self::removeDuplicatedSlashes($path);
        }

        return $path;
    }

    private static function registerRoute(string $type, $path, array $options)
    {
        $funcRegister = function(string $type, $path, array $options) {
            $length = self::lengthRoute($path);
            $path = self::normalizeRoute($path);
            $options['explode'] = explode('/', $path);
            self::$routeMap[$type][$length][$path] = $options;
        };

        if (is_array($path)) {
            foreach ($path as $key => $value) {
                $optionsCopy = $options;
                $alias = gettype($key) == 'string' ? $value : null;
                $value = gettype($key) == 'string' ? $key : $value;
                $optionsCopy['patterns'] = self::prepareOptionsPatterns($value, $alias, $options['patterns']);
                $funcRegister($type, $value, $optionsCopy);
            }
            // stop execution
            return;
        }

        $funcRegister($type, $path, $options);
    }

    private static function prepareOptionsPatterns($path, $alias, array $patterns): array
    {
        $arrMatches = [];

        foreach ($patterns as $key => $value) {
            if ($alias == null && preg_match('/\/' . $key . '\/?/', $path)) {
                $arrMatches[$key] = $value;
                continue;
            }

            if ($alias && preg_match('/^' . $alias . ':/', $key)) {
                $nameAttribute = str_replace($alias, '', $key);
                $arrMatches[$nameAttribute] = $value;
            }
        }

        return $arrMatches;
    }
}
