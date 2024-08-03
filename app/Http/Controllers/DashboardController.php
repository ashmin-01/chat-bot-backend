<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

class DashboardController extends Controller
{
    protected $client;
    protected $baseUrl;

    public function __construct()
    {
        $this->client = new Client();
        $this->baseUrl = 'http://localhost:8002'; // URL to your FastAPI server
    }

    // Method to configure FastAPI
    public function configure(Request $request)
    {
        $response = $this->client->post("{$this->baseUrl}/configure", [
            'form_params' => [
                'embedding_model_name' => $request->input('embedding_model_name'),
                'api_key' => $request->input('api_key'),
                'k' => $request->input('k'),
                'text_splitter_chunk_size' => $request->input('text_splitter_chunk_size'),
                'text_splitter_chunk_overlap' => $request->input('text_splitter_chunk_overlap'),
                'prompt_template' => $request->input('prompt_template'),
            ]
        ]);

        return json_decode($response->getBody(), true);
    }

    // Method to get FastAPI configuration
    public function getConfiguration()
    {
        $response = $this->client->get("{$this->baseUrl}/configuration");
        return json_decode($response->getBody(), true);
    }

    // Method to upload a file to FastAPI
    public function uploadFile(Request $request)
    {
        $file = $request->file('file');

        $response = $this->client->post("{$this->baseUrl}/upload", [
            'multipart' => [
                [
                    'name' => 'file',
                    'contents' => fopen($file->getPathname(), 'r'),
                    'filename' => $file->getClientOriginalName(),
                ]
            ]
        ]);

        return json_decode($response->getBody(), true);
    }
}
