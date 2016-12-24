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
    	
        $controllers = $app['controllers_factory'];

        $controllers->get('/', function (Application $app) {
          
          return "Process Controller";
        });  

        $controllers->post('/', function (Request $request, Application $app) {
            
            $data = json_decode($request->getContent(), true);

            if (!$data) {
            	
        		return $app->json("Bad Request: Missing Parameters", 400);
            }

            $h = $data["h"];

            if ($h == 0) {
            
            	return $app->json("Bad Request: H value is invalid", 400);
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

	            if ($current_height > $well_height) {

	                $message = "Success on day " . $day;
	                break;
	            }

	            $current_height -= $slide_height;

	            if ($current_height < 0) {

	                $message = "Failure on day " . $day;
	                break;
	            }

	            $strength -= $fatigue;

	            if ($strength < 0) {

	                $strength = 0;
	            }


	  		}

            $app['db']->insert('SNAIL_LOG', array(
				'H'        => $data["h"],
				'D'        => $data["d"],
				'U'        => $data["u"],
                'F'        => $data["f"],
				'result'   => $message,
				'DATE'     => date("Y-m-d H:i:s")
            ));


        	return $app->json("message: ".$message, 200);
        });

        $controllers->get('/getCallsTable', function (Application $app, Request $request) {
                      
          if ($request->isXmlHttpRequest()) {

            $table_data = new DataSource($app);

            $table_data->get('SNAIL_LOG', 'ID', array(
                'H', 'U', 'D', 'F', 'result'));

            return "";

          } else {
            
            $app->abort(404, "Method not found");
          }
        });

        return $controllers;
    }

}

class DataSource {
    
    public function __construct($app) {

        $this->_db = $app['db'];
    }

    public function get($table, $index_column, $columns) {
        // Paging
        $sLimit = "";
        if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' ) {
            $sLimit = "LIMIT ".intval( $_GET['iDisplayStart'] ).", ".intval( $_GET['iDisplayLength'] );
        }
        // Ordering
        $sOrder = "";
        if ( isset( $_GET['iSortCol_0'] ) ) {
            $sOrder = "ORDER BY  ";
            for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ ) {
                if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" ) {
                    $sortDir = (strcasecmp($_GET['sSortDir_'.$i], 'ASC') == 0) ? 'ASC' : 'DESC';
                    $sOrder .= "`".$columns[ intval( $_GET['iSortCol_'.$i] ) ]."` ". $sortDir .", ";
                }
            }
            $sOrder = substr_replace( $sOrder, "", -2 );
            if ( $sOrder == "ORDER BY" ) {
                $sOrder = "";
            }
        }
        /*
         * Filtering
         * NOTE this does not match the built-in DataTables filtering which does it
         * word by word on any field. It's possible to do here, but concerned about efficiency
         * on very large tables, and MySQL's regex functionality is very limited
         */
        $sWhere = "";
        if ( isset($_GET['sSearch']) && $_GET['sSearch'] != "" ) {
            $sWhere = "WHERE (";
            for ( $i=0 ; $i<count($columns) ; $i++ ) {
                if ( isset($_GET['bSearchable_'.$i]) && $_GET['bSearchable_'.$i] == "true" ) {
                    $sWhere .= "`".$columns[$i]."` LIKE :search OR ";
                }
            }
            $sWhere = substr_replace( $sWhere, "", -3 );
            $sWhere .= ')';
        }
        // Individual column filtering
        for ( $i=0 ; $i<count($columns) ; $i++ ) {
            if ( isset($_GET['bSearchable_'.$i]) && $_GET['bSearchable_'.$i] == "true" && $_GET['sSearch_'.$i] != '' ) {
                if ( $sWhere == "" ) {
                    $sWhere = "WHERE ";
                }
                else {
                    $sWhere .= " AND ";
                }
                $sWhere .= "`".$columns[$i]."` LIKE :search".$i." ";
            }
        }
        // SQL queries get data to display
        $sQuery = "SELECT SQL_CALC_FOUND_ROWS `".str_replace(" , ", " ", implode("`, `", $columns))."` FROM `".$table."` ".$sWhere." ".$sOrder." ".$sLimit;
        $statement = $this->_db->prepare($sQuery);
        // Bind parameters
        if ( isset($_GET['sSearch']) && $_GET['sSearch'] != "" ) {
                $statement->bindValue(':search', '%'.$_GET['sSearch'].'%');
                //$statement->execute(array(':search' => '%'.$_GET['sSearch'].'%'));
        } else {
            $statement->execute();
        }
        for ( $i=0 ; $i<count($columns) ; $i++ ) {
            if ( isset($_GET['bSearchable_'.$i]) && $_GET['bSearchable_'.$i] == "true" && $_GET['sSearch_'.$i] != '' ) {
                $statement->bindValue(':search'.$i, '%'.$_GET['sSearch_'.$i].'%');
                //$statement->execute(array(':search'.$i => '%'.$_GET['sSearch'.$i].'%'));
            }
        }
        $statement->execute();
        $rResult = $statement->fetchAll();
        $iFilteredTotal = current($this->_db->query('SELECT FOUND_ROWS()')->fetch());
        // Get total number of rows in table
        $sQuery = "SELECT COUNT(`".$index_column."`) FROM `".$table."`";
        $iTotal = current($this->_db->query($sQuery)->fetch());
        // Output
        if (empty($_GET['sEcho'])) {
            $_GET['sEcho'] = '';
        }
        $output = array(
            "sEcho"                => intval($_GET['sEcho']),
            "iTotalRecords"        => $iTotal,
            "iTotalDisplayRecords" => $iFilteredTotal,
            "aaData"               => array()
        );

        foreach($rResult as $aRow) {
            $row = array();
            for ( $i = 0; $i < count($columns); $i++ ) {

                if ( $columns[$i] != ' ' ) {

                    $row[] = $aRow[ $columns[$i] ];
                }

            }
            
            $output['aaData'][] = $row;
        }

        	header('Content-Type: application/json');
            echo json_encode( $output );
    }

}

?>