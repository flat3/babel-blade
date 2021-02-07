<?php

namespace Flat3\Blade\Babel;

use Exception;
use Illuminate\Support\Facades\Blade;
use Symfony\Component\Process\Process;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function boot()
    {
        Blade::precompiler(
            function ($value) {
                $pattern = '#<script type="text/babel">\n?(.*?)\n?</script>#s';

                return preg_replace_callback($pattern, function ($capture) {
                    $cacheLocal = !!env('BABEL_BLADE_CACHE');

                    $viewPath = resource_path('views');
                    $modulePath = base_path('node_modules');
                    $script = $capture[1];

                    $process = Process::fromShellCommandline('node ' . __DIR__ . '/babel.js', $viewPath, [
                        'PATH' => getenv('PATH'),
                        'NODE_PATH' => $modulePath,
                    ], $script);
                    $process->run();

                    if (!$process->isSuccessful()) {
                        throw new Exception($process->getErrorOutput());
                    }

                    $code = $process->getOutput();
                    $result = <<<HERE
<script type="text/javascript">
(function() { $code
  
})();
</script>
HERE;

                    if (!$cacheLocal) {
                        return $result;
                    }

                    $filename = md5($script) . '.blade.php';
                    file_put_contents($viewPath . '/' . $filename, $result);

                    return sprintf("@include('%s')", $filename);
                }, $value);
            }
        );
    }
}