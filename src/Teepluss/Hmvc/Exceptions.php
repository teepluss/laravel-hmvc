<?php namespace Teepluss\Hmvc;

use Symfony\Component\HttpKernel\Exception\FatalErrorException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class HmvcFatalErrorException extends FatalErrorException {}
class HmvcNotFoundHttpException extends NotFoundHttpException {}
