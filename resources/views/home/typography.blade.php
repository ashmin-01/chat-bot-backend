@extends('layouts.base')

@section('title', 'File Dashboard')

@section('stylesheets')
<style>
  .file-card {
    border: 1px solid #ddd;
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 10px;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  .file-list {
    margin-top: 20px;
    margin-bottom: 20px;
  }
  .file-list span {
    color: #007bff;
  }
  .file-list span:hover {
    text-decoration: underline;
    cursor: pointer;
  }
  .delete-button {
    color: #dc3545;
    cursor: pointer;
    font-weight: bold;
  }
  .search-form {
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
  }
  .search-form input {
    padding: 5px;
  }
  .search-form .warning {
    color: red;
    font-size: 12px;
    display: none;
  }
</style>
@endsection

@section('content')
<div class="content">
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Manage Your Files</h3>
        </div>
        <div class="card-body">
          <!-- Search Form -->
          <div class="search-form">
            <input type="text" id="searchProperty" placeholder="Property (e.g., name)">
            <input type="text" id="searchValue" placeholder="Value">
            <button class="btn btn-secondary" onclick="applySearch()">Search</button>
            <button class="btn btn-secondary" onclick="resetSearch()">Reset</button>
            <span class="warning" id="warningMessage">Please fill in both fields.</span>
          </div>

          <!-- List of Files -->
          <div class="file-list" id="fileList">
            @foreach($files as $file)
            <div class="file-card">
              <span>{{ $file['name'] }}</span>
              <span class="delete-button" onclick="deleteFile('{{ $file['name'] }}')">Delete</span>
            </div>
            @endforeach
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  const fileList = document.getElementById('fileList');
  const searchPropertyInput = document.getElementById('searchProperty');
  const searchValueInput = document.getElementById('searchValue');
  const warningMessage = document.getElementById('warningMessage');

  function applySearch() {
    const property = searchPropertyInput.value.trim().toLowerCase();
    const value = searchValueInput.value.trim().toLowerCase();

    if (!property || !value) {
        warningMessage.style.display = 'inline';
        return;
    }

    warningMessage.style.display = 'none';

    // Send search request to Laravel controller
    fetch('/search-documents', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            property: property,
            metadata_filter: value
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            alert('Search failed: ' + data.error);
            return;
        }

        // Clear the current file list
        fileList.innerHTML = '';

        // Display the search results
        data.forEach(result => {
            const fileCard = document.createElement('div');
            fileCard.className = 'file-card';

            const fileName = document.createElement('span');
            fileName.textContent = result.name; // Display the file name

            const deleteButton = document.createElement('span');
            deleteButton.className = 'delete-button';
            deleteButton.textContent = 'Delete';
            deleteButton.onclick = () => deleteFile(result.name); // Handle deletion

            fileCard.appendChild(fileName);
            fileCard.appendChild(deleteButton);
            fileList.appendChild(fileCard);
        });
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

  function resetSearch() {
    searchPropertyInput.value = '';
    searchValueInput.value = '';
    warningMessage.style.display = 'none';

    // Clear the file list or reset the search results
    // You might want to re-fetch all files if needed
  }

  function deleteFile(fileName) {
    // Find the file card to delete
    const fileCard = Array.from(fileList.getElementsByClassName('file-card'))
        .find(card => card.querySelector('span').textContent === fileName);

    if (fileCard) {
        // Extract the property and metadata filter
        const property = 'name'; // or however you determine the property
        const metadataFilter = fileName; // Assuming the fileName is the metadata filter

        // Send the delete request to the Laravel controller
        fetch('/delete-file', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                fileName: fileName,
                property: property,
                metadataFilter: metadataFilter
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Remove the file card from the UI
                fileList.removeChild(fileCard);
                alert('File deleted successfully.');
            } else {
                alert('Failed to delete file: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
}
</script>
@endsection

@section('javascripts')
@endsection
