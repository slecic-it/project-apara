@extends('layouts.app')

@section('title', 'FAQ - APARA System')

@section('content')
<div class="mb-4">
    <h4 class="mb-1">FAQ</h4>
    <p class="text-muted mb-0">Quick answers for the most common operational questions</p>
</div>

<div class="card p-4">
    <div class="accordion" id="faqAccordion">
        @foreach($faqs as $faq)
            <div class="accordion-item">
                <h2 class="accordion-header" id="heading{{ $loop->index }}">
                    <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $loop->index }}">
                        {{ $faq['question'] }}
                    </button>
                </h2>
                <div id="collapse{{ $loop->index }}" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        {{ $faq['answer'] }}
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
