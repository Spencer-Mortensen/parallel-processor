# parallel-processor

You can use this "parallel-processor" library to run many pieces of code at once--and the collect the results at the end..

Here's an example:

```php
<?php

namespace Example;

use SpencerMortensen\ParallelProcessor\Processor;

require 'bootstrap.php';

$processor = new Processor();

$t0 = microtime(true);

$processor->startJob('medium', new SleepJob(2));
$processor->startJob('large', new SleepJob(3));
$processor->startJob('small', new SleepJob(1));

while ($processor->getResult($id, $result)) {
    echo "{$id}: done\n";
}

$t1 = microtime(true);

echo "\ntotal time: ", $t1 - $t0, "\n";
```

Here's the output:
```
small: done
medium: done
large: done

total time: 3.0021669864655
```

You can see that this script finished in just three seconds--even though the jobs slept for a total of _six_ seconds. The parallel processing effectively made everything run twice as fast!
