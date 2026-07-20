@extends('layouts.admin')

@section('content')
<main class="content-area d-flex flex-column w-100 h-100 gap-4 overflow-auto pe-auto p-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-2">
        <div>
            <h1 class="h3 fw-bold text-white mb-0 d-flex align-items-center gap-2">
                <span class="material-symbols-outlined text-purple-neon fs-2">article</span> 
                Analysis Articles Management
            </h1>
            <p class="text-muted fs-7 mt-1">Manage global chain analysis and insights</p>
        </div>
        <button class="btn btn-primary d-flex align-items-center gap-2 px-3 py-2" data-bs-toggle="modal" data-bs-target="#addArticleModal">
            <span class="material-symbols-outlined fs-5">edit_document</span> Write New Article
        </button>
    </div>

    <!-- Articles List -->
    <div class="flex-grow-1">
        <x-card title="Published & Draft Articles" icon="feed" glow="purple" class="h-100">
            <div class="table-responsive p-3 h-100 overflow-auto" style="max-height: calc(100vh - 200px);">
                <table class="table table-dark table-hover align-middle mb-0">
                    <thead>
                        <tr class="border-bottom border-secondary border-opacity-25">
                            <th class="text-muted fs-8 text-uppercase pb-2">Title</th>
                            <th class="text-muted fs-8 text-uppercase pb-2">Country/Source</th>
                            <th class="text-muted fs-8 text-uppercase pb-2">Author</th>
                            <th class="text-muted fs-8 text-uppercase pb-2">Status</th>
                            <th class="text-muted fs-8 text-uppercase pb-2">Date</th>
                            <th class="text-muted fs-8 text-uppercase pb-2 text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($articles as $article)
                        <tr>
                            <td class="text-white fw-bold">
                                {{ Str::limit($article->title, 50) }}
                                <div class="fs-8 text-muted fw-normal mt-1">{{ Str::limit(strip_tags($article->content), 60) }}</div>
                            </td>
                            <td>
                                @if($article->country)
                                    <span class="badge bg-secondary bg-opacity-25 text-light border border-secondary border-opacity-25 mb-1 d-inline-block">{{ $article->country }}</span>
                                @endif
                                @if($article->source_url)
                                    <div class="fs-8"><a href="{{ $article->source_url }}" target="_blank" class="text-info text-decoration-none"><span class="material-symbols-outlined fs-8 align-middle">link</span> Source URL</a></div>
                                @endif
                            </td>
                            <td class="text-muted">{{ $article->author->name ?? 'Unknown' }}</td>
                            <td>
                                @if($article->status == 'published')
                                    <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-25">Published</span>
                                @else
                                    <span class="badge bg-warning bg-opacity-25 text-warning border border-warning border-opacity-25">Draft</span>
                                @endif
                            </td>
                            <td class="text-muted fs-8">{{ $article->created_at->format('d M Y, H:i') }}</td>
                            <td class="text-end">
                                <form action="{{ route('admin.articles.delete', $article->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this article permanently?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-2" title="Delete">
                                        <span class="material-symbols-outlined fs-6">delete</span>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-5">
                                <span class="material-symbols-outlined fs-1 text-secondary mb-2 d-block">inventory_2</span>
                                No articles found. Start writing!
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-4 d-flex justify-content-center pagination-dark">
                    {{ $articles->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </x-card>
    </div>
</main>

<style>
    .pagination-dark .pagination {
        margin-bottom: 0;
        --bs-pagination-bg: transparent;
        --bs-pagination-border-color: rgba(255,255,255,0.1);
        --bs-pagination-color: #a0aec0;
        --bs-pagination-hover-bg: rgba(255,255,255,0.05);
        --bs-pagination-hover-color: #fff;
        --bs-pagination-hover-border-color: rgba(255,255,255,0.2);
        --bs-pagination-focus-bg: rgba(255,255,255,0.05);
        --bs-pagination-active-bg: rgba(168, 85, 247, 0.2);
        --bs-pagination-active-border-color: #a855f7;
        --bs-pagination-disabled-bg: transparent;
        --bs-pagination-disabled-border-color: rgba(255,255,255,0.05);
    }
    .pagination-dark .page-item.active .page-link {
        color: #a855f7;
        box-shadow: 0 0 10px rgba(168, 85, 247, 0.3);
    }
</style>

<!-- Add Article Modal -->
<div class="modal fade" id="addArticleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-secondary shadow-lg" style="background-color: #0b0f19;">
            <form action="{{ route('admin.articles.store') }}" method="POST">
                @csrf
                <div class="modal-header border-secondary border-opacity-25" style="background-color: #121826;">
                    <h5 class="modal-title text-white d-flex align-items-center gap-2">
                        <span class="material-symbols-outlined text-purple-neon">edit_document</span> Write New Article
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label text-muted">Article Title</label>
                        <input type="text" name="title" class="form-control border-secondary text-white shadow-none" style="background-color: #121826;" placeholder="Enter an engaging title..." required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted">Country Tag (Optional)</label>
                            <select name="country" class="form-select border-secondary text-white shadow-none" style="background-color: #121826;">
                                <option value="">Global / No Specific Country</option>
                                @foreach($countries as $c)
                                    <option value="{{ $c['name'] }}">{{ $c['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Source URL (Global News / External)</label>
                            <input type="url" name="source_url" class="form-control border-secondary text-white shadow-none" style="background-color: #121826;" placeholder="https://news.example.com">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label text-muted">Content / Analysis</label>
                        <textarea name="content" class="form-control border-secondary text-white shadow-none" style="background-color: #121826; min-height: 200px;" placeholder="Write your analysis here... (HTML tags supported)" required></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label text-muted">Status</label>
                        <select name="status" class="form-select border-secondary text-white shadow-none" style="background-color: #121826;" required>
                            <option value="draft">Save as Draft</option>
                            <option value="published">Publish Immediately</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-secondary border-opacity-25" style="background-color: #121826;">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm">Save Article</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
