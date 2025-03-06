<!-- resources/views/upload.blade.php -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload & Cek Plagiarisme</title>
</head>
<body>

    <h2>Upload Dokumen & Cek Plagiarisme</h2>
    
    <form action="{{ route('documents.uploadCheck') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <label for="file">Pilih File PDF:</label>
        <input type="file" name="file" required>
        <button type="submit">Upload & Cek</button>
    </form>

    @if(session('result'))
    <h3>Hasil Pemeriksaan Plagiarisme</h3>
    <p><strong>Similarity Tertinggi:</strong> {{ session('result')['highest_similarity']['similarity'] }}%</p>
    <p><strong>Referensi dengan Similarity Tertinggi:</strong> {{ session('result')['highest_similarity']['reference_title'] }}</p>

    <h4>Perbandingan dengan Referensi:</h4>
    <ul>
        @foreach(session('result')['comparisons'] as $comparison)
            <li>{{ $comparison['reference_title'] }} - {{ $comparison['similarity'] }}%</li>
        @endforeach
    </ul>
@endif

    {{-- <h2>Upload Dokumen Referensi</h2>
    
    <form action="{{ route('documents.uploadReference') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <label for="title">Judul Referensi:</label>
        <input type="text" name="title" required>
        <label for="file">Pilih File PDF:</label>
        <input type="file" name="file" required>
        <button type="submit">Upload Referensi</button>
    </form> --}}

</body>
</html>
