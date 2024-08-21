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
                    <h5 class="title">model Information</h5>
                </div>
                <div class="card-body">
                    <form>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Hhgging face api key</label>
                                    <input type="text" class="form-control" placeholder="Enter information" id="field1" disabled>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Groq api key</label>
                                    <input type="text" class="form-control" placeholder="Enter information" id="field2" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Coher api key</label>
                                    <input type="text" class="form-control" placeholder="Enter information" id="field3" disabled>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Weaviate api key</label>
                                    <input type="text" class="form-control" placeholder="Enter information" id="field4" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Weaviate cluster url</label>
                                    <input type="text" class="form-control" placeholder="Enter information" id="field5" disabled>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Weaviate collection name</label>
                                    <input type="text" class="form-control" placeholder="Enter information" id="field6" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="row justify-content-center">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Embedding model name</label>
                                    <input type="text" class="form-control" placeholder="Enter information" id="field7" disabled>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-footer">
                    <button type="button" class="btn btn-outline-primary" id="editButton">Edit</button>
                    <button type="submit" class="btn btn-danger" id="saveButton" disabled>Save</button>
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
        const fields = document.querySelectorAll('.form-control');
        fields.forEach(field => field.disabled = true);
        document.getElementById('saveButton').disabled = true;
        document.getElementById('editButton').classList.remove('btn-fill');
        alert('Information saved successfully!');
    });
</script>
@endsection