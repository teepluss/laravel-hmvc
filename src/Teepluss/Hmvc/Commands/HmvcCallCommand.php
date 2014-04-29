<?php namespace Teepluss\Hmvc\Commands;

use Illuminate\View\View;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class HmvcCallCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'hmvc:call';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Call another controller via CLI.';

	/**
	 * HMVC.
	 *
	 * @var \Teepluss\hmvc\hmvc
	 */
	protected $hmvc;

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct($hmvc)
	{
		parent::__construct();

		$this->hmvc = $hmvc;
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		$url = $this->argument('url');

		// Remote request.
		$invoke = (preg_match('/^http(s)?:/', $url)) ? 'invokeRemote' : 'invoke';

		// Method to call.
		$method = $this->option('request');
		$method = strtolower($method);
		$method = (in_array($method, array('get', 'post', 'put', 'delete', 'patch', 'head'))) ? $method : 'get';

		// Parameters.
		$parameters = $this->option('data');

		if ($parameters)
		{
			parse_str($parameters, $parameters);
		}

		$response = $this->hmvc->$invoke($url, $method, $parameters);

		if ($response instanceof View)
		{
			return $this->info($response->render());
		}

		if (is_array($response) or is_object($response))
		{
			return $this->info(var_export($response, true));
		}

		return $this->info($response);
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			array('url', InputArgument::REQUIRED, 'URL to call'),
		);
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array(
			array('request', 'X', InputOption::VALUE_OPTIONAL, 'Specifies a custom request method.', 'GET'),
			array('data', 'd', InputOption::VALUE_OPTIONAL, 'Sends the specified data in a POST request to the HTTP server.', array())
		);
	}

}
