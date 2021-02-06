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

                return preg_replace_callback($pattern, function ($script) {
                    $viewPath = base_path('resources/views');
                    $modulePath = base_path('node_modules');

                    $process = Process::fromShellCommandline('node ' . __DIR__ . '/babel.js', $viewPath, [
                        'PATH' => getenv('PATH'),
                        'NODE_PATH' => $modulePath,
                    ], $script[1]);
                    $process->run();

                    if (!$process->isSuccessful()) {
                        throw new Exception($process->getErrorOutput());
                    }

                    $code = $process->getOutput();
                    return <<<HERE
<script type="text/javascript">
(function() { $code
  
})();
</script>
HERE;
                }, $value);
            }
        );
    }
}