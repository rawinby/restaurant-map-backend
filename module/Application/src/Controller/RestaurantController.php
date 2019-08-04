<?php
namespace Application\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;
use Zend\Json\Json;
use Zend\Http\Client;
use Zend\Json\Decoder;

class RestaurantController extends AbstractRestfulController
{

    private $config;

    public function __construct(array $config)
    {
        $this->config = $config;
        // $this->config['googlemap_key'];
    }

    public function getList()
    {
        $textsearch = urlencode($this->params('textsearch'));
        $client = new Client();
        
        $type = 'food';
        $api_endpoint = 'https://maps.googleapis.com/maps/api/place/textsearch/json?key='. $this->config['googlemap_key'] .'&language=th&inputtype=textquery&type='. $type .'&query='. $textsearch;
        $client->setUri($api_endpoint);
        $client->setMethod('GET');
        $client->setOptions([
            'timeout'      => 30,
        ]);
        $res = (array) Decoder::decode($client->send()->getBody());

        if(empty($res['results'])){
            $type = 'restaurant';
            $api_endpoint = 'https://maps.googleapis.com/maps/api/place/textsearch/json?key='. $this->config['googlemap_key'] .'&language=th&inputtype=textquery&type='. $type .'&query='. $textsearch;
            $client->setUri($api_endpoint);
            $client->setMethod('GET');
            $client->setOptions([
                'timeout'      => 30,
            ]);
            $res = (array) Decoder::decode($client->send()->getBody());
        }

        return new JsonModel([
            'status' => '000000',
            'message' => 'success',
            'textsearch' => $textsearch,
            'type' => $type,
            'data' => $res['results'],
        ]);
    }

    public function get($id)
    {
        $api_endpoint = 'https://maps.googleapis.com/maps/api/place/details/json?key='. $this->config['googlemap_key'] 
        .'&language=th&placeid='. $id;
        
        $client = new Client();
        $client->setUri($api_endpoint);
        $client->setMethod('GET');
        $client->setOptions([
            'timeout'      => 30,
        ]);
        $res = Decoder::decode($client->send()->getBody());

        return new JsonModel([
            'status' => '000000',
            'message' => 'success',
            'placeid' => $id,
            'data' => $res->result,
        ]);
    }

}
