<?php
$classForTest = new class {

    private $params;
    private $patterns;

    public function setParams($params)
    {
        $this->params = $params;
    }

    public function setPatterns($patterns)
    {
        $this->patterns = $patterns;
    }

    public function __destruct()
    {
        echo "<br>Patterns: <br>" . $this->dumpPatterns() . "<br>";
        echo "<br>Params: <br>" . $this->dumpParams() . "<br>";
    }

    private function dumpParams()
    {
        return var_export($this->params, true);
    }

    private function dumpPatterns()
    {
        return var_export($this->patterns, true);
    }

    public function test1()
    {
        echo "Rota para '/home/:id/teste', '/home/:id/'";
    }

    public function test2()
    {
        echo "Rota para '/', '/:id'";
    }

    public function test3()
    {
        echo "Rota para '/teste'";
    }
};

Ruth\Router\Route::get(
    [
    '/home/:id/teste' => 'home-teste',
    '/home/:id/'
    ], [
    'run' => $classForTest,
    'action' => 'test1',
    'patterns' => [
        'home-teste:id' => '/[a-z]+/',
        ':id' => '/\d/'
    ]
]);

Ruth\Router\Route::get(['/', '/:id'], [
    'run' => $classForTest,
    'action' => 'test2',
    'patterns' => [
        ':id' => '/\w+/'
    ]
]);

Ruth\Router\Route::many(['post', 'DELETE'], '/teste', [
    'run' => $classForTest,
    'action' => 'test3',
    'patterns' => [
        ':id' => '/\w+/'
    ]
]);
