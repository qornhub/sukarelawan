@csrf

<div class="form-container">
    <div class="blog-form-grid">
        <!-- Left Column - Blog Details -->
        <div class="blog-meta-section">
            <div class="row g-3">
                <!-- Blog Title - Full Width -->
                <div class="col-12">
                    <label for="title" class="form-label">Blog Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" id="title"
                        class="form-control @error('title') is-invalid @enderror"
                        value="{{ old('title', $post->title ?? '') }}" placeholder="Enter blog title" required>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>

                    <select name="category_id" id="category_id"
                        class="form-select @error('category_id') is-invalid @enderror" required>
                        <option value="" disabled
                            {{ old('category_id', $post->category_id ?? '') ? '' : 'selected' }}>
                            Choose Your Category
                        </option>

                        @foreach ($categories as $category)
                            <option value="{{ $category->blogCategory_id }}"
                                {{ old('category_id', $post->category_id ?? '') == $category->blogCategory_id ? 'selected' : '' }}>
                                {{ $category->categoryName }}
                            </option>
                        @endforeach

                        {{-- special “other” option (not saved to DB directly) --}}
                        <option value="other"
                            {{ old('category_id') == 'other' || !empty($post->custom_category) ? 'selected' : '' }}>
                            Other (Specify)
                        </option>

                    </select>

                    @error('category_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror

                    {{-- textbox for custom category --}}
                    <div id="otherCategoryWrapper" class="mt-2" style="display:none;">
                        <input type="text" name="custom_category" id="custom_category"
                            class="form-control @error('custom_category') is-invalid @enderror"
                            placeholder="Enter your custom category"
                            value="{{ old('custom_category', $post->custom_category ?? '') }}">
                        @error('custom_category')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>


                <div class="col-md-6">
                    <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                    <select name="status" id="status" class="form-select @error('status') is-invalid @enderror"
                        required>
                        <option value="draft" {{ old('status', $post->status ?? '') == 'draft' ? 'selected' : '' }}>
                            Draft</option>
                        <option value="published"
                            {{ old('status', $post->status ?? '') == 'published' ? 'selected' : '' }}>Published
                        </option>

                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Author & Published At - Half Width Each -->
                <div class="col-md-6">
                    <label for="author" class="form-label">Author</label>
                    <input type="text" name="author" id="author"
                        class="form-control @error('author') is-invalid @enderror"
                        value="{{ old('author', $post->author ?? Auth::user()->name) }}" placeholder="Author name">
                    @error('author')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="published_at" class="form-label">
                        Published At <span style="font-size: 0.85rem; color: #6c757d;">(cannot be edited)</span>
                    </label>

                    <input type="datetime-local" name="published_at" id="published_at"
                        class="form-control @error('published_at') is-invalid @enderror"
                        value="{{ old('published_at', $post->published_at ?? now()->format('Y-m-d\TH:i')) }}" readonly>

                    @error('published_at')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>


                <!-- Blog Summary - Full Width -->
                <div class="col-12">
                    <label for="blogSummary" class="form-label">Blog Summary</label>

                    <textarea name="blogSummary" id="blogSummary" class="form-control @error('blogSummary') is-invalid @enderror"
                        rows="4" maxlength="300" placeholder="Brief summary of your blog post (max 300 characters)">{{ old('blogSummary', $post->blogSummary ?? '') }}</textarea>

                    <div class="char-counter">
                        <span class="char-counter" id="summary-counter">0</span>/300 characters
                    </div>

                    @error('blogSummary')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>


                <!-- Header Image - Full Width -->
                <div class="col-12">
                    <label class="form-label">Header Image</label>
                    <div class="image-upload-section">
                        <div class="image-preview-container" id="imagePreviewContainer">
                            @if (!empty($post->image))
                                <img id="image-preview" src="{{ asset('images/Blog/' . basename($post->image)) }}"
                                    alt="Current Blog Image" style="display: block;">
                                <div class="upload-placeholder" id="image-placeholder" style="display: none;">
                                    <i class="bi bi-cloud-arrow-up"></i>
                                    <div class="upload-text">Select New Image</div>
                                    <div class="upload-hint">or drag and drop</div>
                                </div>
                            @else
                                <div class="upload-placeholder" id="image-placeholder">
                                    <i class="bi bi-cloud-arrow-up"></i>
                                    <div class="upload-text">Select New Image</div>
                                    <div class="upload-hint">or drag and drop</div>
                                </div>
                                <img id="image-preview" src="" alt="Selected Image Preview"
                                    style="display: none;">
                            @endif
                        </div>

                        <div class="file-input-wrapper">
                            <input type="file" name="image" id="image"
                                class="@error('image') is-invalid @enderror" accept="image/*">
                            <label for="image" class="file-input-label">
                                <i class="bi bi-upload"></i> Choose File
                            </label>
                        </div>
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column - Blog Content -->
        <div class="blog-content-section">
            <label for="content" class="content-label">Blog Contents <span class="text-danger">*</span></label>
            <textarea name="content" id="content" rows="14" class="form-control @error('content') is-invalid @enderror"
                placeholder="Write your blog content here..." required>{{ old('content', $post->content ?? '') }}</textarea>
            @error('content')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror

            <!-- Action Buttons -->
            <div class="form-actions ">
                <a href="{{ route('blogs.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> {{ isset($post) ? 'Save Changes' : 'Save Blog' }}
                </button>
            </div>
        </div>


    </div>
</div>

@push('scripts')
    <script src="https://cdn.tiny.cloud/1/pfjth33chx6jf9i6f3dluc05zg5hatcny7fdyaiza5bmpwme/tinymce/6/tinymce.min.js"
        referrerpolicy="origin"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const categorySelect = document.getElementById('category_id');
            const otherWrapper = document.getElementById('otherCategoryWrapper');
            const customCategoryInput = document.getElementById('custom_category');

            if (!categorySelect || !otherWrapper || !customCategoryInput) return;

            function toggleCustomCategory() {
                if (categorySelect.value === 'other') {
                    otherWrapper.style.display = 'block';
                    customCategoryInput.required = true;
                } else {
                    otherWrapper.style.display = 'none';
                    customCategoryInput.required = false;
                    // do NOT auto-clear here, so user doesn't lose data on validation errors
                }
            }

            // initial state (for edit page / after validation error)
            toggleCustomCategory();

            categorySelect.addEventListener('change', toggleCustomCategory);
        });


        // Initialize TinyMCE
        tinymce.init({
            selector: '#content',
            height: 540,
            menubar: false,
            plugins: 'lists link image table code preview',
            toolbar: 'undo redo | styles | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist | link image | code preview',
            content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }',
            placeholder: 'Write your blog content here...'
        });

        // Ensure TinyMCE content saved to textarea before submit
        document.addEventListener('submit', function(e) {
            if (typeof tinymce !== 'undefined') {
                tinymce.triggerSave();
            }
        }, true);

        // Character counter for summary
        document.addEventListener('DOMContentLoaded', function() {
            const summaryTextarea = document.getElementById('blogSummary');
            const counter = document.getElementById('summary-counter');

            if (summaryTextarea && counter) {
                // Set initial count
                counter.textContent = summaryTextarea.value.length;

                // Update on input
                summaryTextarea.addEventListener('input', function() {
                    counter.textContent = this.value.length;
                });
            }

            // Set current datetime as default for published_at if empty
            const publishedAtInput = document.getElementById('published_at');
            if (publishedAtInput && !publishedAtInput.value) {
                const now = new Date();
                // Format to datetime-local format: YYYY-MM-DDTHH:mm
                const year = now.getFullYear();
                const month = String(now.getMonth() + 1).padStart(2, '0');
                const day = String(now.getDate()).padStart(2, '0');
                const hours = String(now.getHours()).padStart(2, '0');
                const minutes = String(now.getMinutes()).padStart(2, '0');

                publishedAtInput.value = `${year}-${month}-${day}T${hours}:${minutes}`;
            }
        });

        // Enhanced Image Preview with Drag & Drop
        (function() {
            const input = document.getElementById('image');
            const previewContainer = document.getElementById('imagePreviewContainer');
            const previewImg = document.getElementById('image-preview');
            const placeholder = document.getElementById('image-placeholder');

            if (!input) return;

            // Click to upload
            previewContainer.addEventListener('click', function() {
                input.click();
            });

            // File selection handler
            input.addEventListener('change', function(evt) {
                handleFileSelection(evt.target.files[0]);
            });

            // Drag and drop handlers
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                previewContainer.addEventListener(eventName, preventDefaults, false);
            });

            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }

            ['dragenter', 'dragover'].forEach(eventName => {
                previewContainer.addEventListener(eventName, highlight, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                previewContainer.addEventListener(eventName, unhighlight, false);
            });

            function highlight() {
                previewContainer.classList.add('dragover');
            }

            function unhighlight() {
                previewContainer.classList.remove('dragover');
            }

            previewContainer.addEventListener('drop', function(e) {
                const dt = e.dataTransfer;
                const files = dt.files;
                handleFileSelection(files[0]);
            });

            function handleFileSelection(file) {
                if (!file || !file.type.match('image.*')) {
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    if (previewImg) {
                        previewImg.src = e.target.result;
                        previewImg.style.display = 'block';
                    }
                    if (placeholder) placeholder.style.display = 'none';
                };
                reader.readAsDataURL(file);
            }
        })();
    </script>
@endpush
