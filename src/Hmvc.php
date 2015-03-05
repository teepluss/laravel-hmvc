<?php namespace Teepluss\Hmvc;

use Guzzle\Http\Client;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router;
use Illuminate\Config\Repository;
use Illuminate\Support\Facades\Response;
use Symfony\Component\Debug\Exception\FatalErrorException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Hmvc {

    /**
     * Repository config.
     *
     * @var \Illuminate\Config\Repository
     */
    protected $config;

    /**
     * Router
     *
     * @var \Illuminate\Routing\Router
     */
    protected $router;

    /**
     * Request
     *
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * Remote client.
     *
     * @var \Guzzle\Http\Client
     */
    protected $remoteClient;

    /**
     * Instance Hmvc.
     *
     * @param arary   $config
     * @param Router  $router
     * @param Request $request
     * @param Client  $remote
     */
    public function __construct($config, Router $router, Request $request, Client $remoteClient)
    {
        $this->config = $config;

        $this->router = $router;

        $this->request = $request;

        $this->remoteClient = $remoteClient;
    }

    /**
     * Remote client for http request.
     *
     * @return Client
     */
    public function getRemoteClient()
    {
        return $this->remoteClient;
    }

    /**
     * Call internal URI with parameters.
     *
     * @param  string $uri
     * @param  string $method
     * @param  array  $parameters
     * @return mixed
     */
    public function invoke($uri, $method, $parameters = array())
    {
        // Request URI.
        $uri = '/'.ltrim($uri, '/');

        try
        {
            // Store the original request data and route.
            $originalInput = $this->request->input();
            $originalRoute = $this->router->getCurrentRoute();

            // Masking route to allow testing with PHPUnit.
            // if ( ! $originalRoute instanceof Route)
            // {
            //     $originalRoute = new Route(new \Symfony\Component\HttpFoundation\Request());
            // }

            $requestMethod = strtoupper($method);

            // Create a new request to the API resource
            $request = $this->request->create($uri, $requestMethod, $parameters);

            // Replace request method and input.
            $this->request->setMethod($requestMethod);
            $this->request->replace($request->input());

            // Dispatch request.
            $dispatch = $this->router->dispatch($request);

            if (method_exists($dispatch, 'getOriginalContent'))
            {
                $response = $dispatch->getOriginalContent();
            }
            else
            {
                $response = $dispatch->getContent();
            }

            // Decode json content.
            if ($dispatch->headers->get('content-type') == 'application/json')
            {
                if (function_exists('json_decode') and is_string($response))
                {
                    $response = json_decode($response, true);
                }
            }

            // Restore the request input and route back to the original state.
            $this->request->replace($originalInput);

            return $response;
        }
        catch (NotFoundHttpException $e)
        {
            throw new HmvcNotFoundHttpException('Request Not Found.');
        }
        catch (FatalErrorException $e)
        {
            throw new HmvcFatalErrorException($e->getMessage());
        }
    }

    /**
     * Configure remote client for http request.
     *
     * @param array $configuration
     *
     * array
     *(
     *   'verify' => false,                               //allows self signed certificates
     *   'verify', '/path/to/cacert.pem',                 //custom certificate
     *   'headers/X-Foo', 'Bar',                          //custom header
     *   'auth', array('username', 'password', 'Digest'), //custom authentication
     *)
     */
    public function configureRemoteClient($configurations)
    {
        foreach ($configurations as $option => $value)
        {
            call_user_func_array(array($this->remoteClient, 'setDefaultOption'), array($option, $value));
        }


        //sd($this->remoteClient);

        return $this;
    }

    /**
     * Invoke with remote request.
     *
     * @param  string $uri
     * @param  string $method
     * @param  array  $parameters
     * @return mixed
     */
    public function invokeRemote($uri, $method = 'GET', $parameters = array())
    {
        $remoteClient = $this->getRemoteClient();

        // Make request.
        $request = call_user_func_array(array($remoteClient, $method), array($uri, null, $parameters));

        // Ignore all SSL case.
        $request->getCurlOptions()->set(CURLOPT_SSL_VERIFYHOST, false);
        $request->getCurlOptions()->set(CURLOPT_SSL_VERIFYPEER, false);

        // Send request.
        $response = $request->send();

        // Body responsed.
        $body = (string) $response->getBody();

        // Decode json content.
        if ($response->getContentType() == 'application/json')
        {
            if (function_exists('json_decode') and is_string($body))
            {
                $body = json_decode($body, true);
            }
        }

        return $body;
    }

    /**
     * Alias call method.
     *
     * @return mixed
     */
    public function __call($method, $parameters = array())
    {
        if (in_array($method, array('get', 'patch', 'post', 'put', 'delete')))
        {
            $uri = array_shift($parameters);

            $parameters = current($parameters);
            $parameters = is_array($parameters) ? $parameters : array();

            if (preg_match('/^http(s)?/', $uri))
            {
                return $this->invokeRemote($uri, $method, $parameters);
            }

            return $this->invoke($uri, $method, $parameters);
        }
    }

}
