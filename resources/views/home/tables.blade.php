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
              @forelse($responses as $response)
                <tr>
                  <td>{{ $response->prompt->prompt_content }}</td>
                  <td>{{ $response->response_content }}</td>
                  <td>{{ $response->feedback->feedback_content }}</td>
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
              <!-- Example rows for demonstration, replace with actual data if available -->
              <tr>
                <td>What is the USSD code for the Shabablink plan?</td>
                <td>Hi there! For the Shabablink plan, you can use the USSD code *555#. Is there anything else I can help you with today?</td>
                <td>Thank you for the feedback. For the Shabablink plan, please dial *556# for a faster response.</td>
                <td>The answering process wasn't smooth and fast.</td>
              </tr>
              <tr>
                <td>What is the USSD code for the Shabablink plan?</td>
                <td>Hi there! For the Shabablink plan, you can use the USSD code *555#. Is there anything else I can help you with today?</td>
                <td>We've streamlined our process. Please try *556# for an instant reply.</td>
                <td>The website layout is a bit confusing.</td>
              </tr>
              <!-- Add more rows if necessary -->
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('javascripts')
@endsection
