<?php

namespace Jigoshop\Api\Routes\V1;

use Jigoshop\Api\Contracts\ApiControllerContract;
use Jigoshop\Entity\Product as ProductEntity;
use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class Products
 * @package Jigoshop\Api\Routes\V1;
 * @author Krzysztof Kasowski
 */
class Products extends PostController implements ApiControllerContract
{
    /** @var  App */
    protected $app;

    /**
     * Products constructor.
     * @param App $app
     */
    public function __construct(App $app)
    {
        parent::__construct($app);
        $this->app = $app;

        $app->get('', array($this, 'findAll'));
        $app->get('/{id:[0-9]+}', array($this, 'findOne'));
        $app->post('', array($this, 'create'));
        $app->put('/{id:[0-9]+}', array($this, 'update'));
        $app->delete('/{id:[0-9]+}', array($this, 'delete'));
    }

    /**
     * overrided create function from PostController
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     */
    public function create(Request $request, Response $response, $args)
    {
        $factory = $this->app->getContainer()->di->get('jigoshop.factory.product');
        self::overridePostProductData();
        $product = $factory->create(null);
        $service = $this->app->getContainer()->di->get('jigoshop.service.product');
        $service->save($product);

        return $response->withJson([
            'success' => true,
            'data' => "$this->entityName successfully created",
        ]);
    }

    /**
     * overrided update function from PostController
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     */
    public function update(Request $request, Response $response, $args)
    {
        $object = $this->validateObjectFinding($args);

        $putData = self::overridePutProductData($request->getParsedBody());
        $factory = $this->app->getContainer()->di->get('jigoshop.factory.product');
        $this->saveAttributes($putData['product']);
        $product = $factory->update($object, $putData); //updating object with parsed variables
        $service = $this->app->getContainer()->di->get('jigoshop.service.product');
        $service->updateAndSavePost($product);

        return $response->withJson([
            'success' => true,
            'data' => "Product successfully updated",
        ]);
    }

    /**
     * helper function that makes product saving available
     */
    public static function overridePostProductData()
    {
        $_POST['product'] = $_POST['jigoshop_product'];
        unset($_POST['jigoshop_product']);
    }

    /**
     * @param array $data
     * @return array
     */
    public static function overridePutProductData(array $data)
    {
        $data['product'] = $data['jigoshop_product'];
        unset($data['jigoshop_product']);
        return $data;
    }


    /**
     * converts attributes in array to updated or created attribute objects
     * @param array $productData
     */
    private function saveAttributes(array &$productData)
    {
        if (isset($productData['attributes'])) {
            foreach ($productData['attributes'] as $key => &$attribute) {
                if (!$dbAttr = $this->service->getAttribute($key)) {
                    $dbAttr = $this->service->createAttribute(null);
                } else {
                    $dbAttr->setExists(true);
                }
                $dbAttr->setValue($attribute);
                $attribute = $this->service->saveAttribute($dbAttr);
            }
        }
    }

}