<?php

namespace @namespace\Rmi\RemoteModels;

use Illuminate\Http\Request;
use Jenssegers\Model\Model;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ClientException;
use Log;

class Remote@model_name extends Model
{
    public static function find($id, $request) {
        if($id) {

            $route = "/@route/{$id}";
            $client = new \GuzzleHttp\Client([
                'headers' => [
                    'authorization' => $request->header('authorization')
                ]
            ]);

            try {
                Log::info(env('@workspace_name_upper_@module_upper_ENDPOINT') . $route);
                $response = $client->request('GET', env('@workspace_name_upper_@module_upper_ENDPOINT') . $route, ['connect_timeout' => env('@workspace_name_upper_RMI_TIMEOUT_SECONDS', 2)]
                );

                if($response->getStatusCode() == 200) {
                    $data = $response->getBody();

                    if($data) {
                        $data = json_decode($data, true);
                        $remoteData = new Remote@model_name($data['data']);
                        return $remoteData;
                    }
                }
            } catch (RequestException $e) {

                if($e->hasResponse()) {

                    $responseBody = $e->getResponse()->getBody()->getContents();
                    $responseCode = $e->getCode();

                    return response()->json(trim($responseBody, '"'), $responseCode);
                
                } else {
                    return response()->json($e->getMessage(), 500);
                }
            }
        }

        return false;
    }

    public static function findByIds($ids, $request) {

        if($ids && count($ids) > 0) {

            $ids = implode(",", $ids);
            $route = "/@route?page=all&ids=$ids";
            $client = new \GuzzleHttp\Client([
                'headers' => [
                    'authorization' => $request->header('authorization')
                ]
            ]);

            try {
                Log::info(env('@workspace_name_upper_@module_upper_ENDPOINT') . $route);
                $response = $client->request('GET', env('@workspace_name_upper_@module_upper_ENDPOINT') . $route, ['connect_timeout' => env('@workspace_name_upper_RMI_TIMEOUT_SECONDS', 2)]);
                
                if($response->getStatusCode() == 200) {
                    $data = $response->getBody();

                    if($data) {
                        $data = json_decode($data, true);
                        foreach($data['data'] as $key => $item) {
                            $data['data'][$key] = new Remote@model_name($item);
                        }

                        if(sizeof($data['data']) < 1) {
                            return response()->json("Invalid Ids", '400');
                        }
    
                        return $data['data'];
                    }
                }
            } catch (RequestException $e) {

                if($e->hasResponse()) {

                    $responseBody = $e->getResponse()->getBody()->getContents();
                    $responseCode = $e->getCode();

                    return response()->json(trim($responseBody, '"'), $responseCode);
                
                } else {
                    return response()->json($e->getMessage(), 500);
                }
            }
        }

        return false;
    }

    public static function findByQuery($query, $request) {

        $route = "/@route";
        $client = new \GuzzleHttp\Client([
            'headers' => [
                'authorization' => $request->header('authorization')
            ]
        ]);

        try {
            Log::info(env('@workspace_name_upper_@module_upper_ENDPOINT') . $route . '?' . http_build_query($query));
            $response = $client->request('GET', env('@workspace_name_upper_@module_upper_ENDPOINT') . $route, ['connect_timeout' => env('@workspace_name_upper_RMI_TIMEOUT_SECONDS', 2), 'query' => $query]);
            
            if($response->getStatusCode() == 200) {
                $data = $response->getBody();

                if($data) {
                    $data = json_decode($data, true);
                    foreach($data['data'] as $key => $item) {
                        $data['data'][$key] = new Remote@model_name($item);
                    }
                    
                    if(sizeof($data['data']) < 1) {
                        return response()->json("Invalid Data", '400');
                    }

                    return $data['data'];
                }
            }
        } catch (RequestException $e) {

            if($e->hasResponse()) {

                $responseBody = $e->getResponse()->getBody()->getContents();
                $responseCode = $e->getCode();

                return response()->json(trim($responseBody, '"'), $responseCode);
            
            } else {
                return response()->json($e->getMessage(), 500);
            }
        }

        return false;
    }

