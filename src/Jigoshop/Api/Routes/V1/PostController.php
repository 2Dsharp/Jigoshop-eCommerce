<?php

namespace Jigoshop\Api\Routes\V1;


use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;
use Jigoshop\Exception;

/**
 * extend this class in order to get RESTful methods for PostArray
 * It takes class name to provide service, entity and type. You can override this if there is need by providing
 * $service, $entityName
 * Class PostController
 * @package Jigoshop\Api\Routes\V1
 */
abstract class PostController
{
    /**
     * prefix to services
     */
    const JIGOSHOP_SERVICE_PREFIX = 'jigoshop.service.';
    /**
     * path to type class
     */
    const JIGOSHOP_TYPES_PREFIX = 'Jigoshop\\Core\\Types::';
    /**
     * path to entities
     */
    const JIGOSHOP_ENTITY_PREFIX = 'Jigoshop\\Entity\\';
    /**
     * @var string
     */
    protected $entityName;
    /**
     * @var string
     */
    protected $serviceName;
    /**
     * @var
     */
    protected $service;
    /**
     * @var App
     */
    protected $app;

    /**
     * PostController constructor.
     * @param App $app
     */
    public function __construct(App $app)
    {
        $this->app = $app;
        $this->entityName = $this->entityName ?: $this->singularize(strtolower((new \ReflectionClass($this))->getShortName()));
        $this->serviceName = $this->serviceName ?: self::JIGOSHOP_SERVICE_PREFIX . strtolower((new \ReflectionClass($this))->getShortName());
        $this->service = $this->app->getContainer()->di->get($this->singularize($this->serviceName));
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     */
    public function findAll(Request $request, Response $response, $args)
    {
        $queryParams = $request->getParams();
        $queryParams['pagelen'] = isset($queryParams['pagelen']) && is_numeric($queryParams['pagelen']) ? (int)$queryParams['pagelen'] : 10;
        $queryParams['page'] = isset($queryParams['page']) && is_numeric($queryParams['page']) ? (int)$queryParams['page'] : 1;

        $items = $this->service->findByQuery(new \WP_Query([
            'post_type' => constant(self::JIGOSHOP_TYPES_PREFIX . strtoupper($this->entityName)),
            'posts_per_page' => $queryParams['pagelen'],
            'paged' => $queryParams['page'],
        ]));

        return $response->withJson([
            'success' => true,
            'all_results' => call_user_func(array($this->service, 'get' . $this->entityName . 'sCount')),
            'pagelen' => $queryParams['pagelen'],
            'page' => $queryParams['page'],
            'next' => '',
            'previous' => '',
            'data' => array_values($items),
        ]);
    }


    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     */
    public function findOne(Request $request, Response $response, $args)
    {
        if (!isset($args['id']) || empty($args['id'])) {
            throw new Exception("$this->entityName ID was not provided");
        }

        $item = $this->service->find($args['id']);
        $entity = self::JIGOSHOP_ENTITY_PREFIX . ucfirst($this->entityName);

        if (!$item instanceof $entity) {
            throw new Exception("$this->entityName not found.", 404);
        }

        return $response->withJson([
            'success' => true,
            'data' => $item,
        ]);
    }

    /**
     * function to shorten plural to singular strings
     *
     * @param $string
     * @param string $ending
     * @return string
     */
    private function singularize($string, $ending = 's')
    {
        return rtrim($string, "$ending");
    }
}