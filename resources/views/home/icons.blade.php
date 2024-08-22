@extends('layouts.base')

@section('title', 'Dashboard - Edit Information')

@section('stylesheets')
@endsection

@section('content')

<div class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="title">Model Information</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('dashboard.configure') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Hugging Face API Key</label>
                                    <input type="text" name="hugging_api_key" class="form-control" placeholder="Enter information" id="field1" disabled>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Groq API Key</label>
                                    <input type="text" name="groq_api_key" class="form-control" placeholder="Enter information" id="field2" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Coher API Key</label>
                                    <input type="text" name="coher_api_key" class="form-control" placeholder="Enter information" id="field3" disabled>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Weaviate API Key</label>
                                    <input type="text" name="weaviate_api_key" class="form-control" placeholder="Enter information" id="field4" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Weaviate Cluster URL</label>
                                    <input type="text" name="weaviate_cluster_url" class="form-control" placeholder="Enter information" id="field5" disabled>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Weaviate Collection Name</label>
                                    <input type="text" name="weaviate_collection_name" class="form-control" placeholder="Enter information" id="field6" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="row justify-content-center">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Embedding Model Name</label>
                                    <input type="text" name="embedding_model_name" class="form-control" placeholder="Enter information" id="field7" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="button" class="btn btn-outline-primary" id="editButton">Edit</button>
                            <button type="submit" class="btn btn-danger" id="saveButton" disabled>Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('javascripts')
<script>
    document.getElementById('editButton').addEventListener('click', function() {
        const fields = document.querySelectorAll('.form-control');
        fields.forEach(field => field.disabled = false);
        document.getElementById('saveButton').disabled = false;
        document.getElementById('editButton').classList.add('btn-fill');
    });

    document.getElementById('saveButton').addEventListener('click', function(event) {
        event.preventDefault();
        document.querySelector('form').submit();  // Submit the form programmatically
    });
</script>
@endsection
