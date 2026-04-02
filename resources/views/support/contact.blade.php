@extends('layouts.app')

@section('title', 'Contact Support - APARA System')

@section('content')
<div class="mb-4">
    <h4 class="mb-1">Contact Support</h4>
    <p class="text-muted mb-0">Internal support channels for operational, finance, and technical issues</p>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card p-4 h-100">
            <h5 class="mb-3">Support Directory</h5>
            <div class="row g-3">
                @foreach($contacts as $contact)
                    <div class="col-md-6">
                        <div class="border rounded p-3 h-100 bg-light">
                            <h6 class="mb-2">{{ $contact['team'] }}</h6>
                            <div><strong>Email:</strong> {{ $contact['email'] }}</div>
                            <div><strong>Phone:</strong> {{ $contact['phone'] }}</div>
                            <div><strong>Hours:</strong> {{ $contact['hours'] }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card p-4 h-100">
            <h5 class="mb-3">Quick Contact Form</h5>
            <form>
                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" class="form-control" placeholder="Enter your name">
                </div>
                <div class="mb-3">
                    <label class="form-label">Department</label>
                    <input type="text" class="form-control" placeholder="Enter your department">
                </div>
                <div class="mb-3">
                    <label class="form-label">Issue Type</label>
                    <select class="form-select">
                        <option>Technical Issue</option>
                        <option>Finance Issue</option>
                        <option>Account Access</option>
                        <option>General Inquiry</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Message</label>
                    <textarea class="form-control" rows="5" placeholder="Describe the issue clearly"></textarea>
                </div>
                <button type="button" class="btn btn-primary w-100">Send Request</button>
            </form>
        </div>
    </div>
</div>
@endsection
