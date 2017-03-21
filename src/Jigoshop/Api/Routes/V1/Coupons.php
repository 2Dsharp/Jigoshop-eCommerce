<?php

namespace Jigoshop\Api\Routes\V1;

use Slim\App;

/**
 * Class Coupons
 * @package Jigoshop\Api\Routes\V1;
 * @author Maciej Maciaszek
 */
class Coupons extends PostController
{
    /** @var  App */
    protected $app;

    /**
     * Coupons constructor.
     * @param App $app
     */
    public function __construct(App $app)
    {
        parent::__construct($app);
        $this->app = $app;
        $app->get('', array($this, 'findAll'));
        $app->get('/{id:[0-9]+}', array($this, 'findOne'));
        $app->post('', array($this, 'create'));
    }


}