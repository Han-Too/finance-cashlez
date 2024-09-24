function to_date_time(date) {
    let tanggal = new Date(date);
    return tanggal.getFullYear()+"-"
        + (tanggal.getMonth()+ 1 > 9 ? (tanggal.getMonth()+ 1).toString() : "0" + (tanggal.getMonth()+ 1).toString())
        +"-"
        +(tanggal.getDate() > 9 ? tanggal.getDate().toString() : "0" + tanggal.getDate().toString())
        + " "
        +(tanggal.getHours().toString() > 9 ? tanggal.getHours().toString() : "0" + tanggal.getHours().toString())
        + ":" + (tanggal.getUTCMinutes().toString() > 9 ? tanggal.getUTCMinutes().toString() : "0" + tanggal.getUTCMinutes().toString())
        + ":" + (tanggal.getUTCSeconds().toString() > 9 ? tanggal.getUTCSeconds().toString() : "0" + tanggal.getUTCSeconds().toString());
}

function to_date(date) {
    let tanggal = new Date(date);
    return tanggal.getFullYear()+"-"
        + (tanggal.getMonth()+ 1 > 9 ? (tanggal.getMonth()+ 1).toString() : "0" + (tanggal.getMonth()+ 1).toString())
        +"-"
        +(tanggal.getDate() > 9 ? tanggal.getDate().toString() : "0" + tanggal.getDate().toString());
}

function oldto_rupiah(nominal) {
    var parts = nominal.toString().split('.');

    var reverseInteger = parts[0].split('').reverse().join('');
    var ribuan = reverseInteger.match(/\d{1,3}/g).join(',').split('').reverse().join('');

    var result = `Rp. ${ribuan}`;
    if (parts.length > 1) {
        result += '.' + parts[1];
    }

    return result;
}

function to_rupiah(angka) {

    var numberString = angka.toString(),
        split = numberString.split('.'),
        sisa = split[0].length % 3,
        rupiah = split[0].substr(0, sisa),
        ribuan = split[0].substr(sisa).match(/\d{3}/gi);

    // Tambahkan titik jika yang diinput sudah menjadi angka ribuan
    if (ribuan) {
        var separator = sisa ? '.' : '';
        rupiah += separator + ribuan.join('.');
    }

    rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
    return `Rp ${rupiah}`;
}

function formatRupiah(number) {
    let reverse = number.toString().split('').reverse().join('');
    let ribuan = reverse.match(/\d{1,3}/g);
    let hasil = ribuan.join('.').split('').reverse().join('');
    return 'Rp ' + hasil;
}

function getTokenFromUrl(regex){
    var currentURL = window.location.href;

    var tokenRegex = regex;

    var match = currentURL.match(tokenRegex);
    var token = match ? match[1] : null;

    return token
}