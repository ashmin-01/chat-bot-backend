<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{


    public function showFiles()
    {
        // Fetch data from the FastAPI endpoint
        $response = Http::get('http://127.0.0.1:9000/dashboard/get-all-files');

        // Decode the JSON response into an array
        $files = $response->json();

        // Pass the data to the view
        return view('home.typography', ['files' => $files]);
    }

    // Method to configure FastAPI
    public function configure(Request $request)
    {

        $chatbotUrl = env('CHATBOT_API_URL');
        // Validate the incoming request data
        $validatedData = $request->validate([
            'embedding_model_name' => 'nullable|string',
            'hugging_api_key' => 'nullable|string',
            'weaviate_cluster_url' => 'nullable|string',
            'weaviate_api_key' => 'nullable|string',
            'weaviate_collection_name' => 'nullable|string',
        ]);

        try {
            // Send POST request to FastAPI
            $fastapiUrl = env('CHATBOT_API_URL') . '/dashboard/update-env';

            $response = Http::timeout(60)  // Set timeout
                    ->withOptions(['verify' => false])  // Disable SSL verification (if necessary)
                    ->asForm()  // Send data as form parameters
                    ->post($fastapiUrl, $validatedData);

            if ($response->successful()) {
                return redirect()->back()->with('status', 'Information saved successfully!');
            } else {
                return redirect()->back()->with('error', 'Failed to save information.');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function update_template(Request $request)
    {
        $validatedData = $request->validate([
            'prompt_template' => 'required|string',
        ]);

        try {
            $fastapiUrl = env('CHATBOT_API_URL') . '/dashboard/update-template';

            $response = Http::timeout(60)
                ->withOptions(['verify' => false])
                ->withHeaders(['Content-Type' => 'text/plain'])
                ->send('POST', $fastapiUrl, [
                    'body' => $validatedData['prompt_template']
                ]);

            // Return JSON response
            return response()->json([
                'status' => 'success',
                'message' => 'Information saved successfully!',
                'response' => $response->body()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    public function upload_document(Request $request)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'name' => 'required|string',
            'active' => 'nullable|boolean',
            'date' => 'required|date',
            'file' => 'required|file|mimes:html|max:2048',
        ]);

        try {
            // Handle the uploaded file
            $file = $request->file('file');
            $filePath = $file->storeAs('documents', $file->getClientOriginalName());

            // Prepare data for FastAPI
            $data = [
                'name' => $validatedData['name'],
                'active' => $validatedData['active'] ?? false,
                'date' => $validatedData['date'],
            ];

            // Send POST request to FastAPI
            $fastapiUrl = env('CHATBOT_API_URL') . '/dashboard/add-document/';

            $response = Http::timeout(60)
                ->withOptions(['verify' => false])
                ->attach(
                    'file', // The name of the file field in the FastAPI endpoint
                    file_get_contents(Storage::path($filePath)),
                    $file->getClientOriginalName()
                )
                ->post($fastapiUrl, $data);

            if ($response->successful()) {
                return redirect()->back()->with('status', 'Document uploaded successfully!');
            } else {
                return redirect()->back()->with('error', 'Failed to upload document.');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }


    public function searchDocuments(Request $request)
    {
        $property = $request->input('property');
        $metadataFilter = $request->input('metadata_filter');

        $fastapiUrl = env('CHATBOT_API_URL') . '/dashboard/search-document';

        // Send the request to the FastAPI server
        $response = Http::post($fastapiUrl, [
            'property' => $property,
            'metadata_filter' => $metadataFilter,
        ]);

        if ($response->successful()) {
            return response()->json($response->json());
        } else {
            return response()->json(['error' => 'Failed to fetch search results'], 500);
        }
    }


    public function deleteFile(Request $request)
    {
        $fileName = $request->input('fileName');
        $property = $request->input('property');
        $metadataFilter = $request->input('metadataFilter');

        try {
            // Make the DELETE request to FastAPI
            $response = Http::post('http://127.0.0.1:9000/dashboard/delete-document', [
                'property' => $property,
                'metadata_filter' => $metadataFilter,
            ]);

            // Handle the response from FastAPI
            $responseData = $response->json();

            // Check if the deletion was successful
            if ($responseData['successful'] > 0) {
                return response()->json(['success' => true]);
            } else {
                return response()->json(['success' => false, 'message' => 'No matches found or deletion failed.']);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }


}
