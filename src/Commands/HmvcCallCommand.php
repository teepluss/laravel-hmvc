<?php namespace Teepluss\Hmvc\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use Teepluss\Hmvc\Hmvc;

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
	protected $description = 'Command description.';

	/**
	 * HMCV.
	 *
	 * @var [type]
	 */
	protected $hmvc;

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct(Hmvc $hmvc)
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
		//var_dump(get_class_methods($this->hmvc));
		echo $this->hmvc->get('/');
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return [
			['example', InputArgument::OPTIONAL, 'An example argument.'],
		];
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return [
			['example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
		];
	}

}
