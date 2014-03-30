# PHP - Reloaded

Tools for doing run-time code updating.
Useful for continuously running PHP applications.

Example using a React server:

    require 'vendor/autoload.php';

    use Letharion\Reloaded\Reloaders\Stat;
    use Letharion\Reloaded\Validators\NumArgs;

    // Create a new loader that decides when to reload code based on the files
    // last modification time.
    // Tell it to load the file 'example.php', and pass it a validator that
    // requires the loaded function to have a single parameter. See validators
    // below
    $stat = new Stat('example.php', new NumArgs(0));

    $app = function ($request, $response) use ($stat) {
      // Get the function we want. On most iterations this will just return
      // a reference to a previously loaded function, and will have negliable
      // performance cost. However, if the file has been modified since it was
      // last loaded, it will now be reloaded, and the runtime behavior has
      // been changed.
      $func = $stat->getFunction();

      $text = $func();

      $headers = array('Content-Type' => 'application/json');

      $response->writeHead(200, $headers);
      $response->end($text);
    };

    $loop = React\EventLoop\Factory::create();
    $socket = new React\Socket\Server($loop);
    $http = new React\Http\Server($socket);

    $http->on('request', $app);

    $socket->listen(1337);
    $loop->run();

The code above will run a HTTP server that responds to all requests with 
what the $func function returns. It will also reload the file 'example.php' if
that files modification time has been updated since it was last loaded. 
Thus it's possible to easily modify the runtime behaviour of the application
without restarting it.

### Validators
The second argument to Stat() is a validator. The validators can be used to
ensure only 'valid' code is loaded as replacement. Should validation fail, the
previously used function will remain in use. This protects the application
from blowing up even when if one accidentally pushes new bad code up.

The example validator simply tests that the new function has the correct number
of arguments, but can be as sophisticated as necessary.
