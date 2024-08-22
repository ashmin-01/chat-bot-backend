@extends('layouts.base')

@section('title', 'Feedback Tables')

@section('stylesheets')
<style>
  .table-container {
    height: 300px;
    overflow-y: auto;
  }
  .table-container::-webkit-scrollbar {
    width: 8px;
  }
  .table-container::-webkit-scrollbar-thumb {
    background-color: #888;
    border-radius: 4px;
  }
  .table-container::-webkit-scrollbar-thumb:hover {
    background-color: #555;
  }
  .table th, .table td {
    text-align: center;
  }
</style>
@endsection

@section('content')
<div class="content">
  <div class="row">
    @isset($badResponses)
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <h4 class="card-title">Bad Response Feedback</h4>
        </div>
        <div class="card-body table-container">
          <table class="table tablesorter">
            <thead class="text-primary">
              <tr>
                <th>Question</th>
                <th>Response</th>
                <th>Feedback</th>
              </tr>
            </thead>
            <tbody>
              @forelse($badResponses as $response)
                <tr>
                  <td>{{ $response->prompt->prompt_content }}</td>
                  <td>{{ $response->response_content }}</td>
                  <td>{{ $response->feedback->context }}</td>
                </tr>
              @empty
                <tr>
                  <td colspan="3">No bad responses found.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
    @endisset

    @isset($regeneratedFeedbacks)
    <div class="col-md-12 mt-4">
      <div class="card">
        <div class="card-header">
          <h4 class="card-title">Regenerated Response Feedback</h4>
        </div>
        <div class="card-body table-container">
          <table class="table tablesorter">
            <thead class="text-primary">
              <tr>
                <th>Question</th>
                <th>Response Before</th>
                <th>Response After</th>
                <th>Feedback</th>
              </tr>
            </thead>
            <tbody>
              @forelse($regeneratedFeedbacks as $feedback)
                @php
                  // Get the new response
                  $newResponse = $feedback->response;

                  // Get the old (archived) response related to the same prompt
                  $oldResponse = $newResponse->prompt->responses->firstWhere('archived', true);
                @endphp
                <tr>
                  <td>{{ $newResponse->prompt->prompt_content }}</td>
                  <td>{{ $oldResponse->response_content ?? 'N/A' }}</td>
                  <td>{{ $newResponse->response_content }}</td>
                  <td>{{ $feedback->regenerate_review }}</td>
                </tr>
              @empty
                <tr>
                  <td colspan="4">No regenerated responses found.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
    @endisset
  </div>
</div>
@endsection
