<?php

Namespace TestSnail\Test;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ParameterBag;

class ProcessDataController implements ControllerProviderInterface
{
    public function __construct(Application $app) {


    }

    public function connect(Application $app) {
        // creates a new controller based on the default route
        $controllers = $app['controllers_factory'];

        $controllers->get('/', function (Application $app) {
          
          return "Process Controller";
        });  


        $controllers->post('/', function (Request $request, Application $app) {
            
            $data = json_decode($request->getContent(), true);

            if (!$data) {
            	
        		return $app->json("Bad Request", 400);
            }

            $h = $data["h"];

            if ($h == 0) {
            
            	return $app->json("Bad Request", 400);
            }

	        // Run the snail!
			$well_height    = $data["h"];
			$slide_height   = $data["d"];
			$strength       = $data["u"];
			$current_height = 0;
			$fatigue        = $data["f"] / 100.0 * $strength;

			$day = 0;
			
	     	while (true) {

	     		$day++;

	            $current_height += $strength;
	            if ($current_height > $well_height)
	            {

	                return $app->json("Success on day " . $day, 200);
	                break;
	            }
	            $current_height -= $slide_height;
	            if ($current_height < 0)
	            {

	                return $app->json("Failure on day " . $day, 200);
	                break;
	            }

	            $strength -= $fatigue;
	            if ($strength < 0)
	            {
	                $strength = 0;
	            }


	  		}

        	return $app->json("message: Ok ", 200);
        });

        return $controllers;
    }

}

?>