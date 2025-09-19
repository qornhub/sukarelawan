<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Profile | SukaRelawan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/edit.css') }}">
</head>
<body>
    @include('layouts.volunteer_header')

    <div class="profile-container">
        <div class="profile-card">
            <div class="profile-header">
                <img id="profilePhotoPreview" src="{{ $profile->profilePhoto ? asset('images/profiles/' . $profile->profilePhoto) : asset('images/default-profile.png') }}" class="profile-avatar" alt="Profile Photo">
                <div class="profile-info">
                    <h1 class="profile-name">{{ $profile->name }}</h1>
                    <span class="profile-role">Volunteer</span>
                </div>
            </div>

            <div class="profile-body">
                <h2 class="section-title">
                    <i class="fas fa-user-edit"></i>
                    Edit Profile Information
                </h2>
                
                @include('layouts.messages')
                {{-- Show back button only if profile update is successful --}}
    @if(session('success'))
        <a href="{{ route('volunteer.profile.profile', auth()->user()->id) }}" class="btn btn-success mb-3">
            <i class="fas fa-arrow-left"></i> Back to Profile
        </a>
    @endif
                
                <form action="{{ route('volunteer.profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-user"></i>
                                    Full Name
                                </label>
                                <input type="text" name="name" value="{{ old('name', $profile->name) }}" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-phone"></i>
                                    Contact Number
                                </label>
                                <input type="text" name="contactNumber" value="{{ old('contactNumber', $profile->contactNumber) }}" class="form-control">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-globe-asia"></i>
                                    Country
                                </label>
                                <input type="text" name="country" value="{{ old('country', $profile->country) }}" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-birthday-cake"></i>
                                    Date of Birth
                                </label>
                                <input type="date" id="dateOfBirth" name="dateOfBirth" value="{{ old('dateOfBirth', $profile->dateOfBirth) }}" class="form-control">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-venus-mars"></i>
                                    Gender
                                </label>
                                <select name="gender" class="form-control">
                                    <option value="">Select Gender</option>
                                    <option value="male" {{ $profile->gender == 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ $profile->gender == 'female' ? 'selected' : '' }}>Female</option>
                                    <option value="other" {{ $profile->gender == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-map-marker-alt"></i>
                                    Address
                                </label>
                                <input type="text" name="address" value="{{ old('address', $profile->address) }}" class="form-control">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-image"></i>
                            Cover Photo
                        </label>
                        <input type="file" name="coverPhoto" class="form-control" accept="image/*" onchange="previewImage(event, 'coverPhotoPreview')">
                        <div class="photo-preview-container">
                            <div class="preview-card">
                                @if($profile->coverPhoto)
                                    <img id="coverPhotoPreview" src="{{ asset('images/covers/' . $profile->coverPhoto) }}" class="photo-preview">
                                @else
                                    <img id="coverPhotoPreview" class="photo-preview" style="display:none;">
                                @endif
                                <div class="preview-label">Cover Photo Preview</div>
                            </div>
                        </div>
                    </div>
                    
                <div class="mb-3">
    <label>Profile Photo</label>
    <input type="file" name="profilePhoto" class="form-control" accept="image/*"
        onchange="previewImage(event, 'profilePhotoPreview', 'profilePhotoPreviewInline')">

    @if($profile->profilePhoto)
        <img id="profilePhotoPreviewInline" src="{{ asset('images/profiles/' . $profile->profilePhoto) }}" class="photo-preview mt-2">
    @else
        <img id="profilePhotoPreviewInline" class="photo-preview mt-2" style="display:none;">
    @endif
     <div class="preview-label">Profile Photo Preview</div>
</div>




                    
                    <button type="submit" class="btn btn-submit">
                        <i class="fas fa-save"></i>
                        Update Profile
                    </button>
                </form>
            </div>
        </div>
    </div>

    @include('layouts.volunteer_footer')

    <script src="{{asset('js/edit.js')}}"></script>
</body>
</html>