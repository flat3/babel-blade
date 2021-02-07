<?php

namespace Flat3\Blade\Babel\Tests;

use Flat3\Blade\Babel\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Orchestra\Testbench\TestCase;
use Spatie\Snapshots\MatchesSnapshots;

class BladeTest extends TestCase
{
    use MatchesSnapshots;

    protected function getPackageProviders($app)
    {
        return [
            ServiceProvider::class,
        ];
    }

    public function assertMatch($str)
    {
        $this->assertMatchesTextSnapshot(Blade::compileString($str));
    }

    public function test_simple()
    {
        $this->assertMatch(<<<'HERE'
<script type="text/babel">
(...args) => console.log(...args)
</script>
HERE
        );
    }

    public function test_ignore_other()
    {
        $this->assertMatch(<<<'HERE'
<div id="test">hello</div>
<script type="text/babel">
(...args) => console.log(...args)
</script>
<div id="test2">world</div>
HERE
        );
    }

    public function test_multiple()
    {
        $this->assertMatch(<<<'HERE'
<script type="text/babel">
(...args) => console.log(...args)
</script>
<div id="test">hello</div>
<script type="text/babel">
(...args) => console.log(...args)
</script>
HERE
        );
    }

    public function test_only_babel()
    {
        $this->assertMatch(<<<'HERE'
<script type="text/javascript">
(...args) => console.log(...args)
</script>
<div id="test">hello</div>
<script type="text/babel">
(...args) => console.log(...args)
</script>
HERE
        );
    }

    public function test_cache() {
        putenv('BABEL_BLADE_CACHE=1');

        $this->assertMatch(<<<'HERE'
<script type="text/babel">
(...args) => console.log(...args)
</script>
HERE
        );
    }
}
