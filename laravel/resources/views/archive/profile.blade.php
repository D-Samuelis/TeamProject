<!DOCTYPE html>
<html>
<head>
    <title>User Profile</title>
    <meta charset="UTF-8">
</head>
<body>

<h1>User Profile</h1>

@if(session('success'))
    <div style="color: green;">
        {{ session('success') }}
    </div>
@endif

<form method="POST" action="{{ route('profile.update') }}">
    @csrf

    <div>
        <label>Name:</label><br>
        <input type="text" name="name" value="{{ old('name', $user->name) }}" required>
    </div>

    <div>
        <label>Email (readonly):</label><br>
        <input type="email" value="{{ $user->email }}" disabled>
    </div>

    <div>
        <label>Country:</label><br>
        <input type="text" name="country" value="{{ old('country', $user->country) }}">
    </div>

    <div>
        <label>City:</label><br>
        <input type="text" name="city" value="{{ old('city', $user->city) }}">
    </div>

    <div>
        <label>Title Prefix:</label><br>
        <input type="text" name="title_prefix" value="{{ old('title_prefix', $user->title_prefix) }}">
    </div>

    <div>
        <label>Title Suffix:</label><br>
        <input type="text" name="title_suffix" value="{{ old('title_suffix', $user->title_suffix) }}">
    </div>

    <div>
        <label>Birth Date:</label><br>
        <input type="date" name="birth_date"
            value="{{ old('birth_date', optional($user->birth_date)->format('Y-m-d')) }}">
    </div>

    <div>
        <label>Phone Number:</label><br>
        <input type="text" name="phone_number" value="{{ old('phone_number', $user->phone_number) }}">
    </div>

    <div>
        <label>Gender:</label><br>
        <input type="text" name="gender" value="{{ old('gender', $user->gender) }}">
    </div>

    <br>
    <button type="submit">Update Profile</button>
</form>

</body>
</html>