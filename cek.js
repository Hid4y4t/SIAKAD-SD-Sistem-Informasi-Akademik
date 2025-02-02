// Membuat array berisi object (struct) buku
let perpustakaan = [{
        judul: "Pemrograman Dasar",
        penulis: "Ahmad",
        tahunTerbit: 2018
    },
    {
        judul: "Algoritma dan Struktur Data",
        penulis: "Budi",
        tahunTerbit: 2020
    },
    {
        judul: "Desain Web dengan HTML dan CSS",
        penulis: "Citra",
        tahunTerbit: 2019
    }
];

// Fungsi untuk menampilkan semua buku di perpustakaan
function tampilkanBuku() {
    console.log("Daftar Buku di Perpustakaan:");
    for (let i = 0; i < perpustakaan.length; i++) {
        console.log(`Judul: ${perpustakaan[i].judul}, Penulis: ${perpustakaan[i].penulis}, Tahun Terbit: ${perpustakaan[i].tahunTerbit}`);
    }
    i
}

// Memanggil fungsi untuk menampilkan buku
tampilkanBuku();