    public static function save($data, $request) {

        if($data) {

            $route = "/@route";
            $client = new \GuzzleHttp\Client([
                'headers' => [
                    'authorization' => $request->header('authorization')
                ]
            ]);

            try {
                Log::info(env('@workspace_name_upper_@module_upper_ENDPOINT') . $route);
                $response = $client->request('POST', env('@workspace_name_upper_@module_upper_ENDPOINT') . $route, ['connect_timeout' => env('@workspace_name_upper_RMI_TIMEOUT_SECONDS', 2), 'json' => $data]);
                
                if($response->getStatusCode() == 201) {
                    $data = $response->getBody();

                    if($data) {
                        $data = json_decode($data, true);
                        $remoteData = new Remote@model_name($data['data']);
                        return $remoteData;
                    }
                }
            } catch (RequestException $e) {

                if($e->hasResponse()) {

                    $responseBody = $e->getResponse()->getBody()->getContents();
                    $responseCode = $e->getCode();
    
                    return response()->json(trim($responseBody, '"'), $responseCode);
                
                } else {
                    return response()->json($e->getMessage(), 500);
                }
            }
        }

        return false;
    }

    public static function update($data, $id, $request) {

        if($data && $id) {

            $route = "/@route/$id";
            $client = new \GuzzleHttp\Client([
                'headers' => [
                    'authorization' => $request->header('authorization')
                ]
            ]);

            try {
                Log::info(env('@workspace_name_upper_@module_upper_ENDPOINT') . $route);
                $response = $client->request('POST', env('@workspace_name_upper_@module_upper_ENDPOINT') . $route, ['connect_timeout' => env('@workspace_name_upper_RMI_TIMEOUT_SECONDS', 2), 'json' => $data]);

                if($response->getStatusCode() == 200) {
                    $data = $response->getBody();

                    if($data) {
                        $data = json_decode($data, true);
                        $remoteData = new Remote@model_name($data['data']);
                        return $remoteData;
                    }
                }
            } catch (RequestException $e) {

                if($e->hasResponse()) {

                    $responseBody = $e->getResponse()->getBody()->getContents();
                    $responseCode = $e->getCode();
    
                    return response()->json(trim($responseBody, '"'), $responseCode);
                
                } else {
                    return response()->json($e->getMessage(), 500);
                }
            }
        }

        return false;
    }

    public static function delete($id, $request) {

        if($id) {

            $route = "/@route/$id";
            $client = new \GuzzleHttp\Client([
                'headers' => [
                    'authorization' => $request->header('authorization')
                ]
            ]);

            try {
                Log::info(env('@workspace_name_upper_@module_upper_ENDPOINT') . $route);
                $response = $client->request('DELETE', env('@workspace_name_upper_@module_upper_ENDPOINT') . $route, [ 'connect_timeout' => env('@workspace_name_upper_RMI_TIMEOUT_SECONDS', 2)]);
                
                if($response->getStatusCode() == 200) {
                    $data = $response->getBody();

                    if($data) {
                        $data = json_decode($data, true);
                        $remoteData = new Remote@model_name($data['data']);
                        return $remoteData;
                    }
                }
            } catch (RequestException $e) {

                if($e->hasResponse()) {

                    $responseBody = $e->getResponse()->getBody()->getContents();
                    $responseCode = $e->getCode();
    
                    return response()->json(trim($responseBody, '"'), $responseCode);
                
                } else {
                    return response()->json($e->getMessage(), 500);
                }
            }
        }

        return false;
    }

    public static function restore($id, $request) {

        if($id) {

            $route = "/@route/$id";
            $client = new \GuzzleHttp\Client([
                'headers' => [
                    'authorization' => $request->header('authorization')
                ]
            ]);

            try {
                Log::info(env('@workspace_name_upper_@module_upper_ENDPOINT') . $route);
                $response = $client->request('POST', env('@workspace_name_upper_@module_upper_ENDPOINT') . $route, ['connect_timeout' =>env('JARPLAY_RMI_TIMEOUT_SECONDS', 2)]);
                
                if($response->getStatusCode() == 200) {
                    $data = $response->getBody();

                    if($data) {
                        $data = json_decode($data, true);
                        $remoteData = new Remote@model_name($data['data']);
                        return $remoteData;
                    }
                }
            } catch (RequestException $e) {

                if($e->hasResponse()) {

                    $responseBody = $e->getResponse()->getBody()->getContents();
                    $responseCode = $e->getCode();
    
                    return response()->json(trim($responseBody, '"'), $responseCode);
                
                } else {
                    return response()->json($e->getMessage(), 500);
                }
            }
        }

        return false;
    }
}