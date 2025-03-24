<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>History - PlagCheck</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">

    <x-navbar />

    <!-- Konten History -->
    <div class="container mx-auto p-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Riwayat Pengecekan Plagiarisme</h1>

        <?php if (empty($histories)) : ?>
            <p class="text-gray-600">Belum ada riwayat pengecekan.</p>
        <?php else : ?>
            <div class="overflow-x-auto bg-white p-6 shadow rounded-lg">
                <table class="w-full border-collapse border border-gray-200">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="border px-4 py-2">Judul</th>
                            <th class="border px-4 py-2">Similarity (%)</th>
                            <th class="border px-4 py-2">Tanggal</th>
                            <th class="border px-4 py-2">Google Drive Link</th> <!-- Tambahkan kolom ini -->
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($histories as $history) : ?>
                            <tr class="border">
                                <td class="border px-4 py-2"><?= htmlspecialchars($history->document->title) ?></td>
                                <td class="border px-4 py-2"><?= htmlspecialchars($history->similarity_percentage ?? '-') ?>%</td>
                                <td class="border px-4 py-2"><?= htmlspecialchars($history->created_at->format('d M Y, H:i')) ?></td>
                                <td class="border px-4 py-2 text-blue-600">
                                    <?php if (isset($history->referenceDocument->google_drive_link)) : ?>
                                        <a href="<?= htmlspecialchars($history->referenceDocument->google_drive_link) ?>" target="_blank" class="underline">
                                            Lihat Dokumen
                                        </a>
                                    <?php else : ?>
                                        <span class="text-gray-500">Tidak tersedia</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <script>
        document.getElementById('userMenuButton').addEventListener('click', function () {
            document.getElementById('userMenu').classList.toggle('hidden');
        });
    </script>

</body>
</html>
