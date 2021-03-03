<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Validation\Exceptions\ValidationException;
use Psr\Log\LoggerInterface;
use Config\Services;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */

class BaseController extends Controller
{
	/**
	 * An array of helpers to be loaded automatically upon
	 * class instantiation. These helpers will be available
	 * to all other controllers that extend BaseController.
	 *
	 * @var array
	 */
	protected $helpers = [];

	/**
	 * Constructor.
	 *
	 * @param RequestInterface  $request
	 * @param ResponseInterface $response
	 * @param LoggerInterface   $logger
	 */
	public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
	{
		// Do Not Edit This Line
		parent::initController($request, $response, $logger);

		//--------------------------------------------------------------------
		// Preload any models, libraries, etc, here.
		//--------------------------------------------------------------------
		// E.g.: $this->session = \Config\Services::session();
	}

	public function sendResponse(array $responseBody, int $code = ResponseInterface::HTTP_OK)
	{
		return $this->response
			->setStatusCode($code)
			->setJSON($responseBody);
	}

	public function getRequestInputs(IncomingRequest $request)
	{
		$inputs = $request->getPost();
		if (empty($inputs))
			$inputs = json_decode($request->getBody(), true);
		return $inputs;
	}

	public function validateRequest($inputs,array $rules, array $messages = [])
	{
		$this->validator = Services::Validation()->setRules($rules);
		if (is_string($rules)) {
			$validation = config('Validation');
			if (!isset($validation->$rules))
			throw ValidationException::forRuleNotFound($rules);

			if(!$messages){
				$errorName = $rules.'_errors';
				$messages = $validation->$errorName ?? [];
			}
			$rules = $validation->$rules;
		}

		return $this->validator->setRules($rules, $messages)->run(($inputs));
	}
}
