<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit NGO Profile | SukaRelawan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- Bootstrap & Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/edit.css') }}">
</head>
<body>
    @include('layouts.ngo_header')

    <div class="profile-container">
        <div class="profile-card">
            <div class="profile-header">
                <img id="profilePhotoPreview" src="{{ $profile->profilePhoto ? asset('images/profiles/' . $profile->profilePhoto) : asset('images/default-profile.png') }}" class="profile-avatar" alt="Profile Photo">
                <div class="profile-info">
                    <h1 class="profile-name">{{ $profile->organizationName ?? 'Unnamed NGO' }}</h1>
                    <span class="profile-role">NGO</span>
                </div>
            </div>

            <div class="profile-body">
                <h2 class="section-title">
                    <i class="fas fa-building"></i>
                    Edit NGO Profile
                </h2>

                @include('layouts.messages')

                {{-- Back to profile when update succeeded --}}
                @if(session('success'))
                    <a href="{{ route('ngo.profile.self') }}" class="btn btn-success mb-3">
                        <i class="fas fa-arrow-left"></i> Back to Profile
                    </a>
                @endif

                <form action="{{ route('ngo.profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label"><i class="fas fa-building"></i> Organization Name</label>
                                <input type="text" name="organizationName" value="{{ old('organizationName', $profile->organizationName) }}" class="form-control" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label"><i class="fas fa-id-card"></i> Registration Number</label>
                                <input type="text" name="registrationNumber" value="{{ old('registrationNumber', $profile->registrationNumber) }}" class="form-control" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label class="form-label"><i class="fas fa-globe-asia"></i> Country</label>
                                <input type="text" name="country" value="{{ old('country', $profile->country) }}" class="form-control">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label class="form-label"><i class="fas fa-phone"></i> Contact Number</label>
                                <input type="text" name="contactNumber" value="{{ old('contactNumber', $profile->contactNumber) }}" class="form-control">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label class="form-label"><i class="fas fa-link"></i> Website</label>
                                <input type="url" name="website" value="{{ old('website', $profile->website) }}" class="form-control" placeholder="https://example.org">
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label"><i class="fas fa-align-left"></i> About</label>
                        <textarea name="about" rows="5" class="form-control">{{ old('about', $profile->about) }}</textarea>
                    </div>

                    <div class="form-group mb-4">
                        <label class="form-label"><i class="fas fa-image"></i> Cover Photo</label>
                        <input type="file" name="coverPhoto" class="form-control" accept="image/*" onchange="previewImage(event, 'coverPhotoPreview')">
                        <div class="photo-preview-container mt-2">
                            <div class="preview-card">
                                @if($profile->coverPhoto)
                                    <img id="coverPhotoPreview" src="{{ asset('images/covers/' . $profile->coverPhoto) }}" class="photo-preview" alt="Cover Preview">
                                @else
                                    <img id="coverPhotoPreview" class="photo-preview" style="display:none;" alt="Cover Preview">
                                @endif
                                <div class="preview-label">Cover Photo Preview</div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <label class="form-label"><i class="fas fa-user-circle"></i> Profile Photo</label>
                        <input type="file" name="profilePhoto" class="form-control" accept="image/*" onchange="previewImage(event, 'profilePhotoPreview', 'profilePhotoPreviewInline')">

                        <div class="mt-2">
                            @if($profile->profilePhoto)
                                <img id="profilePhotoPreviewInline" src="{{ asset('images/profiles/' . $profile->profilePhoto) }}" class="photo-preview mt-2" alt="Profile Preview">
                            @else
                                <img id="profilePhotoPreviewInline" class="photo-preview mt-2" style="display:none;" alt="Profile Preview">
                            @endif
                            <div class="preview-label">Profile Photo Preview</div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-submit">
                        <i class="fas fa-save"></i> Update Profile
                    </button>
                </form>
            </div>
        </div>
    </div>

    @include('layouts.ngo_footer')

    {{-- Use your existing edit.js which should implement previewImage(event, previewId, optionalSecondId) --}}
    <script src="{{ asset('js/edit.js') }}"></script>
</body>
</html>
