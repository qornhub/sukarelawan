@csrf

{{-- Title --}}
<div class="mb-3">
    <label for="title" class="form-label fw-bold">Title</label>
    <input 
        type="text" 
        name="title" 
        id="title" 
        class="form-control @error('title') is-invalid @enderror" 
        value="{{ old('title', $post->title ?? '') }}" 
        required
    >
    @error('title')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

{{-- Category --}}
<div class="mb-3">
    <label for="category_id" class="form-label fw-bold">Category</label>
    <select 
        name="category_id" 
        id="category_id" 
        class="form-select @error('category_id') is-invalid @enderror" 
        required
    >
        <option value="" disabled {{ !isset($post) ? 'selected' : '' }}>-- Select Category --</option>
        @foreach($categories as $category)
            <option 
                value="{{ $category->blogCategory_id }}" 
                {{ old('category_id', $post->category_id ?? '') == $category->blogCategory_id ? 'selected' : '' }}
            >
                {{ $category->categoryName }}
            </option>
        @endforeach
    </select>
    @error('category_id')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

{{-- Image --}}
<div class="mb-3">
    <label for="image" class="form-label fw-bold">Featured Image (Optional)</label>
    <input 
        type="file" 
        name="image" 
        id="image" 
        class="form-control @error('image') is-invalid @enderror" 
        accept="image/*"
    >
    @error('image')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror

    @if(!empty($post->image))
        <div class="mt-2">
            <p class="mb-1 fw-bold">Current Image:</p>
            <img src="{{ asset('images/Blog/' . $post->image) }}" alt="Current Blog Image" class="img-fluid rounded shadow-sm" style="max-height: 200px;">
        </div>
    @endif
</div>

{{-- Content (with TinyMCE) --}}
<div class="mb-3">
    <label for="content" class="form-label fw-bold">Content</label>
    <textarea 
        name="content" 
        id="content" 
        rows="10" 
        class="form-control @error('content') is-invalid @enderror" 
        required
    >{{ old('content', $post->content ?? '') }}</textarea>
    @error('content')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

{{-- Status --}}
<div class="mb-3">
    <label for="status" class="form-label fw-bold">Status</label>
    <select 
        name="status" 
        id="status" 
        class="form-select @error('status') is-invalid @enderror" 
        required
    >
        @php
            $statusValue = old('status', $post->status ?? 'draft');
        @endphp
        <option value="draft" {{ $statusValue == 'draft' ? 'selected' : '' }}>Draft (Save for later)</option>
        <option value="published" {{ $statusValue == 'published' ? 'selected' : '' }}>Published (Make public)</option>
    </select>
    @error('status')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

{{-- Published At --}}
<div class="mb-3">
    <label for="published_at" class="form-label fw-bold">Publish Date (Optional)</label>
    <input 
        type="datetime-local" 
        name="published_at" 
        id="published_at" 
        class="form-control @error('published_at') is-invalid @enderror"
        value="{{ old('published_at', isset($post->published_at) ? \Carbon\Carbon::parse($post->published_at)->format('Y-m-d\TH:i') : '') }}"
    >
    @error('published_at')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
    <small class="text-muted">If you leave this empty and choose "Published", it will be published immediately.</small>
</div>

{{-- Submit Buttons --}}
<div class="text-end mt-4">
    <button type="submit" class="btn btn-success">
        <i class="bi bi-save"></i> {{ isset($post) ? 'Update Post' : 'Create Post' }}
    </button>
    <a href="{{ route('blogs.index') }}" class="btn btn-secondary">Cancel</a>
</div>

{{-- TinyMCE Script --}}
@push('scripts')
<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    tinymce.init({
        selector: '#content',
        height: 400,
        menubar: false,
        plugins: 'lists link image table code preview',
        toolbar: 'undo redo | styles | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist | link image | code preview',
        content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }'
    });
</script>
@endpush
